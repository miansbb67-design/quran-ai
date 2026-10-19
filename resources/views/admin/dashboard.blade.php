@extends('layouts.admin')

@section('title', 'Admin Dashboard - Bayan AI Quran')
@section('page_title', 'Platform Overview & Health')

@section('content')
<div class="container-fluid p-0">

    <!-- KPI Metric Cards -->
    <div class="row g-3 mb-4">
        <!-- Total Users -->
        <div class="col-sm-6 col-xl-3">
            <div class="card card-glass border-0 p-4 h-100 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Total Students</span>
                    <div class="rounded-3 bg-emerald-light text-emerald p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="bi bi-people-fill fs-5"></i>
                    </div>
                </div>
                <div class="display-6 fw-bold text-dark my-1">{{ $totalUsers }}</div>
                <small class="text-success"><i class="bi bi-check-circle me-1"></i>{{ $activeUsers }} Active ({{ $suspendedUsers }} Suspended)</small>
            </div>
        </div>

        <!-- Total Sessions -->
        <div class="col-sm-6 col-xl-3">
            <div class="card card-glass border-0 p-4 h-100 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Practice Sessions</span>
                    <div class="rounded-3 bg-gold-light text-gold p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="bi bi-mic-fill fs-5 text-warning"></i>
                    </div>
                </div>
                <div class="display-6 fw-bold text-dark my-1">{{ $totalSessions }}</div>
                <small class="text-muted">{{ $totalAttempts }} Voice Attempts Evaluated</small>
            </div>
        </div>

        <!-- Average AI Accuracy -->
        <div class="col-sm-6 col-xl-3">
            <div class="card card-glass border-0 p-4 h-100 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Average AI Score</span>
                    <div class="rounded-3 bg-info bg-opacity-10 text-info p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="bi bi-cpu-fill fs-5"></i>
                    </div>
                </div>
                <div class="display-6 fw-bold text-dark my-1">{{ $avgScore }}%</div>
                <small class="text-muted">Platform Pronunciation Accuracy</small>
            </div>
        </div>

        <!-- Total Memorized -->
        <div class="col-sm-6 col-xl-3">
            <div class="card card-glass border-0 p-4 h-100 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Memorized Verses</span>
                    <div class="rounded-3 bg-success bg-opacity-10 text-success p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="bi bi-award-fill fs-5"></i>
                    </div>
                </div>
                <div class="display-6 fw-bold text-dark my-1">{{ $totalMemorized }}</div>
                <small class="text-muted">{{ $totalPracticing }} Verses in Practice</small>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row g-4 mb-4">
        <!-- Recent Users Table -->
        <div class="col-lg-7">
            <div class="card card-glass border-0 p-4 shadow-sm h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-people text-emerald me-2"></i>Recent Registrations</h5>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill">View All Users</a>
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
                                            <div class="rounded-circle bg-emerald-light text-emerald d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.75rem;">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <strong class="d-block text-dark small">{{ $user->name }}</strong>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $user->isOwner() ? 'bg-danger' : ($user->isAdmin() ? 'bg-warning text-dark' : 'bg-light text-muted border') }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $user->isActive() ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }}">
                                            {{ ucfirst($user->status) }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">{{ $user->created_at->diffForHumans() }}</td>
                                    <td>
                                        <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-light border rounded-pill px-2" title="Inspect user">
                                            <i class="bi bi-eye"></i>
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
            <div class="card card-glass border-0 p-4 shadow-sm h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history text-gold me-2"></i>Admin Audit Feed</h5>
                    <a href="{{ route('admin.settings.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill">Logs</a>
                </div>

                @if($activityLogs->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($activityLogs as $log)
                            <div class="list-group-item px-0 py-2 border-bottom">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge bg-light text-dark border small">{{ $log->action }}</span>
                                    <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                                </div>
                                <div class="small text-secondary">
                                    <strong>{{ $log->user->name ?? 'Admin' }}</strong> performed {{ $log->target_type ?? 'System' }} action.
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4 text-muted small">
                        No admin activity logs recorded yet.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
