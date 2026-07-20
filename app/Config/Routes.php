<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\TransactionsController;
/**
 * @var RouteCollection $routes
 */
// $routes->get('/', 'Home::index');

$routes->group('transaction', function ($routes) {
    // Affichage du solde et de l'historique d'un compte
    // Exemple d'URL : GET /transaction/compte/1
    $routes->get('compte/(:num)', 'TransactionsController::index/$1');

    // Effectuer les opérations (méthode POST)
    $routes->post('depot', 'TransactionsController::depot');
    $routes->post('retrait', 'TransactionsController::retrait');
    $routes->post('transfert', 'TransactionsController::transfert');
});