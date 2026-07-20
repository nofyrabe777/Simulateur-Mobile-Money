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

    private function getCompteActifId()
    {
        $compteId = $this->request->getPost('compte_id');
        if (empty($compteId)) {
            $session = session()->get('client_session');
            $compteId = $session['id'] ?? null;
        }

        return $compteId;
    }

    private function handleResponse(array $payload)
    {
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($payload);
        }

        $redirect = redirect()->to('/client/dashboard');

        return $payload['status'] === 'success'
            ? $redirect->with('success', $payload['message'])
            : $redirect->with('error', $payload['message']);
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

    public function store()
    {
        $typeOperation = (int) $this->request->getPost('type_operation');

        return match ($typeOperation) {
            1 => $this->depot(),
            2 => $this->retrait(),
            3 => $this->transfert(),
            default => $this->handleResponse(['status' => 'error', 'message' => 'Type d\'opération invalide'])
        };
    }

    /**
     * Dépôt : Aucun frais
     */
    public function depot()
    {
        $compteId = $this->getCompteActifId();
        $montant  = (float) $this->request->getPost('montant');

        if (empty($compteId)) {
            return $this->handleResponse(['status' => 'error', 'message' => 'Compte non identifié']);
        }

        if ($montant <= 0) {
            return $this->handleResponse(['status' => 'error', 'message' => 'Montant invalide']);
        }

        $compte = $this->compteModel->find($compteId);
        if (!$compte) {
            return $this->handleResponse(['status' => 'error', 'message' => 'Compte inexistant']);
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
            return $this->handleResponse(['status' => 'error', 'message' => 'Erreur lors du dépôt']);
        }

        return $this->handleResponse([
            'status'        => 'success',
            'message'       => 'Dépôt réussi',
            'nouveau_solde' => $nouveauSolde
        ]);
    }

    /**
     * Retrait : Calcul des frais + Vérification solde
     */
    public function retrait()
    {
        $compteId = $this->getCompteActifId();
        $montant  = (float) $this->request->getPost('montant');

        if (empty($compteId)) {
            return $this->handleResponse(['status' => 'error', 'message' => 'Compte non identifié']);
        }

        if ($montant <= 0) {
            return $this->handleResponse(['status' => 'error', 'message' => 'Montant invalide']);
        }

        $compte = $this->compteModel->find($compteId);
        if (!$compte) {
            return $this->handleResponse(['status' => 'error', 'message' => 'Compte inexistant']);
        }

        // --- CALCULS ET VALIDATION DANS LE CONTRÔLEUR ---
        $frais = $this->calculerFrais(2, $montant); // 2 = Retrait
        $totalADebiter = $montant + $frais;

        if ($compte['solde'] < $totalADebiter) {
            return $this->handleResponse([
                'status'  => 'error', 
                'message' => 'Solde insuffisant. Requis : ' . $totalADebiter . ' Ar (Montant: ' . $montant . ' + Frais: ' . $frais . ')'
            ]);
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
            return $this->handleResponse(['status' => 'error', 'message' => 'Erreur lors du retrait']);
        }

        return $this->handleResponse([
            'status'        => 'success',
            'message'       => 'Retrait effectué avec succès',
            'frais'         => $frais,
            'nouveau_solde' => $nouveauSolde
        ]);
    }

    /**
     * Transfert : Calcul des frais + Débit expéditeur + Crédit destinataire
     */
    public function transfert()
    {
        $expediteurId = $this->request->getPost('expediteur_id') ?: $this->getCompteActifId();
        $destinataireTelephone = trim($this->request->getPost('destinataire') ?? '');
        $montant        = (float) $this->request->getPost('montant');

        if (empty($expediteurId) || empty($destinataireTelephone)) {
            return $this->handleResponse(['status' => 'error', 'message' => 'Informations de transfert invalides']);
        }

        if ($montant <= 0) {
            return $this->handleResponse(['status' => 'error', 'message' => 'Montant invalide']);
        }

        $expediteur = $this->compteModel->find($expediteurId);
        $destinataire = $this->compteModel->where('telephone', $destinataireTelephone)->first();

        if (!$expediteur || !$destinataire) {
            return $this->handleResponse(['status' => 'error', 'message' => 'Compte expéditeur ou destinataire introuvable']);
        }

        $destinataireId = $destinataire['id'];

        if ($expediteurId == $destinataireId) {
            return $this->handleResponse(['status' => 'error', 'message' => 'Vous ne pouvez pas vous transférer de l\'argent à vous-même']);
        }

        // --- CALCULS ET VALIDATION DANS LE CONTRÔLEUR ---
        $frais = $this->calculerFrais(3, $montant); // 3 = Transfert
        $totalADebiter = $montant + $frais;

        if ($expediteur['solde'] < $totalADebiter) {
            return $this->handleResponse([
                'status'  => 'error', 
                'message' => 'Solde insuffisant pour le transfert (Total requis avec frais: ' . $totalADebiter . ' Ar)'
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Débit expéditeur
        $nouveauSoldeExp = $expediteur['solde'] - $totalADebiter;
        $this->compteModel->update($expediteurId, ['solde' => $nouveauSoldeExp]);

        // 2. Crédit destinataire
        $nouveauSoldeDest = $destinataire['solde'] + $montant;
        $this->compteModel->update($destinataireId, ['solde' => $nouveauSoldeDest]);

        // 3. Enregistrement transaction
        $this->transactionModel->insert([
            'id_type_operation'     => 3,
            'id_compte_expediteur'  => $expediteurId,
            'id_compte_destinataire'=> $destinataireId,
            'montant'               => $montant,
            'frais'                 => $frais
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->handleResponse(['status' => 'error', 'message' => 'Erreur lors du transfert']);
        }

        return $this->handleResponse([
            'status'        => 'success',
            'message'       => 'Transfert réussi',
            'frais'         => $frais,
            'nouveau_solde' => $nouveauSoldeExp
        ]);
    }
}