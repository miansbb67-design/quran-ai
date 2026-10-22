<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserPreference;
use App\Models\SiteSetting;
use App\Models\SitePage;
use App\Models\Faq;
use App\Models\Announcement;
use App\Models\ReadingProgress;
use App\Models\MemorizationProgress;
use App\Models\PracticeSession;
use App\Models\PracticeAttempt;
use App\Models\Bookmark;
use App\Models\LearningGoal;
use App\Models\RevisionSchedule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Platform Users (Admin and Student)

        $admin = User::updateOrCreate(
            ['email' => 'admin@quran-ai.com'],
            [
                'name' => 'Lead Teacher Admin',
                'password' => Hash::make('Password123!'),
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        $student = User::updateOrCreate(
            ['email' => 'student@quran-ai.com'],
            [
                'name' => 'Demo Student',
                'password' => Hash::make('Password123!'),
                'role' => 'user',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // User Preferences for student
        UserPreference::updateOrCreate(
            ['user_id' => $student->id],
            [
                'preferred_reciter' => 'ar.alafasy',
                'preferred_translation' => 'en.asad',
                'show_transliteration' => true,
                'show_translation' => true,
                'daily_goal_minutes' => 20,
                'daily_ayah_goal' => 10,
                'playback_speed' => 1.0,
                'auto_play_next' => true,
                'theme' => 'light',
            ]
        );

        // 2. Seed Default Site Settings
        $settings = [
            'site_name' => 'Bayan AI Quran Teacher',
            'site_tagline' => 'Learn, Recite, and Memorize the Holy Quran with Intelligent Voice Feedback',
            'contact_email' => 'support@quran-ai.com',
            'default_reciter' => 'ar.alafasy',
            'default_translation' => 'en.asad',
            'ai_provider' => 'heuristic',
            'ai_model' => 'quran-phonetic-v1',
            'ai_confidence_threshold' => '75',
            'enable_user_registration' => '1',
            'enable_voice_recognition' => '1',
            'maintenance_mode' => '0',
        ];

        foreach ($settings as $key => $val) {
            SiteSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $val, 'type' => 'string', 'group' => 'general']
            );
        }

        // 3. Seed Static Site Pages
        $pages = [
            [
                'title' => 'About Bayan AI Quran Teacher',
                'slug' => 'about',
                'content' => '<h2>Empowering Quran Learners Globally</h2><p>Bayan AI Quran Teacher combines cutting-edge speech recognition technology with classical Islamic scholarship. Our mission is to make authentic Quranic reading, proper pronunciation (Tajweed), and disciplined memorization (Hifz) accessible to every Muslim and seeker of knowledge around the world.</p><h3>Key Pillars</h3><ul><li><strong>Interactive Quran Reading:</strong> Uthmani script typography accompanied by clear English translations and verse-by-verse recitations from world-renowned Qaris.</li><li><strong>AI Recitation Teacher:</strong> Immediate, discreet acoustic feedback highlighting correctly pronounced words and pointing out missed phrases.</li><li><strong>Spaced-Repetition Hifz:</strong> Proven cognitive scheduling algorithms that ensure you never forget what you have memorized.</li></ul>',
                'is_active' => true,
            ],
            [
                'title' => 'Privacy Policy & Voice Data Governance',
                'slug' => 'privacy-policy',
                'content' => '<h2>Privacy & Voice Data Governance</h2><p>At Bayan AI Quran Teacher, safeguarding your privacy and respecting the sanctity of Quranic recitation is central to our ethics.</p><h3>Voice Data Handling</h3><ul><li>Voice analysis is conducted in real-time. Browser speech recognition processes audio through secure client-side APIs.</li><li>Raw voice recordings are never stored permanently or sold to third-party advertising companies.</li><li>You maintain absolute sovereignty over your learning history and may permanently purge your study records from your account settings at any moment.</li></ul>',
                'is_active' => true,
            ],
            [
                'title' => 'Terms of Service',
                'slug' => 'terms-of-service',
                'content' => '<h2>Terms of Service</h2><p>Welcome to Bayan AI Quran Teacher. By accessing or utilizing our learning portal, you agree to abide by these Terms of Service.</p><h3>Respectful Usage</h3><p>Users must use the platform respectfully for educational, spiritual, and personal development purposes. The automated voice assessment system provides assistive guidance and is designed to supplement, rather than entirely supplant, traditional one-on-one study with qualified human Quran teachers (Ijaza holders).</p>',
                'is_active' => true,
            ],
        ];

        foreach ($pages as $p) {
            SitePage::updateOrCreate(['slug' => $p['slug']], $p);
        }

        // 4. Seed FAQs
        $faqs = [
            [
                'question' => 'How does the AI Quran voice feedback work?',
                'answer' => 'The system leverages modern Web Speech recognition combined with an Arabic phonetic normalization algorithm. As you recite into your microphone, the AI matches your spoken Arabic words against the Uthmani script of the selected Ayah, color-coding correctly matched words in green and pointing out areas needing repetition in amber or red.',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'question' => 'Does this platform replace human Quran teachers?',
                'answer' => 'Our platform is designed as an intelligent home practice companion. While our AI gives rapid real-time feedback on pronunciation accuracy and verse sequencing, we enthusiastically advocate continued study with certified Quran scholars (Ijaza holders) for nuanced Tajweed rules.',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'question' => 'Which audio reciters are available on the platform?',
                'answer' => 'We provide crystal-clear verse-by-verse recitations from celebrated Qaris including Sheikh Mishary Rashid Alafasy, Sheikh Abdul Basit Abdul Samad, Sheikh Mahmoud Khalil Al-Husary, and Sheikh Abu Bakr Al-Shatri.',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'question' => 'How does the Hifz (Memorization) module track my retention?',
                'answer' => 'The Hifz module employs a spaced-repetition algorithm. When you mark an Ayah or Surah as memorized, the system automatically schedules review dates at increasing intervals (1 day, 3 days, 7 days, 14 days, 30 days) to lock the verses securely into your long-term memory.',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'question' => 'Can I practice on mobile phones and tablets?',
                'answer' => 'Yes! The entire interface is built on responsive Bootstrap 5.3 standards and works seamlessly on Chrome, Safari, Edge, and Firefox across iPhones, Android phones, iPads, and desktop computers.',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'question' => 'Are my voice recordings saved or shared?',
                'answer' => 'No. Audio analysis is executed transiently in memory for real-time comparison. We do not store raw microphone audio files on our servers, ensuring complete privacy for you and your family.',
                'sort_order' => 6,
                'is_active' => true,
            ],
        ];

        foreach ($faqs as $f) {
            Faq::updateOrCreate(['question' => $f['question']], $f);
        }

        // 5. Seed Announcements
        Announcement::updateOrCreate(
            ['title' => 'Welcome to Bayan AI Quran Teacher v1.0!'],
            [
                'content' => 'Explore our brand new AI Recitation Coach, Hifz Spaced Repetition engine, and interactive Quran Reader. Begin by practicing Surah Al-Fatihah!',
                'type' => 'info',
                'is_active' => true,
                'starts_at' => now()->subDay(),
                'ends_at' => now()->addMonths(3),
            ]
        );

        // 6. Seed Student Progress Data
        ReadingProgress::updateOrCreate(
            ['user_id' => $student->id],
            [
                'last_surah_number' => 1,
                'last_ayah_number' => 7,
                'total_ayahs_read' => 28,
                'current_streak_days' => 4,
                'longest_streak_days' => 7,
                'last_read_date' => today(),
                'total_reading_time_seconds' => 3600,
            ]
        );

        // Bookmarks
        Bookmark::updateOrCreate(
            ['user_id' => $student->id, 'surah_number' => 1, 'ayah_number' => 1],
            ['note' => 'Opening verse of the Holy Quran']
        );
        Bookmark::updateOrCreate(
            ['user_id' => $student->id, 'surah_number' => 2, 'ayah_number' => 255],
            ['note' => 'Ayat al-Kursi (The Throne Verse)']
        );

        // Memorization progress for Surah Al-Fatihah
        for ($ayah = 1; $ayah <= 7; $ayah++) {
            MemorizationProgress::updateOrCreate(
                ['user_id' => $student->id, 'surah_number' => 1, 'ayah_number' => $ayah],
                [
                    'status' => 'memorized',
                    'total_practice_count' => 12,
                    'consecutive_correct' => 5,
                    'confidence_level' => 95.0,
                    'last_practiced_at' => now()->subHours(2),
                    'next_review_at' => now()->addDays(7),
                    'review_interval_days' => 7,
                ]
            );
        }

        // Learning Goal
        LearningGoal::updateOrCreate(
            ['user_id' => $student->id, 'goal_date' => today(), 'goal_type' => 'daily_reading'],
            [
                'target_value' => 10,
                'target_unit' => 'ayahs',
                'current_value' => 7,
                'completed' => false,
            ]
        );

        // Practice Session
        $session = PracticeSession::create([
            'user_id' => $student->id,
            'session_type' => 'recitation',
            'surah_number' => 1,
            'start_ayah' => 1,
            'end_ayah' => 7,
            'duration_seconds' => 420,
            'overall_score' => 92.5,
        ]);

        PracticeAttempt::create([
            'practice_session_id' => $session->id,
            'user_id' => $student->id,
            'surah_number' => 1,
            'ayah_number' => 1,
            'attempt_number' => 1,
            'recognized_text' => 'بسم الله الرحمن الرحيم',
            'confidence_score' => 98.0,
            'result' => 'correct',
            'word_analysis' => [
                ['word' => 'بِسْمِ', 'matched' => true, 'status' => 'matched'],
                ['word' => 'اللَّهِ', 'matched' => true, 'status' => 'matched'],
                ['word' => 'الرَّحْمَٰنِ', 'matched' => true, 'status' => 'matched'],
                ['word' => 'الرَّحِيمِ', 'matched' => true, 'status' => 'matched'],
            ],
            'feedback_message' => 'Excellent recitation! All words pronounced with high accuracy.',
        ]);

        // 10. Seed Authentic Daily Hadiths
        $this->call(HadithSeeder::class);
    }
}
