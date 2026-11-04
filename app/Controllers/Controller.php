<?php

require_once __DIR__ . '/../Services/Security.php';
require_once __DIR__ . '/../Services/Auth.php';

abstract class Controller
{
    protected function view(string $viewPath, array $data = []): void
    {
        extract($data);
        $user = Auth::user();
        $csrfToken = Security::generateCsrf();
        $csrfField = Security::csrfField();

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
        header("Location: {$url}");
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
