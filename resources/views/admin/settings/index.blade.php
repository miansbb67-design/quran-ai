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

                    <!-- Default Reciter and Translation -->
                    <h6 class="fw-bold text-emerald mb-3 text-uppercase small" style="letter-spacing: 1px;">Quran Defaults</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Default Audio Reciter</label>
                            <select name="default_reciter" class="form-select">
                                <option value="ar.alafasy" {{ ($settings['default_reciter'] ?? '') === 'ar.alafasy' ? 'selected' : '' }}>Mishary Rashid Alafasy</option>
                                <option value="ar.abdulbasitmurattal" {{ ($settings['default_reciter'] ?? '') === 'ar.abdulbasitmurattal' ? 'selected' : '' }}>Abdul Basit Abdul Samad</option>
                                <option value="ar.husary" {{ ($settings['default_reciter'] ?? '') === 'ar.husary' ? 'selected' : '' }}>Mahmoud Khalil Al-Husary</option>
                                <option value="ar.shaatree" {{ ($settings['default_reciter'] ?? '') === 'ar.shaatree' ? 'selected' : '' }}>Abu Bakr Al-Shatri</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Default English Translation</label>
                            <select name="default_translation" class="form-select">
                                <option value="en.asad" {{ ($settings['default_translation'] ?? '') === 'en.asad' ? 'selected' : '' }}>Muhammad Asad</option>
                                <option value="en.sahih" {{ ($settings['default_translation'] ?? '') === 'en.sahih' ? 'selected' : '' }}>Saheeh International</option>
                                <option value="en.pickthall" {{ ($settings['default_translation'] ?? '') === 'en.pickthall' ? 'selected' : '' }}>Pickthall</option>
                                <option value="en.yusufali" {{ ($settings['default_translation'] ?? '') === 'en.yusufali' ? 'selected' : '' }}>Yusuf Ali</option>
                            </select>
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
@endsection
