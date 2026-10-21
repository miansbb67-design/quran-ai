<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Models\AdminActivityLog;
use App\Models\ApiUsageLog;
use App\Services\Quran\QuranApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    protected QuranApiService $quranApi;

    public function __construct(QuranApiService $quranApi)
    {
        $this->quranApi = $quranApi;
    }

    /**
     * Show site settings, AI configuration, and system logs.
     */
    public function index()
    {
        $settings = SiteSetting::all()->pluck('value', 'key')->toArray();

        $apiLogs = ApiUsageLog::latest()->take(30)->get();
        $activityLogs = AdminActivityLog::with('user')->latest()->take(30)->get();

        $apiStats = [
            'total_calls' => ApiUsageLog::count(),
            'avg_response_time' => round(ApiUsageLog::avg('response_time_ms') ?? 0, 1),
            'success_rate' => ApiUsageLog::count() > 0 
                ? round((ApiUsageLog::where('response_code', 200)->count() / ApiUsageLog::count()) * 100, 1) 
                : 100,
        ];

        return view('admin.settings.index', compact('settings', 'apiLogs', 'activityLogs', 'apiStats'));
    }

    /**
     * Preview sample audio for a selected reciter (AJAX).
     */
    public function previewReciter(Request $request)
    {
        $reciter = $request->query('reciter', 'ar.alafasy');
        $audio = $this->quranApi->getAyahAudio(1, 1, $reciter);

        if (!$audio || empty($audio['audio'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sample audio could not be loaded for reciter: ' . $reciter,
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'reciter' => $reciter,
            'reciter_name' => $audio['edition']['englishName'] ?? $reciter,
            'audio_url' => $audio['audio'],
        ]);
    }

    /**
     * Update platform settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:100'],
            'site_tagline' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'default_reciter' => ['required', 'string', 'max:100'],
            'custom_reciter' => ['nullable', 'string', 'max:100'],
            'audio_quality' => ['required', 'string', 'in:64,128,192'],
            'audio_cdn_source' => ['required', 'string', 'in:islamic_network,every_ayah,quran_cdn'],
            'allow_student_reciter_change' => ['nullable', 'boolean'],
            'teacher_voice_speed' => ['required', 'numeric', 'min:0.5', 'max:1.5'],
            'teacher_voice_pitch' => ['required', 'numeric', 'min:0.5', 'max:1.5'],
            'teacher_default_language' => ['required', 'string', 'in:en,ur,ar'],
            'enable_teacher_auto_speech' => ['nullable', 'boolean'],
            'default_translation' => ['required', 'string'],
            'ai_provider' => ['required', 'string', 'in:heuristic,openai,azure,gemini'],
            'ai_model' => ['nullable', 'string', 'max:100'],
            'ai_api_key' => ['nullable', 'string'],
            'ai_confidence_threshold' => ['required', 'numeric', 'min:0', 'max:100'],
            'enable_user_registration' => ['nullable', 'boolean'],
            'enable_voice_recognition' => ['nullable', 'boolean'],
            'maintenance_mode' => ['nullable', 'boolean'],
        ]);

        if (!empty($validated['custom_reciter'])) {
            $validated['default_reciter'] = trim($validated['custom_reciter']);
        }

        $booleanFields = [
            'enable_user_registration',
            'enable_voice_recognition',
            'maintenance_mode',
            'enable_teacher_auto_speech',
            'allow_student_reciter_change',
        ];
        foreach ($booleanFields as $field) {
            $validated[$field] = $request->has($field) ? '1' : '0';
        }

        foreach ($validated as $key => $value) {
            if ($value !== null) {
                // If it's ai_api_key and empty, don't overwrite existing
                if ($key === 'ai_api_key' && empty($value)) {
                    continue;
                }
                SiteSetting::set($key, $value);
            }
        }

        AdminActivityLog::logActivity(
            $request->user()->id,
            'update_settings',
            'SiteSetting',
            null,
            ['action' => 'Updated platform settings, audio reciter and voice systems']
        );

        return back()->with('success', 'Settings saved successfully.');
    }

    /**
     * Clear application cache.
     */
    public function clearCache(Request $request)
    {
        Cache::flush();
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        Artisan::call('config:clear');

        AdminActivityLog::logActivity(
            $request->user()->id,
            'clear_cache',
            'System',
            null,
            ['action' => 'Flushed application cache and views']
        );

        return back()->with('success', 'System cache cleared successfully.');
    }
}
