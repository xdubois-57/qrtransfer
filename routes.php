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

// Include necessary controller classes
require_once 'controllers/HomeController.php';
require_once 'controllers/GDPRController.php';
require_once 'controllers/WhyUsController.php';
require_once 'controllers/Router.php';
require_once 'controllers/LanguageController.php';
require_once 'controllers/QRController.php';
require_once 'controllers/SupportController.php';

// Initialize the router
$router = new Router();

// Localized page routes
$router->get('/{lang}', 'HomeController@index');
$router->get('/{lang}/gdpr', 'GDPRController@index');
$router->get('/{lang}/why-us', 'WhyUsController@index');
$router->get('/{lang}/support', 'SupportController@index');

// Page routes
// These routes handle the main navigation of the site
$router->get('/gdpr', 'GDPRController@index');     // GDPR/Privacy policy page
$router->get('/why-us', 'WhyUsController@index');     // Why Us page
$router->get('/support', 'SupportController@index');     // Support/Buy me a coffee page

// Redirect root to browser language if supported, else to English
$router->get('/', function() {
    $config = require __DIR__ . '/config/languages.php';
    $browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '', 0, 2);
    if (isset($config['available_languages'][$browserLang])) {
        header('Location: /' . $browserLang);
    } else {
        header('Location: /en');
    }
    exit;
});

// Language routes
// These routes handle language changes via both GET and POST requests
$router->post('/language/{lang}', 'LanguageController@change');  // For direct language links
$router->post('/language', 'LanguageController@change');        // For form submissions

// API routes
// These routes handle AJAX requests and return JSON responses
$router->post('/{lang}/generate-qr', 'QRController@generate');     // Handles QR code generation requests
$router->post('/generate-qr', 'QRController@generate');     // Fallback for legacy/non-localized requests
