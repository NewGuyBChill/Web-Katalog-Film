<?php

use App\Core\Router;
use Modules\Auth\Controllers\AuthController;

/** @var Router $router */

$router->get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
$router->post('/login', [AuthController::class, 'login'])->middleware('guest');
$router->get('/signup', [AuthController::class, 'showSignup'])->name('signup')->middleware('guest');
$router->post('/signup', [AuthController::class, 'register'])->middleware('guest');
$router->get('/logout', [AuthController::class, 'logout'])->name('logout');
