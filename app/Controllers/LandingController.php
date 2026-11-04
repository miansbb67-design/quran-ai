<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Services/Database.php';

class LandingController extends Controller
{
    public function index(): void
    {
        // Fetch featured Qaris
        $featuredQaris = Database::fetchAll("SELECT * FROM qari_profiles WHERE is_active = 1 ORDER BY is_featured DESC, id ASC LIMIT 6");

        // Fetch popular Surahs for quick recitation preview
        $popularSurahs = Database::fetchAll("SELECT * FROM surahs WHERE id IN (1, 18, 36, 55, 67, 112, 113, 114) ORDER BY id ASC");

        // Fetch SEO metadata for homepage
        $seo = Database::fetch("SELECT * FROM seo_metadata WHERE route_path = '/'") ?: [
            'meta_title' => 'Bayan AI Quran Teacher — Learn, Recite & Memorize Holy Quran',
            'meta_description' => 'Master Quran recitation, Tajweed, and Hifz with authentic Qari voices and real-time AI pronunciation feedback.',
            'meta_keywords' => 'Quran recitation, AI Quran Teacher, Tajweed practice, Hifz revision',
            'og_image' => '/assets/images/og-bayan.jpg'
        ];

        // Track landing page visit event
        try {
            Database::insert('analytics_events', [
                'event_type' => 'landing_visit',
                'page_url' => '/',
                'referrer' => $_SERVER['HTTP_REFERER'] ?? null,
                'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
                'ip_hash' => hash('sha256', $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1')
            ]);
        } catch (\Exception $e) {
            // Non-blocking analytics
        }

        $this->view('landing/index', [
            'seo' => $seo,
            'featuredQaris' => $featuredQaris,
            'popularSurahs' => $popularSurahs
        ]);
    }
}
