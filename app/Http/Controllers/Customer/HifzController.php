<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\Quran\QuranApiService;
use App\Models\MemorizationProgress;
use App\Models\RevisionSchedule;
use App\Models\UserPreference;
use Illuminate\Http\Request;

class HifzController extends Controller
{
    protected QuranApiService $quranApi;

    public function __construct(QuranApiService $quranApi)
    {
        $this->quranApi = $quranApi;
    }

    /**
     * Show the Hifz module main page.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $surahs = $this->quranApi->getSurahs();

        // Memorization statistics
        $stats = [
            'total_memorized' => MemorizationProgress::where('user_id', $user->id)->where('status', 'memorized')->count(),
            'total_practicing' => MemorizationProgress::where('user_id', $user->id)->where('status', 'practicing')->count(),
            'total_learning' => MemorizationProgress::where('user_id', $user->id)->where('status', 'learning')->count(),
        ];

        // Upcoming revisions
        $upcomingRevisions = RevisionSchedule::where('user_id', $user->id)
            ->where('completed', false)
            ->where('scheduled_date', '<=', now()->addDays(7))
            ->orderBy('scheduled_date')
            ->take(10)
            ->get();

        // Recent memorization progress
        $recentProgress = MemorizationProgress::where('user_id', $user->id)
            ->whereNotNull('last_practiced_at')
            ->orderByDesc('last_practiced_at')
            ->take(10)
            ->get();

        return view('customer.hifz.index', compact('surahs', 'stats', 'upcomingRevisions', 'recentProgress'));
    }

    /**
     * Show Hifz practice for a specific Surah/Ayah range.
     */
    public function practice(Request $request, int $surahNumber)
    {
        $user = $request->user();
        $prefs = UserPreference::firstOrCreate(['user_id' => $user->id]);

        $surah = $this->quranApi->getSurah($surahNumber);
        $audio = $this->quranApi->getSurahAudio($surahNumber, $prefs->preferred_reciter);

        if (!$surah) {
            return back()->with('error', 'Unable to load Surah data.');
        }

        // Get memorization progress for this surah
        $memProgress = MemorizationProgress::where('user_id', $user->id)
            ->where('surah_number', $surahNumber)
            ->get()
            ->keyBy('ayah_number');

        $surahs = $this->quranApi->getSurahs();

        return view('customer.hifz.practice', compact('surah', 'audio', 'memProgress', 'surahs', 'surahNumber', 'prefs'));
    }

    /**
     * Update memorization status for an Ayah (AJAX).
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'surah_number' => 'required|integer|min:1|max:114',
            'ayah_number' => 'required|integer|min:1',
            'status' => 'required|in:not_started,learning,practicing,memorized',
        ]);

        $progress = MemorizationProgress::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'surah_number' => $request->surah_number,
                'ayah_number' => $request->ayah_number,
            ],
            [
                'status' => $request->status,
                'last_practiced_at' => now(),
            ]
        );

        // Schedule revision if marked as memorized
        if ($request->status === 'memorized') {
            RevisionSchedule::firstOrCreate(
                [
                    'user_id' => $request->user()->id,
                    'surah_number' => $request->surah_number,
                    'start_ayah' => $request->ayah_number,
                    'end_ayah' => $request->ayah_number,
                    'scheduled_date' => now()->addDay(),
                    'completed' => false,
                ]
            );
        }

        return response()->json([
            'success' => true,
            'progress' => $progress,
        ]);
    }

    /**
     * Record a memorization practice result.
     */
    public function recordPractice(Request $request)
    {
        $request->validate([
            'surah_number' => 'required|integer|min:1|max:114',
            'ayah_number' => 'required|integer|min:1',
            'result' => 'required|in:correct,uncertain,incorrect',
        ]);

        $progress = MemorizationProgress::firstOrCreate(
            [
                'user_id' => $request->user()->id,
                'surah_number' => $request->surah_number,
                'ayah_number' => $request->ayah_number,
            ],
            ['status' => 'learning']
        );

        $progress->recordPractice($request->result);

        return response()->json([
            'success' => true,
            'progress' => $progress->fresh(),
        ]);
    }

    /**
     * Complete a revision schedule.
     */
    public function completeRevision(Request $request)
    {
        $request->validate([
            'revision_id' => 'required|integer|exists:revision_schedules,id',
            'score' => 'nullable|numeric|min:0|max:100',
        ]);

        $revision = RevisionSchedule::where('id', $request->revision_id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $revision->update([
            'completed' => true,
            'completed_at' => now(),
            'score' => $request->score,
        ]);

        // Schedule next revision (spaced repetition)
        $lastInterval = max(1, $revision->scheduled_date->diffInDays($revision->created_at));
        $nextInterval = min(30, $lastInterval * 2);

        RevisionSchedule::create([
            'user_id' => $request->user()->id,
            'surah_number' => $revision->surah_number,
            'start_ayah' => $revision->start_ayah,
            'end_ayah' => $revision->end_ayah,
            'scheduled_date' => now()->addDays($nextInterval),
        ]);

        return response()->json([
            'success' => true,
            'next_revision_date' => now()->addDays($nextInterval)->format('Y-m-d'),
        ]);
    }

    /**
     * Get progress chart data (AJAX).
     */
    public function progressData(Request $request)
    {
        $user = $request->user();

        // Memorization by surah
        $bySurah = MemorizationProgress::where('user_id', $user->id)
            ->selectRaw('surah_number, status, count(*) as count')
            ->groupBy('surah_number', 'status')
            ->get()
            ->groupBy('surah_number');

        // Weekly practice trend
        $weeklyPractice = MemorizationProgress::where('user_id', $user->id)
            ->whereNotNull('last_practiced_at')
            ->where('last_practiced_at', '>=', now()->subWeeks(4))
            ->selectRaw('DATE(last_practiced_at) as date, count(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'by_surah' => $bySurah,
            'weekly_practice' => $weeklyPractice,
        ]);
    }
}
