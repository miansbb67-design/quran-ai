<?php

require_once __DIR__ . '/../Controller.php';
require_once __DIR__ . '/../../Services/Database.php';
require_once __DIR__ . '/../../Services/Auth.php';

class AdminDashboardController extends Controller
{
    public function index(): void
    {
        Auth::requireAdmin();

        $stats = [
            'total_users' => Database::fetch("SELECT COUNT(*) as c FROM users WHERE role = 'student'")['c'] ?? 0,
            'active_users' => Database::fetch("SELECT COUNT(*) as c FROM users WHERE status = 'active'")['c'] ?? 0,
            'total_practices' => Database::fetch("SELECT COUNT(*) as c FROM practice_sessions")['c'] ?? 0,
            'total_hifz_records' => Database::fetch("SELECT COUNT(*) as c FROM hifz_progress")['c'] ?? 0,
            'total_events' => Database::fetch("SELECT COUNT(*) as c FROM analytics_events")['c'] ?? 0
        ];

        $recentUsers = Database::fetchAll("SELECT * FROM users ORDER BY created_at DESC LIMIT 5");
        $recentPractices = Database::fetchAll(
            "SELECT p.*, u.name as user_name, s.name_english 
             FROM practice_sessions p 
             JOIN users u ON p.user_id = u.id 
             JOIN surahs s ON p.surah_id = s.id 
             ORDER BY p.created_at DESC LIMIT 5"
        );

        $this->view('admin/index', [
            'title' => 'Admin Executive Hub — Bayan AI Quran Teacher',
            'stats' => $stats,
            'recentUsers' => $recentUsers,
            'recentPractices' => $recentPractices
        ]);
    }
}
