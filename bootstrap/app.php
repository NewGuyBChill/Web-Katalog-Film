<?php

/**
 * CelesView — Application Bootstrap
 * 
 * Loads Composer autoloader, starts session, and initializes the App instance.
 * This file is required by public/index.php.
 */

// Load Composer autoloader (PSR-4)
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Start session
\App\Core\Session::start();

// Generate CSRF token if not exists
if (!\App\Core\Session::has('_csrf_token')) {
    \App\Core\Session::set('_csrf_token', bin2hex(random_bytes(32)));
}

// Boot the application
$app = \App\Core\App::getInstance();

return $app;
