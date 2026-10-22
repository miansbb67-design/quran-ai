@extends('layouts.admin')

@section('title', 'Admin Dashboard - Bayan AI Quran')
@section('page_title', 'Platform Overview & Control Center')

@section('content')
<div class="container-fluid p-0">

    <!-- Quick Hub Navigation Bar -->
    <div class="card card-glass border-0 p-3 mb-4 shadow-sm rounded-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-emerald text-white p-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px;">
                    <i class="bi bi-speedometer2 fs-5"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-0">Platform Health & Real-time Operations</h5>
                    <small class="text-muted">Islamic AI recitation platform telemetry and management controls</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('admin.analytics.index') }}" class="btn btn-sm btn-outline-info rounded-pill px-3">
                    <i class="bi bi-graph-up-arrow me-1"></i> Google Analytics
                </a>
                <a href="{{ route('admin.hadiths.index') }}" class="btn btn-sm btn-outline-warning text-dark rounded-pill px-3">
                    <i class="bi bi-quote me-1"></i> Hadith Manager
                </a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-emerald rounded-pill px-3">
                    <i class="bi bi-people me-1"></i> Manage Students
                </a>
                <form method="POST" action="{{ route('admin.settings.clear-cache') }}" class="d-inline mb-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-light border rounded-pill px-3 text-secondary" title="Flush cache">
                        <i class="bi bi-arrow-repeat text-emerald me-1"></i> Clear Cache
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- KPI Metric Cards -->
    <div class="row g-3 mb-4">
        <!-- Total Users -->
        <div class="col-sm-6 col-xl-3">
            <div class="card admin-stat-card p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Total Students</span>
                    <div class="rounded-3 bg-emerald-light text-emerald p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-people-fill fs-5"></i>
                    </div>
                </div>
                <div class="display-6 fw-bold text-dark my-1">{{ number_format($totalUsers) }}</div>
                <small class="text-success"><i class="bi bi-check-circle me-1"></i>{{ $activeUsers }} Active Students ({{ $suspendedUsers }} Suspended)</small>
            </div>
        </div>

        <!-- Total Sessions -->
        <div class="col-sm-6 col-xl-3">
            <div class="card admin-stat-card p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Practice Sessions</span>
                    <div class="rounded-3 bg-warning-subtle text-warning p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-mic-fill fs-5 text-gold"></i>
                    </div>
                </div>
                <div class="display-6 fw-bold text-dark my-1">{{ number_format($totalSessions) }}</div>
                <small class="text-muted">{{ number_format($totalAttempts) }} AI Voice Evaluations</small>
            </div>
        </div>

        <!-- Average AI Accuracy -->
        <div class="col-sm-6 col-xl-3">
            <div class="card admin-stat-card p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Average Recitation Score</span>
                    <div class="rounded-3 bg-info-subtle text-info p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-cpu-fill fs-5"></i>
                    </div>
                </div>
                <div class="display-6 fw-bold text-dark my-1">{{ $avgScore }}%</div>
                <small class="text-emerald fw-semibold"><i class="bi bi-shield-check me-1"></i>High Tajweed Precision</small>
            </div>
        </div>

        <!-- Total Memorized -->
        <div class="col-sm-6 col-xl-3">
            <div class="card admin-stat-card p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Memorized Verses</span>
                    <div class="rounded-3 bg-success-subtle text-success p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-award-fill fs-5"></i>
                    </div>
                </div>
                <div class="display-6 fw-bold text-dark my-1">{{ number_format($totalMemorized) }}</div>
                <small class="text-muted">{{ number_format($totalPracticing) }} Verses Under Active Revision</small>
            </div>
        </div>
    </div>

    <!-- Interactive Practice Activity Chart -->
    <div class="card card-glass border-0 p-4 mb-4 shadow-sm rounded-4">
        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 mb-3 border-bottom pb-3">
            <div>
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-bar-chart-line-fill text-emerald me-2"></i>Weekly Practice Activity & Student Engagement</h5>
                <small class="text-muted">Recitation sessions recorded across the past 7 days</small>
            </div>
            <a href="{{ route('admin.analytics.index') }}" class="btn btn-sm btn-outline-emerald rounded-pill px-3">
                <i class="bi bi-graph-up me-1"></i> View Full Web Analytics
            </a>
        </div>
        @php
            $chartLabels = [];
            $chartTotals = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $chartLabels[] = now()->subDays($i)->format('D, M d');
                $found = $dailySessions->firstWhere('date', $date);
                $chartTotals[] = $found ? $found->total : 0;
            }
        @endphp
        <div style="height: 260px; position: relative;">
            <canvas id="overviewActivityChart" data-labels="{{ json_encode($chartLabels) }}" data-totals="{{ json_encode($chartTotals) }}"></canvas>
        </div>
    </div>

    <!-- Main Content Row: Recent Students & Audit Feed -->
    <div class="row g-4 mb-4">
        <!-- Recent Users Table -->
        <div class="col-lg-7">
            <div class="card card-glass border-0 p-4 shadow-sm rounded-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-people text-emerald me-2"></i>Recent Student Registrations</h5>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill">View All Students</a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="small text-muted">
                                <th>Student</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Joined</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentUsers as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-emerald-light text-emerald d-flex align-items-center justify-content-center fw-bold shadow-2xs" style="width: 32px; height: 32px; font-size: 0.75rem;">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <strong class="d-block text-dark small">{{ $user->name }}</strong>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $user->isOwner() ? 'bg-danger' : ($user->isAdmin() ? 'bg-warning text-dark' : 'bg-light text-muted border') }} rounded-pill px-2">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $user->isActive() ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }} rounded-pill px-2">
                                            {{ ucfirst($user->status) }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">{{ $user->created_at->diffForHumans() }}</td>
                                    <td>
                                        <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-light border rounded-pill px-2" title="Inspect user profile">
                                            <i class="bi bi-eye text-emerald"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Audit Log Feed -->
        <div class="col-lg-5">
            <div class="card card-glass border-0 p-4 shadow-sm rounded-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history text-gold me-2"></i>Admin Audit Feed</h5>
                    <a href="{{ route('admin.settings.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill">Logs & Settings</a>
                </div>

                @if($activityLogs->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($activityLogs as $log)
                            <div class="list-group-item px-0 py-2 border-bottom">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge bg-light text-dark border small rounded-pill">{{ $log->action }}</span>
                                    <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                                </div>
                                <div class="small text-secondary">
                                    <strong>{{ $log->user->name ?? 'Admin' }}</strong> performed {{ $log->target_type ?? 'System' }} action.
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5 text-muted small">
                        <i class="bi bi-shield-check fs-2 text-secondary d-block mb-2"></i>
                        No admin activity logs recorded yet.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const canvas = document.getElementById('overviewActivityChart');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    const labels = JSON.parse(canvas.getAttribute('data-labels') || '[]');
    const totals = JSON.parse(canvas.getAttribute('data-totals') || '[]');

    const gradient = ctx.createLinearGradient(0, 0, 0, 240);
    gradient.addColorStop(0, 'rgba(16, 185, 129, 0.4)');
    gradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Student Practice Sessions',
                data: totals,
                backgroundColor: 'rgba(16, 185, 129, 0.85)',
                hoverBackgroundColor: '#059669',
                borderRadius: 8,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
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
                    beginAtZero: true,
                    grid: { color: 'rgba(226, 232, 240, 0.6)' },
                    ticks: { precision: 0 }
                }
            }
        }
    });
});
</script>
@endpush
