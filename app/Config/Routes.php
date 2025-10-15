<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->post('/users/login', 'UserController::login');
$routes->get('/users/clientHomepage', 'UserController::clientHomepage');
$routes->get('/users/agentHomepage', 'UserController::agentDashboard');

$routes->post('/users/signup', 'UserController::StoreUsers');
$routes->post('/users/request-otp', 'UserController::requestOtp');
$routes->get('/verify-email', 'UserController::verifyEmail');

service('auth')->routes($routes);





