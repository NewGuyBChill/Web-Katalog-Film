<?php

use App\Core\Router;
use Modules\Reviews\Controllers\ReviewController;

/** @var Router $router */

$router->post('/api/review', [ReviewController::class, 'store'])->name('api.review.store')->middleware('auth');
