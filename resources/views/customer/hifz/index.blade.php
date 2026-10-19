@extends('layouts.customer')

@section('title', 'Hifz Memorization Companion - Bayan AI Quran')
@section('page_title', 'Hifz & Memorization Journey')

@section('content')
<div class="container-fluid p-0">

    <!-- Top Banner -->
    <div class="card card-glass border-0 p-4 mb-4 bg-emerald-grad text-white rounded-4 shadow-sm">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <span class="badge badge-gold mb-2"><i class="bi bi-clock-history me-1"></i> Spaced Repetition Engine</span>
                <h3 class="fw-bold mb-1">Quranic Memorization (Hifz)</h3>
                <p class="text-white-50 mb-0 small">Commit verses to your heart using active recall and spaced intervals so you never forget what you have learned.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="{{ route('hifz.practice', 1) }}" class="btn btn-gold rounded-pill px-4 shadow-sm">
                    <i class="bi bi-play-fill me-1"></i> Practice Al-Fatihah
                </a>
            </div>
        </div>
    </div>

    <!-- Memorization Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card card-glass border-0 p-4 h-100 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Fully Memorized</span>
                    <div class="rounded-circle bg-success bg-opacity-10 text-success p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-check-circle-fill fs-5"></i>
                    </div>
                </div>
                <div class="display-6 fw-bold text-success mb-1">{{ $stats['total_memorized'] }}</div>
                <small class="text-muted">Ayahs mastered and scheduled for periodic spaced review</small>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-glass border-0 p-4 h-100 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Practicing (Consolidating)</span>
                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-hourglass-split fs-5"></i>
                    </div>
                </div>
                <div class="display-6 fw-bold text-warning mb-1">{{ $stats['total_practicing'] }}</div>
                <small class="text-muted">Ayahs currently in active repetition cycles</small>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-glass border-0 p-4 h-100 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Currently Learning</span>
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-book-half fs-5"></i>
                    </div>
                </div>
                <div class="display-6 fw-bold text-primary mb-1">{{ $stats['total_learning'] }}</div>
                <small class="text-muted">New Ayahs initiated this week</small>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Upcoming Spaced Revisions -->
        <div class="col-lg-6">
            <div class="card card-glass border-0 p-4 shadow-sm h-100">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-calendar-check text-emerald me-2"></i>Upcoming Revisions</h5>
                    <span class="badge badge-emerald">Spaced Review</span>
                </div>

                @if($upcomingRevisions->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($upcomingRevisions as $rev)
                            <div class="list-group-item px-0 py-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <strong class="d-block text-dark">Surah #{{ $rev->surah_number }} (Ayah {{ $rev->start_ayah }}-{{ $rev->end_ayah }})</strong>
                                    <small class="text-muted">
                                        <i class="bi bi-clock me-1"></i> Scheduled: {{ $rev->scheduled_date->format('M d, Y') }}
                                        @if($rev->scheduled_date->isPast())
                                            <span class="badge bg-danger-subtle text-danger ms-1">Due Now</span>
                                        @endif
                                    </small>
                                </div>
                                <a href="{{ route('hifz.practice', $rev->surah_number) }}" class="btn btn-sm btn-outline-emerald rounded-pill px-3">
                                    Revise Now
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-check2-all text-success fs-1 mb-2 d-block"></i>
                        <h6 class="fw-bold text-dark">All Caught Up!</h6>
                        <p class="text-secondary small mb-3">No revisions due at this moment. Start memorizing a new Surah below.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Memorization Activity -->
        <div class="col-lg-6">
            <div class="card card-glass border-0 p-4 shadow-sm h-100">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-activity text-emerald me-2"></i>Recent Memory Updates</h5>
                    <span class="badge bg-light text-muted border">Latest</span>
                </div>

                @if($recentProgress->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentProgress as $p)
                            <div class="list-group-item px-0 py-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <strong class="d-block text-dark">Surah {{ $p->surah_number }} : Ayah {{ $p->ayah_number }}</strong>
                                    <small class="text-muted">Practiced {{ $p->last_practiced_at ? $p->last_practiced_at->diffForHumans() : 'Recently' }} &bull; {{ $p->total_practice_count }} repetitions</small>
                                </div>
                                <div>
                                    @if($p->status === 'memorized')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Memorized</span>
                                    @elseif($p->status === 'practicing')
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill">Practicing</span>
                                    @else
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">Learning</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4 text-muted small">
                        Start your first memorization session below to populate your revision schedule.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Surah Directory for Hifz -->
    <div class="card card-glass border-0 p-4 shadow-sm">
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold text-dark mb-1">Select Surah for Memorization</h5>
                <small class="text-muted">Launch the interactive Hide & Reveal Memorization Gym for any chapter</small>
            </div>
            <input type="text" id="hifzSurahSearch" class="form-control form-control-sm w-auto" placeholder="Search Surah...">
        </div>

        <div class="row g-3" id="hifzSurahsList">
            @if(is_array($surahs))
                @foreach($surahs as $s)
                    <div class="col-md-6 col-lg-4 hifz-surah-item" data-name="{{ strtolower($s['englishName'] . ' ' . $s['name']) }}">
                        <div class="p-3 bg-white rounded-3 border d-flex align-items-center justify-content-between hover-shadow transition">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge badge-emerald fw-bold">{{ $s['number'] }}</span>
                                <div>
                                    <strong class="d-block text-dark">{{ $s['englishName'] }}</strong>
                                    <small class="text-muted">{{ $s['numberOfAyahs'] }} Ayahs</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="font-arabic fs-5 text-emerald mb-1">{{ $s['name'] }}</div>
                                <a href="{{ route('hifz.practice', $s['number']) }}" class="btn btn-sm btn-outline-emerald rounded-pill px-3">
                                    Memorize
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#hifzSurahSearch').on('keyup', function() {
            var val = $(this).val().toLowerCase();
            $('.hifz-surah-item').each(function() {
                var name = $(this).data('name');
                if (name.indexOf(val) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    });
</script>
@endpush
@endsection
