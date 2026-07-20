<?php

namespace App\Controllers;

use App\Models\CompteModel;
use App\Models\TransactionsModel;

class ClientController extends BaseController
{
    /**
     * ÉTAPE 1 : Page d'accueil générale (La Passerelle)
     * Contient les deux gros boutons : "Côté Client" et "Côté Opérateur"
     */
    public function index()
    {
        return view('index'); // Charge app/Views/index.php
    }

    /**
     * ÉTAPE 2 : Formulaire de connexion unique pour le client
     * URL : /auth/login (GET)
     */
    public function loginForm()
    {
        // Si le client est déjà connecté, inutile de se reconnecter
        if (session()->get('client_session')) {
            return redirect()->to('/client/dashboard');
        }

        return view('login'); // Charge app/Views/login.php
    }

    /**
     * ÉTAPE 3 : Espace client privé et sécurisé
     * URL : /client/dashboard
     */
    public function dashboard()
    {
        $session = session()->get('client_session');
        
        // CORRECTION DE SÉCURITÉ : Si pas de session, on renvoie vers le login, pas vers l'index !
        if (!$session) {
            return redirect()->to('/auth/login')->with('error', 'Veuillez vous connecter pour accéder à votre espace.');
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