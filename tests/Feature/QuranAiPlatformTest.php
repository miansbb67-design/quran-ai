<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\PracticeSession;
use App\Services\AI\QuranRecitationAnalysisService;
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
}
