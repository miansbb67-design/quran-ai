<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Models\AdminActivityLog;
use App\Models\ApiUsageLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
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
     * Update platform settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:100'],
            'site_tagline' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'default_reciter' => ['required', 'string'],
            'default_translation' => ['required', 'string'],
            'ai_provider' => ['required', 'string', 'in:heuristic,openai,azure,gemini'],
            'ai_model' => ['nullable', 'string', 'max:100'],
            'ai_api_key' => ['nullable', 'string'],
            'ai_confidence_threshold' => ['required', 'numeric', 'min:0', 'max:100'],
            'enable_user_registration' => ['nullable', 'boolean'],
            'enable_voice_recognition' => ['nullable', 'boolean'],
            'maintenance_mode' => ['nullable', 'boolean'],
        ]);

        $booleanFields = ['enable_user_registration', 'enable_voice_recognition', 'maintenance_mode'];
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
            ['action' => 'Updated platform settings and AI configuration']
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
