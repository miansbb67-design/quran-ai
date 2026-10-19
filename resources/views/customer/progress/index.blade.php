@extends('layouts.customer')

@section('title', 'Learning Analytics & Progress - Bayan AI Quran')
@section('page_title', 'Learning Progress & Analytics')

@section('content')
<div class="container-fluid p-0">

    <!-- Top Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card card-glass border-0 p-4 h-100 shadow-sm">
                <span class="text-muted small fw-semibold">Total Practice Sessions</span>
                <div class="display-6 fw-bold text-emerald my-1">{{ $totalSessions }}</div>
                <small class="text-muted">{{ $totalAttempts }} Recitation Attempts</small>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card card-glass border-0 p-4 h-100 shadow-sm">
                <span class="text-muted small fw-semibold">Average AI Score</span>
                <div class="display-6 fw-bold text-gold my-1">{{ round($avgScore, 1) }}%</div>
                <small class="text-muted">Pronunciation Confidence</small>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card card-glass border-0 p-4 h-100 shadow-sm">
                <span class="text-muted small fw-semibold">Memorized Verses</span>
                <div class="display-6 fw-bold text-success my-1">{{ $memStats['memorized'] }}</div>
                <small class="text-muted">{{ $memStats['practicing'] }} in active practice</small>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card card-glass border-0 p-4 h-100 shadow-sm">
                <span class="text-muted small fw-semibold">Reading Streak</span>
                <div class="display-6 fw-bold text-danger my-1">{{ $readingProgress->current_streak_days }} <span class="fs-6 fw-normal text-muted">days</span></div>
                <small class="text-muted">Best streak: {{ $readingProgress->longest_streak_days }} days</small>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Daily Practice Chart -->
        <div class="col-lg-8">
            <div class="card card-glass border-0 p-4 shadow-sm h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-graph-up text-emerald me-2"></i>Daily Practice Activity (Last 30 Days)</h5>
                    <span class="badge badge-emerald">Sessions & Scores</span>
                </div>
                <div style="height: 300px; position: relative;">
                    <canvas id="practiceChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Memorization Breakdown Doughnut -->
        <div class="col-lg-4">
            <div class="card card-glass border-0 p-4 shadow-sm h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-pie-chart text-emerald me-2"></i>Hifz Retention</h5>
                    <span class="badge badge-gold">Status</span>
                </div>
                <div style="height: 250px; position: relative;">
                    <canvas id="hifzChart"></canvas>
                </div>
                <div class="d-flex justify-content-around text-center mt-3 pt-3 border-top small">
                    <div>
                        <strong class="d-block text-success">{{ $memStats['memorized'] }}</strong>
                        <span class="text-muted">Memorized</span>
                    </div>
                    <div>
                        <strong class="d-block text-warning">{{ $memStats['practicing'] }}</strong>
                        <span class="text-muted">Practicing</span>
                    </div>
                    <div>
                        <strong class="d-block text-primary">{{ $memStats['learning'] }}</strong>
                        <span class="text-muted">Learning</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Privacy & Purge Card -->
    <div class="card card-glass border-0 p-4 shadow-sm border border-danger border-opacity-25 bg-white">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <h6 class="fw-bold text-danger mb-1"><i class="bi bi-shield-lock-fill me-2"></i>Privacy & Data Sovereignty</h6>
                <p class="text-secondary small mb-0">
                    You have complete control over your voice attempts, recitation scores, and reading logs. You can permanently wipe your practice history at any time.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#deleteHistoryModal">
                    <i class="bi bi-trash me-1"></i> Manage / Clear History
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Clear History Modal -->
<div class="modal fade" id="deleteHistoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Delete Learning History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-secondary small mb-3">
                    Select the data categories you wish to purge. This operation is permanent and cannot be undone.
                </p>
                <form id="deleteHistoryForm">
                    @csrf
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="delete_type" id="delSessions" value="sessions" checked>
                        <label class="form-check-label small" for="delSessions">
                            Practice sessions and AI recitation attempts only
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="delete_type" id="delMem" value="memorization">
                        <label class="form-check-label small" for="delMem">
                            Hifz memorization progress and revision schedules
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="delete_type" id="delBookmarks" value="bookmarks">
                        <label class="form-check-label small" for="delBookmarks">
                            Saved verse bookmarks
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" name="delete_type" id="delAll" value="all">
                        <label class="form-check-label small text-danger fw-bold" for="delAll">
                            Wipe all learning history, streaks, and data completely
                        </label>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                        <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4" id="btnConfirmDelete">Permanently Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Fetch chart data via AJAX
        $.get("{{ route('progress.chart-data') }}", function(data) {
            // Render Practice Activity Line Chart
            var dates = [];
            var counts = [];
            var scores = [];

            if (data.daily_sessions && data.daily_sessions.length > 0) {
                data.daily_sessions.forEach(function(item) {
                    dates.push(item.date);
                    counts.push(item.count);
                    scores.push(Math.round(item.avg_score || 0));
                });
            } else {
                // Placeholder demo dates if empty
                dates = ['Day 1', 'Day 2', 'Day 3', 'Today'];
                counts = [1, 2, 1, 3];
                scores = [85, 90, 88, 92];
            }

            var ctx1 = document.getElementById('practiceChart').getContext('2d');
            new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [
                        {
                            label: 'Recitation Score (%)',
                            data: scores,
                            borderColor: '#c59b27',
                            backgroundColor: 'rgba(197, 155, 39, 0.1)',
                            fill: true,
                            tension: 0.3,
                            yAxisID: 'y1',
                        },
                        {
                            label: 'Sessions Count',
                            data: counts,
                            borderColor: '#064e3b',
                            backgroundColor: '#064e3b',
                            type: 'bar',
                            yAxisID: 'y',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            position: 'left',
                            title: { display: true, text: 'Sessions' }
                        },
                        y1: {
                            beginAtZero: true,
                            max: 100,
                            position: 'right',
                            grid: { drawOnChartArea: false },
                            title: { display: true, text: 'Confidence %' }
                        }
                    }
                }
            });

            // Render Hifz Status Doughnut Chart
            var ctx2 = document.getElementById('hifzChart').getContext('2d');
            new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: ['Memorized', 'Practicing', 'Learning'],
                    datasets: [{
                        data: [
                            {{ $memStats['memorized'] ?: 1 }},
                            {{ $memStats['practicing'] ?: 0 }},
                            {{ $memStats['learning'] ?: 0 }}
                        ],
                        backgroundColor: ['#22c55e', '#eab308', '#3b82f6'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        });

        // Form submit for delete history
        $('#deleteHistoryForm').on('submit', function(e) {
            e.preventDefault();
            var type = $('input[name="delete_type"]:checked').val();

            if (confirm('Are you absolutely certain you want to purge this data?')) {
                $.post("{{ route('progress.delete-history') }}", {
                    type: type,
                    _token: "{{ csrf_token() }}"
                }, function(res) {
                    alert('Data deleted successfully.');
                    location.reload();
                });
            }
        });
    });
</script>
@endpush
@endsection
