<?php

use App\Core\Router;
use Modules\Watchlist\Controllers\WatchlistController;

/** @var Router $router */

$router->get('/watchlist', [WatchlistController::class, 'index'])->name('watchlist')->middleware('auth');
$router->post('/api/watchlist', [WatchlistController::class, 'toggle'])->name('api.watchlist')->middleware('auth');
