<?php

/**
 * Global Helper Functions for CelesView
 * 
 * Auto-loaded via composer.json "files" configuration.
 * Available everywhere in the application.
 */

if (!function_exists('env')) {
    /**
     * Get an environment variable value.
     */
    function env(string $key, mixed $default = null): mixed
    {
        $value = $_ENV[$key] ?? getenv($key);

        if ($value === false || $value === null) {
            return $default;
        }

        // Convert string booleans
        return match (strtolower((string) $value)) {
            'true', '(true)'   => true,
            'false', '(false)' => false,
            'null', '(null)'   => null,
            'empty', '(empty)' => '',
            default            => $value,
        };
    }
}

if (!function_exists('config')) {
    /**
     * Get a config value using dot notation.
     */
    function config(string $key, mixed $default = null): mixed
    {
        return \App\Core\App::getInstance()->config($key, $default);
    }
}

if (!function_exists('base_path')) {
    /**
     * Get the project root path.
     */
    function base_path(string $path = ''): string
    {
        $root = dirname(__DIR__, 3);
        return $root . ($path ? DIRECTORY_SEPARATOR . ltrim($path, '/\\') : '');
    }
}

if (!function_exists('public_path')) {
    /**
     * Get the public directory path.
     */
    function public_path(string $path = ''): string
    {
        return base_path('public' . ($path ? '/' . ltrim($path, '/') : ''));
    }
}

if (!function_exists('asset')) {
    /**
     * Generate a URL for a public asset.
     */
    function asset(string $path): string
    {
        $base = rtrim(env('APP_URL', ''), '/');
        return $base . '/assets/' . ltrim($path, '/');
    }
}

if (!function_exists('url')) {
    /**
     * Generate a full URL for a path.
     */
    function url(string $path = ''): string
    {
        $base = rtrim(env('APP_URL', ''), '/');
        return $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirect to a URL.
     */
    function redirect(string $url): void
    {
        \App\Core\Response::redirect($url);
    }
}

if (!function_exists('session')) {
    /**
     * Get or set a session value.
     */
    function session(string $key = null, mixed $value = null): mixed
    {
        if ($key === null) {
            return \App\Core\Session::class;
        }

        if ($value !== null) {
            \App\Core\Session::set($key, $value);
            return null;
        }

        return \App\Core\Session::get($key);
    }
}

if (!function_exists('auth')) {
    /**
     * Quick auth check helpers.
     */
    function auth(): object
    {
        return new class {
            public function check(): bool { return \App\Core\Session::isLoggedIn(); }
            public function id(): ?int { return \App\Core\Session::userId(); }
            public function user(?string $key = null): mixed {
                if (!$this->check()) return null;
                $db = \App\Core\Database::getInstance();
                $user = $db->fetch("SELECT * FROM users WHERE id = ?", [$this->id()]);
                if (!$user) return null;
                return $key ? ($user[$key] ?? null) : $user;
            }
        };
    }
}

if (!function_exists('old')) {
    /**
     * Get old input value (for form repopulation after validation errors).
     */
    function old(string $key, mixed $default = ''): mixed
    {
        return \App\Core\Session::getFlash('_old_input')[$key] ?? $default;
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Generate a hidden CSRF token field.
     */
    function csrf_field(): string
    {
        $token = \App\Core\Session::get('_csrf_token', '');
        return '<input type="hidden" name="_csrf_token" value="' . htmlspecialchars($token) . '">';
    }
}

if (!function_exists('e')) {
    /**
     * Escape HTML entities (shortcut for htmlspecialchars).
     */
    function e(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('db')) {
    /**
     * Get the Database instance.
     */
    function db(): \App\Core\Database
    {
        return \App\Core\Database::getInstance();
    }
}
