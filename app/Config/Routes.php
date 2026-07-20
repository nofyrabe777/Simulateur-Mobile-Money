<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
<?php

use CodeIgniter\Router\RouteCollection;

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