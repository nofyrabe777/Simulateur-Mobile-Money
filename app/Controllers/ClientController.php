<?php

namespace App\Controllers;

use App\Models\CompteModel;
use App\Models\TransactionsModel;

class ClientController extends BaseController
{
    // Affiche la page de connexion
    public function index()
    {
        // Si le client est déjà connecté, on l'envoie directement sur le dashboard
        if (session()->get('client_session')) {
            return redirect()->to('/client/dashboard');
        }

        return view('login'); // Charge app/Views/login.php
    }

    // Affiche l'espace client sécurisé
    public function dashboard()
    {
        $session = session()->get('client_session');
        
        // Si PAS de session, on redirige vers l'index (/) et on ARRÊTE la boucle
        if (!$session) {
            return redirect()->to('/');
        }

        $compteModel = new CompteModel();
        $compte = $compteModel->find($session['id']);

        $txModel = new TransactionsModel();
        $historique = $txModel->select('transactions.*, type_operations.nom as type')
                              ->join('type_operations', 'type_operations.id = transactions.id_type_operation')
                              ->groupStart()
                                  ->where('id_compte_expediteur', $compte['id'])
                                  ->orWhere('id_compte_destinataire', $compte['id'])
                              ->groupEnd()
                              ->orderBy('date_transaction', 'DESC')
                              ->findAll();

        return view('client/dashboard', [
            'compte' => $compte,
            'historique' => $historique
        ]);
    }
}