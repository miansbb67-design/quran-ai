<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\PracticeSession;
use App\Models\PracticeAttempt;
use App\Models\MemorizationProgress;
use App\Models\ReadingProgress;
use App\Models\Bookmark;
use App\Models\LearningGoal;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    /**
     * Show the progress page with charts.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $readingProgress = ReadingProgress::firstOrCreate(
            ['user_id' => $user->id],
            ['last_surah_number' => 1, 'last_ayah_number' => 1]
        );

        $totalSessions = PracticeSession::where('user_id', $user->id)->count();
        $totalAttempts = PracticeAttempt::where('user_id', $user->id)->count();
        $avgScore = PracticeAttempt::where('user_id', $user->id)->avg('confidence_score') ?? 0;

        $memStats = [
            'memorized' => MemorizationProgress::where('user_id', $user->id)->where('status', 'memorized')->count(),
            'practicing' => MemorizationProgress::where('user_id', $user->id)->where('status', 'practicing')->count(),
            'learning' => MemorizationProgress::where('user_id', $user->id)->where('status', 'learning')->count(),
        ];

        $bookmarksCount = Bookmark::where('user_id', $user->id)->count();

        return view('customer.progress.index', compact(
            'readingProgress',
            'totalSessions',
            'totalAttempts',
            'avgScore',
            'memStats',
            'bookmarksCount'
        ));
    }

    /**
     * Get chart data for progress charts (AJAX).
     */
    public function chartData(Request $request)
    {
        $user = $request->user();

        // Practice sessions per day (last 30 days)
        $dailySessions = PracticeSession::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, AVG(overall_score) as avg_score, SUM(duration_seconds) as total_duration')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Memorization progress by status
        $memProgress = MemorizationProgress::where('user_id', $user->id)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Weekly learning goals completion
        $weeklyGoals = LearningGoal::where('user_id', $user->id)
            ->where('goal_date', '>=', now()->subWeeks(4))
            ->selectRaw('WEEK(goal_date) as week, SUM(CASE WHEN completed = 1 THEN 1 ELSE 0 END) as completed, COUNT(*) as total')
            ->groupBy('week')
            ->get();

        return response()->json([
            'daily_sessions' => $dailySessions,
            'memorization_progress' => $memProgress,
            'weekly_goals' => $weeklyGoals,
        ]);
    }

    /**
     * Delete user learning history.
     */
    public function deleteHistory(Request $request)
    {
        $request->validate([
            'type' => 'required|in:all,sessions,memorization,bookmarks,progress',
        ]);

        $user = $request->user();

        switch ($request->type) {
            case 'sessions':
                PracticeSession::where('user_id', $user->id)->delete();
                break;
            case 'memorization':
                MemorizationProgress::where('user_id', $user->id)->delete();
                break;
            case 'bookmarks':
                Bookmark::where('user_id', $user->id)->delete();
                break;
            case 'progress':
                ReadingProgress::where('user_id', $user->id)->delete();
                break;
            case 'all':
                PracticeSession::where('user_id', $user->id)->delete();
                MemorizationProgress::where('user_id', $user->id)->delete();
                Bookmark::where('user_id', $user->id)->delete();
                ReadingProgress::where('user_id', $user->id)->delete();
                LearningGoal::where('user_id', $user->id)->delete();
                break;
        }

        return response()->json(['success' => true, 'message' => 'Data deleted successfully.']);
    }
}
