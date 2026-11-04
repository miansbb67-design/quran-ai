<?php

require_once __DIR__ . '/../Services/Security.php';
require_once __DIR__ . '/../Services/Auth.php';

abstract class Controller
{
    public static function basePath(): string
    {
        $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        $scriptDir = str_replace('\\', '/', $scriptDir);
        $parentDir = preg_replace('#/public$#', '', $scriptDir);
        
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        if (str_starts_with($requestUri, $scriptDir) && $scriptDir !== '/') {
            return $scriptDir;
        } elseif (str_starts_with($requestUri, $parentDir) && $parentDir !== '/') {
            return $parentDir;
        }
        return '';
    }

    public static function url(string $path = '/'): string
    {
        $base = self::basePath();
        $path = '/' . ltrim($path, '/');
        if ($path === '/' && !empty($base)) {
            return $base;
        }
        return rtrim($base, '/') . $path;
    }

    protected function view(string $viewPath, array $data = []): void
    {
        extract($data);
        $user = Auth::user();
        $csrfToken = Security::generateCsrf();
        $csrfField = Security::csrfField();
        $baseUrl = self::basePath();

        $viewFile = __DIR__ . "/../Views/{$viewPath}.php";
        if (!file_exists($viewFile)) {
            die("View template not found: {$viewPath}");
        }

        require $viewFile;
    }

    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    protected function redirect(string $url): void
    {
        $target = self::url($url);
        header("Location: {$target}");
        exit;
    }

    protected function validateCsrf(): void
    {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        if (!Security::verifyCsrf($token)) {
            $this->json(['success' => false, 'error' => 'CSRF token mismatch. Please refresh the page.'], 419);
        }
    }
}
