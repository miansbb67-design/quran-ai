@extends('layouts.customer')

@section('title', 'Student Dashboard - Bayan AI Quran')
@section('page_title', 'My Learning Dashboard')

@section('content')
<div class="container-fluid p-0">

    <!-- Active Announcements Alert -->
    @foreach($announcements as $announcement)
        <div class="alert alert-{{ $announcement->type === 'warning' ? 'warning' : 'info' }} border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center gap-3">
            <i class="bi bi-megaphone-fill fs-4 text-{{ $announcement->type === 'warning' ? 'warning' : 'primary' }}"></i>
            <div class="flex-grow-1">
                <strong class="d-block text-dark">{{ $announcement->title }}</strong>
                <small class="text-secondary">{{ $announcement->content }}</small>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endforeach

    <!-- Welcome Hero Card -->
    <div class="card card-glass border-0 shadow-sm p-4 mb-4 bg-emerald-grad text-white rounded-4 position-relative overflow-hidden">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <span class="badge badge-gold mb-2"><i class="bi bi-sun-fill me-1"></i> As-salamu alaykum</span>
                <h2 class="fw-bold mb-2">Welcome back, {{ Auth::user()->name }}!</h2>
                <p class="text-white-50 mb-3">
                    Continue your daily recitation and strengthen your Quranic memorization with instant AI feedback.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('quran.show', $readingProgress->last_surah_number) }}" class="btn btn-gold rounded-pill px-4 shadow-sm">
                        <i class="bi bi-book me-1"></i> Continue Reading (Surah {{ $readingProgress->last_surah_number }})
                    </a>
                    <a href="{{ route('ai-teacher.index') }}" class="btn btn-outline-light rounded-pill px-4">
                        <i class="bi bi-mic-fill me-1 text-warning"></i> AI Voice Studio
                    </a>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end text-center d-none d-lg-block">
                <div class="font-arabic fs-1 text-gold opacity-75">
                    الْحَمْدُ لِلَّهِ رَبِّ الْعَالَمِينَ
                </div>
                <small class="text-white-50">"All praise is due to Allah, Lord of the worlds."</small>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards Row -->
    <div class="row g-3 mb-4">
        <!-- Streak Card -->
        <div class="col-6 col-md-3">
            <div class="card card-glass border-0 p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <small class="text-muted fw-semibold">Current Streak</small>
                    <div class="rounded-3 bg-danger bg-opacity-10 text-danger p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="bi bi-fire fs-5"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold text-dark">{{ $readingProgress->current_streak_days }} <span class="fs-6 fw-normal text-muted">days</span></div>
                <small class="text-muted">Longest: {{ $readingProgress->longest_streak_days }} days</small>
            </div>
        </div>

        <!-- Total Ayahs Read -->
        <div class="col-6 col-md-3">
            <div class="card card-glass border-0 p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <small class="text-muted fw-semibold">Verses Read</small>
                    <div class="rounded-3 bg-emerald-light text-emerald p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="bi bi-check2-circle fs-5"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold text-dark">{{ $readingProgress->total_ayahs_read }} <span class="fs-6 fw-normal text-muted">ayahs</span></div>
                <small class="text-muted">Last at Surah {{ $readingProgress->last_surah_number }}:{{ $readingProgress->last_ayah_number }}</small>
            </div>
        </div>

        <!-- Memorization Status -->
        <div class="col-6 col-md-3">
            <div class="card card-glass border-0 p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <small class="text-muted fw-semibold">Memorized</small>
                    <div class="rounded-3 bg-gold-light text-gold p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="bi bi-award-fill fs-5 text-warning"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold text-dark">{{ $memStats['memorized'] }} <span class="fs-6 fw-normal text-muted">ayahs</span></div>
                <small class="text-muted">{{ $memStats['practicing'] }} practicing, {{ $memStats['learning'] }} learning</small>
            </div>
        </div>

        <!-- Learning Time -->
        <div class="col-6 col-md-3">
            <div class="card card-glass border-0 p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <small class="text-muted fw-semibold">Practice Time</small>
                    <div class="rounded-3 bg-info bg-opacity-10 text-info p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="bi bi-hourglass-split fs-5"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold text-dark">{{ $totalLearningMinutes }} <span class="fs-6 fw-normal text-muted">min</span></div>
                <small class="text-muted">{{ $bookmarksCount }} Bookmarks saved</small>
            </div>
        </div>
    </div>

    <!-- Main Dashboard Row -->
    <div class="row g-4 mb-4">
        <!-- Left: Quick Action Cards -->
        <div class="col-lg-8">
            <!-- Learning Goal Card -->
            <div class="card card-glass border-0 p-4 mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h5 class="fw-bold text-dark mb-1"><i class="bi bi-bullseye text-emerald me-2"></i>Daily Goal Progress</h5>
                        <small class="text-muted">Target for today ({{ now()->format('l, M d') }})</small>
                    </div>
                    <a href="{{ route('progress.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill">View Goals</a>
                </div>

                @php
                    $targetAyahs = $todayGoal ? $todayGoal->target_value : 10;
                    $completedAyahs = $todayGoal ? $todayGoal->current_value : min(7, $readingProgress->total_ayahs_read);
                    $percentage = min(100, round(($completedAyahs / max(1, $targetAyahs)) * 100));
                @endphp

                <div class="mb-2 d-flex justify-content-between small fw-semibold">
                    <span>{{ $completedAyahs }} / {{ $targetAyahs }} Ayahs Completed</span>
                    <span class="text-emerald">{{ $percentage }}%</span>
                </div>
                <div class="progress" style="height: 10px; border-radius: 5px;">
                    <div class="progress-bar bg-emerald-grad" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>

            <!-- Recent Practice Attempts -->
            <div class="card card-glass border-0 p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history text-emerald me-2"></i>Recent Recitation Sessions</h5>
                    <a href="{{ route('ai-teacher.index') }}" class="btn btn-sm btn-emerald rounded-pill px-3">Practice Now</a>
                </div>

                @if($recentSessions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="small text-muted">
                                    <th>Surah</th>
                                    <th>Verses</th>
                                    <th>Type</th>
                                    <th>Duration</th>
                                    <th>Score</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentSessions as $session)
                                    <tr>
                                        <td>
                                            <span class="fw-semibold text-dark">Surah #{{ $session->surah_number }}</span>
                                        </td>
                                        <td>Ayah {{ $session->start_ayah }} - {{ $session->end_ayah }}</td>
                                        <td>
                                            <span class="badge bg-light text-dark border text-capitalize">{{ $session->session_type }}</span>
                                        </td>
                                        <td>{{ round($session->duration_seconds / 60, 1) }} min</td>
                                        <td>
                                            @if($session->overall_score)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                    {{ $session->overall_score }}%
                                                </span>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                        <td class="text-muted small">{{ $session->created_at->diffForHumans() }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-mic text-muted fs-1 mb-2 d-block"></i>
                        <p class="text-secondary small mb-3">You haven't recorded any voice practice sessions yet.</p>
                        <a href="{{ route('ai-teacher.index') }}" class="btn btn-emerald rounded-pill px-4">Launch AI Voice Teacher</a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right: Quran Quick Access & Shortcuts -->
        <div class="col-lg-4">
            <!-- Continue Reading Box -->
            <div class="card card-glass border-0 p-4 mb-4 bg-light border">
                <h6 class="fw-bold text-emerald mb-3"><i class="bi bi-bookmark-check-fill text-gold me-2"></i>Current Reading Bookmark</h6>
                <div class="p-3 bg-white rounded-3 border mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-bold text-dark">Surah #{{ $readingProgress->last_surah_number }}</span>
                        <span class="badge badge-emerald">Ayah {{ $readingProgress->last_ayah_number }}</span>
                    </div>
                    <small class="text-muted">Last visited {{ $readingProgress->updated_at->diffForHumans() }}</small>
                </div>
                <a href="{{ route('quran.show', $readingProgress->last_surah_number) }}" class="btn btn-emerald w-100 rounded-pill">
                    <i class="bi bi-book-half me-1"></i> Open Surah Reader
                </a>
            </div>

            <!-- Quick Action Hub -->
            <div class="card card-glass border-0 p-4">
                <h6 class="fw-bold text-dark mb-3">Learning Modules</h6>
                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('quran.index') }}" class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-white border text-decoration-none text-dark hover-shadow">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-journal-richtext text-emerald fs-4"></i>
                            <div>
                                <span class="fw-semibold d-block">Full Quran Index</span>
                                <small class="text-muted">Browse all 114 Surahs</small>
                            </div>
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>

                    <a href="{{ route('hifz.index') }}" class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-white border text-decoration-none text-dark hover-shadow">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-award text-gold fs-4"></i>
                            <div>
                                <span class="fw-semibold d-block">Hifz Memorization</span>
                                <small class="text-muted">Spaced repetition schedules</small>
                            </div>
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>

                    <a href="{{ route('progress.index') }}" class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-white border text-decoration-none text-dark hover-shadow">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-bar-chart-line text-primary fs-4"></i>
                            <div>
                                <span class="fw-semibold d-block">Analytics & Charts</span>
                                <small class="text-muted">Review progress over time</small>
                            </div>
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
