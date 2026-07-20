<?php

namespace App\Controllers;

use App\Models\TransactionsModel;
use App\Models\CompteModel;
use App\Models\BaremesModel;

class TransactionsController extends BaseController
{
    protected $transactionModel;
    protected $compteModel;
    protected $baremeModel;

    public function __construct()
    {
        $this->transactionModel = new TransactionsModel();
        $this->compteModel      = new CompteModel();
        $this->baremeModel      = new BaremesModel();
    }

    /**
     * Dispatche les opérations selon le type
     */
    public function store()
    {
        $typeOp = (int) $this->request->getPost('type_operation');
        $session = session()->get('client_session');

        if (!$session) {
            return redirect()->to('/');
        }

        $montant = (float) $this->request->getPost('montant');

        if ($typeOp === 1) {
            return $this->depot($session['id'], $montant);
        } elseif ($typeOp === 2) {
            return $this->retrait($session['id'], $montant);
        } elseif ($typeOp === 3) {
            $destinataire = $this->request->getPost('destinataire');
            return $this->transfert($session['id'], $destinataire, $montant);
        }

        return redirect()->back()->with('error', 'Type d\'opération invalide.');
    }

    /**
     * Calcul des frais selon le barème (Méthode privée du contrôleur)
     */
    private function calculerFrais(int $typeOpId, float $montant): float
    {
        $bareme = $this->baremeModel
                       ->where('id_type_operation', $typeOpId)
                       ->where('montant_min <=', $montant)
                       ->where('montant_max >=', $montant)
                       ->first();

        return $bareme ? (float)$bareme['frais'] : 0.0;
    }

    /**
     * Consultation du solde et historique
     */
    public function index($compteId)
    {
        $compte = $this->compteModel->find($compteId);

        if (!$compte) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Compte introuvable']);
        }

        $historique = $this->transactionModel->getHistoriqueByCompte($compteId);

