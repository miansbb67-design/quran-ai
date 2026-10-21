<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\PracticeSession;
use App\Services\AI\QuranRecitationAnalysisService;
use App\Services\AI\RecitationAnalysisInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuranAiPlatformTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the public landing page loads successfully.
     */
    public function test_landing_page_loads_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Bayan');
        $response->assertSee('AI Voice Feedback');
    }

    /**
     * Test that guest users cannot access student dashboard.
     */
    public function test_guests_cannot_access_dashboard(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    /**
     * Test that regular users cannot access the admin panel.
     */
    public function test_regular_user_cannot_access_admin_panel(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->get('/admin');
        $response->assertStatus(403);
    }

    /**
     * Test that student dashboard displays Islamic calendar date box.
     */
    public function test_student_dashboard_displays_islamic_calendar_box(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Islamic Calendar');
        $response->assertSee('التقويم الهجري');
        $response->assertViewHas('hijriDate');
    }

    /**
     * Test that an admin user can access the admin panel.
     */
    public function test_admin_user_can_access_admin_panel(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Admin Dashboard');
    }

    /**
     * Test that the AI Recitation Analysis Service computes phonetic alignment.
     */
    public function test_ai_recitation_analysis_service_accuracy(): void
    {
        $service = new QuranRecitationAnalysisService();

        $target = "بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ";
        $recited = "بسم الله الرحمن الرحيم";

        $analysis = $service->analyzeRecitation($recited, $target, ['surah' => 1, 'ayah' => 1]);

        $this->assertArrayHasKey('confidence_score', $analysis);
        $this->assertArrayHasKey('overall_result', $analysis);
        $this->assertArrayHasKey('word_analysis', $analysis);
        $this->assertGreaterThanOrEqual(90, $analysis['confidence_score']);
        $this->assertEquals('correct', $analysis['overall_result']);
    }

    /**
     * Test that an authenticated user can access the Quran index.
     */
    public function test_user_can_access_quran_index(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->get('/quran');
        $response->assertStatus(200);
    }

    /**
     * Test that an authenticated user can access Surah 2 (Al-Baqarah) without timeout.
     */
    public function test_user_can_access_surah_baqarah(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->get('/quran/2');
        $response->assertStatus(200);
        $response->assertSee('Al-Baqara');
    }

    /**
     * Test that an authenticated user can toggle verse bookmarks.
     */
    public function test_user_can_toggle_bookmark(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->postJson('/quran/bookmark', [
            'surah_number' => 1,
            'ayah_number' => 1,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['bookmarked' => true]);
    }

    /**
     * Test that an authenticated student can access the AI teacher studio.
     */
    public function test_student_can_access_ai_teacher_studio(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->get('/ai-teacher');
        $response->assertStatus(200);
        $response->assertSee('Recitation Analysis Studio');
    }

    /**
     * Test that an admin can access the settings and telemetry page.
     */
    public function test_admin_can_access_settings_page(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->get('/admin/settings');
        $response->assertStatus(200);
        $response->assertSee('Platform Settings');
    }

    /**
     * Test that recitation analysis generates Tajweed rules and multi-language spoken explanations.
     */
    public function test_ai_recitation_analysis_service_tajweed_and_voice_explanations(): void
    {
        $service = app(RecitationAnalysisInterface::class);
        $analysis = $service->analyzeRecitation(
            'بسم الله الرحيم',
            'بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ'
        );

        $this->assertNotEmpty($analysis['word_analysis']);
        $firstWord = $analysis['word_analysis'][0];
        $this->assertArrayHasKey('makhraj', $firstWord);
        $this->assertArrayHasKey('tajweed_rule', $firstWord);
        $this->assertArrayHasKey('spoken_explanation_en', $firstWord);
        $this->assertArrayHasKey('spoken_explanation_ur', $firstWord);
        $this->assertArrayHasKey('spoken_explanation_ar', $firstWord);
    }

    /**
     * Test that user can practice an individual word via AJAX.
     */
    public function test_user_can_practice_single_word(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->postJson('/ai-teacher/analyze-word', [
            'target_word' => 'الرَّحْمَٰنِ',
            'recognized_word' => 'الرحمن',
            'surah_number' => 1,
            'ayah_number' => 1,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'matched' => true,
            'status' => 'correct',
        ]);
        $response->assertJsonStructure([
            'target_word',
            'recognized_word',
            'status',
            'matched',
            'similarity',
            'makhraj',
            'tajweed_rule',
            'teacher_tip',
            'spoken_explanation_en',
        ]);
    }

    /**
     * Test that user can delete their practice session history for privacy.
     */
    public function test_user_can_delete_session_history(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'status' => 'active',
        ]);

        $session = \App\Models\PracticeSession::create([
            'user_id' => $user->id,
            'session_type' => 'recitation',
            'surah_number' => 1,
            'start_ayah' => 1,
            'end_ayah' => 1,
        ]);

        $response = $this->actingAs($user)->postJson('/ai-teacher/delete-session-history', [
            'session_id' => $session->id,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('practice_sessions', ['id' => $session->id]);
    }

    /**
     * Test that Ayah 1 of Surahs 2-114 cleanly strips Bismillah and BOM, and correctly evaluates recitation.
     */
    public function test_surah_ayah_bismillah_and_bom_cleaning(): void
    {
        $quranApi = app(\App\Services\Quran\QuranApiService::class);
        $aiService = app(\App\Services\AI\RecitationAnalysisInterface::class);

        // Test 1: Surah 1:1 should not have BOM
        $cleaned1 = $quranApi->cleanAyahArabicText(1, 1, "\xEF\xBB\xBFبِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ");
        $this->assertEquals("بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ", $cleaned1);

        // Reciting 1:1 should score 100%
        $analysis1 = $aiService->analyzeRecitation("بسم الله الرحمن الرحيم", $cleaned1, ['surah' => 1, 'ayah' => 1]);
        $this->assertEquals(100.0, $analysis1['confidence_score']);

        // Test 2: Surah 2:1 prepended Bismillah from raw API should be stripped to only the verse 'الٓمٓ'
        $cleaned2 = $quranApi->cleanAyahArabicText(2, 1, "بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ الٓمٓ");
        $this->assertEquals("الٓمٓ", $cleaned2);

        // Reciting 'الم' for Surah 2:1 should match 100%
        $analysis2 = $aiService->analyzeRecitation("الم", $cleaned2, ['surah' => 2, 'ayah' => 1]);
        $this->assertEquals(100.0, $analysis2['confidence_score']);

        // Reciting with introductory Bismillah + 'الم' should also match 100%
        $analysis2WithBismillah = $aiService->analyzeRecitation("بسم الله الرحمن الرحيم الم", $cleaned2, ['surah' => 2, 'ayah' => 1]);
        $this->assertEquals(100.0, $analysis2WithBismillah['confidence_score']);
    }

    /**
     * Test that admin can preview reciter audio and save comprehensive voice settings.
     */
    public function test_admin_can_preview_reciter_voice_and_update_voice_settings(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        // Preview reciter voice endpoint
        $previewRes = $this->actingAs($admin)->getJson('/admin/settings/preview-reciter?reciter=ar.alafasy');
        $previewRes->assertStatus(200);
        $previewRes->assertJsonFragment(['status' => 'success', 'reciter' => 'ar.alafasy']);
        $this->assertNotNull($previewRes->json('audio_url'));

        // Save expanded voice system settings
        $updateRes = $this->actingAs($admin)->post('/admin/settings', [
            'site_name' => 'Bayan AI Recitation Studio',
            'default_reciter' => 'ar.husary',
            'audio_quality' => '128',
            'audio_cdn_source' => 'islamic_network',
            'allow_student_reciter_change' => '1',
            'teacher_voice_speed' => '0.95',
            'teacher_voice_pitch' => '1.0',
            'teacher_default_language' => 'ur',
            'enable_teacher_auto_speech' => '1',
            'default_translation' => 'en.asad',
            'ai_provider' => 'heuristic',
            'ai_confidence_threshold' => 75,
        ]);

        $updateRes->assertRedirect();
        $this->assertEquals('ar.husary', \App\Models\SiteSetting::get('default_reciter'));
        $this->assertEquals('ur', \App\Models\SiteSetting::get('teacher_default_language'));
        $this->assertEquals('128', \App\Models\SiteSetting::get('audio_quality'));
    }

    /**
     * Test that user reading/listening progress accurately records stopped Ayah and provides it to the view.
     */
    public function test_user_progress_remembers_stopped_ayah_and_resumes(): void
    {
        $user = User::factory()->create();

        // 1. Send progress update for Surah 2, Ayah 3 (where playback was stopped)
        $response = $this->actingAs($user)->postJson('/quran/progress', [
            'surah_number' => 2,
            'ayah_number' => 3,
        ]);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify database persistence
        $this->assertDatabaseHas('reading_progress', [
            'user_id' => $user->id,
            'last_surah_number' => 2,
            'last_ayah_number' => 3,
        ]);

        // 2. Access Surah 2 reader page and verify savedAyahNumber (3) is provided to view
        $viewResponse = $this->actingAs($user)->get('/quran/2');
        $viewResponse->assertStatus(200);
        $viewResponse->assertViewHas('savedAyahNumber', 3);
        $viewResponse->assertSee('Resume Surah (Ayah 3)');
    }

    /**
     * Test that student dashboard displays daily Hadith box with Arabic and translations.
     */
    public function test_student_dashboard_displays_daily_hadith(): void
    {
        $user = User::factory()->create();

        // Seed Hadiths if not already present
        (new \Database\Seeders\HadithSeeder())->run();

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Daily Hadith');
        $response->assertSee('حدیثِ نبوی ﷺ');
        $response->assertViewHas('dailyHadith');
    }

    /**
     * Test that user can access the dedicated Hadith collection page, filter, and search.
     */
    public function test_user_can_access_and_filter_hadiths(): void
    {
        $user = User::factory()->create();

        (new \Database\Seeders\HadithSeeder())->run();

        // Index view
        $response = $this->actingAs($user)->get('/hadith');
        $response->assertStatus(200);
        $response->assertSee('Daily Hadith &amp; Sunnah', false);
        $response->assertSee('Sahih al-Bukhari');

        // Filter by category
        $filterResponse = $this->actingAs($user)->get('/hadith?category=quran');
        $filterResponse->assertStatus(200);
        $filterResponse->assertSee('خَيْرُكُمْ مَنْ تَعَلَّمَ الْقُرْآنَ وَعَلَّمَهُ');

        // Search query
        $searchResponse = $this->actingAs($user)->get('/hadith?search=teach+it+to+others');
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('Sahih al-Bukhari 5027');
    }

    /**
     * Test that admin can access Google and Web Analytics dashboard.
     */
    public function test_admin_can_access_analytics_page(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->get('/admin/analytics');
        $response->assertStatus(200);
        $response->assertSee('Google Analytics 4');
        $response->assertSee('Daily Practice');
    }

    /**
     * Test that admin can update Google Analytics configuration.
     */
    public function test_admin_can_update_google_analytics_config(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->post('/admin/analytics/config', [
            'google_analytics_id' => 'G-TEST999999',
            'google_tag_manager_id' => 'GTM-TEST123',
            'enable_google_analytics' => '1',
        ]);

        $response->assertRedirect();
        $this->assertEquals('G-TEST999999', \App\Models\SiteSetting::getValue('google_analytics_id'));
        $this->assertEquals('GTM-TEST123', \App\Models\SiteSetting::getValue('google_tag_manager_id'));
        $this->assertTrue((bool) \App\Models\SiteSetting::getValue('enable_google_analytics'));
    }

    /**
     * Test that admin can manage Hadiths collection.
     */
    public function test_admin_can_manage_hadiths(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        // View hadiths manager
        $response = $this->actingAs($admin)->get('/admin/hadiths');
        $response->assertStatus(200);
        $response->assertSee('Hadith Collection Manager');

        // Create new hadith
        $createResponse = $this->actingAs($admin)->post('/admin/hadiths', [
            'book' => 'Sunan Abi Dawud',
            'hadith_number' => '1464',
            'chapter' => 'Recitation with pleasant voice',
            'narrator' => 'Al-Bara ibn Azib',
            'category' => 'Quran Virtues',
            'arabic' => 'زَيِّنُوا الْقُرْآنَ بِأَصْوَاتِكُمْ',
            'english' => 'Beautify the Quran with your voices.',
            'urdu' => 'اپنی آوازوں سے قرآن کو زینت بخشو۔',
        ]);

        $createResponse->assertRedirect();
        $this->assertDatabaseHas('hadiths', [
            'arabic_text' => 'زَيِّنُوا الْقُرْآنَ بِأَصْوَاتِكُمْ',
            'source_book' => 'Sunan Abi Dawud',
        ]);
    }
}

