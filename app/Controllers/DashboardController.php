<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Services/Database.php';
require_once __DIR__ . '/../Services/Auth.php';

class DashboardController extends Controller
{
    public function index(): void
    {
        Auth::requireAuth();
        $user = Auth::user();
        $userId = $user['id'];

        // Last reading progress
        $lastRead = Database::fetch(
            "SELECT r.*, s.name_english, s.name_arabic, s.total_ayahs 
             FROM reading_progress r 
             JOIN surahs s ON r.surah_id = s.id 
             WHERE r.user_id = :uid 
             ORDER BY r.last_read_at DESC LIMIT 1",
            ['uid' => $userId]
        );

        // Recent Bookmarks
        $bookmarks = Database::fetchAll(
            "SELECT b.*, s.name_english, s.name_arabic 
             FROM bookmarks b 
             JOIN surahs s ON b.surah_id = s.id 
             WHERE b.user_id = :uid 
             ORDER BY b.created_at DESC LIMIT 5",
            ['uid' => $userId]
        );

        // Practice History
        $recentPractices = Database::fetchAll(
            "SELECT p.*, s.name_english, s.name_arabic 
             FROM practice_sessions p 
             JOIN surahs s ON p.surah_id = s.id 
             WHERE p.user_id = :uid 
             ORDER BY p.created_at DESC LIMIT 5",
            ['uid' => $userId]
        );

        // Hifz stats
        $hifzStats = Database::fetch(
            "SELECT 
                COUNT(*) as total_targets,
                SUM(CASE WHEN status = 'memorized' THEN 1 ELSE 0 END) as memorized
             FROM hifz_progress 
             WHERE user_id = :uid",
            ['uid' => $userId]
        );

        $this->view('dashboard/index', [
            'title' => 'Student Dashboard — Bayan AI Quran',
            'user' => $user,
            'lastRead' => $lastRead,
            'bookmarks' => $bookmarks,
            'recentPractices' => $recentPractices,
            'hifzStats' => $hifzStats
        ]);
    }
}
