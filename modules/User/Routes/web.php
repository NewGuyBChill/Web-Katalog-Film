<?php

use App\Core\Router;
use Modules\User\Controllers\UserController;

use Modules\User\Controllers\ReviewController;

/** @var Router $router */

$router->get('/profile', [UserController::class, 'profile'])->name('profile')->middleware('auth');
$router->get('/profile/reviews', [UserController::class, 'reviews'])->name('profile.reviews')->middleware('auth');
$router->post('/api/reviews', [ReviewController::class, 'ajax'])->name('api.reviews');
