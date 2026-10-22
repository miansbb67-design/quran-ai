@extends('layouts.admin')

@section('title', 'User Profile: ' . $user->name . ' - Bayan Admin')
@section('page_title', 'Student Profile Details')

@section('content')
<div class="container-fluid p-0">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Back to Users
        </a>
    </div>

    <div class="row g-4 mb-4">
        <!-- User Info Card -->
        <div class="col-lg-4">
            <div class="card card-glass border-0 p-4 shadow-sm text-center mb-4">
                <div class="rounded-circle bg-emerald-light text-emerald mx-auto d-flex align-items-center justify-content-center fw-bold fs-2 mb-3" style="width: 80px; height: 80px;">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <h4 class="fw-bold text-dark mb-1">{{ $user->name }}</h4>
                <p class="text-muted small mb-3">{{ $user->email }}</p>

                <div class="d-flex justify-content-center gap-2 mb-4">
                    @if($user->isAdmin())
                        <span class="badge bg-warning text-dark px-3 py-2">
                            <i class="bi bi-shield-lock-fill me-1"></i> Admin
                        </span>
                    @else
                        <span class="badge bg-light text-dark border px-3 py-2">
                            <i class="bi bi-mortarboard-fill text-emerald me-1"></i> Student
                        </span>
                    @endif
                    <span class="badge {{ $user->isActive() ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }} px-3 py-2">
                        {{ ucfirst($user->status) }}
                    </span>
                </div>

                <div class="text-start border-top pt-3 small">
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted">Account ID:</span>
                        <span class="fw-semibold text-dark">#{{ $user->id }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted">Member Since:</span>
                        <span class="fw-semibold text-dark">{{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted">Email Verified:</span>
                        <span class="fw-semibold text-{{ $user->email_verified_at ? 'success' : 'warning' }}">
                            {{ $user->email_verified_at ? 'Verified' : 'Pending' }}
                        </span>
                    </div>
                </div>

                @if(Auth::id() !== $user->id)
                    <div class="border-top pt-3 mt-3">
                        <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" onsubmit="return confirm('Permanently delete this user account?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill w-100">
                                <i class="bi bi-trash me-1"></i> Delete User Account
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            <!-- Reading Stats -->
            <div class="card card-glass border-0 p-4 shadow-sm">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-book-half text-emerald me-2"></i>Reading Progress</h6>
                @if($user->readingProgress)
                    <div class="d-flex justify-content-between py-2 border-bottom small">
                        <span class="text-muted">Current Reading Position:</span>
                        <span class="fw-bold text-emerald">Surah {{ $user->readingProgress->last_surah_number }}:{{ $user->readingProgress->last_ayah_number }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom small">
                        <span class="text-muted">Total Verses Read:</span>
                        <span class="fw-bold text-dark">{{ $user->readingProgress->total_ayahs_read }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom small">
                        <span class="text-muted">Current Daily Streak:</span>
                        <span class="fw-bold text-danger">{{ $user->readingProgress->current_streak_days }} days</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 small">
                        <span class="text-muted">Total Practice Time:</span>
                        <span class="fw-bold text-dark">{{ round($user->readingProgress->total_reading_time_seconds / 60) }} min</span>
                    </div>
                @else
                    <p class="text-muted small mb-0">No reading progress recorded yet.</p>
                @endif
            </div>
        </div>

        <!-- Activity & Memorization Logs -->
        <div class="col-lg-8">
            <!-- Recent Practice Sessions -->
            <div class="card card-glass border-0 p-4 shadow-sm mb-4">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-mic text-emerald me-2"></i>Recent Voice Practice Sessions</h6>
                @if($user->practiceSessions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th>Surah</th>
                                    <th>Verses</th>
                                    <th>Duration</th>
                                    <th>Score</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->practiceSessions as $s)
                                    <tr>
                                        <td>Surah #{{ $s->surah_number }}</td>
                                        <td>Ayah {{ $s->start_ayah }}-{{ $s->end_ayah }}</td>
                                        <td>{{ round($s->duration_seconds / 60, 1) }} min</td>
                                        <td>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                {{ $s->overall_score }}%
                                            </span>
                                        </td>
                                        <td class="text-muted">{{ $s->created_at->diffForHumans() }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted small mb-0">No practice sessions found for this user.</p>
                @endif
            </div>

            <!-- Memorization Progress -->
            <div class="card card-glass border-0 p-4 shadow-sm">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-award text-gold me-2"></i>Memorization Entries</h6>
                @if($user->memorizationProgress->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th>Surah : Ayah</th>
                                    <th>Status</th>
                                    <th>Repetitions</th>
                                    <th>Last Practiced</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->memorizationProgress as $m)
                                    <tr>
                                        <td>Surah {{ $m->surah_number }} : Ayah {{ $m->ayah_number }}</td>
                                        <td>
                                            <span class="badge {{ $m->status === 'memorized' ? 'bg-success' : ($m->status === 'practicing' ? 'bg-warning text-dark' : 'bg-info') }}">
                                                {{ ucfirst($m->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $m->total_practice_count }} times</td>
                                        <td class="text-muted">{{ $m->last_practiced_at ? $m->last_practiced_at->diffForHumans() : '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted small mb-0">No memorization entries recorded yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
