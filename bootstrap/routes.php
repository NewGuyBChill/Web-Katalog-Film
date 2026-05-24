<?php

/**
 * CelesView - Central Route Loader
 * 
 * This file is responsible for aggregating all routes from the 
 * central routes/ directory and every individual modules/[moduleName]/Routes/ directory.
 */

use App\Core\Router;

/** @var Router $router */

$centralRoutes = [
    dirname(__DIR__) . '/routes/web.php',
    dirname(__DIR__) . '/routes/api.php',
    dirname(__DIR__) . '/routes/admin.php',
];

foreach ($centralRoutes as $routeFile) {
    if (file_exists($routeFile)) {
        require_once $routeFile;
    }
}

$moduleRoutes = glob(dirname(__DIR__) . '/modules/*/Routes/*.php');

if ($moduleRoutes) {
    foreach ($moduleRoutes as $routeFile) {
        if (file_exists($routeFile)) {
            require_once $routeFile;
        }
    }
}
