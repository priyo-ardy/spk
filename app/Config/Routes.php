<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth\Auth::index');
$routes->post('/proses', 'Auth\Auth::prosesLogin');
$routes->get('logout', 'Auth\Auth::logOut');

$routes->group('', ['filter' => 'auth'], static function ($routes) {
    // Dashboard
    $routes->group('/dashboard', static function ($routes) {
        $routes->get('', 'Dashboard\Dashboard::index');
    });

    // Master Data -> Material Management -> Material Category
    $routes->group('/material_category', static function ($routes) {
        $routes->get('', 'MasterData\MaterialManagement\MaterialCategory\MaterialCategory::index');
        $routes->post('table', 'MasterData\MaterialManagement\MaterialCategory\MaterialCategory::loadTable');
        $routes->post('save', 'MasterData\MaterialManagement\MaterialCategory\MaterialCategory::saveData');
        $routes->post('get', 'MasterData\MaterialManagement\MaterialCategory\MaterialCategory::getData');
    });
});
