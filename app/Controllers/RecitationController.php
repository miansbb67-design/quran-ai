<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Services/Database.php';

class RecitationController extends Controller
{
    public function index(): void
    {
        $qaris = Database::fetchAll("SELECT * FROM qari_profiles WHERE is_active = 1 ORDER BY is_featured DESC, id ASC");
        $surahs = Database::fetchAll("SELECT id, name_arabic, name_english, total_ayahs, revelation_type FROM surahs ORDER BY id ASC");

        $this->view('recitations/index', [
            'title' => 'Authentic Quran Recitations & Tilawat Library — Bayan AI',
            'qaris' => $qaris,
            'surahs' => $surahs
        ]);
    }
}
