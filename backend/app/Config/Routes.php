<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('api', ['namespace' => 'App\Controllers\Api'], static function ($routes) {
    // Catégories
    $routes->get('categories', 'Categories::index');

    // Plats (CRUD complet)
    $routes->get('plats', 'Plats::index');
    $routes->get('plats/(:num)', 'Plats::show/$1');
    $routes->post('plats', 'Plats::create');
    $routes->put('plats/(:num)', 'Plats::update/$1');
    $routes->delete('plats/(:num)', 'Plats::delete/$1');

    // Commandes
    $routes->get('commandes', 'Commandes::index');
    $routes->get('commandes/(:num)', 'Commandes::show/$1');
    $routes->post('commandes', 'Commandes::create');

    // Plat du jour (Correction du préfixe imbriqué et des fonctions correspondantes)
    $routes->get('plat-du-jour', 'PlatDuJour::index');
    $routes->put('plat-du-jour/(:num)', 'PlatDuJour::update/$1');
    $routes->get('plat-du-jour/historique', 'PlatDuJour::historique');
});
