<?php

namespace Modules\Search\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\TMDB\TMDBClient;

class SearchController extends Controller
{
    private TMDBClient $tmdb;

    public function __construct()
    {
        parent::__construct();
        $this->tmdb = new TMDBClient();
    }

    public function index(Request $request): void
    {
        $query = $request->query('q', '');
        $page = (int) $request->query('page', 1);

        if (empty($query)) {
            $this->redirect(url(''));
            return;
        }

        $data = $this->tmdb->searchMovies($query, $page);
        $results = $this->tmdb->formatResults($data['results'] ?? []);
        
        // Filter out people, only keep movies/tv
        $results = array_filter($results, fn($r) => in_array($r['media_type'] ?? 'movie', ['movie', 'tv']));

        $this->view('layouts.app', [
            'content' => $this->captureModuleView('Search', 'index', [
                'query' => $query,
                'results' => $results,
                'page' => $page,
                'totalPages' => $data['total_pages'] ?? 1
            ]),
            'title' => "Search Results for '{$query}' — CelesView"
        ]);
    }

    public function liveSearch(Request $request): void
    {
        $query = $request->query('q', '');
        if (empty(trim($query))) {
            Response::json([]);
            return;
        }

        $data = $this->tmdb->searchMovies($query, 1);
        $results = $this->tmdb->formatResults($data['results'] ?? []);
        
        // Filter and map to a simpler array for AJAX autocomplete
        $formatted = [];
        foreach (array_slice($results, 0, 5) as $item) {
            if (!in_array($item['media_type'] ?? 'movie', ['movie', 'tv'])) continue;
            
            $formatted[] = [
                'id' => $item['id'],
                'title' => $item['title'] ?? $item['name'] ?? 'Unknown',
                'image' => !empty($item['poster_path']) ? 'https://image.tmdb.org/t/p/w92' . $item['poster_path'] : url('assets/images/no-poster-small.png'),
                'rating' => round($item['vote_average'] ?? 0, 1),
                'year' => !empty($item['release_date']) ? substr($item['release_date'], 0, 4) : (!empty($item['first_air_date']) ? substr($item['first_air_date'], 0, 4) : ''),
                'type' => $item['media_type'] ?? 'movie'
            ];
        }

        Response::json($formatted);
    }
}
