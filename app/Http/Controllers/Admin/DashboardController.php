<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PracticeSession;
use App\Models\PracticeAttempt;
use App\Models\MemorizationProgress;
use App\Models\AdminActivityLog;
use App\Models\ApiUsageLog;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display admin overview dashboard.
     */
    public function index()
    {
        $totalUsers = User::where('role', 'user')->count();
        $totalAdmins = User::where('role', 'admin')->count();
        $activeUsers = User::where('status', 'active')->count();
        $suspendedUsers = User::where('status', 'suspended')->count();

        $totalSessions = PracticeSession::count();
        $totalAttempts = PracticeAttempt::count();
        $avgScore = round(PracticeAttempt::avg('confidence_score') ?? 0, 1);

        $totalMemorized = MemorizationProgress::where('status', 'memorized')->count();
        $totalPracticing = MemorizationProgress::where('status', 'practicing')->count();

        // Recent users
        $recentUsers = User::latest()->take(5)->get();

        // Recent practice attempts
        $recentAttempts = PracticeAttempt::with(['session', 'user'])
            ->latest()
            ->take(6)
            ->get();

        // Recent activity logs
        $activityLogs = AdminActivityLog::with('user')
            ->latest()
            ->take(8)
            ->get();

        // Daily sessions last 7 days
        $dailySessions = PracticeSession::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalAdmins',
            'activeUsers',
            'suspendedUsers',
            'totalSessions',
            'totalAttempts',
            'avgScore',
            'totalMemorized',
            'totalPracticing',
            'recentUsers',
            'recentAttempts',
            'activityLogs',
            'dailySessions'
        ));
    }
}
