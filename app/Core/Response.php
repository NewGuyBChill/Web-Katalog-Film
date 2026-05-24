<?php

namespace App\Core;

/**
 * Response — Handles HTTP responses.
 * 
 * Static helper for sending JSON, redirects, status codes, and error pages.
 */
class Response
{
    /**
     * Send a JSON response and terminate.
     */
    public static function json(mixed $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Redirect to a given URL.
     */
    public static function redirect(string $url, int $statusCode = 302): void
    {
        http_response_code($statusCode);
        header("Location: {$url}");
        exit;
    }

    /**
     * Send a 404 Not Found response.
     */
    public static function notFound(): void
    {
        http_response_code(404);

        $viewPath = dirname(__DIR__, 2) . '/resources/views/errors/404.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            echo '<div style="padding:150px 20px;text-align:center;height:60vh;font-family:Inter,sans-serif;color:#fff;background:#121212;">';
            echo '<h1 style="font-size:6rem;margin-bottom:1rem;color:#00d2ff;">404</h1>';
            echo '<h2 style="color:#a0a0a0;">Halaman Tidak Ditemukan</h2>';
            echo '<p style="color:#666;margin-top:1rem;">Halaman yang Anda cari tidak tersedia.</p>';
            echo '<a href="/" style="display:inline-block;margin-top:2rem;padding:0.7rem 2rem;background:#00d2ff;color:#000;border-radius:30px;text-decoration:none;font-weight:600;">Kembali ke Home</a>';
            echo '</div>';
        }
        exit;
    }

    /**
     * Send a 500 Internal Server Error response.
     */
    public static function serverError(string $message = ''): void
    {
        http_response_code(500);

        $viewPath = dirname(__DIR__, 2) . '/resources/views/errors/500.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            echo '<div style="padding:150px 20px;text-align:center;height:60vh;font-family:Inter,sans-serif;color:#fff;background:#121212;">';
            echo '<h1 style="font-size:6rem;margin-bottom:1rem;color:#ff3b3b;">500</h1>';
            echo '<h2 style="color:#a0a0a0;">Server Error</h2>';
            if (env('APP_DEBUG', false) && $message) {
                echo '<p style="color:#ff6b6b;margin-top:1rem;font-family:monospace;">' . htmlspecialchars($message) . '</p>';
            }
            echo '</div>';
        }
        exit;
    }

    /**
     * Send a 403 Forbidden response.
     */
    public static function forbidden(): void
    {
        http_response_code(403);
        echo '<div style="padding:150px 20px;text-align:center;font-family:Inter,sans-serif;color:#fff;background:#121212;">';
        echo '<h1 style="font-size:4rem;color:#FCD34D;">403</h1>';
        echo '<h2 style="color:#a0a0a0;">Akses Ditolak</h2>';
        echo '</div>';
        exit;
    }
}
