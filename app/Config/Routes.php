<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/dashboard', 'Home::index');
$routes->get('/riwayat', 'Riwayat::index');
$routes->get('/aqi', 'Aqi::index');
$routes->post('/insert', 'Home::insert');
$routes->get('/riwayat-data', 'History::index');
$routes->get('/info-aqi', 'AqiController::index');