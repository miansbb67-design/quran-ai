@extends('layouts.customer')

@section('title', 'My Saved Bookmarks - Bayan AI Quran')
@section('page_title', 'Saved Quran Verses')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold text-emerald mb-1">My Bookmarks</h4>
            <small class="text-muted">Quick access to verses and passages you saved for review</small>
        </div>
        <a href="{{ route('quran.index') }}" class="btn btn-emerald rounded-pill px-4">
            <i class="bi bi-book me-1"></i> Browse Quran
        </a>
    </div>

    @if($bookmarks->count() > 0)
        <div class="row g-3">
            @foreach($bookmarks as $b)
                <div class="col-md-6 col-lg-4" id="bookmark-card-{{ $b->id }}">
                    <div class="card card-glass border-0 p-4 h-100 position-relative shadow-sm">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="badge badge-gold px-3 py-2 rounded-pill">
                                Surah {{ $b->surah_number }} : Ayah {{ $b->ayah_number }}
                            </span>
                            <button class="btn btn-sm btn-light border rounded-circle text-danger remove-bm-btn" 
                                    data-surah="{{ $b->surah_number }}" 
                                    data-ayah="{{ $b->ayah_number }}" 
                                    data-card="bookmark-card-{{ $b->id }}"
                                    title="Remove bookmark">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>

                        <p class="text-secondary small mb-3">
                            {{ $b->note ?? 'No custom notes added.' }}
                        </p>

                        <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                            <small class="text-muted">{{ $b->created_at->format('M d, Y') }}</small>
                            <div class="d-flex gap-2">
                                <a href="{{ route('quran.show', $b->surah_number) }}#ayah-{{ $b->ayah_number }}" class="btn btn-sm btn-emerald rounded-pill px-3">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> Read
                                </a>
                                <a href="{{ route('ai-teacher.index', ['surah' => $b->surah_number, 'ayah' => $b->ayah_number]) }}" class="btn btn-sm btn-outline-warning text-dark rounded-pill px-2" title="Practice recitation">
                                    <i class="bi bi-mic-fill text-warning"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $bookmarks->links() }}
        </div>
    @else
        <div class="card card-glass border-0 p-5 text-center shadow-sm">
            <i class="bi bi-bookmark-star text-muted fs-1 mb-3 d-block"></i>
            <h5 class="fw-bold text-dark mb-2">No Bookmarks Saved Yet</h5>
            <p class="text-secondary small mb-4 max-w-500 mx-auto">
                While reading any Surah, click the star/bookmark icon on any Ayah to save it here for quick contemplation and practice.
            </p>
            <a href="{{ route('quran.index') }}" class="btn btn-emerald rounded-pill px-5 mx-auto">
                Start Reading the Quran
            </a>
        </div>
    @endif
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('.remove-bm-btn').on('click', function() {
            var btn = $(this);
            var surah = btn.data('surah');
            var ayah = btn.data('ayah');
            var cardId = btn.data('card');

            if (confirm('Remove this verse from bookmarks?')) {
                $.post("{{ route('quran.bookmark.toggle') }}", {
                    surah_number: surah,
                    ayah_number: ayah,
                    _token: "{{ csrf_token() }}"
                }, function(res) {
                    $('#' + cardId).fadeOut(300, function() { $(this).remove(); });
                });
            }
        });
    });
</script>
@endpush
@endsection
