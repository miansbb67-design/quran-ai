<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\Quran\QuranApiService;
use App\Services\AI\RecitationAnalysisInterface;
use App\Models\PracticeSession;
use App\Models\PracticeAttempt;
use App\Models\ReadingProgress;
use App\Models\UserPreference;
use Illuminate\Http\Request;

class AITeacherController extends Controller
{
    protected QuranApiService $quranApi;
    protected RecitationAnalysisInterface $aiService;

    public function __construct(QuranApiService $quranApi, RecitationAnalysisInterface $aiService)
    {
        $this->quranApi = $quranApi;
        $this->aiService = $aiService;
    }

    /**
     * Show the AI Teacher interface.
     */
    public function index(Request $request)
    {
        $surahs = $this->quranApi->getSurahs();

        $readingProgress = ReadingProgress::firstOrCreate(
            ['user_id' => $request->user()->id],
            ['last_surah_number' => 1, 'last_ayah_number' => 1]
        );

        $prefs = UserPreference::firstOrCreate(
            ['user_id' => $request->user()->id]
        );

        return view('customer.ai-teacher.index', compact('surahs', 'readingProgress', 'prefs'));
    }

    /**
     * Get Ayah data for practice (AJAX).
     */
    public function getAyahForPractice(Request $request, int $surahNumber, int $ayahNumber)
    {
        $prefs = UserPreference::firstOrCreate(
            ['user_id' => $request->user()->id]
        );

        $defaultReciter = \App\Models\SiteSetting::get('default_reciter', 'ar.alafasy');
        $allowStudentChange = \App\Models\SiteSetting::get('allow_student_reciter_change', '1') == '1';
        $activeReciter = ($allowStudentChange && !empty($prefs->preferred_reciter))
            ? $prefs->preferred_reciter
            : $defaultReciter;

        $ayah = $this->quranApi->getAyah($surahNumber, $ayahNumber);
        $audio = $this->quranApi->getAyahAudio($surahNumber, $ayahNumber, $activeReciter);
        $translation = $this->quranApi->getAyah($surahNumber, $ayahNumber, $prefs->preferred_translation);
        $words = $this->quranApi->getAyahWords($surahNumber, $ayahNumber);

        if (!$ayah) {
            return response()->json(['error' => 'Unable to load Ayah data.'], 500);
        }

        return response()->json([
            'ayah' => $ayah,
            'audio' => $audio,
            'translation' => $translation,
            'words' => $words,
        ]);
    }

    /**
     * Start a new practice session.
     */
    public function startSession(Request $request)
    {
        $request->validate([
            'surah_number' => 'required|integer|min:1|max:114',
            'start_ayah' => 'required|integer|min:1',
            'end_ayah' => 'required|integer|min:1',
            'session_type' => 'required|in:reading,recitation,memorization',
        ]);

        $session = PracticeSession::create([
            'user_id' => $request->user()->id,
            'session_type' => $request->session_type,
            'surah_number' => $request->surah_number,
            'start_ayah' => $request->start_ayah,
            'end_ayah' => $request->end_ayah,
        ]);

        return response()->json([
            'session_id' => $session->id,
            'message' => 'Practice session started.',
        ]);
    }

    /**
     * Analyze a recitation attempt.
     */
    public function analyzeRecitation(Request $request)
    {
        $request->validate([
            'session_id' => 'required|integer|exists:practice_sessions,id',
            'surah_number' => 'required|integer|min:1|max:114',
            'ayah_number' => 'required|integer|min:1',
            'recognized_text' => 'required|string|max:5000',
        ]);

        // Verify session belongs to user
        $session = PracticeSession::where('id', $request->session_id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        // Get the target Ayah text
        $ayah = $this->quranApi->getAyah($request->surah_number, $request->ayah_number);

        if (!$ayah) {
            return response()->json(['error' => 'Unable to load target Ayah for comparison.'], 500);
        }

        $targetText = $ayah['text'];

        // Run AI analysis
        $analysis = $this->aiService->analyzeRecitation(
            $request->recognized_text,
            $targetText,
            [
                'surah' => $request->surah_number,
                'ayah' => $request->ayah_number,
            ]
        );

        // Get attempt number
        $attemptNumber = PracticeAttempt::where('practice_session_id', $session->id)
            ->where('surah_number', $request->surah_number)
            ->where('ayah_number', $request->ayah_number)
            ->count() + 1;

        // Store the attempt
        $attempt = PracticeAttempt::create([
            'practice_session_id' => $session->id,
            'user_id' => $request->user()->id,
            'surah_number' => $request->surah_number,
            'ayah_number' => $request->ayah_number,
            'attempt_number' => $attemptNumber,
            'recognized_text' => $request->recognized_text,
            'confidence_score' => $analysis['confidence_score'],
            'result' => $analysis['overall_result'],
            'word_analysis' => $analysis['word_analysis'],
            'feedback_message' => $analysis['feedback_message'],
        ]);

        return response()->json([
            'attempt_id' => $attempt->id,
            'attempt_number' => $attemptNumber,
            'analysis' => $analysis,
        ]);
    }

    /**
     * End a practice session.
     */
    public function endSession(Request $request)
    {
        $request->validate([
            'session_id' => 'required|integer|exists:practice_sessions,id',
            'duration_seconds' => 'required|integer|min:0',
        ]);

        $session = PracticeSession::where('id', $request->session_id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        // Calculate overall score from attempts
        $avgScore = $session->attempts()->avg('confidence_score') ?? 0;

        $session->update([
            'duration_seconds' => $request->duration_seconds,
            'overall_score' => round($avgScore, 2),
        ]);

        // Update reading progress
        $progress = ReadingProgress::firstOrCreate(
            ['user_id' => $request->user()->id]
        );
        $progress->total_reading_time_seconds += $request->duration_seconds;
        $progress->updateStreak();

        return response()->json([
            'message' => 'Session completed!',
            'overall_score' => round($avgScore, 2),
            'total_attempts' => $session->attempts()->count(),
        ]);
    }

    /**
     * Analyze a single word recitation attempt (targeted repeat practice).
     */
    public function analyzeSingleWord(Request $request)
    {
        $request->validate([
            'target_word' => 'required|string|max:200',
            'recognized_word' => 'required|string|max:200',
            'surah_number' => 'nullable|integer|min:1|max:114',
            'ayah_number' => 'nullable|integer|min:1',
            'word_position' => 'nullable|integer',
        ]);

        $result = method_exists($this->aiService, 'compareSingleWord')
            ? $this->aiService->compareSingleWord($request->target_word, $request->recognized_word)
            : [
                'target_word' => $request->target_word,
                'recognized_word' => $request->recognized_word,
                'status' => 'correct',
                'matched' => true,
                'similarity' => 1.0,
                'makhraj' => 'Standard',
                'tajweed_rule' => 'None',
                'teacher_tip' => 'Pronounced correctly!',
                'spoken_explanation_en' => 'Well done! Word pronounced correctly.',
                'spoken_explanation_ur' => 'بہت خوب! لفظ صحیح ادا کیا گیا۔',
                'spoken_explanation_ar' => 'أحسنت! نطق صحيح.',
            ];

        return response()->json($result);
    }

    /**
     * Delete practice history for current session (Privacy compliance).
     */
    public function deleteSessionHistory(Request $request)
    {
        $request->validate([
            'session_id' => 'required|integer|exists:practice_sessions,id',
        ]);

        $session = PracticeSession::where('id', $request->session_id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $session->attempts()->delete();
        $session->delete();

        return response()->json([
            'message' => 'Practice session history deleted successfully.',
        ]);
    }
}
