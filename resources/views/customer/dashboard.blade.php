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

            <!-- Daily Hadith (حدیثِ نبوی ﷺ) Box -->
            @if(isset($dailyHadith) && $dailyHadith)
                <div class="card card-glass border-0 p-4 mb-4 shadow-sm position-relative overflow-hidden" style="border-left: 4px solid #10b981 !important; background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(240,253,244,0.75) 100%);">
                    <!-- Top Badge & Header -->
                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-emerald-light text-emerald rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1">
                                <i class="bi bi-quote fs-6"></i> Daily Hadith &bull; حدیثِ نبوی ﷺ
                            </span>
                            <span class="badge bg-gold-light text-dark rounded-pill px-2 py-1 small fw-semibold">
                                {{ $dailyHadith->grade ?? 'Sahih' }}
                            </span>
                            @if($dailyHadith->chapter)
                                <span class="badge bg-light text-muted border rounded-pill px-2 py-1 small d-none d-md-inline-block">
                                    {{ $dailyHadith->chapter }}
                                </span>
                            @endif
                        </div>
                        <a href="{{ route('hadith.index') }}" class="btn btn-sm btn-outline-emerald rounded-pill px-3 d-inline-flex align-items-center gap-1 shadow-2xs">
                            <span>Browse All Hadiths</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>

                    <!-- Arabic Hadith Text -->
                    <div class="p-3 bg-white rounded-3 border mb-3 text-center position-relative shadow-2xs">
                        <div class="font-arabic fs-3 text-emerald fw-bold mb-1" dir="rtl" style="line-height: 2;">
                            « {{ $dailyHadith->arabic_text }} »
                        </div>
                    </div>

                    <!-- Urdu Translation -->
                    @if(!empty($dailyHadith->urdu_translation))
                        <div class="p-2 px-3 bg-light rounded-3 mb-2 text-end text-dark font-urdu" dir="rtl" style="font-size: 1.05rem; line-height: 1.8;">
                            {{ $dailyHadith->urdu_translation }}
                        </div>
                    @endif

                    <!-- English Translation -->
                    <div class="text-secondary small fst-italic mb-3 px-1 lh-base">
                        "{{ $dailyHadith->english_translation }}"
                    </div>

                    <!-- Reference & Narrator Footer -->
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-2 border-top text-muted small">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-person-check text-emerald"></i>
                            <span class="fw-semibold text-dark">{{ $dailyHadith->narrator }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-book text-gold"></i>
                            <span class="badge bg-white text-dark border">{{ $dailyHadith->source_book }}</span>
                        </div>
                    </div>
                </div>
            @endif

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
            <!-- Islamic (Hijri) Calendar Box -->
            <div class="card border-0 p-3 mb-4 shadow-sm position-relative overflow-hidden" style="background: linear-gradient(135deg, #064e3b 0%, #046c4e 50%, #065f46 100%); border-radius: 1rem; color: #ffffff;">
                <!-- Subtle corner radial glow -->
                <div class="position-absolute" style="top: -40px; right: -40px; width: 120px; height: 120px; background: radial-gradient(circle, rgba(251, 191, 36, 0.25) 0%, transparent 70%); pointer-events: none;"></div>

                <!-- Card Header -->
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-white border-opacity-10 position-relative" style="z-index: 1;">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(251, 191, 36, 0.15); border: 1px solid rgba(251, 191, 36, 0.35); color: #fbbf24;">
                            <i class="bi bi-moon-stars-fill fs-6"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-white small lh-sm">Islamic Calendar</div>
                            <div class="text-white-50 font-arabic" style="font-size: 0.8rem; line-height: 1;">التقويم الهجري</div>
                        </div>
                    </div>
                    <span class="badge rounded-pill fw-semibold px-2 py-1" style="background-color: rgba(251, 191, 36, 0.18); color: #fde68a; border: 1px solid rgba(251, 191, 36, 0.35); font-size: 0.72rem;">
                        <i class="bi bi-circle-fill me-1" style="font-size: 0.45rem; color: #34d399;"></i> Today
                    </span>
                </div>

                <!-- Main Date Display Box -->
                <div class="p-3 rounded-3 position-relative" style="background: rgba(0, 0, 0, 0.2); border: 1px solid rgba(255, 255, 255, 0.12); z-index: 1;">
                    <!-- Day Name Badge & Arabic Day -->
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge rounded-pill px-2 py-1" style="background: rgba(255, 255, 255, 0.15); color: #ffffff; font-size: 0.72rem;">
                            {{ $hijriDate['day_name_en'] ?? 'Wednesday' }}
                        </span>
                        <span class="font-arabic fw-semibold" style="color: #fbbf24; font-size: 1.05rem;">
                            {{ $hijriDate['day_name_ar'] ?? 'الأَرْبِعَاء' }}
                        </span>
                    </div>

                    <!-- Primary Arabic Hijri Date -->
                    <div class="font-arabic fw-bold text-center py-2" dir="rtl" style="font-size: 1.85rem; color: #fef3c7; text-shadow: 0 2px 4px rgba(0,0,0,0.3); line-height: 1.3;">
                        {{ $hijriDate['formatted_ar'] ?? '' }}
                    </div>

                    <!-- English Hijri Date -->
                    <div class="text-center fw-bold small text-white pb-2 mb-2 border-bottom border-white border-opacity-10" style="letter-spacing: 0.3px;">
                        {{ $hijriDate['formatted_en'] ?? '' }}
                    </div>

                    <!-- Gregorian Reference Footer -->
                    <div class="d-flex align-items-center justify-content-center gap-1 text-white-50" style="font-size: 0.75rem;">
                        <i class="bi bi-calendar3" style="color: #fbbf24;"></i>
                        <span>{{ $hijriDate['gregorian_formatted'] ?? '' }}</span>
                    </div>
                </div>
            </div>

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
