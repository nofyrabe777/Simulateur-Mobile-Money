<?php
use CodeIgniter\Router\RouteCollection;
use App\Controllers\ClientController;
use App\Controllers\HistoriqueController;
use App\Controllers\PrefixeController;
use App\Controllers\BaremeController;
use App\Controllers\TransactionsController;
/**
 * @var RouteCollection $routes
 */
// --- ACCUEIL & AUTHENTIFICATION ---
$routes->get('/', 'ClientController::dashboard'); 
// Redirige vers le dashboard (qui gère le filtre de session) ou une vue login
$routes->post('auth/login', 'AuthController::login');
$routes->get('auth/logout', 'AuthController::logout');

// --- ESPACE CLIENT (Sécurisé par session) ---
$routes->group('client', function($routes) {
    $routes->get('dashboard', 'ClientController::dashboard');
    $routes->get('historique', 'HistoriqueController::index');
});

// --- ESPACE OPÉRATEUR (Back-Office) ---
$routes->group('operateur', function($routes) {
    // Tableau de bord principal de l'opérateur (Gains + Situation des comptes)
    $routes->get('dashboard', 'Operateur::dashboard'); 
    
    // Gestion des préfixes (Ajout)
    $routes->post('prefixe/add', 'PrefixeController::store');
    
    // Gestion des barèmes (Mise à jour des tranches de frais)
    $routes->post('bareme/update', 'BaremeController::update');
});

$routes->group('transaction', function ($routes) {
    // Affichage du solde et de l'historique d'un compte
    // Exemple d'URL : GET /transaction/compte/1
    $routes->get('compte/(:num)', 'TransactionsController::index/$1');

    // Effectuer les opérations (méthode POST)
    $routes->post('depot', 'TransactionsController::depot');
    $routes->post('retrait', 'TransactionsController::retrait');
    $routes->post('transfert', 'TransactionsController::transfert');
});