<?php

use App\Core\Router;
use Modules\Search\Controllers\SearchController;

/** @var Router $router */

$router->get('/search', [SearchController::class, 'index'])->name('search');
$router->get('/api/search', [SearchController::class, 'liveSearch'])->name('api.search');
