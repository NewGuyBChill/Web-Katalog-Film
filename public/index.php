<?php

/**
 * CelesView — Public Entry Point
 * 
 * All HTTP requests are routed through this file via .htaccess rewriting.
 * This is the ONLY PHP file that should be directly accessible from the web.
 */

// Boot the application
$app = require_once dirname(__DIR__) . '/bootstrap/app.php';

// Load route definitions
$router = $app->getRouter();
require_once dirname(__DIR__) . '/bootstrap/routes.php';

// Dispatch the request
$app->run();
