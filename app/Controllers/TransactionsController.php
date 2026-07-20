<?php

namespace App\Controllers;

use App\Models\CompteModel;
use App\Models\TransactionsModel;
use App\Models\PrefixeModel;
use App\Models\BaremeModel;

class TransactionsController extends BaseController
{
    public function store()
    {
        $session = session()->get('client_session');
        if (!$session) {
            return redirect()->to('/auth/login');
        }

        $compteModel = new CompteModel();
        $prefixeModel = new PrefixeModel();
        $baremeModel = new BaremeModel();
        $txModel = new TransactionsModel();

        $expediteur = $compteModel->find($session['id']);
        $montantTotal = (float)$this->request->getPost('montant');
        $rawDestinataires = $this->request->getPost('destinataires');
        $inclureFraisRetrait = $this->request->getPost('inclure_frais') == '1';

        // 1. Nettoyer et extraire la liste des numéros
        $destinataires = array_filter(array_map('trim', explode(',', $rawDestinataires)));
        $totalDestinataires = count($destinataires);

        if ($totalDestinataires === 0 || $montantTotal <= 0) {
            return redirect()->back()->with('error', 'Données de transaction invalides.');
        }

        // 2. Vérifier que tous les destinataires appartiennent au MÊME opérateur
        $idOperateurCommun = null;
        $prefixesData = $prefixeModel->findAll(); // Doit contenir un champ 'id_operateur' ou 'type_reseau' grâce à la tâche de Nofy

        foreach ($destinataires as $num) {
            $operateurDuNumero = null;
            $estExterne = true;

            foreach ($prefixesData as $p) {
                if (strpos($num, $p['prefixe']) === 0) {
                    // Supposons que Nofy a ajouté 'type_reseau' ('propre' ou 'externe') ou 'id_operateur'
                    $operateurDuNumero = $p['id_operateur'] ?? $p['type_reseau'] ?? 'default';
                    $estExterne = isset($p['type_reseau']) && $p['type_reseau'] === 'externe';
                    break;
                }
            }

            if (!$operateurDuNumero) {
                return redirect()->back()->with('error', "Le numéro $num n'est pas reconnu par le système.");
            }

            if ($idOperateurCommun === null) {
                $idOperateurCommun = $operateurDuNumero;
                $communEstExterne = $estExterne; 
            } elseif ($idOperateurCommun !== $operateurDuNumero) {
                return redirect()->back()->with('error', "Tous les numéros doivent appartenir au même opérateur.");
            }
        }

        // 3. Division équitable du montant
        $montantParDestinataire = $montantTotal / $totalDestinataires;
        
        // Détermination des frais d'envoi globaux pour la tranche du montant divisé
        // Type 3 = Transfert
        $baremeEnvoi = $baremeModel->where('id_type_operation', 3)
                                   ->where('montant_min <=', $montantParDestinataire)
                                   ->where('montant_max >=', $montantParDestinataire)
                                   ->first();
        $fraisEnvoiUnitaire = $baremeEnvoi ? (float)$baremeEnvoi['frais'] : 0;

        // Calcul des frais de retrait unitaires si l'option est cochée ET que ce n'est pas un réseau externe
        $fraisRetraitUnitaire = 0;
        if ($inclureFraisRetrait && !$communEstExterne) {
            // Type 2 = Retrait
            $baremeRetrait = $baremeModel->where('id_type_operation', 2)
                                         ->where('montant_min <=', $montantParDestinataire)
                                         ->where('montant_max >=', $montantParDestinataire)
                                         ->first();
            $fraisRetraitUnitaire = $baremeRetrait ? (float)$baremeRetrait['frais'] : 0;
        }

        // 4. Calcul du coût total requis chez l'expéditeur
        // Formule : (Montant à envoyer + Frais Envoi + Frais Retrait éventuels) * Nombre de personnes
        $coutTotalRetire = ($montantParDestinataire + $fraisEnvoiUnitaire + $fraisRetraitUnitaire) * $totalDestinataires;

        if ($expediteur['solde'] < $coutTotalRetire) {
            return redirect()->back()->with('error', "Solde insuffisant. Il vous faut au total " . number_format($coutTotalRetire, 2, ',', ' ') . " Ar (Frais inclus).");
        }

        // 5. Exécution des transactions
        $db = \Config\Database::connect();
        $db->transStart();

        // Déduction globale chez l'expéditeur
        $compteModel->update($expediteur['id'], [
            'solde' => $expediteur['solde'] - $coutTotalRetire
        ]);

        foreach ($destinataires as $num) {
            // Connexion/Création automatique du compte destinataire
            $destCompte = $compteModel->where('telephone', $num)->orWhere('tel', $num)->first();
            if (!$destCompte) {
                $fieldName = array_contains($compteModel->allowedFields, 'tel') ? 'tel' : 'telephone';
                $destId = $compteModel->insert([$fieldName => $num, 'solde' => 0.0]);
                $destCompte = $compteModel->find($destId);
            }

            // Calcul du montant exact reçu par le destinataire (+ le supplément retrait si actif)
            $montantRecu = $montantParDestinataire + $fraisRetraitUnitaire;

            // Créditer le destinataire
            $compteModel->update($destCompte['id'], [
                'solde' => $destCompte['solde'] + $montantRecu
            ]);

            // Enregistrer la transaction
            $txModel->insert([
                'id_compte_expediteur'   => $expediteur['id'],
                'id_compte_destinataire' => $destCompte['id'],
                'id_type_operation'      => 3, // Transfert
                'montant'                => $montantParDestinataire,
                'frais'                  => $fraisEnvoiUnitaire,
                'frais_retrait_inclus'   => $fraisRetraitUnitaire, // Nouveau champ pour le suivi opérateur si nécessaire
                'date_transaction'       => date('Y-m-d H:i:s')
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', "Une erreur technique est survenue lors du transfert.");
        }

        return redirect()->to('/client/dashboard')->with('success', "Transfert effectué avec succès à $totalDestinataires destinataire(s) !");
    }
}