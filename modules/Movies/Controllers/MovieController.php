<?php

namespace Modules\Movies\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Services\TMDB\TMDBClient;

/**
 * MovieController — Handles all movie-related pages.
 */
class MovieController extends Controller
{
    private TMDBClient $tmdb;

    public function __construct()
    {
        parent::__construct();
        $this->tmdb = new TMDBClient();
    }

    /**
     * GET /movies — Browse/Discover movies with filters.
     */
    public function index(Request $request): void
    {
        $page   = (int) $request->query('page', 1);
        $genre  = $request->query('genre', '');
        $year   = $request->query('year', '');
        $rating = $request->query('rating', '');
        $sort   = $request->query('sort', 'popularity.desc');
        $lang   = $request->query('lang', '');

        // Build discover filters
        $filters = ['sort_by' => $sort];

        if ($genre)  $filters['with_genres'] = $genre;
        if ($year)   $filters['primary_release_year'] = $year;
        if ($lang)   $filters['with_original_language'] = $lang;
        if ($rating) {
            $filters['vote_average.gte'] = $rating;
            $filters['vote_count.gte'] = 50;
        }

        $data = $this->tmdb->discover($filters, $page);
        $movies = $this->tmdb->formatResults($data, 20);

        $this->view('layouts.app', [
            'content' => $this->capture('movies.index', [
                'movies'      => $movies,
                'currentPage' => $page,
                'totalPages'  => $data['total_pages'] ?? 1,
                'filters'     => compact('genre', 'year', 'rating', 'sort', 'lang'),
                'genreList'   => $this->getGenreList(),
            ]),
            'title' => 'Explore Movies — CelesView',
        ]);
    }

    /**
     * GET /movies/{id} — Movie detail page.
     */
    public function show(Request $request): void
    {
        $id = $request->param('id');
        $_GET['id'] = $id; // Inject for legacy views

        $movie = $this->tmdb->movieDetail($id);

        if (!$movie || isset($movie['status_code'])) {
            $this->view('errors.404');
            return;
        }

        // Get user review if logged in
        $userReview = null;
        $reviews = [];
        if (auth()->check()) {
            $userReview = $this->db->fetch(
                "SELECT * FROM reviews WHERE user_id = ? AND media_id = ?",
                [auth()->id(), $id]
            );
        }

        // Fetch all reviews for this movie
        $reviews = $this->db->fetchAll(
            "SELECT r.*, u.username, u.avatar_color FROM reviews r 
             JOIN users u ON r.user_id = u.id 
             WHERE r.media_id = ? 
             ORDER BY r.created_at DESC",
            [$id]
        );

        $this->view('layouts.app', [
            'content' => $this->capture('movies.detail', [
                'movie'      => $movie,
                'tmdb'       => $this->tmdb,
                'userReview' => $userReview,
                'reviews'    => $reviews,
            ]),
            'title' => ($movie['title'] ?? 'Movie') . ' — CelesView',
        ]);
    }

    /**
     * GET /movies/trending — Trending movies page.
     */
    public function trending(Request $request): void
    {
        $data = $this->tmdb->trending('movie', 'week');
        $movies = $this->tmdb->formatResults($data, 20);

        $_GET['sort'] = 'trending';
        $this->view('layouts.app', [
            'content' => $this->capture('movies.index', [
                'movies' => $movies,
            ]),
            'title' => 'Trending Movies — CelesView',
        ]);
    }

    /**
     * GET /movies/popular — Popular movies page.
     */
    public function popular(Request $request): void
    {
        $page = (int) $request->query('page', 1);
        $data = $this->tmdb->popular($page);
        $movies = $this->tmdb->formatResults($data, 20);

        $_GET['sort'] = 'popularity.desc';
        $this->view('layouts.app', [
            'content' => $this->capture('movies.index', [
                'movies'      => $movies,
                'currentPage' => $page,
                'totalPages'  => $data['total_pages'] ?? 1,
            ]),
            'title' => 'Popular Movies — CelesView',
        ]);
    }

    /**
     * GET /movies/upcoming — Upcoming movies page.
     */
    public function upcoming(Request $request): void
    {
        $page = (int) $request->query('page', 1);
        $data = $this->tmdb->upcoming($page);
        $movies = $this->tmdb->formatResults($data, 20);

        $_GET['sort'] = 'upcoming';
        $this->view('layouts.app', [
            'content' => $this->capture('movies.index', [
                'movies'      => $movies,
                'currentPage' => $page,
                'totalPages'  => $data['total_pages'] ?? 1,
            ]),
            'title' => 'Upcoming Movies — CelesView',
        ]);
    }

    /**
     * Capture a module view to a string (for injecting into layout).
     */
    private function capture(string $view, array $data = []): string
    {
        extract($data);
        $viewPath = $this->resolveModuleView($view);

        ob_start();
        require $viewPath;
        return ob_get_clean();
    }

    /**
     * Resolve a module view path from dot notation (e.g., 'movies.detail').
     */
    private function resolveModuleView(string $view): string
    {
        $parts = explode('.', $view);
        $module = ucfirst($parts[0]);
        $file = $parts[1] ?? 'index';

        $path = base_path("modules/{$module}/Views/{$file}.php");

        if (!file_exists($path)) {
            throw new \RuntimeException("Module view [{$view}] not found at: {$path}");
        }

        return $path;
    }

    /**
     * Get the cached genre list from TMDB.
     */
    private function getGenreList(): array
    {
        $data = $this->tmdb->genres();
        return $data['genres'] ?? [];
    }
}
