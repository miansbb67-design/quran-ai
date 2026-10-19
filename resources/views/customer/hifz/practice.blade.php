@extends('layouts.customer')

@section('title', 'Hifz Practice - ' . ($surah['englishName'] ?? 'Surah ' . $surahNumber))
@section('page_title', 'Hifz Gym: ' . ($surah['englishName'] ?? 'Surah ' . $surahNumber))

@section('content')
<div class="container-fluid p-0">

    <!-- Top Gym Controller Bar -->
    <div class="card card-glass border-0 p-3 mb-4 shadow-sm">
        <div class="row align-items-center g-3">
            <div class="col-md-4">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('hifz.index') }}" class="btn btn-sm btn-outline-secondary rounded-circle" title="Back to Hifz overview" style="width: 36px; height: 36px;">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">{{ $surah['englishName'] ?? 'Surah ' . $surahNumber }} ({{ $surah['name'] ?? '' }})</h6>
                        <small class="text-muted">{{ $surah['numberOfAyahs'] ?? count($surah['ayahs'] ?? []) }} Verses</small>
                    </div>
                </div>
            </div>

            <!-- Mode Selector -->
            <div class="col-md-5 text-center">
                <div class="btn-group btn-group-sm bg-light rounded-pill border p-1" role="group">
                    <button type="button" class="btn btn-light rounded-pill px-3 active mode-btn" data-mode="show">
                        <i class="bi bi-eye me-1"></i> Show Text
                    </button>
                    <button type="button" class="btn btn-light rounded-pill px-3 mode-btn" data-mode="mask-words">
                        <i class="bi bi-puzzle me-1"></i> Mask Words
                    </button>
                    <button type="button" class="btn btn-light rounded-pill px-3 mode-btn" data-mode="hide-all">
                        <i class="bi bi-eye-slash me-1"></i> Blind Recall
                    </button>
                </div>
            </div>

            <!-- Surah Switcher -->
            <div class="col-md-3 text-md-end">
                <select class="form-select form-select-sm fw-semibold border-emerald" onchange="location = this.value;">
                    @if(is_array($surahs))
                        @foreach($surahs as $s)
                            <option value="{{ route('hifz.practice', $s['number']) }}" {{ $s['number'] == $surahNumber ? 'selected' : '' }}>
                                {{ $s['number'] }}. {{ $s['englishName'] }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
    </div>

    <!-- Instructions Tip -->
    <div class="alert alert-info border-0 rounded-4 shadow-sm py-2 px-3 mb-4 d-flex align-items-center justify-content-between small">
        <div>
            <i class="bi bi-lightbulb-fill text-warning me-2"></i>
            <strong>Active Recall Tip:</strong> Switch to <em>"Mask Words"</em> or <em>"Blind Recall"</em>. Try reciting each verse from memory, click any hidden word if you get stuck, and rate your retention.
        </div>
        <span class="badge bg-white text-info border">Hifz Gym</span>
    </div>

    <!-- Ayahs List for Memorization -->
    <div id="hifzGymContainer">
        @if(isset($surah['ayahs']) && is_array($surah['ayahs']))
            @foreach($surah['ayahs'] as $index => $ayah)
                @php
                    $ayahNum = $ayah['numberInSurah'];
                    $progressItem = $memProgress[$ayahNum] ?? null;
                    $status = $progressItem ? $progressItem->status : 'not_started';
                    $words = explode(' ', trim($ayah['text']));
                @endphp

                <div class="card card-glass border-0 p-4 mb-3 hifz-ayah-card shadow-sm" id="hifz-ayah-{{ $ayahNum }}" data-ayah="{{ $ayahNum }}">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge badge-emerald rounded-pill px-3 py-2 fw-bold">
                                Ayah {{ $ayahNum }}
                            </span>
                            <span class="badge hifz-status-badge status-{{ $status }}" id="badge-status-{{ $ayahNum }}">
                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                            </span>
                        </div>

                        <!-- Test with AI Recite Button -->
                        <div class="d-flex gap-2 align-items-center">
                            <a href="{{ route('ai-teacher.index', ['surah' => $surahNumber, 'ayah' => $ayahNum]) }}" class="btn btn-sm btn-outline-warning text-dark fw-semibold rounded-pill px-3" title="Test recitation with voice AI">
                                <i class="bi bi-mic-fill text-warning me-1"></i> AI Verify
                            </a>
                        </div>
                    </div>

                    <!-- Arabic Verse Stage with Maskable Words -->
                    <div class="quran-verse-text mb-3 arabic-verse-line hifz-text-area" id="verse-text-{{ $ayahNum }}" style="font-size: 2.1rem;">
                        @foreach($words as $w)
                            <span class="hifz-word" title="Click to reveal/hide">{{ $w }}</span>
                        @endforeach
                        <span class="ayah-end-marker">{{ $ayahNum }}</span>
                    </div>

                    <!-- Self-Evaluation Grading Row -->
                    <div class="pt-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <small class="text-muted fw-semibold">How well did you recall this verse?</small>

                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 rate-hifz-btn" data-surah="{{ $surahNumber }}" data-ayah="{{ $ayahNum }}" data-status="learning">
                                <i class="bi bi-x-circle me-1"></i> Hard / Repeat
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-warning text-dark rounded-pill px-3 rate-hifz-btn" data-surah="{{ $surahNumber }}" data-ayah="{{ $ayahNum }}" data-status="practicing">
                                <i class="bi bi-dash-circle me-1"></i> Good / Practicing
                            </button>
                            <button type="button" class="btn btn-sm btn-emerald rounded-pill px-3 rate-hifz-btn" data-surah="{{ $surahNumber }}" data-ayah="{{ $ayahNum }}" data-status="memorized">
                                <i class="bi bi-check-circle me-1"></i> Memorized!
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>

@push('styles')
<style>
    .hifz-word {
        display: inline-block;
        padding: 0 4px;
        margin: 0 2px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    /* Mask Words Mode */
    .mode-mask-words .hifz-word {
        background-color: #cbd5e1;
        color: transparent;
        border-radius: 6px;
        user-select: none;
    }

    .mode-mask-words .hifz-word.revealed {
        background-color: #ecfdf5;
        color: var(--primary-emerald);
        border-bottom: 2px solid var(--primary-emerald);
    }

    /* Blind Recall Mode */
    .mode-hide-all .hifz-text-area {
        filter: blur(8px);
        user-select: none;
        transition: filter 0.3s ease;
        cursor: pointer;
    }

    .mode-hide-all .hifz-text-area.revealed {
        filter: none;
    }

    .status-memorized { background-color: #dcfce7; color: #166534; border: 1px solid #86efac; }
    .status-practicing { background-color: #fef9c3; color: #854d0e; border: 1px solid #fde047; }
    .status-learning { background-color: #dbeafe; color: #1e40af; border: 1px solid #93c5fd; }
    .status-not_started { background-color: #f3f4f6; color: #4b5563; border: 1px solid #e5e7eb; }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Mode switching
        $('.mode-btn').on('click', function() {
            $('.mode-btn').removeClass('btn-emerald active').addClass('btn-light');
            $(this).removeClass('btn-light').addClass('btn-emerald active');

            var mode = $(this).data('mode');
            $('#hifzGymContainer').removeClass('mode-mask-words mode-hide-all');

            if (mode === 'mask-words') {
                $('#hifzGymContainer').addClass('mode-mask-words');
                $('.hifz-word').removeClass('revealed');
            } else if (mode === 'hide-all') {
                $('#hifzGymContainer').addClass('mode-hide-all');
                $('.hifz-text-area').removeClass('revealed');
            }
        });

        // Click word to reveal in mask-words mode
        $(document).on('click', '.mode-mask-words .hifz-word', function() {
            $(this).toggleClass('revealed');
        });

        // Click text area to reveal in hide-all mode
        $(document).on('click', '.mode-hide-all .hifz-text-area', function() {
            $(this).toggleClass('revealed');
        });

        // Self-evaluation button rating
        $('.rate-hifz-btn').on('click', function() {
            var btn = $(this);
            var surah = btn.data('surah');
            var ayah = btn.data('ayah');
            var status = btn.data('status');

            $.post("{{ route('hifz.status') }}", {
                surah_number: surah,
                ayah_number: ayah,
                status: status,
                _token: "{{ csrf_token() }}"
            }, function(res) {
                var badge = $('#badge-status-' + ayah);
                badge.removeClass('status-memorized status-practicing status-learning status-not_started')
                     .addClass('status-' + status)
                     .text(status.charAt(0).toUpperCase() + status.slice(1).replace('_', ' '));

                // Visual flash
                $('#hifz-ayah-' + ayah).css('border', '2px solid #22c55e');
                setTimeout(function() {
                    $('#hifz-ayah-' + ayah).css('border', 'none');
                }, 1000);
            });
        });
    });
</script>
@endpush
@endsection
