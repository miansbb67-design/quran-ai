<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ReadingProgress;
use App\Models\LearningGoal;
use App\Models\PracticeSession;
use App\Models\MemorizationProgress;
use App\Models\Bookmark;
use App\Models\Announcement;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Get or create reading progress
        $readingProgress = ReadingProgress::firstOrCreate(
            ['user_id' => $user->id],
            ['last_surah_number' => 1, 'last_ayah_number' => 1]
        );

        // Today's learning goal
        $todayGoal = LearningGoal::where('user_id', $user->id)
            ->where('goal_date', today())
            ->first();

        // Recent practice sessions
        $recentSessions = PracticeSession::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        // Memorization stats
        $memStats = [
            'total' => MemorizationProgress::where('user_id', $user->id)->count(),
            'memorized' => MemorizationProgress::where('user_id', $user->id)->where('status', 'memorized')->count(),
            'practicing' => MemorizationProgress::where('user_id', $user->id)->where('status', 'practicing')->count(),
            'learning' => MemorizationProgress::where('user_id', $user->id)->where('status', 'learning')->count(),
        ];

        // Bookmarks count
        $bookmarksCount = Bookmark::where('user_id', $user->id)->count();

        // Active announcements
        $announcements = Announcement::active()->latest()->take(3)->get();

        // Total learning time (in minutes)
        $totalLearningMinutes = intval(PracticeSession::where('user_id', $user->id)
            ->sum('duration_seconds') / 60);

        return view('customer.dashboard', compact(
            'readingProgress',
            'todayGoal',
            'recentSessions',
            'memStats',
            'bookmarksCount',
            'announcements',
            'totalLearningMinutes'
        ));
    }
}
