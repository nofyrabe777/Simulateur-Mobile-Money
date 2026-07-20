<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// --- ZONE PUBLIQUE & ACCUEIL ---
$routes->get('/', 'ClientController::index');              // Affiche la page d'accueil avec les 2 choix (Client / Opérateur)
$routes->get('auth/login', 'ClientController::loginForm'); // Affiche le formulaire de connexion client
$routes->post('auth/login', 'AuthController::login');      // Traite la soumission du formulaire de connexion
$routes->get('auth/logout', 'AuthController::logout');    // Gère la déconnexion du client

// --- ESPACE CLIENT (Sécurisé par session) ---
$routes->group('client', function($routes) {
    $routes->get('dashboard', 'ClientController::dashboard');
    $routes->get('historique', 'HistoriqueController::index');
    
    // Route unique pour le traitement centralisé (Dépôt, Retrait, Transfert)
    $routes->post('transaction', 'TransactionsController::store');
});

// --- ESPACE OPÉRATEUR (Back-Office) ---
$routes->group('operateur', function($routes) {
    // Tableau de bord de l'opérateur (Situation gains + Comptes clients)
    $routes->get('dashboard', 'OperateurController::dashboard'); 
    
    // Actions de configuration
    $routes->post('prefixe/add', 'PrefixeController::store');
    $routes->post('bareme/update', 'BaremeController::update');
});