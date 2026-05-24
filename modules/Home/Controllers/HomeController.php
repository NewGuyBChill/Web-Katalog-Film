<?php

namespace Modules\Home\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Services\TMDB\TMDBClient;

class HomeController extends Controller
{
    private TMDBClient $tmdb;

    public function __construct()
    {
        parent::__construct();
        $this->tmdb = new TMDBClient();
    }

    public function index(Request $request): void
    {
        // Fetch data from TMDB
        $trendingData = $this->tmdb->trending('movie', 'day');
        $popularData = $this->tmdb->popular();
        $topRatedData = $this->tmdb->topRated();

        // Format results
        $trending = $this->tmdb->formatResults($trendingData['results'] ?? [], 1, 'movie');
        $popular = $this->tmdb->formatResults($popularData['results'] ?? [], 1, 'movie');
        $topRated = $this->tmdb->formatResults($topRatedData['results'] ?? [], 1, 'movie');

        // Hero banner uses top 5 trending
        $heroMovies = array_slice($trending, 0, 5);

        $this->view('layouts.app', [
            'content' => $this->captureModuleView('Home', 'index', [
                'heroMovies' => $heroMovies,
                'trending' => array_slice($trending, 5, 12),
                'popular' => array_slice($popular, 0, 12),
                'topRated' => array_slice($topRated, 0, 12)
            ]),
            'title' => 'CelesView — Discover, rate, and discuss movies'
        ]);
    }
}
