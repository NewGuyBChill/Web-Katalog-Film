<?php

namespace App\Core;

/**
 * Controller — Base controller class.
 * 
 * All module controllers should extend this class.
 * Provides view rendering, redirects, JSON responses, and shared utilities.
 */
abstract class Controller
{
    protected Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Render a view file with data.
     * 
     * @param string $view   Path relative to resources/views/ (dot notation: 'layouts.app')
     * @param array  $data   Variables to extract into view scope
     * @param string|null $layout  Optional layout to wrap the view (e.g., 'layouts.app')
     */
    protected function view(string $view, array $data = [], ?string $layout = null): void
    {
        // Extract data into local variables for the view
        extract($data);

        $viewPath = $this->resolveViewPath($view);

        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View [{$view}] not found at: {$viewPath}");
        }

        if ($layout) {
            // Capture the view content, then inject it into the layout
            ob_start();
            require $viewPath;
            $content = ob_get_clean();

            $layoutPath = $this->resolveViewPath($layout);
            if (!file_exists($layoutPath)) {
                throw new \RuntimeException("Layout [{$layout}] not found at: {$layoutPath}");
            }
            require $layoutPath;
        } else {
            require $viewPath;
        }
    }

    /**
     * Render a module-specific view.
     * 
     * @param string $module  Module name (e.g., 'Movies')
     * @param string $view    View filename without .php
     * @param array  $data    Variables for the view
     */
    protected function moduleView(string $module, string $view, array $data = []): void
    {
        extract($data);

        $basePath = dirname(__DIR__, 2) . '/modules';
        $viewPath = "{$basePath}/{$module}/Views/{$view}.php";

        if (!file_exists($viewPath)) {
            throw new \RuntimeException("Module view [{$module}/{$view}] not found.");
        }

        require $viewPath;
    }

    /**
     * Capture a module-specific view into a string (useful for passing as 'content' to layouts).
     */
    protected function captureModuleView(string $module, string $view, array $data = []): string
    {
        ob_start();
        $this->moduleView($module, $view, $data);
        return ob_get_clean();
    }

    /**
     * Convert dot notation to a file path under resources/views/.
     */
    private function resolveViewPath(string $view): string
    {
        $relativePath = str_replace('.', DIRECTORY_SEPARATOR, $view) . '.php';
        return dirname(__DIR__, 2) . '/resources/views/' . $relativePath;
    }

    /**
     * Return a JSON response.
     */
    protected function json(mixed $data, int $statusCode = 200): void
    {
        Response::json($data, $statusCode);
    }

    /**
     * Redirect to a URL.
     */
    protected function redirect(string $url): void
    {
        Response::redirect($url);
    }

    /**
     * Redirect back to the previous page.
     */
    protected function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        Response::redirect($referer);
    }
}
