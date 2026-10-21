@extends('layouts.admin')

@section('title', 'Google Analytics & Platform Insights - Bayan Admin')
@section('page_title', 'Google Analytics & Traffic Intelligence')

@section('content')
<div class="container-fluid p-0">

    <!-- Top Notice & GA4 Status Card -->
    <div class="card card-glass border-0 p-4 mb-4 shadow-sm rounded-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-inline-flex align-items-center justify-content-center bg-emerald-light text-emerald rounded-4 p-3 shadow-2xs" style="width: 54px; height: 54px;">
                        <i class="bi bi-google fs-3"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h5 class="fw-bold text-dark mb-0">Google Analytics 4 (GA4) & Tag Manager Suite</h5>
                            @if($gaEnabled && !empty($gaId))
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">
                                    <i class="bi bi-check-circle-fill me-1"></i> Active & Tracking
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3 py-1">
                                    <i class="bi bi-exclamation-circle-fill me-1"></i> Not Configured / Paused
                                </span>
                            @endif
                        </div>
                        <p class="text-muted small mb-0">
                            Connect your official Google Analytics 4 Measurement ID (<code class="text-emerald">G-XXXXXXXXXX</code>) or Google Tag Manager (<code class="text-emerald">GTM-XXXXXXX</code>) to track real-time student recitations, Quran reading pageviews, and audio engagement.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end d-flex flex-wrap gap-2 justify-content-lg-end">
                <a href="{{ route('sitemap') }}" target="_blank" class="btn btn-outline-emerald rounded-pill px-3 shadow-2xs" title="Open dynamic sitemap.xml">
                    <i class="bi bi-diagram-3-fill text-emerald me-1"></i> XML Sitemap (114+ URLs)
                </a>
                <button type="button" class="btn btn-emerald rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#configGaModal">
                    <i class="bi bi-gear-fill me-1"></i> Configure GA4 / GTM
                </button>
            </div>
        </div>
    </div>

    <!-- KPI Metric Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card admin-stat-card p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Active Students</span>
                    <div class="rounded-3 bg-emerald-light text-emerald p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="bi bi-people-fill fs-5"></i>
                    </div>
                </div>
                <div class="display-6 fw-bold text-dark my-1">{{ number_format($totalUsers) }}</div>
                <small class="text-emerald fw-semibold"><i class="bi bi-arrow-up-right me-1"></i>+14% new this week</small>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card admin-stat-card p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Practice Sessions</span>
                    <div class="rounded-3 bg-warning-subtle text-warning p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="bi bi-mic-fill fs-5 text-gold"></i>
                    </div>
                </div>
                <div class="display-6 fw-bold text-dark my-1">{{ number_format($totalSessions) }}</div>
                <small class="text-muted">{{ number_format($totalAttempts) }} Voice Attempts Evaluated</small>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card admin-stat-card p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Average Recitation Score</span>
                    <div class="rounded-3 bg-info-subtle text-info p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="bi bi-cpu-fill fs-5"></i>
                    </div>
                </div>
                <div class="display-6 fw-bold text-dark my-1">{{ $avgScore }}%</div>
                <small class="text-success"><i class="bi bi-shield-check me-1"></i>High Tajweed Precision</small>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card admin-stat-card p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">GA4 Tracking Status</span>
                    <div class="rounded-3 {{ $gaEnabled ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="bi bi-broadcast fs-5"></i>
                    </div>
                </div>
                <div class="h4 fw-bold text-dark my-2">{{ $gaId ?: 'Unassigned' }}</div>
                <small class="text-muted">{{ $gaEnabled ? 'Automated script injection on all pages' : 'Click Configure to activate' }}</small>
            </div>
        </div>
    </div>

    <!-- Charts Row: Traffic Trend & Devices -->
    <div class="row g-4 mb-4">
        <!-- 14-Day Activity Trend Chart -->
        <div class="col-lg-8">
            <div class="card card-glass border-0 p-4 shadow-sm rounded-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
                    <div>
                        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-graph-up text-emerald me-2"></i>Daily Practice & Student Engagement Trend</h5>
                        <small class="text-muted">Recorded activity volume across the past 14 days</small>
                    </div>
                    <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill small">Last 14 Days</span>
                </div>
                <div style="height: 310px; position: relative;">
                    <canvas id="trafficTrendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Device Type Distribution -->
        <div class="col-lg-4">
            <div class="card card-glass border-0 p-4 shadow-sm rounded-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-phone text-emerald me-2"></i>Device Split</h5>
                    <span class="badge bg-light text-muted border">Platform</span>
                </div>
                <div style="height: 220px; position: relative;" class="my-auto">
                    <canvas id="deviceChart"></canvas>
                </div>
                <div class="d-flex justify-content-around text-center border-top pt-3 mt-3">
                    <div>
                        <small class="text-muted d-block"><i class="bi bi-phone text-emerald me-1"></i>Mobile</small>
                        <strong class="text-dark">{{ $devices['mobile'] }}%</strong>
                    </div>
                    <div>
                        <small class="text-muted d-block"><i class="bi bi-laptop text-info me-1"></i>Desktop</small>
                        <strong class="text-dark">{{ $devices['desktop'] }}%</strong>
                    </div>
                    <div>
                        <small class="text-muted d-block"><i class="bi bi-tablet text-warning me-1"></i>Tablet</small>
                        <strong class="text-dark">{{ $devices['tablet'] }}%</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Row: Top Visited Modules & Geographic Distribution -->
    <div class="row g-4 mb-4">
        <!-- Top Modules & Quran Pages -->
        <div class="col-lg-7">
            <div class="card card-glass border-0 p-4 shadow-sm rounded-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
                    <div>
                        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-layers-fill text-gold me-2"></i>Most Engaged Quran Modules</h5>
                        <small class="text-muted">Breakdown by student practice time and pageviews</small>
                    </div>
                    <span class="badge bg-emerald-light text-emerald px-3 py-1 rounded-pill">Top Routes</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="small text-muted">
                                <th>Module / Feature</th>
                                <th>Route Path</th>
                                <th>Est. Views</th>
                                <th>Engagement Share</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($moduleStats as $module)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-3 d-flex align-items-center justify-content-center text-white" style="width: 34px; height: 34px; background: {{ $module['color'] }}; font-size: 0.9rem;">
                                                <i class="bi {{ $module['icon'] }}"></i>
                                            </div>
                                            <span class="fw-semibold text-dark small">{{ $module['name'] }}</span>
                                        </div>
                                    </td>
                                    <td><code class="text-emerald">{{ $module['route'] }}</code></td>
                                    <td class="fw-bold small">{{ number_format($module['views']) }}</td>
                                    <td style="width: 25%;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                <div class="progress-bar" style="width: {{ $module['percentage'] }}%; background-color: {{ $module['color'] }};"></div>
                                            </div>
                                            <small class="text-muted fw-semibold">{{ $module['percentage'] }}%</small>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Geographic Audience Reach -->
        <div class="col-lg-5">
            <div class="card card-glass border-0 p-4 shadow-sm rounded-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
                    <div>
                        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-globe-americas text-info me-2"></i>Global Audience Reach</h5>
                        <small class="text-muted">Top countries accessing Bayan AI Quran</small>
                    </div>
                </div>

                <div class="list-group list-group-flush">
                    @foreach($countries as $country)
                        <div class="list-group-item px-0 py-2 d-flex align-items-center justify-content-between border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fs-4">{{ $country['flag'] }}</span>
                                <div>
                                    <div class="fw-semibold text-dark small">{{ $country['name'] }}</div>
                                    <small class="text-muted">{{ $country['code'] }}</small>
                                </div>
                            </div>
                            <span class="badge bg-light text-dark border fw-bold px-3 py-1 rounded-pill">{{ $country['users'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Configure GA4 / GTM -->
<div class="modal fade" id="configGaModal" tabindex="-1" aria-labelledby="configGaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-emerald text-white border-0 py-3 rounded-top-4">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-google fs-4 text-gold"></i>
                    <h5 class="modal-title fw-bold" id="configGaModalLabel">Configure Google Analytics 4</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.analytics.config.update') }}">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-muted small mb-4">
                        Enter your Measurement ID from your Google Analytics 4 Data Stream. Bayan AI will automatically inject the official <code>gtag.js</code> tracking snippet on all customer and public pages.
                    </p>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">
                            <i class="bi bi-tag-fill text-emerald me-1"></i> GA4 Measurement ID
                        </label>
                        <input type="text" name="google_analytics_id" class="form-control" value="{{ $gaId }}" placeholder="e.g. G-ABC123XYZ0">
                        <small class="text-muted d-block mt-1">Found in Google Analytics -> Admin -> Data Streams -> Web.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">
                            <i class="bi bi-box text-info me-1"></i> Google Tag Manager (GTM) Container ID (Optional)
                        </label>
                        <input type="text" name="google_tag_manager_id" class="form-control" value="{{ $gtmId }}" placeholder="e.g. GTM-XXXXXXX">
                        <small class="text-muted d-block mt-1">Use if you prefer managing tags via Tag Manager.</small>
                    </div>

                    <div class="form-check form-switch p-3 bg-light rounded-3 border mb-3">
                        <input class="form-check-input" type="checkbox" name="enable_google_analytics" id="enableGaToggle" value="1" {{ $gaEnabled ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold text-dark small" for="enableGaToggle">
                            Enable Google Analytics tracking script across application
                        </label>
                    </div>

                    <div class="alert alert-info py-2 small mb-0 rounded-3 border-0 bg-info bg-opacity-10 text-dark">
                        <i class="bi bi-info-circle me-1 text-info"></i> Changes take effect immediately without requiring application restart.
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-emerald rounded-pill px-4 fw-semibold">
                        <i class="bi bi-check-lg me-1"></i> Save Configuration
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    // 14-Day Activity Chart
    const ctx = document.getElementById('trafficTrendChart').getContext('2d');
    const dates = {!! json_encode($dates) !!};
    const sessions = {!! json_encode($sessionsData) !!};
    const scores = {!! json_encode($scoresData) !!};

    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(16, 185, 129, 0.35)');
    gradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [
                {
                    label: 'Practice Sessions',
                    data: sessions,
                    borderColor: '#10b981',
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.35,
                    borderWidth: 2.5,
                    pointBackgroundColor: '#10b981',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    yAxisID: 'y'
                },
                {
                    label: 'AI Accuracy %',
                    data: scores,
                    borderColor: '#f59e0b',
                    borderDash: [5, 5],
                    borderWidth: 2,
                    fill: false,
                    tension: 0.3,
                    pointBackgroundColor: '#f59e0b',
                    pointRadius: 3,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        font: { family: 'Plus Jakarta Sans', size: 12 }
                    }
                },
                tooltip: {
                    backgroundColor: '#062820',
                    titleColor: '#fbbf24',
                    padding: 10,
                    cornerRadius: 8
                }
            },
            scales: {
                x: {
                    grid: { display: false }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    grid: { color: 'rgba(226, 232, 240, 0.6)' },
                    title: { display: true, text: 'Sessions Count' }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: { drawOnChartArea: false },
                    min: 50,
                    max: 100,
                    title: { display: true, text: 'Avg Accuracy %' }
                }
            }
        }
    });

    // Device Split Donut Chart
    const ctxDevice = document.getElementById('deviceChart').getContext('2d');
    new Chart(ctxDevice, {
        type: 'doughnut',
        data: {
            labels: ['Mobile', 'Desktop', 'Tablet'],
            datasets: [{
                data: [{{ $devices['mobile'] }}, {{ $devices['desktop'] }}, {{ $devices['tablet'] }}],
                backgroundColor: ['#10b981', '#3b82f6', '#f59e0b'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        font: { family: 'Plus Jakarta Sans', size: 11 }
                    }
                }
            },
            cutout: '68%'
        }
    });
});
</script>
@endpush
