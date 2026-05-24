<?php

use App\Core\Router;
use Modules\Movies\Controllers\MovieController;

/** @var Router $router */

$router->get('/movies', [MovieController::class, 'index'])->name('movies.index');
$router->get('/movies/trending', [MovieController::class, 'trending'])->name('movies.trending');
$router->get('/movies/popular', [MovieController::class, 'popular'])->name('movies.popular');
$router->get('/movies/upcoming', [MovieController::class, 'upcoming'])->name('movies.upcoming');
$router->get('/movies/{id}', [MovieController::class, 'show'])->name('movies.show');
