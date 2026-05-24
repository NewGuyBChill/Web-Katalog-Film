<?php

use App\Core\Router;
use Modules\Recommendation\Controllers\RecommendationController;

/** @var Router $router */

// Rekomendasi wajib login agar bisa dipersonalisasi
$router->get('/recommendations', [RecommendationController::class, 'index'])->name('recommendations.index')->middleware('auth');
