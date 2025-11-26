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
$routes->get('/properties/all', 'PropertyController::getAllProperties');
$routes->get('properties/view/(:num)', 'PropertyController::viewProperty/$1');

$routes->post('bookings/create', 'UserController::create');
$routes->get('bookings/mine', 'UserController::mine');
$routes->post('bookings/cancel', 'UserController::cancel');
$routes->get('users/getUser/(:num)', 'PropertyController::getUser/$1');




//Agent Routes
$routes->get('/users/agentHomepage', 'AgentController::agentDashboard');
$routes->get('/users/agentprofile', 'AgentController::agentProfile');


$routes->get('/users/agentclients', 'AgentController::agentClients');
$routes->get('/users/agentchat', 'AgentController::agentChat');
$routes->get('/users/agentbookings', 'AgentController::agentBookings');
$routes->get('/users/logoutagent', 'AgentController::logoutAgent');

//Agent Property Management Routes
$routes->get('/users/agentproperties', 'AgentController::agentProperties');
$routes->get('users/getBooking/(:segment)', 'AgentController::getBooking/$1');         // GET /users/getBooking/{id}
$routes->post('users/updateBookingStatus', 'AgentController::updateBookingStatus');    // POST { booking_id, status, reason? }
$routes->post('property/updateStatus', 'PropertyController::updateStatus');



//chat Routes
$routes->get('/chat/messages/(:num)', 'ChatController::getMessages/$1');
$routes->post('/chat/send', 'ChatController::sendMessage');
$routes->post('chat/startSession', 'ChatController::startSession');



// Admin Routes
$routes->get('/admin/adminHomepage', 'AdminController::adminDashboard');
$routes->get('/admin/Reports', 'AdminController::generateReports');
$routes->get('/admin/ManageProperties', 'AdminController::manageProperties');
$routes->get('/admin/manageUsers', 'AdminController::manageUsers');
$routes->get('/admin/userBookings', 'AdminController::userBooking');
// Fetch booking details (JSON) for Admin detail popups
$routes->get('/admin/booking/(:num)', 'AdminController::getBookingDetails/$1');
// Removed admin chat viewing for privacy reasons
$routes->get('/admin/logout', 'AdminController::logoutAdmin');
$routes->get('/admin/getusers', 'AdminController::getUsers');

// Admin Property Management Routes
$routes->get('/admin/getProperty/(:num)', 'AdminController::getProperty/$1');
$routes->delete('admin/property/delete-property/(:num)', 'AdminController::deleteProperty/$1');
$routes->post('admin/property/store-property', 'AdminController::storePropertys');
$routes->get('chat/view/(:num)', 'ChatController::view/$1');
$routes->post('chat/startSession', 'ChatController::startSession');


// Admin User Management Routes
$routes->post('/admin/store-agent', 'AdminController::storeAgent');
// Admin user actions (deactivate / delete)
$routes->post('admin/user/deactivate/(:num)', 'AdminController::deactivateUser/$1');
$routes->delete('admin/user/delete/(:num)', 'AdminController::deleteUser/$1');
// Reactivate and update user
$routes->post('admin/user/reactivate/(:num)', 'AdminController::reactivateUser/$1');
$routes->post('admin/user/update/(:num)', 'AdminController::updateUser/$1');







$routes->post('/users/signup', 'UserController::StoreUsers');
$routes->post('/users/request-otp', 'UserController::requestOtp');
$routes->get('/verify-email', 'UserController::verifyEmail');


/*
if (function_exists('service') && service('auth') !== null) {
    service('auth')->routes($routes);
} */

