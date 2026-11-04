<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Services/Database.php';
require_once __DIR__ . '/../Services/Auth.php';

class HifzController extends Controller
{
    public function index(): void
    {
        Auth::requireAuth();
        $userId = Auth::id();

        $hifzList = Database::fetchAll(
            "SELECT h.*, s.name_english, s.name_arabic, s.total_ayahs 
             FROM hifz_progress h 
             JOIN surahs s ON h.surah_id = s.id 
             WHERE h.user_id = :uid 
             ORDER BY h.updated_at DESC",
            ['uid' => $userId]
        );

        $surahs = Database::fetchAll("SELECT id, name_english, name_arabic, total_ayahs FROM surahs ORDER BY id ASC");

        $stats = Database::fetch(
            "SELECT 
                COUNT(*) as total_items,
                SUM(CASE WHEN status = 'memorized' THEN 1 ELSE 0 END) as memorized_count,
                SUM(CASE WHEN status = 'learning' THEN 1 ELSE 0 END) as learning_count,
                SUM(CASE WHEN status = 'needs_revision' THEN 1 ELSE 0 END) as revision_count
             FROM hifz_progress 
             WHERE user_id = :uid",
            ['uid' => $userId]
        );

        $this->view('hifz/index', [
            'title' => 'Quran Hifz & Revision Tracker — Bayan AI',
            'hifzList' => $hifzList,
            'surahs' => $surahs,
            'stats' => $stats
        ]);
    }

    public function store(): void
    {
        Auth::requireAuth();
        $this->validateCsrf();

        $userId = Auth::id();
        $surahId = (int) ($_POST['surah_id'] ?? 1);
        $startAyah = (int) ($_POST['start_ayah'] ?? 1);
        $endAyah = (int) ($_POST['end_ayah'] ?? 1);
        $status = in_array($_POST['status'] ?? '', ['learning', 'memorized', 'needs_revision']) ? $_POST['status'] : 'learning';
        $confidence = max(1, min(5, (int) ($_POST['confidence_level'] ?? 3)));
        $notes = trim($_POST['notes'] ?? '');

        Database::insert('hifz_progress', [
            'user_id' => $userId,
            'surah_id' => $surahId,
            'start_ayah' => $startAyah,
            'end_ayah' => $endAyah,
            'status' => $status,
            'confidence_level' => $confidence,
            'notes' => $notes,
            'next_revision_date' => date('Y-m-d', strtotime('+3 days'))
        ]);

        $this->redirect('/hifz');
    }

    public function updateStatus(): void
    {
        Auth::requireAuth();
        $this->validateCsrf();

        $userId = Auth::id();
        $id = (int) ($_POST['id'] ?? 0);
        $status = in_array($_POST['status'] ?? '', ['learning', 'memorized', 'needs_revision']) ? $_POST['status'] : 'learning';

        Database::update('hifz_progress', [
            'status' => $status,
            'last_reviewed_at' => date('Y-m-d H:i:s'),
            'next_revision_date' => ($status === 'memorized') ? date('Y-m-d', strtotime('+7 days')) : date('Y-m-d', strtotime('+1 days'))
        ], 'id = :id AND user_id = :uid', ['id' => $id, 'uid' => $userId]);

        $this->json(['success' => true]);
    }
}
