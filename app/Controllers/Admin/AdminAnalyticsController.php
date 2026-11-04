<?php

require_once __DIR__ . '/../Controller.php';
require_once __DIR__ . '/../../Services/Database.php';
require_once __DIR__ . '/../../Services/Auth.php';

class AdminAnalyticsController extends Controller
{
    public function index(): void
    {
        Auth::requireAdmin();

        $eventCounts = Database::fetchAll(
            "SELECT event_type, COUNT(*) as count 
             FROM analytics_events 
             GROUP BY event_type 
             ORDER BY count DESC"
        );

        $recentEvents = Database::fetchAll(
            "SELECT a.*, u.name as user_name 
             FROM analytics_events a 
             LEFT JOIN users u ON a.user_id = u.id 
             ORDER BY a.created_at DESC LIMIT 20"
        );

        $dailyVisits = Database::fetchAll(
            "SELECT DATE(created_at) as date, COUNT(*) as total 
             FROM analytics_events 
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) 
             GROUP BY DATE(created_at) 
             ORDER BY date ASC"
        );

        $this->view('admin/analytics/index', [
            'title' => 'Website Analytics & Metrics — Bayan AI Admin',
            'eventCounts' => $eventCounts,
            'recentEvents' => $recentEvents,
            'dailyVisits' => $dailyVisits
        ]);
    }
}
