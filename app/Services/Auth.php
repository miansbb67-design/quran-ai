<?php

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Security.php';

class Auth
{
    public static function user(): ?array
    {
        Security::startSession();
        if (empty($_SESSION['user_id'])) {
            return null;
        }

        static $cachedUser = null;
        if ($cachedUser && $cachedUser['id'] == $_SESSION['user_id']) {
            return $cachedUser;
        }

        $user = Database::fetch(
            "SELECT u.*, p.phone, p.country, p.preferred_qari_id, p.preferred_translation, 
                    p.daily_goal_minutes, p.daily_ayah_target, p.arabic_font_size
             FROM users u 
             LEFT JOIN user_profiles p ON u.id = p.user_id 
             WHERE u.id = :id AND u.status = 'active'",
            ['id' => $_SESSION['user_id']]
        );

        if (!$user) {
            self::logout();
            return null;
        }

        $cachedUser = $user;
        return $user;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function id(): ?int
    {
        return self::user()['id'] ?? null;
    }

    public static function isAdmin(): bool
    {
        $user = self::user();
        return $user && ($user['role'] === 'admin');
    }

    public static function login(int $userId): void
    {
        Security::startSession();
        session_regenerate_id(true);
        $_SESSION['user_id'] = $userId;
    }

    public static function logout(): void
    {
        Security::startSession();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    public static function requireAuth(): void
    {
        if (!self::check()) {
            $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'] ?? Controller::url('/dashboard');
            header('Location: ' . Controller::url('/login'));
            exit;
        }
    }

    public static function requireAdmin(): void
    {
        if (!self::isAdmin()) {
            http_response_code(403);
            die('<!DOCTYPE html><html><body style="font-family:sans-serif;text-align:center;padding:50px;">
                <h1 style="color:#d9534f;">403 — Unauthorized Access</h1>
                <p>You do not have administrative privileges to access this area.</p>
                <a href="/" style="display:inline-block;padding:10px 20px;background:#059669;color:#fff;text-decoration:none;border-radius:5px;">Return Home</a>
            </body></html>');
        }
    }
}

