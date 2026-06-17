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

//realtime
$routes->get('/latest-data', 'Home::latestData');


// Routes untuk prediksi
$routes->group('prediction', function($routes) {
    $routes->get('generate', 'Prediction::generate');
    $routes->get('getLatest', 'Prediction::getLatest');
    $routes->get('getHistory', 'Prediction::getHistory');
    $routes->get('getByDateRange', 'Prediction::getByDateRange');
    $routes->get('getStatistics', 'Prediction::getStatistics');
    $routes->get('exportCsv', 'Prediction::exportCsv');
    $routes->get('chart', 'Prediction::chart');
    $routes->get('status', 'Prediction::status');
    $routes->delete('cleanOldData', 'Prediction::cleanOldData');
    $routes->get('autoGenerate', 'Prediction::autoGenerate');
});

// Alias untuk akses mudah
$routes->get('prediksi/generate', 'Prediction::generate');
$routes->get('prediksi/latest', 'Prediction::getLatest');
$routes->get('prediksi/history', 'Prediction::getHistory');