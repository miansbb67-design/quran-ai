@extends('layouts.customer')

@section('title', 'Authentic Daily Hadith & Sunnah - Bayan AI Quran')
@section('page_title', 'Daily Hadith & Sunnah Collection')

@section('content')
<div class="container-fluid p-0">

    <!-- Hero Header Banner -->
    <div class="card card-glass border-0 p-4 mb-4 bg-emerald-grad text-white rounded-4 shadow-sm position-relative overflow-hidden">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <span class="badge badge-gold mb-2"><i class="bi bi-book-half me-1"></i> Authentic Prophetic Traditions</span>
                <h2 class="fw-bold mb-2">Daily Hadith & Sunnah of the Prophet ﷺ</h2>
                <p class="text-white-50 mb-0">
                    Nurture your daily routine with wisdom from Sahih al-Bukhari, Sahih Muslim, and authentic Sunnah collections. A new Hadith is featured every single day.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end text-center d-none d-lg-block">
                <div class="font-arabic fs-2 text-gold opacity-75" dir="rtl">
                    « خَيْرُكُمْ مَنْ تَعَلَّمَ الْقُرْآنَ وَعَلَّمَهُ »
                </div>
                <small class="text-white-50">Sahih al-Bukhari 5027</small>
            </div>
        </div>
    </div>

    <!-- Featured Today's Hadith Spotlight -->
    @if(isset($dailyHadith) && $dailyHadith)
        <div class="card card-glass border-0 p-4 mb-4 shadow-sm" style="border-left: 5px solid #10b981 !important; background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(240,253,244,0.85) 100%);">
            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1 shadow-sm" style="background: #065f46; color: #ffffff;">
                        <i class="bi bi-star-fill text-gold"></i> Today's Featured Hadith &bull; حدیثِ آج کا
                    </span>
                    <span class="badge bg-gold text-dark rounded-pill px-2 py-1 fw-semibold small">
                        {{ $dailyHadith->grade ?? 'Sahih' }}
                    </span>
                    @if($dailyHadith->chapter)
                        <span class="badge bg-light text-secondary border rounded-pill px-2 py-1 small">
                            {{ $dailyHadith->chapter }}
                        </span>
                    @endif
                </div>
                <button type="button" class="btn btn-sm btn-outline-emerald rounded-pill px-3 btn-copy-hadith" data-text="{{ $dailyHadith->arabic_text }} - {{ $dailyHadith->english_translation }} ({{ $dailyHadith->source_book }})">
                    <i class="bi bi-clipboard me-1"></i> Copy Hadith
                </button>
            </div>

            <!-- Arabic Content -->
            <div class="p-3 p-md-4 bg-white rounded-3 border mb-3 text-center shadow-2xs">
                <div class="font-arabic fs-2 text-emerald fw-bold mb-2 lh-lg" dir="rtl" style="line-height: 2.1;">
                    « {{ $dailyHadith->arabic_text }} »
                </div>
            </div>

            <!-- Urdu Translation -->
            @if(!empty($dailyHadith->urdu_translation))
                <div class="p-3 bg-light rounded-3 mb-2 text-end text-dark font-urdu" dir="rtl" style="font-size: 1.15rem; line-height: 1.9;">
                    {{ $dailyHadith->urdu_translation }}
                </div>
            @endif

            <!-- English Translation -->
            <div class="text-secondary fst-italic mb-3 px-1" style="font-size: 1.02rem; line-height: 1.7;">
                "{{ $dailyHadith->english_translation }}"
            </div>

            <!-- Footer Details -->
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-3 border-top text-muted small">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-person-circle text-emerald fs-6"></i>
                    <span><strong>Narrated by:</strong> {{ $dailyHadith->narrator }}</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-journal-bookmark text-gold fs-6"></i>
                    <span class="badge bg-white text-dark border px-3 py-2 fw-semibold">{{ $dailyHadith->source_book }}</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Search & Filter Controls -->
    <div class="card card-glass border-0 p-3 mb-4 shadow-sm">
        <form method="GET" action="{{ route('hadith.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search by topic, keyword, or narrator..." value="{{ request('search') }}">
                    @if(request('search'))
                        <a href="{{ route('hadith.index', ['category' => request('category')]) }}" class="btn btn-outline-secondary">Clear</a>
                    @endif
                </div>
            </div>

            <div class="col-md-7 d-flex justify-content-md-end align-items-center gap-1 flex-wrap">
                @foreach($categories as $key => $label)
                    <a href="{{ route('hadith.index', ['category' => $key, 'search' => request('search')]) }}" class="btn btn-sm rounded-pill px-3 {{ $selectedCategory === $key ? 'btn-emerald text-white fw-bold shadow-sm' : 'btn-light border text-secondary' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </form>
    </div>

    <!-- Hadith Cards Grid -->
    <div class="row g-4 mb-4">
        @forelse($hadiths as $hadith)
            <div class="col-md-6 col-xl-4">
                <div class="card card-glass border-0 p-4 h-100 shadow-sm d-flex flex-column transition hover-shadow" style="border-top: 3px solid #10b981 !important;">
                    <!-- Card Top Header -->
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="badge bg-emerald-light text-emerald rounded-pill px-2 py-1 small fw-semibold">
                            {{ ucfirst($hadith->category) }}
                        </span>
                        <div class="d-flex align-items-center gap-1">
                            <span class="badge bg-gold-light text-dark rounded-pill px-2 py-1" style="font-size: 0.72rem;">
                                {{ $hadith->grade ?? 'Sahih' }}
                            </span>
                            <button type="button" class="btn btn-sm btn-light rounded-circle p-1 btn-copy-hadith" title="Copy Hadith" data-text="{{ $hadith->arabic_text }} - {{ $hadith->english_translation }} ({{ $hadith->source_book }})" style="width: 28px; height: 28px;">
                                <i class="bi bi-clipboard" style="font-size: 0.8rem;"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Arabic Text -->
                    <div class="p-3 bg-white rounded-3 border mb-3 text-center flex-grow-0">
                        <div class="font-arabic text-emerald fw-bold" dir="rtl" style="font-size: 1.25rem; line-height: 1.9;">
                            « {{ $hadith->arabic_text }} »
                        </div>
                    </div>

                    <!-- Urdu Translation -->
                    @if(!empty($hadith->urdu_translation))
                        <div class="p-2 px-3 bg-light rounded-3 mb-2 text-end text-dark font-urdu small" dir="rtl" style="line-height: 1.8;">
                            {{ $hadith->urdu_translation }}
                        </div>
                    @endif

                    <!-- English Translation -->
                    <div class="text-secondary small fst-italic mb-3 flex-grow-1" style="line-height: 1.6;">
                        "{{ $hadith->english_translation }}"
                    </div>

                    <!-- Card Footer Reference -->
                    <div class="pt-2 border-top mt-auto d-flex align-items-center justify-content-between text-muted" style="font-size: 0.75rem;">
                        <span class="text-truncate me-2"><i class="bi bi-person me-1 text-emerald"></i>{{ $hadith->narrator }}</span>
                        <span class="badge bg-light text-dark border">{{ $hadith->source_book }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-warning text-center rounded-4 py-5 shadow-sm">
                    <i class="bi bi-search fs-1 text-warning mb-2 d-block"></i>
                    <h5>No Hadiths Found</h5>
                    <p class="text-secondary small">No prophetic traditions matched your filter or search criteria.</p>
                    <a href="{{ route('hadith.index') }}" class="btn btn-emerald rounded-pill px-4 mt-2">View All Hadiths</a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination Links -->
    @if($hadiths->hasPages())
        <div class="d-flex justify-content-center mb-4">
            {{ $hadiths->links('pagination::bootstrap-5') }}
        </div>
    @endif

</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Quick copy hadith to clipboard
        $('.btn-copy-hadith').on('click', function() {
            var btn = $(this);
            var text = btn.data('text');

            if (navigator.clipboard) {
                navigator.clipboard.writeText(text).then(function() {
                    var originalHtml = btn.html();
                    btn.html('<i class="bi bi-check2 text-success"></i> Copied!');
                    setTimeout(function() {
                        btn.html(originalHtml);
                    }, 2000);
                });
            }
        });
    });
</script>
@endpush
@endsection
