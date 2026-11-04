<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Services/Database.php';
require_once __DIR__ . '/../Services/Auth.php';
require_once __DIR__ . '/../Services/Env.php';

class AITeacherController extends Controller
{
    public function index(): void
    {
        $surahs = Database::fetchAll("SELECT id, name_arabic, name_english, total_ayahs FROM surahs ORDER BY id ASC");
        $qaris = Database::fetchAll("SELECT * FROM qari_profiles WHERE is_active = 1 ORDER BY is_featured DESC LIMIT 4");

        $user = Auth::user();
        $recentSessions = [];
        if ($user) {
            $recentSessions = Database::fetchAll(
                "SELECT p.*, s.name_english, s.name_arabic 
                 FROM practice_sessions p 
                 JOIN surahs s ON p.surah_id = s.id 
                 WHERE p.user_id = :uid 
                 ORDER BY p.created_at DESC LIMIT 5",
                ['uid' => $user['id']]
            );
        }

        $aiProvider = Env::get('AI_PROVIDER', 'gemini');
        $hasAiKey = !empty(Env::get('AI_API_KEY'));

        $this->view('ai_teacher/index', [
            'title' => 'AI Quran Recitation Teacher & Tajweed Assistant — Bayan AI',
            'surahs' => $surahs,
            'qaris' => $qaris,
            'recentSessions' => $recentSessions,
            'aiProvider' => $aiProvider,
            'hasAiKey' => $hasAiKey
        ]);
    }

    public function evaluateRecitation(): void
    {
        Auth::requireAuth();
        $this->validateCsrf();

        $userId = Auth::id();
        $surahId = (int) ($_POST['surah_id'] ?? 1);
        $ayahNumber = (int) ($_POST['ayah_number'] ?? 1);
        $recognizedText = trim($_POST['recognized_text'] ?? '');
        $targetText = trim($_POST['target_text'] ?? '');

        if (empty($targetText)) {
            $this->json(['success' => false, 'error' => 'Target ayah text is required.'], 400);
        }

        if (empty($recognizedText)) {
            $this->json([
                'success' => false,
                'error' => 'No speech input detected. Please allow microphone access and recite clearly.'
            ], 400);
        }

        // Clean diacritics and tashkeel for honest phonetic/word matching comparison
        $normalize = function(string $text) {
            // Remove Arabic harakat/tashkeel (Fathah, Dammah, Kasrah, Sukun, Shaddah, Tanween)
            $text = preg_replace('/[\x{064B}-\x{065F}\x{0670}\x{06D6}-\x{06ED}]/u', '', $text);
            // Normalize Alef variants
            $text = preg_replace('/[إأآا]/u', 'ا', $text);
            // Normalize Taa Marbuta & Haa
            $text = preg_replace('/ة/u', 'ه', $text);
            // Normalize Yaa
            $text = preg_replace('/[يى]/u', 'ي', $text);
            // Clean whitespace
            return preg_replace('/\s+/u', ' ', trim($text));
        };

        $cleanTarget = $normalize($targetText);
        $cleanRecognized = $normalize($recognizedText);

        $targetWords = explode(' ', $cleanTarget);
        $recognizedWords = explode(' ', $cleanRecognized);

        $totalWords = count($targetWords);
        $matchedWords = 0;
        $wordDiffs = [];

        foreach ($targetWords as $idx => $tWord) {
            $rWord = $recognizedWords[$idx] ?? null;
            if ($rWord && ($rWord === $tWord || similar_text($tWord, $rWord, $perc) > 80)) {
                $matchedWords++;
                $wordDiffs[] = ['word' => $tWord, 'status' => 'correct'];
            } else {
                $wordDiffs[] = ['word' => $tWord, 'status' => 'diff', 'recited' => $rWord];
            }
        }

        $accuracy = $totalWords > 0 ? round(($matchedWords / $totalWords) * 100, 1) : 0;
        $status = ($accuracy >= 80) ? 'passed' : 'needs_practice';

        // Save session in database
        $feedbackJson = json_encode([
            'word_diffs' => $wordDiffs,
            'clean_target' => $cleanTarget,
            'clean_recited' => $cleanRecognized,
            'disclaimer' => 'AI speech recognition is an educational aid. For certified Tajweed verification, consult a qualified Qari.'
        ], JSON_UNESCAPED_UNICODE);

        $sessionId = Database::insert('practice_sessions', [
            'user_id' => $userId,
            'surah_id' => $surahId,
            'ayah_number' => $ayahNumber,
            'recognized_text' => $recognizedText,
            'target_text' => $targetText,
            'accuracy_score' => $accuracy,
            'status' => $status,
            'feedback_json' => $feedbackJson,
            'ai_provider' => Env::get('AI_PROVIDER', 'speech_api')
        ]);

        $this->json([
            'success' => true,
            'session_id' => $sessionId,
            'accuracy' => $accuracy,
            'status' => $status,
            'word_diffs' => $wordDiffs,
            'disclaimer' => 'Educational assessment only. Tajweed verification requires a certified teacher.'
        ]);
    }
}
