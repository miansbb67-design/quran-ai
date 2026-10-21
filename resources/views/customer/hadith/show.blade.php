@extends('layouts.customer')

@section('title', 'Hadith Details - ' . ($hadith->source_book ?? 'Authentic Sunnah'))
@section('page_title', 'Prophetic Tradition (Hadith)')

@section('content')
<div class="container-fluid p-0">

    <!-- Back to collection button -->
    <div class="mb-3">
        <a href="{{ route('hadith.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Back to All Hadiths
        </a>
    </div>

    <!-- Main Hadith Card -->
    <div class="card card-glass border-0 p-4 p-md-5 mb-4 shadow-sm" style="border-top: 4px solid #10b981 !important;">
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-emerald-light text-emerald rounded-pill px-3 py-2 fw-semibold">
                    {{ ucfirst($hadith->category) }}
                </span>
                <span class="badge bg-gold text-dark rounded-pill px-3 py-2 fw-semibold">
                    {{ $hadith->grade ?? 'Sahih' }}
                </span>
                @if($hadith->chapter)
                    <span class="badge bg-light text-dark border rounded-pill px-3 py-2">
                        {{ $hadith->chapter }}
                    </span>
                @endif
            </div>

            <button type="button" class="btn btn-sm btn-outline-emerald rounded-pill px-3 btn-copy-hadith" data-text="{{ $hadith->arabic_text }} - {{ $hadith->english_translation }} ({{ $hadith->source_book }})">
                <i class="bi bi-clipboard me-1"></i> Copy Hadith
            </button>
        </div>

        <!-- Arabic Display -->
        <div class="p-4 p-md-5 bg-white rounded-4 border mb-4 text-center shadow-2xs">
            <div class="font-arabic fs-1 text-emerald fw-bold lh-lg" dir="rtl" style="line-height: 2.2;">
                « {{ $hadith->arabic_text }} »
            </div>
        </div>

        <!-- Urdu Translation -->
        @if(!empty($hadith->urdu_translation))
            <div class="p-3 p-md-4 bg-light rounded-3 mb-3 text-end text-dark font-urdu border" dir="rtl" style="font-size: 1.25rem; line-height: 2;">
                {{ $hadith->urdu_translation }}
            </div>
        @endif

        <!-- English Translation -->
        <div class="text-secondary fst-italic mb-4 px-2" style="font-size: 1.15rem; line-height: 1.8;">
            "{{ $hadith->english_translation }}"
        </div>

        <!-- Metadata Footer -->
        <div class="p-3 bg-light rounded-3 d-flex align-items-center justify-content-between flex-wrap gap-2 text-muted small">
            <div>
                <i class="bi bi-person-fill text-emerald me-1"></i>
                <strong>Narrator:</strong> {{ $hadith->narrator }}
            </div>
            <div>
                <i class="bi bi-book-half text-gold me-1"></i>
                <strong>Source:</strong> <span class="badge bg-white text-dark border px-2 py-1">{{ $hadith->source_book }}</span>
            </div>
        </div>
    </div>

    <!-- Related Hadiths -->
    @if(isset($relatedHadiths) && $relatedHadiths->count() > 0)
        <div class="mb-4">
            <h5 class="fw-bold text-dark mb-3">More on {{ ucfirst($hadith->category) }}</h5>
            <div class="row g-3">
                @foreach($relatedHadiths as $rel)
                    <div class="col-md-4">
                        <div class="card card-glass border-0 p-3 h-100 shadow-2xs">
                            <div class="font-arabic text-emerald fw-bold mb-2 small text-truncate" dir="rtl">
                                « {{ $rel->arabic_text }} »
                            </div>
                            <p class="text-secondary small fst-italic mb-2 text-truncate">
                                "{{ $rel->english_translation }}"
                            </p>
                            <a href="{{ route('hadith.show', $rel->id) }}" class="btn btn-sm btn-link p-0 text-emerald mt-auto text-decoration-none">
                                View Hadith <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('.btn-copy-hadith').on('click', function() {
            var btn = $(this);
            var text = btn.data('text');
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text).then(function() {
                    var originalHtml = btn.html();
                    btn.html('<i class="bi bi-check2 text-success"></i> Copied!');
                    setTimeout(function() { btn.html(originalHtml); }, 2000);
                });
            }
        });
    });
</script>
@endpush
@endsection