        return $this->response->setJSON([
            'status'     => 'success',
            'compte'     => $compte,
            'historique' => $historique
        ]);
    }

    /**
     * Dépôt : Aucun frais
     */
    public function depot($compteId = null, $montant = null)
    {
        if (!$compteId || !$montant) {
            $compteId = $this->request->getPost('compte_id');
            $montant  = (float) $this->request->getPost('montant');
        }

        if ($montant <= 0) {
            return redirect()->back()->with('error', 'Montant invalide');
        }

        try {
            $compte = $this->compteModel->find($compteId);
            if (!$compte) {
                return redirect()->back()->with('error', 'Compte inexistant');
            }

            $db = \Config\Database::connect();
            $db->transStart();

            // 1. Calcul et mise à jour du nouveau solde
            $nouveauSolde = $compte['solde'] + $montant;
            $this->compteModel->update($compteId, ['solde' => $nouveauSolde]);

            // 2. Enregistrement de la transaction (1 = Dépôt)
            $this->transactionModel->insert([
                'id_type_operation'     => 1,
                'id_compte_expediteur'  => $compteId,
                'id_compte_destinataire'=> null,
                'montant'               => $montant,
                'frais'                 => 0.0
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->with('error', 'Erreur lors du dépôt');
            }

            return redirect()->to('client/dashboard')->with('success', 'Dépôt de ' . number_format($montant, 2, ',', ' ') . ' Ar réussi!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Retrait : Calcul des frais + Vérification solde
     */
    public function retrait($compteId = null, $montant = null)
    {
        if (!$compteId || !$montant) {
            $compteId = $this->request->getPost('compte_id');
            $montant  = (float) $this->request->getPost('montant');
        }

        if ($montant <= 0) {
            return redirect()->back()->with('error', 'Montant invalide');
        }

        try {
            $compte = $this->compteModel->find($compteId);
            if (!$compte) {
                return redirect()->back()->with('error', 'Compte inexistant');
            }

            // --- CALCULS ET VALIDATION DANS LE CONTRÔLEUR ---
            $frais = $this->calculerFrais(2, $montant); // 2 = Retrait
            $totalADebiter = $montant + $frais;

            if ($compte['solde'] < $totalADebiter) {
                return redirect()->back()->with('error', 'Solde insuffisant. Requis: ' . number_format($totalADebiter, 2, ',', ' ') . ' Ar (Montant: ' . number_format($montant, 2, ',', ' ') . ' + Frais: ' . number_format($frais, 2, ',', ' ') . ')');
            }

            $db = \Config\Database::connect();
            $db->transStart();

            // 1. Mise à jour du solde
            $nouveauSolde = $compte['solde'] - $totalADebiter;
            $this->compteModel->update($compteId, ['solde' => $nouveauSolde]);

            // 2. Enregistrement transaction
            $this->transactionModel->insert([
                'id_type_operation'     => 2,
                'id_compte_expediteur'  => $compteId,
                'id_compte_destinataire'=> null,
                'montant'               => $montant,
                'frais'                 => $frais
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->with('error', 'Erreur lors du retrait');
            }

            return redirect()->to('client/dashboard')->with('success', 'Retrait de ' . number_format($montant, 2, ',', ' ') . ' Ar effectué (frais: ' . number_format($frais, 2, ',', ' ') . ' Ar)');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Transfert : Calcul des frais + Débit expéditeur + Crédit destinataire
     */
    public function transfert($expediteurId = null, $destinataireTel = null, $montant = null)
    {
        if (!$expediteurId || !$destinataireTel || !$montant) {
            $expediteurId   = $this->request->getPost('expediteur_id');
            $destinataireTel = $this->request->getPost('destinataire_tel');
            $montant        = (float) $this->request->getPost('montant');
        }

        if ($montant <= 0) {
            return redirect()->back()->with('error', 'Montant invalide');
        }

        try {
            $expediteur   = $this->compteModel->find($expediteurId);
            $destinataire = $this->compteModel->where('telephone', $destinataireTel)->first();

            if (!$expediteur) {
                return redirect()->back()->with('error', 'Compte expéditeur introuvable');
            }

            if (!$destinataire) {
                return redirect()->back()->with('error', 'Destinataire non trouvé: ' . $destinataireTel);
            }

            if ($expediteurId == $destinataire['id']) {
                return redirect()->back()->with('error', 'Impossible de transférer vers votre propre compte');
            }

            // --- CALCULS ET VALIDATION DANS LE CONTRÔLEUR ---
            $frais = $this->calculerFrais(3, $montant); // 3 = Transfert
            $totalADebiter = $montant + $frais;

            if ($expediteur['solde'] < $totalADebiter) {
                return redirect()->back()->with('error', 'Solde insuffisant. Requis: ' . number_format($totalADebiter, 2, ',', ' ') . ' Ar');
            }

            $db = \Config\Database::connect();
            $db->transStart();

            // 1. Débit expéditeur
            $nouveauSoldeExp = $expediteur['solde'] - $totalADebiter;
            $this->compteModel->update($expediteurId, ['solde' => $nouveauSoldeExp]);

            // 2. Crédit destinataire
            $nouveauSoldeDest = $destinataire['solde'] + $montant;
            $this->compteModel->update($destinataire['id'], ['solde' => $nouveauSoldeDest]);

            // 3. Enregistrement transaction
            $this->transactionModel->insert([
                'id_type_operation'     => 3,
                'id_compte_expediteur'  => $expediteurId,
                'id_compte_destinataire'=> $destinataire['id'],
                'montant'               => $montant,
                'frais'                 => $frais
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->with('error', 'Erreur lors du transfert');
            }

            return redirect()->to('client/dashboard')->with('success', 'Transfert de ' . number_format($montant, 2, ',', ' ') . ' Ar vers ' . $destinataireTel . ' réussi (frais: ' . number_format($frais, 2, ',', ' ') . ' Ar)');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
}