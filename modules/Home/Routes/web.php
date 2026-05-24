<?php

use App\Core\Router;
use Modules\Home\Controllers\HomeController;

/** @var Router $router */

$router->get('/', [HomeController::class, 'index'])->name('home');
