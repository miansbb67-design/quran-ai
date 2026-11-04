<?php

class Router
{
    private static array $routes = [];
    private static ?string $basePath = null;

    public static function get(string $uri, $action): void
    {
        self::add('GET', $uri, $action);
    }

    public static function post(string $uri, $action): void
    {
        self::add('POST', $uri, $action);
    }

    public static function any(string $uri, $action): void
    {
        self::add(['GET', 'POST'], $uri, $action);
    }

    private static function add($methods, string $uri, $action): void
    {
        $methods = (array) $methods;
        $uri = '/' . trim($uri, '/');
        if ($uri !== '/') {
            $uri = rtrim($uri, '/');
        }

        foreach ($methods as $method) {
            self::$routes[strtoupper($method)][$uri] = $action;
        }
    }

    public static function dispatch(): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';

        // Strip query string
        if (false !== $pos = strpos($requestUri, '?')) {
            $requestUri = substr($requestUri, 0, $pos);
        }

        // Handle base path (e.g. /quran-ai/public or /quran-ai)
        $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        $scriptDir = str_replace('\\', '/', $scriptDir);
        
        // Remove /public if rewritten from parent folder
        $parentDir = preg_replace('#/public$#', '', $scriptDir);

        if (!empty($scriptDir) && $scriptDir !== '/' && str_starts_with($requestUri, $scriptDir)) {
            $requestUri = substr($requestUri, strlen($scriptDir));
        } elseif (!empty($parentDir) && $parentDir !== '/' && str_starts_with($requestUri, $parentDir)) {
            $requestUri = substr($requestUri, strlen($parentDir));
        }

        $requestUri = '/' . trim($requestUri, '/');
        if ($requestUri !== '/') {
            $requestUri = rtrim($requestUri, '/');
        }

        $routes = self::$routes[$requestMethod] ?? [];

        // Direct match
        if (isset($routes[$requestUri])) {
            self::execute($routes[$requestUri], []);
            return;
        }

        // Regex match with route parameters (e.g., /quran/{id})
        foreach ($routes as $route => $action) {
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $route);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $requestUri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                self::execute($action, $params);
                return;
            }
        }

        // 404 Not Found
        http_response_code(404);
        require_once __DIR__ . '/../Views/partials/404.php';
    }

    private static function execute($action, array $params = []): void
    {
        if (is_callable($action)) {
            call_user_func_array($action, $params);
            return;
        }

        if (is_string($action)) {
            [$controllerName, $method] = explode('@', $action);
            
            // Check controller paths (__DIR__ is app/Services, so controllers are in app/Controllers)
            $file = __DIR__ . "/../Controllers/{$controllerName}.php";
            if (!file_exists($file)) {
                $file = __DIR__ . "/../Controllers/Admin/{$controllerName}.php";
            }

            if (file_exists($file)) {
                require_once $file;
                $controller = new $controllerName();
                if (method_exists($controller, $method)) {
                    call_user_func_array([$controller, $method], $params);
                    return;
                }
            }
        }

        die("Routing Error: Action '{$action}' could not be resolved.");
    }
}
