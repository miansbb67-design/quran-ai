@extends('layouts.customer')

@section('title', 'Holy Quran Reader - Bayan AI Quran')
@section('page_title', 'The Holy Quran')

@section('content')
<div class="container-fluid p-0">

    <!-- Top Banner & Search -->
    <div class="card card-glass border-0 p-4 mb-4 bg-emerald-grad text-white rounded-4 shadow-sm">
        <div class="row align-items-center g-3">
            <div class="col-lg-6">
                <h3 class="fw-bold mb-2">114 Surahs of the Holy Quran</h3>
                <p class="text-white-50 mb-0">Browse chapters in authentic Uthmani script with verse-by-verse audio and translation.</p>
            </div>
            <div class="col-lg-6">
                <div class="input-group input-group-lg shadow-sm">
                    <span class="input-group-text bg-white border-0 text-emerald"><i class="bi bi-search"></i></span>
                    <input type="text" id="surahSearch" class="form-control border-0" placeholder="Search by name (e.g. Al-Fatihah, Baqarah, Yasin)...">
                </div>
            </div>
        </div>
    </div>

    @if(isset($error))
        <div class="alert alert-warning rounded-4 shadow-sm mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $error }}
        </div>
    @endif

    <!-- Surah Filter Tabs -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div class="btn-group rounded-pill p-1 bg-white border shadow-sm" role="group">
            <button type="button" class="btn btn-sm btn-emerald rounded-pill px-3 active filter-btn" data-filter="all">All (114)</button>
            <button type="button" class="btn btn-sm btn-light rounded-pill px-3 filter-btn" data-filter="Meccan">Meccan</button>
            <button type="button" class="btn btn-sm btn-light rounded-pill px-3 filter-btn" data-filter="Medinan">Medinan</button>
        </div>

        <div class="text-muted small">
            Last read: <a href="{{ route('quran.show', $readingProgress->last_surah_number) }}" class="fw-semibold text-emerald text-decoration-none">Surah #{{ $readingProgress->last_surah_number }} (Ayah {{ $readingProgress->last_ayah_number }})</a>
        </div>
    </div>

    <!-- Surahs Grid -->
    <div class="row g-3" id="surahGrid">
        @if(is_array($surahs))
            @foreach($surahs as $s)
                <div class="col-md-6 col-xl-4 surah-card-wrapper" 
                     data-name="{{ strtolower($s['englishName'] . ' ' . $s['name'] . ' ' . $s['englishNameTranslation']) }}"
                     data-revelation="{{ $s['revelationType'] }}">
                    <div class="card card-glass border-0 p-3 h-100 hover-shadow transition">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge badge-emerald fw-bold" style="width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%;">
                                    {{ $s['number'] }}
                                </span>
                                <div>
                                    <h6 class="fw-bold text-dark mb-0">{{ $s['englishName'] }}</h6>
                                    <small class="text-muted">{{ $s['englishNameTranslation'] }}</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="font-arabic fs-4 text-emerald lh-1">{{ $s['name'] }}</div>
                                <span class="badge bg-light text-muted border text-uppercase" style="font-size: 0.65rem;">
                                    {{ $s['revelationType'] }} &bull; {{ $s['numberOfAyahs'] }} Ayahs
                                </span>
                            </div>
                        </div>

                        <hr class="my-2 text-muted">

                        <div class="d-flex justify-content-between align-items-center pt-1">
                            <a href="{{ route('quran.show', $s['number']) }}" class="btn btn-sm btn-emerald rounded-pill px-3">
                                <i class="bi bi-book me-1"></i> Read Surah
                            </a>
                            <a href="{{ route('ai-teacher.index', ['surah' => $s['number']]) }}" class="btn btn-sm btn-outline-warning rounded-pill px-3 text-dark fw-semibold" title="Practice recitation with AI">
                                <i class="bi bi-mic-fill text-warning me-1"></i> AI Practice
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Search filter
        $('#surahSearch').on('keyup', function() {
            var val = $(this).val().toLowerCase();
            $('.surah-card-wrapper').each(function() {
                var name = $(this).data('name');
                if (name.indexOf(val) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Revelation type filter
        $('.filter-btn').on('click', function() {
            $('.filter-btn').removeClass('btn-emerald active').addClass('btn-light');
            $(this).removeClass('btn-light').addClass('btn-emerald active');

            var filter = $(this).data('filter');
            if (filter === 'all') {
                $('.surah-card-wrapper').show();
            } else {
                $('.surah-card-wrapper').each(function() {
                    if ($(this).data('revelation') === filter) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }
        });
    });
</script>
@endpush
@endsection
