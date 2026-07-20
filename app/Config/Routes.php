<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/produit','Produit::index');
$routes->get('/produit/(:num)', 'Produit::show/$1');
