<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->post('/users/login', 'Usercontroller::login');
$routes->get('/users/clientHomepage', 'Usercontroller::clienthomepage');
$routes->post('/users/signup', 'Usercontroller::StoreUsers');
