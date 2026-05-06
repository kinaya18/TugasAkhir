<?php

namespace Config;
use CodeIgniter\Router\RouteCollection;

$routes = Services::routes();
$routes->setAutoRoute(false);

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

// API routes for ESP32 (POST and GET)
$routes->post('api/insert_data', 'Api::insertData');
$routes->get('api/insert_data', 'Api::insertData');
$routes->get('api/latest', 'Api::getLatest');
$routes->get('api/history/(:num)', 'Api::getHistory/$1');

$routes->options('(:any)', 'Api::options');