<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;
use Config\Services;

/**
 * @var RouteCollection $routes
 */
$routes = Services::routes();

// Load the system’s routing file first, so that the app and ENVIRONMENT can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/**
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

$routes->get('/', 'Home::index');
$routes->post('/users/login', 'UserController::login');


// Client Routes
$routes->get('/users/clientHomepage', 'UserController::clientHomepage');
$routes->get('/users/clientprofile', 'UserController::ClientProfile');
$routes->get('/users/clientbookings', 'UserController::ClientBookings');
$routes->get('/users/clientbrowse', 'UserController::ClientBrowse');
$routes->get('/users/logout', 'UserController::logoutClient');
$routes->get('/users/chat', 'UserController::cleintChat');




//Agent Routes
$routes->get('/users/agentHomepage', 'AgentController::agentDashboard');
$routes->get('/users/agentprofile', 'AgentController::agentProfile');
$routes->get('/users/agentproperties', 'AgentController::agentProperties');
$routes->get('/users/agentclients', 'AgentController::agentClients');
$routes->get('/users/agentchat', 'AgentController::agentChat');
$routes->get('/users/agentbookings', 'AgentController::agentBookings');
$routes->get('/users/logoutagent', 'AgentController::logoutAgent');

//chat Routes
$routes->get('/chat/messages/(:num)', 'ChatController::getMessages/$1');
$routes->post('/chat/send', 'ChatController::sendMessage');


// Admin Routes
$routes->get('/admin/adminHomepage', 'AdminController::adminDashboard');
$routes->get('/admin/Reports', 'AdminController::generateReports');
$routes->get('/admin/ManageProperties', 'AdminController::manageProperties');
$routes->get('/admin/manageUsers', 'AdminController::manageUsers');
$routes->get('/admin/userBookings', 'AdminController::userBooking');
$routes->get('/admin/viewChats', 'AdminController::viewChats');
$routes->get('/admin/logout', 'AdminController::logoutAdmin');
$routes->get('/admin/getusers', 'AdminController::getUsers');

// Admin Property Management Routes
$routes->get('/admin/getProperty/(:num)', 'AdminController::getProperty/$1');
$routes->delete('admin/property/delete-property/(:num)', 'AdminController::deleteProperty/$1');
$routes->post('admin/property/store-property', 'AdminController::storePropertys');







$routes->post('/users/signup', 'UserController::StoreUsers');
$routes->post('/users/request-otp', 'UserController::requestOtp');
$routes->get('/verify-email', 'UserController::verifyEmail');


/*
if (function_exists('service') && service('auth') !== null) {
    service('auth')->routes($routes);
} */

