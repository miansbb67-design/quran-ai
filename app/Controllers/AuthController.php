<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Services/Database.php';
require_once __DIR__ . '/../Services/Auth.php';
require_once __DIR__ . '/../Services/Security.php';

class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }
        $this->view('auth/login', [
            'title' => 'Sign In — Bayan AI Quran Teacher',
            'error' => $_SESSION['login_error'] ?? null
        ]);
        unset($_SESSION['login_error']);
    }

    public function handleLogin(): void
    {
        $this->validateCsrf();

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!Security::checkRateLimit("login_{$email}", 5, 300)) {
            $_SESSION['login_error'] = 'Too many failed attempts. Please wait 5 minutes before trying again.';
            $this->redirect('/login');
        }

        if (empty($email) || empty($password)) {
            $_SESSION['login_error'] = 'Please enter both your email address and password.';
            $this->redirect('/login');
        }

        $user = Database::fetch("SELECT * FROM users WHERE email = :email LIMIT 1", ['email' => $email]);

        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['login_error'] = 'Invalid email address or password.';
            $this->redirect('/login');
        }

        if ($user['status'] !== 'active') {
            $_SESSION['login_error'] = 'Your account has been deactivated or suspended. Please contact support.';
            $this->redirect('/login');
        }

        // Login user
        Auth::login($user['id']);

        // Record Analytics
        try {
            Database::insert('analytics_events', [
                'user_id' => $user['id'],
                'event_type' => 'user_login',
                'page_url' => '/login',
                'ip_hash' => hash('sha256', $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1')
            ]);
        } catch (\Exception $e) {}

        if ($user['role'] === 'admin') {
            $this->redirect('/admin');
        }

        $intended = $_SESSION['intended_url'] ?? '/dashboard';
        unset($_SESSION['intended_url']);
        $this->redirect($intended);
    }

    public function showRegister(): void
    {
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }
        $this->view('auth/register', [
            'title' => 'Create Free Account — Bayan AI Quran Teacher',
            'error' => $_SESSION['register_error'] ?? null
        ]);
        unset($_SESSION['register_error']);
    }

    public function handleRegister(): void
    {
        $this->validateCsrf();

        $name = trim($_POST['name'] ?? '');
        $email = trim(strtolower($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['password_confirmation'] ?? '';

        if (empty($name) || empty($email) || empty($password)) {
            $_SESSION['register_error'] = 'All required fields must be completed.';
            $this->redirect('/register');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['register_error'] = 'Please provide a valid email address.';
            $this->redirect('/register');
        }

        if (strlen($password) < 8) {
            $_SESSION['register_error'] = 'Password must be at least 8 characters long.';
            $this->redirect('/register');
        }

        if ($password !== $confirmPassword) {
            $_SESSION['register_error'] = 'Password confirmation does not match.';
            $this->redirect('/register');
        }

        $existing = Database::fetch("SELECT id FROM users WHERE email = :email LIMIT 1", ['email' => $email]);
        if ($existing) {
            $_SESSION['register_error'] = 'An account with this email address already exists.';
            $this->redirect('/register');
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $userId = Database::insert('users', [
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
            'role' => 'student',
            'status' => 'active',
            'email_verified_at' => date('Y-m-d H:i:s')
        ]);

        Database::insert('user_profiles', [
            'user_id' => $userId,
            'preferred_qari_id' => 1,
            'daily_goal_minutes' => 15,
            'daily_ayah_target' => 5,
            'arabic_font_size' => 28
        ]);

        // Record Analytics
        try {
            Database::insert('analytics_events', [
                'user_id' => $userId,
                'event_type' => 'user_registered',
                'page_url' => '/register',
                'ip_hash' => hash('sha256', $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1')
            ]);
        } catch (\Exception $e) {}

        Auth::login($userId);
        $this->redirect('/dashboard');
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect('/');
    }
}
