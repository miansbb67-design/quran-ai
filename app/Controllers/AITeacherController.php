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
        $qaris = Database::fetchAll("SELECT * FROM qari_profiles WHERE is_active = 1 ORDER BY is_featured DESC LIMIT 6");

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
            'title' => 'AI Quran Recitation Teacher & Tajweed Assistant — Life Deen',
            'surahs' => $surahs,
            'qaris' => $qaris,
            'recentSessions' => $recentSessions,
            'aiProvider' => $aiProvider,
            'hasAiKey' => $hasAiKey
        ]);
    }

    public function evaluateRecitation(): void
    {
        $userId = Auth::id() ?: 1;

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
            $text = preg_replace('/[\x{064B}-\x{065F}\x{0670}\x{06D6}-\x{06ED}]/u', '', $text);
            $text = preg_replace('/[إأآٱا]/u', 'ا', $text);
            $text = preg_replace('/ة/u', 'ه', $text);
            $text = preg_replace('/[يى]/u', 'ي', $text);
            $text = preg_replace('/[^\p{Arabic}\s]/u', '', $text);
            return preg_replace('/\s+/u', ' ', trim($text));
        };

        $cleanTarget = $normalize($targetText);
        $cleanRecognized = $normalize($recognizedText);

        $targetWords = array_values(array_filter(explode(' ', $cleanTarget)));
        $recognizedWords = array_values(array_filter(explode(' ', $cleanRecognized)));

        $totalWords = count($targetWords);
        $matchedWords = 0;
        $wordDiffs = [];
        $mistakesList = [];

        foreach ($targetWords as $idx => $tWord) {
            $rWord = $recognizedWords[$idx] ?? null;
            $isMatch = false;
            $matchConfidence = 0;

            if ($rWord !== null) {
                if ($rWord === $tWord) {
                    $isMatch = true;
                    $matchConfidence = 100;
                } else {
                    similar_text($tWord, $rWord, $perc);
                    if ($perc >= 75) {
                        $isMatch = true;
                        $matchConfidence = round($perc);
                    }
                }
            }

            if ($isMatch) {
                $matchedWords++;
                $wordDiffs[] = [
                    'index' => $idx + 1,
                    'original' => $tWord,
                    'recited' => $rWord,
                    'status' => 'correct',
                    'confidence' => $matchConfidence,
                    'message' => 'Bilkul Sahi (Correct)'
                ];
            } else {
                $diffItem = [
                    'index' => $idx + 1,
                    'original' => $tWord,
                    'recited' => $rWord ?: '(Chhoot gaya / Omitted)',
                    'status' => 'incorrect',
                    'confidence' => 0,
                    'message' => $rWord ? "Aapne bola: '{$rWord}' | Sahi: '{$tWord}'" : "Yeh lafz chhoot gaya hai"
                ];
                $wordDiffs[] = $diffItem;
                $mistakesList[] = $diffItem;
            }
        }

        $accuracy = $totalWords > 0 ? round(($matchedWords / $totalWords) * 100, 1) : 0;
        $status = ($accuracy >= 80) ? 'passed' : 'needs_practice';

        // Advanced AI Tajweed Explanation via Google Gemini
        $apiKey = Env::get('AI_API_KEY');
        $aiAnalysisText = null;

        if (!empty($apiKey) && count($mistakesList) > 0) {
            $prompt = "You are an expert Islamic Tajweed Teacher. The student practiced reciting Surah {$surahId}, Ayah {$ayahNumber}. "
                    . "Target text: '{$cleanTarget}'. Student recognized recitation: '{$cleanRecognized}'. "
                    . "Differences: " . json_encode($mistakesList, JSON_UNESCAPED_UNICODE) . ". "
                    . "In 2-3 brief, helpful sentences in Roman Urdu, give friendly advice on pronunciation (Makharij) and how to improve.";

            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;
            $payload = json_encode([
                "contents" => [
                    ["parts" => [["text" => $prompt]]]
                ]
            ]);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            $response = curl_exec($ch);
            curl_close($ch);

            if ($response) {
                $resData = json_decode($response, true);
                $aiAnalysisText = $resData['candidates'][0]['content']['parts'][0]['text'] ?? null;
            }
        }

        // Save session in database
        $feedbackJson = json_encode([
            'word_diffs' => $wordDiffs,
            'clean_target' => $cleanTarget,
            'clean_recited' => $cleanRecognized,
            'ai_advice' => $aiAnalysisText
        ], JSON_UNESCAPED_UNICODE);

        try {
            Database::insert('practice_sessions', [
                'user_id' => $userId,
                'surah_id' => $surahId,
                'ayah_number' => $ayahNumber,
                'recognized_text' => $recognizedText,
                'target_text' => $targetText,
                'accuracy_score' => $accuracy,
                'status' => $status,
                'feedback_json' => $feedbackJson,
                'ai_provider' => !empty($apiKey) ? 'gemini-1.5-flash' : 'speech_api'
            ]);
        } catch (\Exception $e) {}

        $this->json([
            'success' => true,
            'accuracy' => $accuracy,
            'status' => $status,
            'total_words' => $totalWords,
            'matched_words' => $matchedWords,
            'word_diffs' => $wordDiffs,
            'ai_advice' => $aiAnalysisText,
            'recognized_text' => $recognizedText
        ]);
    }
}
