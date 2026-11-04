<?php

class Security
{
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
                       (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ||
                       (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

            session_set_cookie_params([
                'lifetime' => 86400 * 7, // 7 days
                'path'     => '/',
                'domain'   => '',
                'secure'   => $isHttps,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);

            session_start();
        }
    }

    public static function generateCsrf(): string
    {
        self::startSession();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function csrfField(): string
    {
        $token = self::generateCsrf();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    public static function verifyCsrf(?string $token): bool
    {
        self::startSession();
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function sanitize(string $input): string
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    public static function escape(?string $str): string
    {
        return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
    }

    public static function checkRateLimit(string $key, int $maxAttempts = 5, int $decaySeconds = 60): bool
    {
        self::startSession();
        $rateKey = "rate_limit_{$key}";
        $now = time();

        if (!isset($_SESSION[$rateKey])) {
            $_SESSION[$rateKey] = ['attempts' => 1, 'reset_at' => $now + $decaySeconds];
            return true;
        }

        if ($now > $_SESSION[$rateKey]['reset_at']) {
            $_SESSION[$rateKey] = ['attempts' => 1, 'reset_at' => $now + $decaySeconds];
            return true;
        }

        $_SESSION[$rateKey]['attempts']++;
        return $_SESSION[$rateKey]['attempts'] <= $maxAttempts;
    }
}
