<?php

namespace App\Core;

/**
 * Router — Maps HTTP requests to controller actions.
 * 
 * Supports:
 * - GET, POST, PUT, DELETE methods
 * - Named route parameters: /movies/{id}
 * - Middleware groups
 * - Route naming for URL generation
 */
class Router
{
    private array $routes = [];
    private array $namedRoutes = [];
    private array $globalMiddleware = [];

    /**
     * Register a GET route.
     */
    public function get(string $uri, array|callable $action): self
    {
        return $this->addRoute('GET', $uri, $action);
    }

    /**
     * Register a POST route.
     */
    public function post(string $uri, array|callable $action): self
    {
        return $this->addRoute('POST', $uri, $action);
    }

    /**
     * Register a PUT route.
     */
    public function put(string $uri, array|callable $action): self
    {
        return $this->addRoute('PUT', $uri, $action);
    }

    /**
     * Register a DELETE route.
     */
    public function delete(string $uri, array|callable $action): self
    {
        return $this->addRoute('DELETE', $uri, $action);
    }

    /**
     * Add a route to the internal collection.
     */
    private function addRoute(string $method, string $uri, array|callable $action): self
    {
        $uri = '/' . trim($uri, '/');

        $this->routes[] = [
            'method'     => $method,
            'uri'        => $uri,
            'action'     => $action,
            'middleware'  => [],
            'name'       => null,
            'pattern'    => $this->buildPattern($uri),
        ];

        return $this;
    }

    /**
     * Assign a name to the last registered route.
     */
    public function name(string $name): self
    {
        $lastIndex = count($this->routes) - 1;
        if ($lastIndex >= 0) {
            $this->routes[$lastIndex]['name'] = $name;
            $this->namedRoutes[$name] = $this->routes[$lastIndex]['uri'];
        }
        return $this;
    }

    /**
     * Assign middleware to the last registered route.
     */
    public function middleware(string|array $middleware): self
    {
        $lastIndex = count($this->routes) - 1;
        if ($lastIndex >= 0) {
            $middlewares = is_array($middleware) ? $middleware : [$middleware];
            $this->routes[$lastIndex]['middleware'] = array_merge(
                $this->routes[$lastIndex]['middleware'],
                $middlewares
            );
        }
        return $this;
    }

    /**
     * Register global middleware that applies to all routes.
     */
    public function pushGlobalMiddleware(string $middleware): void
    {
        $this->globalMiddleware[] = $middleware;
    }

    /**
     * Convert a URI pattern like /movies/{id} into a regex.
     */
    private function buildPattern(string $uri): string
    {
        // Replace {param} with a named capture group
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $uri);
        return '#^' . $pattern . '$#';
    }

    /**
     * Generate a URL for a named route.
     */
    public function url(string $name, array $params = []): string
    {
        if (!isset($this->namedRoutes[$name])) {
            throw new \RuntimeException("Route [{$name}] not defined.");
        }

        $uri = $this->namedRoutes[$name];

        foreach ($params as $key => $value) {
            $uri = str_replace('{' . $key . '}', $value, $uri);
        }

        return $uri;
    }

    /**
     * Dispatch the incoming request: match a route and execute it.
     */
    public function dispatch(Request $request): void
    {
        $method = $request->method();
        $uri = $request->uri();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                // Extract named parameters
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $request->setRouteParams($params);

                // Run middleware chain
                $allMiddleware = array_merge($this->globalMiddleware, $route['middleware']);
                foreach ($allMiddleware as $mw) {
                    $middlewareClass = $this->resolveMiddleware($mw);
                    if (class_exists($middlewareClass)) {
                        $instance = new $middlewareClass();
                        $result = $instance->handle($request);
                        if ($result === false) {
                            return; // Middleware blocked the request
                        }
                    }
                }

                // Execute the controller action or closure
                $this->executeAction($route['action'], $request);
                return;
            }
        }

        // No route matched — 404
        Response::notFound();
    }

    /**
     * Resolve a middleware alias to its full class name.
     */
    private function resolveMiddleware(string $alias): string
    {
        $map = [
            'auth'      => \App\Middleware\AuthMiddleware::class,
            'guest'     => \App\Middleware\GuestMiddleware::class,
            'admin'     => \App\Middleware\AdminMiddleware::class,
            'csrf'      => \App\Middleware\CSRFMiddleware::class,
            'ratelimit' => \App\Middleware\RateLimitMiddleware::class,
        ];

        return $map[$alias] ?? $alias;
    }

    /**
     * Execute a route action (controller method or closure).
     */
    private function executeAction(array|callable $action, Request $request): void
    {
        if (is_callable($action)) {
            call_user_func($action, $request);
            return;
        }

        // Array format: [ControllerClass::class, 'methodName']
        [$controllerClass, $method] = $action;

        if (!class_exists($controllerClass)) {
            throw new \RuntimeException("Controller [{$controllerClass}] not found.");
        }

        $controller = new $controllerClass();
        $controller->$method($request);
    }
}
