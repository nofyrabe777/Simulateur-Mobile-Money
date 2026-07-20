<?php
namespace App\Controllers;

use App\Models\CompteModel;
use App\Models\TransactionsModel;

class ClientController extends BaseController
{
    public function dashboard()
    {
        $session = session()->get('client_session');
        if (!$session) return redirect()->to('/');

        // Recharger le solde à jour
        $compteModel = new CompteModel();
        $compte = $compteModel->find($session['id']);

        // Charger l'historique récent pour la vue principale
        $txModel = new TransactionsModel();
        $historique = $txModel->select('transactions.*, type_operations.nom as type')
                              ->join('type_operations', 'type_operations.id = transactions.id_type_operation')
                              ->groupStart()
                                  ->where('id_compte_expediteur', $compte['id'])
                                  ->orWhere('id_compte_destinataire', $compte['id'])
                              ->groupEnd()
                              ->orderBy('date_transaction', 'DESC')
                              ->findAll();

        return view('index', [
            'compte' => $compte,
            'historique' => $historique
        ]);
    }
}