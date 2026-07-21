<?php

namespace App\Controllers;

use App\Models\CompteModel;
use App\Models\TransactionsModel;
use App\Models\PrefixeModel;
use App\Models\BaremesModel;
use App\Models\OperateurModel;
use App\Models\InteroperatorCommissionModel;

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
        $baremeModel = new BaremesModel();
        $operateurModel = new OperateurModel();
        $commissionModel = new InteroperatorCommissionModel();
        $txModel = new TransactionsModel();

        $expediteur = $compteModel->find($session['id']);
        $typeOperation = (int)$this->request->getPost('type_operation');
        $montantTotal = (float)$this->request->getPost('montant');
        $inclureFraisRetrait = $this->request->getPost('inclure_frais') == '1';

        if ($montantTotal <= 0) {
            return redirect()->back()->with('error', 'Montant invalide.');
        }

        if ($typeOperation === 1) {
            // Dépôt
            $compteModel->update($expediteur['id'], [
                'solde' => $expediteur['solde'] + $montantTotal
            ]);

            $txModel->insert([
                'id_compte_expediteur'   => $expediteur['id'],
                'id_compte_destinataire' => null,
                'id_type_operation'      => 1,
                'montant'                => $montantTotal,
                'frais'                  => 0,
                'frais_retrait_inclus'   => 0,
                'commission_interoperateur' => 0,
                'id_operateur_destinataire' => null,
                'date_transaction'       => date('Y-m-d H:i:s')
            ]);

            return redirect()->to('/client/dashboard')->with('success', 'Dépôt effectué avec succès.');
        }

        if ($typeOperation === 2) {
            // Retrait
            $baremeRetrait = $baremeModel->where('id_type_operation', 2)
                                         ->where('montant_min <=', $montantTotal)
                                         ->where('montant_max >=', $montantTotal)
                                         ->first();
            $fraisRetrait = $baremeRetrait ? (float)$baremeRetrait['frais'] : 0;
            $coutTotal = $montantTotal + $fraisRetrait;

            if ($expediteur['solde'] < $coutTotal) {
                return redirect()->back()->with('error', 'Solde insuffisant pour ce retrait.');
            }

            $compteModel->update($expediteur['id'], [
                'solde' => $expediteur['solde'] - $coutTotal
            ]);

            $txModel->insert([
                'id_compte_expediteur'   => $expediteur['id'],
                'id_compte_destinataire' => null,
                'id_type_operation'      => 2,
                'montant'                => $montantTotal,
                'frais'                  => $fraisRetrait,
                'frais_retrait_inclus'   => 0,
                'commission_interoperateur' => 0,
                'id_operateur_destinataire' => null,
                'date_transaction'       => date('Y-m-d H:i:s')
            ]);

            return redirect()->to('/client/dashboard')->with('success', 'Retrait effectué avec succès.');
        }

        if ($typeOperation !== 3) {
            return redirect()->back()->with('error', 'Type d\'opération invalide.');
        }

        $rawDestinataires = $this->request->getPost('destinataires');
        $destinataires = array_filter(array_map('trim', explode(',', $rawDestinataires)));
        $totalDestinataires = count($destinataires);

        if ($totalDestinataires === 0) {
            return redirect()->back()->with('error', 'Aucun destinataire spécifié.');
        }

        // 2. Vérifier que tous les destinataires appartiennent au MÊME opérateur
        $idOperateurCommun = null;
        $communEstExterne = false;
        $prefixesData = $prefixeModel->findAll();

        usort($prefixesData, function ($a, $b) {
            return strlen($b['prefixe']) - strlen($a['prefixe']);
        });

        foreach ($destinataires as $num) {
            $operateurDuNumero = null;

            foreach ($prefixesData as $p) {
                if (strpos($num, $p['prefixe']) === 0) {
                    $operateurDuNumero = $p['id_operateur'];
                    break;
                }
            }

            if (!$operateurDuNumero) {
                return redirect()->back()->with('error', "Le numéro $num n'est pas reconnu par le système.");
            }

            if ($idOperateurCommun === null) {
                $idOperateurCommun = $operateurDuNumero;
                $operateurDest = $operateurModel->find($operateurDuNumero);
                $communEstExterne = isset($operateurDest['type_reseau']) && $operateurDest['type_reseau'] === 'externe';
            } elseif ($idOperateurCommun !== $operateurDuNumero) {
                return redirect()->back()->with('error', 'Tous les numéros doivent appartenir au même opérateur.');
            }
        }

        $commissionRate = 0;
        if ($communEstExterne) {
            $commissionConfig = $commissionModel->where('id_operateur', $idOperateurCommun)->first();
            $commissionRate = $commissionConfig ? (float)$commissionConfig['pourcentage'] : 0;
        }

        // 3. Montant saisi par destinataire
        $montantParDestinataire = $montantTotal;
        
        // Détermination des frais d'envoi globaux pour la tranche du montant par destinataire
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

        $commissionUnitaire = 0;
        if ($communEstExterne && $commissionRate > 0) {
            $commissionUnitaire = ($montantParDestinataire * $commissionRate) / 100;
        }

        // 4. Calcul du coût total requis chez l'expéditeur
        // Formule : (Montant à envoyer + Frais Envoi + Frais Retrait éventuels + Commission) * Nombre de personnes
        $coutTotalRetire = ($montantParDestinataire + $fraisEnvoiUnitaire + $fraisRetraitUnitaire + $commissionUnitaire) * $totalDestinataires;

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
            $query = $compteModel->where('telephone', $num);
            if (in_array('tel', $compteModel->allowedFields, true)) {
                $query = $query->orWhere('tel', $num);
            }
            $destCompte = $query->first();

            if (!$destCompte) {
                $fieldName = in_array('tel', $compteModel->allowedFields, true) ? 'tel' : 'telephone';
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
                'id_compte_expediteur'        => $expediteur['id'],
                'id_compte_destinataire'      => $destCompte['id'],
                'id_type_operation'           => 3, // Transfert
                'montant'                     => $montantParDestinataire,
                'frais'                       => $fraisEnvoiUnitaire,
                'frais_retrait_inclus'        => $fraisRetraitUnitaire,
                'commission_interoperateur'   => $commissionUnitaire,
                'id_operateur_destinataire'   => $idOperateurCommun,
                'date_transaction'            => date('Y-m-d H:i:s')
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', "Une erreur technique est survenue lors du transfert.");
        }

        return redirect()->to('/client/dashboard')->with('success', "Transfert effectué avec succès à $totalDestinataires destinataire(s) !");
    }
}