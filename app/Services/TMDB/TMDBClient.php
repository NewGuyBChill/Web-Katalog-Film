<?php

namespace App\Services\TMDB;

/**
 * TMDBClient — Centralized TMDB API Client
 * 
 * Replaces the legacy fetchTMDB() function with a proper OOP service.
 * Handles API requests, file-based caching, and response formatting.
 */
class TMDBClient
{
    private string $apiKey;
    private string $baseUrl;
    private string $imageBase;
    private string $language;
    private string $cacheDir;
    private int $defaultCacheTtl;

    public function __construct()
    {
        $this->apiKey   = env('TMDB_API_KEY', '');
        $this->baseUrl  = env('TMDB_BASE_URL', 'https://api.themoviedb.org/3');
        $this->imageBase = env('TMDB_IMAGE_BASE', 'https://image.tmdb.org/t/p');
        $this->language = isset($_COOKIE['site_lang']) && $_COOKIE['site_lang'] === 'id-ID' ? 'id-ID' : 'en-US';
        $this->cacheDir = base_path('storage/cache/tmdb/');
        $this->defaultCacheTtl = 3600;

        if (!is_dir($this->cacheDir)) {
            @mkdir($this->cacheDir, 0777, true);
        }
    }

    /**
     * Make an API request to TMDB.
     */
    public function fetch(string $endpoint, int $cacheTtl = null): ?array
    {
        $cacheTtl = $cacheTtl ?? $this->defaultCacheTtl;
        $separator = str_contains($endpoint, '?') ? '&' : '?';
        $url = "{$this->baseUrl}/{$endpoint}{$separator}api_key={$this->apiKey}&language={$this->language}";

        // Garbage collection (5% chance)
        $this->garbageCollect($cacheTtl);

        // Check cache
        $cacheFile = $this->cacheDir . md5($url) . '.json';

        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTtl) {
            $cached = @file_get_contents($cacheFile);
            if ($cached !== false) {
                return json_decode($cached, true);
            }
        }

        // Make cURL request
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Cache on success
        if ($response && $httpCode === 200) {
            @file_put_contents($cacheFile, $response, LOCK_EX);
            return json_decode($response, true);
        }

        // Fallback to stale cache
        if (file_exists($cacheFile)) {
            return json_decode(@file_get_contents($cacheFile), true);
        }

        return null;
    }

    /**
     * Get trending movies/tv for a time window.
     */
    public function trending(string $mediaType = 'movie', string $timeWindow = 'week'): ?array
    {
        return $this->fetch("trending/{$mediaType}/{$timeWindow}");
    }

    /**
     * Get now playing movies.
     */
    public function nowPlaying(int $page = 1): ?array
    {
        return $this->fetch("movie/now_playing?page={$page}");
    }

    /**
     * Get popular movies.
     */
    public function popular(int $page = 1): ?array
    {
        return $this->fetch("movie/popular?page={$page}");
    }

    /**
     * Get upcoming movies.
     */
    public function upcoming(int $page = 1): ?array
    {
        return $this->fetch("movie/upcoming?page={$page}");
    }

    /**
     * Get top rated movies.
     */
    public function topRated(int $page = 1): ?array
    {
        return $this->fetch("movie/top_rated?page={$page}");
    }

    /**
     * Get movie details by ID.
     */
    public function movieDetail(int $id): ?array
    {
        return $this->fetch("movie/{$id}?append_to_response=credits,videos,similar,recommendations");
    }

    /**
     * Search for movies.
     */
    public function searchMovies(string $query, int $page = 1): ?array
    {
        $query = urlencode($query);
        return $this->fetch("search/multi?query={$query}&page={$page}");
    }

    /**
     * Discover movies with filters.
     */
    public function discover(array $filters = [], int $page = 1): ?array
    {
        $params = http_build_query(array_merge($filters, ['page' => $page]));
        return $this->fetch("discover/movie?{$params}");
    }

    /**
     * Get the genre list.
     */
    public function genres(): ?array
    {
        return $this->fetch("genre/movie/list", 86400); // Cache for 24h
    }

    /**
     * Get movie trailers/videos.
     */
    public function videos(int $movieId): ?array
    {
        return $this->fetch("movie/{$movieId}/videos");
    }

    /**
     * Build a full image URL.
     */
    public function imageUrl(?string $path, string $size = 'w500'): string
    {
        if (empty($path)) {
            return "data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22500%22%20height%3D%22750%22%20viewBox%3D%220%200%20500%20750%22%3E%3Crect%20width%3D%22500%22%20height%3D%22750%22%20fill%3D%22%231a1a1a%22%2F%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2250%25%22%20font-family%3D%22sans-serif%22%20font-size%3D%2230%22%20fill%3D%22%23555555%22%20text-anchor%3D%22middle%22%20dominant-baseline%3D%22middle%22%3ENo%20Poster%3C%2Ftext%3E%3C%2Fsvg%3E";
        }
        return "{$this->imageBase}/{$size}{$path}";
    }

    /**
     * Format TMDB results into a cleaner array.
     */
    public function formatResults(?array $data, int $limit = 20, string $type = 'movie'): array
    {
        $genreMap = [
            28 => "Action", 12 => "Adventure", 16 => "Animation", 35 => "Comedy", 80 => "Crime",
            99 => "Documentary", 18 => "Drama", 10751 => "Family", 14 => "Fantasy", 36 => "History",
            27 => "Horror", 10402 => "Music", 9648 => "Mystery", 10749 => "Romance", 878 => "Sci-Fi",
            10770 => "TV Movie", 53 => "Thriller", 10752 => "War", 37 => "Western"
        ];

        $results = $data['results'] ?? $data ?? [];
        $formatted = [];

        foreach (array_slice($results, 0, $limit) as $item) {
            $genre = $type === 'tv' ? 'TV Show' : 'Movie';
            if (!empty($item['genre_ids'])) {
                $genre = $genreMap[$item['genre_ids'][0]] ?? $genre;
            }

            $formatted[] = [
                'id'       => $item['id'] ?? 0,
                'title'    => $item['title'] ?? $item['name'] ?? 'Unknown',
                'year'     => $this->extractYear($item),
                'genre'    => $genre,
                'rating'   => isset($item['vote_average']) ? round($item['vote_average'], 1) : 0,
                'image'    => $this->imageUrl($item['poster_path'] ?? null),
                'backdrop' => $this->imageUrl($item['backdrop_path'] ?? null, 'original'),
                'overview' => $item['overview'] ?? '',
                'type'     => $type,
            ];
        }

        return $formatted;
    }

    /**
     * Extract year from release_date or first_air_date.
     */
    private function extractYear(array $item): string
    {
        $date = $item['release_date'] ?? $item['first_air_date'] ?? '';
        return strlen($date) >= 4 ? substr($date, 0, 4) : '-';
    }

    /**
     * Run garbage collection on cache files.
     */
    private function garbageCollect(int $ttl): void
    {
        if (rand(1, 20) !== 1) return;

        try {
            $deleted = 0;
            $now = time();
            foreach (new \DirectoryIterator($this->cacheDir) as $file) {
                if ($file->isFile() && $file->getExtension() === 'json') {
                    if (($now - $file->getMTime()) >= $ttl) {
                        @unlink($file->getPathname());
                        if (++$deleted >= 30) break;
                    }
                }
            }
        } catch (\Exception $e) {}
    }

    /**
     * Get the current language setting.
     */
    public function getLanguage(): string
    {
        return $this->language;
    }
}
