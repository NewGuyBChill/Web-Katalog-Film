<?php

namespace App\Core;

/**
 * Request — Wraps the current HTTP request.
 * 
 * Provides clean access to:
 * - GET/POST parameters
 * - Route parameters
 * - Request method and URI
 * - File uploads
 * - Headers
 */
class Request
{
    private array $routeParams = [];

    /**
     * Get the HTTP method (GET, POST, PUT, DELETE).
     * Supports method spoofing via _method field.
     */
    public function method(): string
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        // Support method spoofing for PUT/DELETE via hidden _method field
        if ($method === 'POST' && isset($_POST['_method'])) {
            $spoofed = strtoupper($_POST['_method']);
            if (in_array($spoofed, ['PUT', 'PATCH', 'DELETE'])) {
                return $spoofed;
            }
        }

        return $method;
    }

    /**
     * Get the cleaned request URI (without query string and base path).
     */
    public function uri(): string
    {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

        // Strip the base path prefix for subdirectory deployments
        $basePath = env('APP_BASE_PATH', '/kinema/Web-Katalog-Film/public');
        if ($basePath && str_starts_with($uri, $basePath)) {
            $uri = substr($uri, strlen($basePath));
        }

        $uri = '/' . trim($uri, '/');

        // LEGACY SUPPORT: Convert index.php?page=movies to /movies
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
            if ($page === 'home') return '/';
            if ($page === 'details') return '/movies/' . ($_GET['id'] ?? 1);
            if ($page === 'ajax_watchlist') return '/api/watchlist';
            if ($page === 'ajax_search') return '/api/search';
            if ($page === 'ajax_review') return '/api/reviews';
            if ($page === 'my_reviews') return '/profile/reviews';
            if ($page === 'register') return '/signup';
            return '/' . $page;
        }

        return $uri ?: '/';
    }

    /**
     * Get a query parameter (?key=value).
     */
    public function query(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }

    /**
     * Get a POST input value.
     */
    public function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $default;
    }

    /**
     * Get all POST data.
     */
    public function all(): array
    {
        return array_merge($_GET, $_POST);
    }

    /**
     * Check if the request has a specific input key.
     */
    public function has(string $key): bool
    {
        return isset($_POST[$key]) || isset($_GET[$key]);
    }

    /**
     * Get only specific keys from the input.
     */
    public function only(array $keys): array
    {
        $all = $this->all();
        return array_intersect_key($all, array_flip($keys));
    }

    /**
     * Set route parameters (called by the Router after matching).
     */
    public function setRouteParams(array $params): void
    {
        $this->routeParams = $params;
    }

    /**
     * Get a route parameter (e.g., {id} from /movies/{id}).
     */
    public function param(string $key, mixed $default = null): mixed
    {
        return $this->routeParams[$key] ?? $default;
    }

    /**
     * Get all route parameters.
     */
    public function params(): array
    {
        return $this->routeParams;
    }

    /**
     * Get an uploaded file.
     */
    public function file(string $key): ?array
    {
        return $_FILES[$key] ?? null;
    }

    /**
     * Check if the request expects JSON.
     */
    public function expectsJson(): bool
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        return str_contains($accept, 'application/json');
    }

    /**
     * Check if the request is an AJAX/XHR request.
     */
    public function isAjax(): bool
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            || $this->expectsJson();
    }

    /**
     * Get a header value.
     */
    public function header(string $key, mixed $default = null): mixed
    {
        $serverKey = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
        return $_SERVER[$serverKey] ?? $default;
    }
}
