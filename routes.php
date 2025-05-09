<?php
/*
 * QR Transfer
 * Copyright (C) 2025 Xavier Dubois
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * Main routing configuration file
 * 
 * This file sets up all the routes for the application, including:
 * - Page routes (GET requests for viewing pages)
 * - Language change routes (both GET and POST for changing the interface language)
 * - API routes (POST requests for AJAX operations)
 */

// Load controllers and core components
require_once 'controllers/Router.php';
require_once 'controllers/LandingController.php';
require_once 'controllers/LanguageController.php';
require_once 'controllers/SetupController.php';
require_once 'controllers/QrController.php';
require_once 'controllers/GDPRController.php';
require_once 'controllers/SupportController.php';
require_once 'core/AppRegistry.php';

// Core global controllers remain at root-level controllers

// Initialize the router
$router = new Router();

// Setup pages (not visible in navigation)
$router->get('/setup', 'SetupController@index');
$router->post('/setup/db', 'SetupController@initializeDatabase');

// Initialize app registry with the router
$registry = AppRegistry::getInstance();
$registry->setRouter($router);

// Register all apps by scanning apps directory
require_once 'apps/register_apps.php';

// Register all app routes through the registry
$registry->registerAllRoutes();

// Landing page (select app)
$router->get('/{lang}', 'LandingController@index');

// Global pages accessible from any app
$router->get('/{lang}/gdpr', 'GDPRController@index');
$router->get('/{lang}/support', 'SupportController@index');

// Waiting room for event
$router->get('/{lang}/involved/{code}/wait', 'InvolvedHomeController@showWaitingRoom');

// Global QR code SVG endpoint (AJAX)
$router->get('/qr/svg', 'GenericQrController@svg');

// Redirect root to detected language based on browser preferences
$router->get('/', function() {
    $lang = LanguageController::detectBrowserLanguage();
    header('Location: /' . $lang);
    exit;
});
