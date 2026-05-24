<?php

namespace App\Core;

use Dotenv\Dotenv;

/**
 * App — Application Bootstrap & Service Container
 * 
 * Initializes the application: loads environment, registers services,
 * and dispatches the request through the router.
 */
class App
{
    private static ?App $instance = null;
    private Router $router;
    private array $config = [];

    private function __construct()
    {
        $this->loadEnvironment();
        $this->loadConfig();
    }

    /**
     * Singleton: returns the single App instance.
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Load .env variables into $_ENV and getenv().
     */
    private function loadEnvironment(): void
    {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->safeLoad();
    }

    /**
     * Load all config files from /config directory.
     */
    private function loadConfig(): void
    {
        $configPath = dirname(__DIR__, 2) . '/config';
        foreach (glob($configPath . '/*.php') as $file) {
            $key = basename($file, '.php');
            // Skip legacy config files that don't return arrays
            $result = require $file;
            if (is_array($result)) {
                $this->config[$key] = $result;
            }
        }
    }

    /**
     * Get a config value using dot notation: config('database.host')
     */
    public function config(string $key, mixed $default = null): mixed
    {
        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    /**
     * Get the Router instance.
     */
    public function getRouter(): Router
    {
        if (!isset($this->router)) {
            $this->router = new Router();
        }
        return $this->router;
    }

    /**
     * Run the application: resolve the current request through the router.
     */
    public function run(): void
    {
        $router = $this->getRouter();
        $request = new Request();
        $router->dispatch($request);
    }
}
