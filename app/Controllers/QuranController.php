<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Services/Database.php';
require_once __DIR__ . '/../Services/Auth.php';

class QuranController extends Controller
{
    public function index(): void
    {
        $surahs = Database::fetchAll("SELECT * FROM surahs ORDER BY id ASC");
        $qaris = Database::fetchAll("SELECT * FROM qari_profiles WHERE is_active = 1 ORDER BY is_featured DESC, id ASC");

        $user = Auth::user();
        $lastRead = null;
        if ($user) {
            $lastRead = Database::fetch(
                "SELECT r.*, s.name_english, s.name_arabic 
                 FROM reading_progress r 
                 JOIN surahs s ON r.surah_id = s.id 
                 WHERE r.user_id = :uid 
                 ORDER BY r.last_read_at DESC LIMIT 1",
                ['uid' => $user['id']]
            );
        }

        $this->view('quran/index', [
            'title' => 'The Holy Quran — Read, Search & Listen | Bayan AI',
            'surahs' => $surahs,
            'qaris' => $qaris,
            'lastRead' => $lastRead
        ]);
    }

    public function show(string $id): void
    {
        $surahId = (int) $id;
        if ($surahId < 1 || $surahId > 114) {
            $surahId = 1;
        }

        $surah = Database::fetch("SELECT * FROM surahs WHERE id = :id", ['id' => $surahId]);
        $allSurahs = Database::fetchAll("SELECT id, name_arabic, name_english, total_ayahs FROM surahs ORDER BY id ASC");
        $qaris = Database::fetchAll("SELECT * FROM qari_profiles WHERE is_active = 1 ORDER BY is_featured DESC, id ASC");

        $user = Auth::user();
        $bookmarks = [];
        if ($user) {
            // Save reading progress
            Database::query(
                "INSERT INTO reading_progress (user_id, surah_id, ayah_number, last_read_at) 
                 VALUES (:uid, :sid, 1, NOW()) 
                 ON DUPLICATE KEY UPDATE ayah_number = VALUES(ayah_number), last_read_at = NOW()",
                ['uid' => $user['id'], 'sid' => $surahId]
            );

            // Fetch bookmarks for this Surah
            $userBookmarks = Database::fetchAll(
                "SELECT ayah_number FROM bookmarks WHERE user_id = :uid AND surah_id = :sid",
                ['uid' => $user['id'], 'sid' => $surahId]
            );
            $bookmarks = array_column($userBookmarks, 'ayah_number');
        }

        $this->view('quran/show', [
            'title' => "Surah {$surah['name_english']} ({$surah['name_arabic']}) — Bayan AI Quran",
            'surah' => $surah,
            'allSurahs' => $allSurahs,
            'qaris' => $qaris,
            'bookmarks' => $bookmarks
        ]);
    }

    public function apiSurahAyahs(string $id): void
    {
        $surahId = (int) $id;
        $edition = $_GET['edition'] ?? 'en.walk'; // Default English translation

        // We fetch authenticated Quran text from official Al-Quran Cloud API or local verified dataset
        $arabicUrl = "https://api.alquran.cloud/v1/surah/{$surahId}/quran-uthmani";
        $transUrl  = "https://api.alquran.cloud/v1/surah/{$surahId}/{$edition}";

        $context = stream_context_create([
            'http' => ['timeout' => 8, 'user_agent' => 'Bayan-AI-Quran-App']
        ]);

        $arabicData = @file_get_contents($arabicUrl, false, $context);
        $transData  = @file_get_contents($transUrl, false, $context);

        if (!$arabicData) {
            $this->json(['success' => false, 'error' => 'Unable to load authentic Quranic text source.'], 503);
        }

        $arabicJson = json_decode($arabicData, true)['data']['ayahs'] ?? [];
        $transJson  = $transData ? (json_decode($transData, true)['data']['ayahs'] ?? []) : [];

        $ayahs = [];
        foreach ($arabicJson as $i => $item) {
            $ayahs[] = [
                'number' => $item['numberInSurah'],
                'global_number' => $item['number'],
                'text' => $item['text'],
                'translation' => $transJson[$i]['text'] ?? '',
                'audio_num' => str_pad($surahId, 3, '0', STR_PAD_LEFT) . str_pad($item['numberInSurah'], 3, '0', STR_PAD_LEFT) . '.mp3'
            ];
        }

        $this->json(['success' => true, 'surah_id' => $surahId, 'ayahs' => $ayahs]);
    }

    public function toggleBookmark(): void
    {
        Auth::requireAuth();
        $this->validateCsrf();

        $userId = Auth::id();
        $surahId = (int) ($_POST['surah_id'] ?? 0);
        $ayahNumber = (int) ($_POST['ayah_number'] ?? 0);

        if (!$surahId || !$ayahNumber) {
            $this->json(['success' => false, 'error' => 'Invalid parameters'], 400);
        }

        $existing = Database::fetch(
            "SELECT id FROM bookmarks WHERE user_id = :uid AND surah_id = :sid AND ayah_number = :ayah",
            ['uid' => $userId, 'sid' => $surahId, 'ayah' => $ayahNumber]
        );

        if ($existing) {
            Database::delete('bookmarks', 'id = :id', ['id' => $existing['id']]);
            $this->json(['success' => true, 'bookmarked' => false, 'message' => 'Bookmark removed.']);
        } else {
            Database::insert('bookmarks', [
                'user_id' => $userId,
                'surah_id' => $surahId,
                'ayah_number' => $ayahNumber
            ]);
            $this->json(['success' => true, 'bookmarked' => true, 'message' => 'Ayah saved to bookmarks!']);
        }
    }
}
