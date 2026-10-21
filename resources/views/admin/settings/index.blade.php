@extends('layouts.admin')

@section('title', 'Platform Settings & AI Configuration - Bayan Admin')
@section('page_title', 'System Settings & AI Configuration')

@section('content')
<div class="container-fluid p-0">

    <div class="row g-4 mb-4">
        <!-- Settings Form Column -->
        <div class="col-lg-7">
            <div class="card card-glass border-0 p-4 shadow-sm mb-4">
                <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                    <div>
                        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-sliders text-emerald me-2"></i>Platform Settings</h5>
                        <small class="text-muted">Configure site branding, AI engine, and defaults</small>
                    </div>

                    <form method="POST" action="{{ route('admin.settings.clear-cache') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-warning text-dark rounded-pill px-3 shadow-sm" title="Clear application and route caches">
                            <i class="bi bi-arrow-repeat me-1"></i> Flush Cache
                        </button>
                    </form>
                </div>

                <form method="POST" action="{{ route('admin.settings.update') }}">
                    @csrf

                    <!-- General Platform Details -->
                    <h6 class="fw-bold text-emerald mb-3 text-uppercase small" style="letter-spacing: 1px;">Branding & Identity</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Platform Name</label>
                            <input type="text" name="site_name" class="form-control" value="{{ $settings['site_name'] ?? 'Bayan AI Quran Teacher' }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Support Email</label>
                            <input type="email" name="contact_email" class="form-control" value="{{ $settings['contact_email'] ?? 'support@quran-ai.com' }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Site Tagline</label>
                            <input type="text" name="site_tagline" class="form-control" value="{{ $settings['site_tagline'] ?? 'Learn, Recite, and Memorize the Holy Quran with Intelligent Voice Feedback' }}">
                        </div>
                    </div>

                    <!-- AI Recitation Engine Configuration -->
                    <h6 class="fw-bold text-emerald mb-3 text-uppercase small" style="letter-spacing: 1px;"><i class="bi bi-cpu me-1 text-gold"></i> AI Voice Provider Configuration</h6>
                    <div class="row g-3 mb-4 bg-light p-3 rounded-4 border">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">AI Engine Provider</label>
                            <select name="ai_provider" class="form-select">
                                <option value="heuristic" {{ ($settings['ai_provider'] ?? '') === 'heuristic' ? 'selected' : '' }}>Internal Heuristic & Phonetic Matcher (Zero Latency / Default)</option>
                                <option value="openai" {{ ($settings['ai_provider'] ?? '') === 'openai' ? 'selected' : '' }}>OpenAI Whisper API</option>
                                <option value="azure" {{ ($settings['ai_provider'] ?? '') === 'azure' ? 'selected' : '' }}>Azure Cognitive Speech Services</option>
                                <option value="gemini" {{ ($settings['ai_provider'] ?? '') === 'gemini' ? 'selected' : '' }}>Google Cloud Speech / Gemini</option>
                            </select>
                            <small class="text-muted d-block mt-1">Replaceable driver layer via <code>RecitationAnalysisInterface</code></small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">AI Confidence Match Threshold (%)</label>
                            <input type="number" name="ai_confidence_threshold" class="form-control" value="{{ $settings['ai_confidence_threshold'] ?? 75 }}" min="10" max="100" required>
                            <small class="text-muted d-block mt-1">Scores above this qualify as passing recitation</small>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold">Provider API Key (Optional)</label>
                            <input type="password" name="ai_api_key" class="form-control" placeholder="Leave blank to maintain current setting">
                        </div>
                    </div>

                    <!-- Quran Recitation Voices & Audio Systems -->
                    <h6 class="fw-bold text-emerald mb-3 text-uppercase small" style="letter-spacing: 1px;">
                        <i class="bi bi-soundwave text-gold me-1"></i> Recitation Voices & Audio Engine Studio (صوتی نظام و قراء کرام)
                    </h6>
                    <div class="p-3 bg-light rounded-4 border mb-4">
                        <div class="row g-3">
                            <!-- Reciter Selector -->
                            <div class="col-md-7">
                                <label class="form-label small fw-bold text-dark">
                                    <i class="bi bi-mic-fill text-emerald me-1"></i> Platform Default Reciter (تلاوت کی آواز)
                                </label>
                                <select name="default_reciter" id="selectDefaultReciter" class="form-select fw-semibold">
                                    @php $currentReciter = $settings['default_reciter'] ?? 'ar.alafasy'; @endphp
                                    <optgroup label="📖 Tajweed & Teaching Masters (تعلیم و ترتیل - سست رفتار)">
                                        <option value="ar.husary" {{ $currentReciter === 'ar.husary' ? 'selected' : '' }}>محمود خلیل الحصری (مرتل) - Mahmoud Khalil Al-Husary</option>
                                        <option value="ar.husarymujawwad" {{ $currentReciter === 'ar.husarymujawwad' ? 'selected' : '' }}>محمود خلیل الحصری (مجوَّد) - Al-Husary (Mujawwad)</option>
                                        <option value="ar.minshawi" {{ $currentReciter === 'ar.minshawi' ? 'selected' : '' }}>محمد صدیق المنشاوی (مرتل) - Muhammad Siddiq Al-Minshawi</option>
                                        <option value="ar.abdulsamad" {{ ($currentReciter === 'ar.abdulsamad' || $currentReciter === 'ar.abdulbasitmurattal') ? 'selected' : '' }}>عبد الباسط عبد الصمد (مرتل) - Abdul Basit Abdul Samad</option>
                                        <option value="ar.aymanswoaid" {{ $currentReciter === 'ar.aymanswoaid' ? 'selected' : '' }}>د. أيمن سويد (عالم التجويد) - Dr. Ayman Sowaid</option>
                                        <option value="ar.hudhaify" {{ $currentReciter === 'ar.hudhaify' ? 'selected' : '' }}>علي بن عبد الرحمن الحذيفي - Ali Al-Hudhaify</option>
                                        <option value="ar.ibrahimakhbar" {{ $currentReciter === 'ar.ibrahimakhbar' ? 'selected' : '' }}>إبراهيم الأخضر - Ibrahim Al-Akhdar</option>
                                        <option value="ar.parhizgar" {{ $currentReciter === 'ar.parhizgar' ? 'selected' : '' }}>شهريار پرهيزگار - Shahriar Parhizgar</option>
                                    </optgroup>
                                    <optgroup label="🕋 Imams of Harameen (أئمة الحرمين الشريفين)">
                                        <option value="ar.abdurrahmaansudais" {{ $currentReciter === 'ar.abdurrahmaansudais' ? 'selected' : '' }}>عبد الرحمن السديس (إمام الحرم) - Abdur-Rahman As-Sudais</option>
                                        <option value="ar.saoodshuraym" {{ $currentReciter === 'ar.saoodshuraym' ? 'selected' : '' }}>سعود الشريم - Saud Ash-Shuraim</option>
                                        <option value="ar.mahermuaiqly" {{ $currentReciter === 'ar.mahermuaiqly' ? 'selected' : '' }}>ماهر المعيقلي - Maher Al-Muaiqly</option>
                                        <option value="ar.abdullahbasfar" {{ $currentReciter === 'ar.abdullahbasfar' ? 'selected' : '' }}>عبد الله بصفر - Abdullah Basfar</option>
                                    </optgroup>
                                    <optgroup label="🌟 Famous Contemporary Qaris (مشہور و مقبول قراء)">
                                        <option value="ar.alafasy" {{ $currentReciter === 'ar.alafasy' ? 'selected' : '' }}>مشاري راشد العفاسي - Mishary Rashid Alafasy (Default)</option>
                                        <option value="ar.shaatree" {{ $currentReciter === 'ar.shaatree' ? 'selected' : '' }}>أبو بكر الشاطري - Abu Bakr Ash-Shaatree</option>
                                        <option value="ar.ahmedajamy" {{ $currentReciter === 'ar.ahmedajamy' ? 'selected' : '' }}>أحمد بن علي العجمي - Ahmed Al-Ajamy</option>
                                        <option value="ar.hanirifai" {{ $currentReciter === 'ar.hanirifai' ? 'selected' : '' }}>هاني الرفاعي - Hani Ar-Rifai</option>
                                        <option value="ar.muhammadayyoub" {{ $currentReciter === 'ar.muhammadayyoub' ? 'selected' : '' }}>محمد أيوب - Muhammad Ayyoub</option>
                                        <option value="ar.muhammadjibreel" {{ $currentReciter === 'ar.muhammadjibreel' ? 'selected' : '' }}>محمد جبريل - Muhammad Jibreel</option>
                                    </optgroup>
                                </select>
                                <small class="text-muted d-block mt-1">Select the Qari whose voice will be used for Ayah recitation, playback, and AI teacher comparison.</small>
                            </div>

                            <!-- Live Audio Test & Preview -->
                            <div class="col-md-5">
                                <label class="form-label small fw-bold text-dark">
                                    <i class="bi bi-headphones text-gold me-1"></i> Preview Voice (آواز سنیں)
                                </label>
                                <div>
                                    <button type="button" id="btnTestVoice" class="btn btn-gold btn-sm w-100 rounded-pill fw-bold shadow-sm py-2">
                                        <i class="bi bi-volume-up-fill me-1" id="previewVoiceIcon"></i> <span id="previewVoiceText">Test Sample Voice</span>
                                    </button>
                                    <audio id="reciterPreviewPlayer" preload="none"></audio>
                                </div>
                                <div id="previewStatusBox" class="small text-muted mt-1 fst-italic text-truncate" style="min-height: 20px;">
                                    Click above to listen to Bismillah by selected Qari.
                                </div>
                            </div>

                            <!-- Custom Reciter Identifier -->
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Custom Reciter Identifier (اختیاری کوڈ)</label>
                                <input type="text" name="custom_reciter" id="customReciterInput" class="form-control form-control-sm font-monospace" placeholder="e.g. ar.aymanswoaid" value="">
                                <small class="text-muted d-block mt-1">Leave blank to use the dropdown above, or enter any custom Quran API reciter edition code.</small>
                            </div>

                            <!-- Audio Quality / Bitrate -->
                            <div class="col-md-3 col-6">
                                <label class="form-label small fw-semibold">Audio Quality</label>
                                <select name="audio_quality" class="form-select form-select-sm">
                                    @php $q = $settings['audio_quality'] ?? '128'; @endphp
                                    <option value="128" {{ $q === '128' ? 'selected' : '' }}>128 kbps (Standard High Quality)</option>
                                    <option value="64" {{ $q === '64' ? 'selected' : '' }}>64 kbps (Low Bandwidth / Mobile)</option>
                                    <option value="192" {{ $q === '192' ? 'selected' : '' }}>192 kbps (Studio Master Quality)</option>
                                </select>
                            </div>

                            <!-- Audio Server / CDN Source -->
                            <div class="col-md-3 col-6">
                                <label class="form-label small fw-semibold">Audio Server Engine</label>
                                <select name="audio_cdn_source" class="form-select form-select-sm">
                                    @php $cdn = $settings['audio_cdn_source'] ?? 'islamic_network'; @endphp
                                    <option value="islamic_network" {{ $cdn === 'islamic_network' ? 'selected' : '' }}>Islamic Network CDN</option>
                                    <option value="quran_cdn" {{ $cdn === 'quran_cdn' ? 'selected' : '' }}>Quran.com Cloud CDN</option>
                                    <option value="every_ayah" {{ $cdn === 'every_ayah' ? 'selected' : '' }}>EveryAyah Server</option>
                                </select>
                            </div>

                            <!-- Student Reciter Policy Toggle -->
                            <div class="col-12 pt-1 border-top">
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="allow_student_reciter_change" value="1" id="allowReciterSwitch" {{ ($settings['allow_student_reciter_change'] ?? '1') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label small fw-semibold text-dark" for="allowReciterSwitch">
                                        Allow Students to choose their own Reciter in their Profile
                                    </label>
                                    <small class="text-muted d-block">If unchecked, all students are enforced to use the Admin Default Reciter chosen above.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- AI Spoken Teacher Guidance Voice Engine (معلم صوتی نظام) -->
                    <h6 class="fw-bold text-emerald mb-3 text-uppercase small" style="letter-spacing: 1px;">
                        <i class="bi bi-chat-quote-fill text-gold me-1"></i> AI Voice Teacher Spoken Feedback Engine (استاد کی آواز کی ترتیبات)
                    </h6>
                    <div class="p-3 bg-light rounded-4 border mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Default Spoken Language</label>
                                <select name="teacher_default_language" class="form-select form-select-sm fw-semibold">
                                    @php $tLang = $settings['teacher_default_language'] ?? 'en'; @endphp
                                    <option value="en" {{ $tLang === 'en' ? 'selected' : '' }}>English</option>
                                    <option value="ur" {{ $tLang === 'ur' ? 'selected' : '' }}>اردو (Urdu)</option>
                                    <option value="ar" {{ $tLang === 'ar' ? 'selected' : '' }}>العربية (Arabic)</option>
                                </select>
                                <small class="text-muted d-block mt-1">Default language for spoken voice explanations</small>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Teacher Speaking Rate (رفتار)</label>
                                <select name="teacher_voice_speed" class="form-select form-select-sm">
                                    @php $tSpeed = $settings['teacher_voice_speed'] ?? '0.95'; @endphp
                                    <option value="0.80" {{ $tSpeed == '0.80' ? 'selected' : '' }}>0.80x (Slow / Tajweed Clarity)</option>
                                    <option value="0.95" {{ $tSpeed == '0.95' ? 'selected' : '' }}>0.95x (Natural / Recommended)</option>
                                    <option value="1.05" {{ $tSpeed == '1.05' ? 'selected' : '' }}>1.05x (Brisk / Fast)</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Teacher Voice Pitch (سر)</label>
                                <select name="teacher_voice_pitch" class="form-select form-select-sm">
                                    @php $tPitch = $settings['teacher_voice_pitch'] ?? '1.0'; @endphp
                                    <option value="0.9" {{ $tPitch == '0.9' ? 'selected' : '' }}>0.9 (Calm / Deeper)</option>
                                    <option value="1.0" {{ $tPitch == '1.0' ? 'selected' : '' }}>1.0 (Natural Standard)</option>
                                    <option value="1.15" {{ $tPitch == '1.15' ? 'selected' : '' }}>1.15 (Clear / Higher)</option>
                                </select>
                            </div>

                            <div class="col-12 pt-1 border-top">
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="enable_teacher_auto_speech" value="1" id="autoSpeechSwitch" {{ ($settings['enable_teacher_auto_speech'] ?? '1') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label small fw-semibold text-dark" for="autoSpeechSwitch">
                                        Auto-read Pronunciation Mistakes Aloud by Default
                                    </label>
                                    <small class="text-muted d-block">When enabled, the voice teacher immediately explains any detected recitation mistakes out loud.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Translation Default -->
                    <h6 class="fw-bold text-emerald mb-3 text-uppercase small" style="letter-spacing: 1px;">Quran Translation Default</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label class="form-label small fw-semibold">Default English Translation Edition</label>
                            <select name="default_translation" class="form-select">
                                <option value="en.asad" {{ ($settings['default_translation'] ?? '') === 'en.asad' ? 'selected' : '' }}>Muhammad Asad</option>
                                <option value="en.sahih" {{ ($settings['default_translation'] ?? '') === 'en.sahih' ? 'selected' : '' }}>Saheeh International</option>
                                <option value="en.pickthall" {{ ($settings['default_translation'] ?? '') === 'en.pickthall' ? 'selected' : '' }}>Pickthall</option>
                                <option value="en.yusufali" {{ ($settings['default_translation'] ?? '') === 'en.yusufali' ? 'selected' : '' }}>Yusuf Ali</option>
                            </select>
                        </div>
                    </div>

                    <!-- Google Analytics & Traffic Intelligence -->
                    <h6 class="fw-bold text-emerald mb-3 text-uppercase small" style="letter-spacing: 1px;">
                        <i class="bi bi-google text-gold me-1"></i> Google Analytics (GA4) & Tracking Suite
                    </h6>
                    <div class="row g-3 mb-4 bg-light p-3 rounded-4 border">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">GA4 Measurement ID</label>
                            <input type="text" name="google_analytics_id" class="form-control" value="{{ $settings['google_analytics_id'] ?? '' }}" placeholder="G-XXXXXXXXXX">
                            <small class="text-muted d-block mt-1">Official Google Analytics 4 Measurement ID</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">GTM Container ID (Optional)</label>
                            <input type="text" name="google_tag_manager_id" class="form-control" value="{{ $settings['google_tag_manager_id'] ?? '' }}" placeholder="GTM-XXXXXXX">
                            <small class="text-muted d-block mt-1">Google Tag Manager Container</small>
                        </div>
                        <div class="col-12 pt-1 border-top">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" name="enable_google_analytics" value="1" id="gaSwitch" {{ ($settings['enable_google_analytics'] ?? '0') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label small fw-semibold text-dark" for="gaSwitch">
                                    Enable Real-time Google Analytics Tracking Script
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- SEO & Search Engine Optimization -->
                    <h6 class="fw-bold text-emerald mb-3 text-uppercase small" style="letter-spacing: 1px;">
                        <i class="bi bi-search text-emerald me-1"></i> SEO & Search Metadata
                    </h6>
                    <div class="row g-3 mb-4 bg-light p-3 rounded-4 border">
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Default Meta Title</label>
                            <input type="text" name="seo_meta_title" class="form-control" value="{{ $settings['seo_meta_title'] ?? 'Bayan AI Quran Teacher - Recite, Memorize, Learn Tajweed' }}" placeholder="Meta Title for search engines">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Default Meta Description</label>
                            <textarea name="seo_meta_description" class="form-control" rows="2" placeholder="Search snippet description">{{ $settings['seo_meta_description'] ?? 'AI-powered Quran recitation analysis, interactive tajweed error detection, authentic Hadith library, and Hifz memorization tracker.' }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Meta Keywords</label>
                            <input type="text" name="seo_meta_keywords" class="form-control" value="{{ $settings['seo_meta_keywords'] ?? 'quran ai, learn quran, tajweed voice, hadith, hifz tracker' }}" placeholder="Comma separated keywords">
                        </div>
                    </div>

                    <!-- Feature Toggles -->
                    <h6 class="fw-bold text-emerald mb-3 text-uppercase small" style="letter-spacing: 1px;">Feature Toggles</h6>
                    <div class="p-3 bg-light rounded-4 border mb-4 d-flex flex-column gap-2">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="enable_user_registration" value="1" id="regSwitch" {{ ($settings['enable_user_registration'] ?? '1') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label small" for="regSwitch">Allow New Student Self-Registrations</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="enable_voice_recognition" value="1" id="voiceSwitch" {{ ($settings['enable_voice_recognition'] ?? '1') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label small" for="voiceSwitch">Enable AI Voice Recognition Module</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="maintenance_mode" value="1" id="maintSwitch" {{ ($settings['maintenance_mode'] ?? '0') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label small text-danger fw-semibold" for="maintSwitch">Maintenance Mode (Site Offline for Students)</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-emerald rounded-pill px-5">
                        <i class="bi bi-save me-1"></i> Save Platform Settings
                    </button>
                </form>
            </div>
        </div>

        <!-- System Logs & Metrics Column -->
        <div class="col-lg-5">
            <!-- API Metrics Card -->
            <div class="card card-glass border-0 p-4 shadow-sm mb-4">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-cloud-check text-emerald me-2"></i>Quran API Cloud Telemetry</h6>
                <div class="row g-2 text-center mb-3">
                    <div class="col-4">
                        <div class="p-2 bg-light rounded-3 border">
                            <strong class="d-block text-dark fs-5">{{ $apiStats['total_calls'] }}</strong>
                            <small class="text-muted" style="font-size: 0.7rem;">API Calls</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 bg-light rounded-3 border">
                            <strong class="d-block text-dark fs-5">{{ $apiStats['avg_response_time'] }}ms</strong>
                            <small class="text-muted" style="font-size: 0.7rem;">Avg Latency</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 bg-light rounded-3 border">
                            <strong class="d-block text-success fs-5">{{ $apiStats['success_rate'] }}%</strong>
                            <small class="text-muted" style="font-size: 0.7rem;">Success Rate</small>
                        </div>
                    </div>
                </div>

                <div class="table-responsive" style="max-height: 250px;">
                    <table class="table table-sm table-hover mb-0" style="font-size: 0.75rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Endpoint</th>
                                <th>Status</th>
                                <th>Latency</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($apiLogs->take(8) as $log)
                                <tr>
                                    <td><code>{{ Str::limit($log->endpoint, 20) }}</code></td>
                                    <td><span class="badge {{ $log->response_code == 200 ? 'bg-success' : 'bg-danger' }}">{{ $log->response_code }}</span></td>
                                    <td>{{ $log->response_time_ms }}ms</td>
                                    <td class="text-muted">{{ $log->created_at->format('H:i:s') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-2">No API logs yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Admin Activity Audit Log -->
            <div class="card card-glass border-0 p-4 shadow-sm">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-shield-check text-gold me-2"></i>Admin Audit Trail</h6>
                <div class="list-group list-group-flush" style="max-height: 300px; overflow-y: auto;">
                    @forelse($activityLogs->take(10) as $act)
                        <div class="list-group-item px-0 py-2 border-bottom small">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge bg-light text-dark border">{{ $act->action }}</span>
                                <small class="text-muted">{{ $act->created_at->diffForHumans() }}</small>
                            </div>
                            <div class="text-secondary" style="font-size: 0.78rem;">
                                <strong>{{ $act->user->name ?? 'Admin' }}</strong>:
                                @if(is_array($act->details))
                                    {{ json_encode($act->details) }}
                                @else
                                    {{ $act->action }} on {{ $act->target_type }}
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted small py-3">No activity logs recorded yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    $(document).ready(function() {
        var player = document.getElementById('reciterPreviewPlayer');
        var isPlaying = false;

        function getActiveReciter() {
            var custom = $('#customReciterInput').val().trim();
            if (custom) return custom;
            return $('#selectDefaultReciter').val();
        }

        $('#btnTestVoice').on('click', function() {
            if (isPlaying && !player.paused) {
                player.pause();
                $('#previewVoiceIcon').removeClass('bi-pause-fill').addClass('bi-volume-up-fill');
                $('#previewVoiceText').text('Resume Voice');
                $('#previewStatusBox').html('<span class="text-secondary">Audio paused.</span>');
                isPlaying = false;
                return;
            }

            var reciter = getActiveReciter();
            $('#previewVoiceIcon').removeClass('bi-volume-up-fill bi-pause-fill').addClass('bi-arrow-clockwise');
            $('#previewVoiceText').text('Loading Audio...');
            $('#previewStatusBox').html('<span class="text-primary"><div class="spinner-border spinner-border-sm me-1"></div> Fetching sample recitation for ' + reciter + '...</span>');

            $.get("{{ route('admin.settings.preview-reciter') }}", { reciter: reciter }, function(res) {
                if (res.status === 'success' && res.audio_url) {
                    player.src = res.audio_url;
                    player.play().then(function() {
                        isPlaying = true;
                        $('#previewVoiceIcon').removeClass('bi-arrow-clockwise bi-volume-up-fill').addClass('bi-pause-fill');
                        $('#previewVoiceText').text('Pause Sample');
                        $('#previewStatusBox').html('<span class="text-success fw-bold"><i class="bi bi-soundwave text-gold me-1"></i> Playing: ' + (res.reciter_name || reciter) + ' (Bismillah)</span>');
                    }).catch(function(e) {
                        $('#previewVoiceIcon').removeClass('bi-arrow-clockwise').addClass('bi-volume-up-fill');
                        $('#previewVoiceText').text('Test Sample Voice');
                        $('#previewStatusBox').html('<span class="text-danger">Audio playback blocked by browser.</span>');
                    });
                }
            }).fail(function(xhr) {
                $('#previewVoiceIcon').removeClass('bi-arrow-clockwise').addClass('bi-volume-up-fill');
                $('#previewVoiceText').text('Test Sample Voice');
                var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Could not load audio for this reciter.';
                $('#previewStatusBox').html('<span class="text-danger"><i class="bi bi-x-circle me-1"></i> ' + msg + '</span>');
            });
        });

        player.onended = function() {
            isPlaying = false;
            $('#previewVoiceIcon').removeClass('bi-pause-fill').addClass('bi-volume-up-fill');
            $('#previewVoiceText').text('Test Sample Voice');
            $('#previewStatusBox').html('<span class="text-muted">Sample finished. Ready to test another reciter.</span>');
        };

        $('#selectDefaultReciter').on('change', function() {
            if (isPlaying) {
                player.pause();
                isPlaying = false;
                $('#previewVoiceIcon').removeClass('bi-pause-fill').addClass('bi-volume-up-fill');
                $('#previewVoiceText').text('Test Sample Voice');
            }
            $('#previewStatusBox').html('<span class="text-muted">Selected: ' + $(this).find(':selected').text().trim() + '. Click Test to listen.</span>');
        });
    });
</script>
@endpush
@endsection
