@extends('layouts.customer')

@section('title', ($surah['englishName'] ?? 'Surah ' . $surahNumber) . ' - Holy Quran Reader')
@section('page_title', ($surah['englishName'] ?? 'Surah ' . $surahNumber) . ' (' . ($surah['name'] ?? '') . ')')

@section('content')
<div class="container-fluid p-0">

    <!-- Top Navigation Toolbar -->
    <div class="card card-glass border-0 p-3 mb-4 shadow-sm">
        <div class="row align-items-center g-3">
            <!-- Surah Switcher -->
            <div class="col-md-5">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('quran.show', max(1, $surahNumber - 1)) }}" class="btn btn-sm btn-outline-emerald rounded-circle {{ $surahNumber <= 1 ? 'disabled' : '' }}" title="Previous Surah" style="width: 36px; height: 36px;">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                    
                    <select id="surahSelect" class="form-select form-select-sm fw-semibold border-emerald" onchange="location = this.value;">
                        @if(is_array($surahs))
                            @foreach($surahs as $s)
                                <option value="{{ route('quran.show', $s['number']) }}" {{ $s['number'] == $surahNumber ? 'selected' : '' }}>
                                    {{ $s['number'] }}. {{ $s['englishName'] }} ({{ $s['name'] }})
                                </option>
                            @endforeach
                        @else
                            <option value="{{ route('quran.show', $surahNumber) }}">Surah {{ $surahNumber }}</option>
                        @endif
                    </select>

                    <a href="{{ route('quran.show', min(114, $surahNumber + 1)) }}" class="btn btn-sm btn-outline-emerald rounded-circle {{ $surahNumber >= 114 ? 'disabled' : '' }}" title="Next Surah" style="width: 36px; height: 36px;">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
            </div>

            <!-- Font Size & View Adjusters -->
            <div class="col-md-7 d-flex justify-content-md-end align-items-center gap-2 flex-wrap">
                <div class="btn-group btn-group-sm bg-light rounded-pill border p-1" role="group">
                    <button type="button" class="btn btn-light rounded-pill" id="fontDecrease" title="Decrease font size">A-</button>
                    <button type="button" class="btn btn-light rounded-pill" id="fontReset" title="Reset font size">Reset</button>
                    <button type="button" class="btn btn-light rounded-pill" id="fontIncrease" title="Increase font size">A+</button>
                </div>

                <!-- Continuous Play All Audio with Resume Memory -->
                <div class="d-inline-flex align-items-center gap-1">
                    <button type="button" id="btnPlayFullSurah" class="btn btn-sm btn-gold rounded-pill px-3 shadow-sm d-inline-flex align-items-center gap-1" title="Play or Resume Surah recitation">
                        <i class="bi bi-play-fill fs-5" id="playFullIcon"></i>
                        <span id="playFullText">{{ ($savedAyahNumber ?? 1) > 1 ? 'Resume Surah (Ayah ' . $savedAyahNumber . ')' : 'Listen Full Surah' }}</span>
                    </button>
                    <button type="button" id="btnRestartSurah" class="btn btn-sm btn-outline-secondary bg-white rounded-circle shadow-sm {{ ($savedAyahNumber ?? 1) > 1 ? '' : 'd-none' }}" title="Restart from Ayah 1" style="width: 32px; height: 32px; padding: 0;">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </button>
                </div>

                <a href="{{ route('ai-teacher.index', ['surah' => $surahNumber, 'ayah' => 1]) }}" class="btn btn-sm btn-emerald rounded-pill px-3 d-inline-flex align-items-center gap-1">
                    <i class="bi bi-mic-fill text-gold"></i>
                    <span>Practice AI</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Surah Header Banner -->
    <div class="card card-glass border-0 p-4 mb-4 bg-emerald-grad text-white rounded-4 shadow-sm text-center position-relative overflow-hidden">
        <div class="py-2">
            <span class="badge badge-gold mb-2 text-uppercase">{{ $surah['revelationType'] ?? 'Meccan' }} &bull; {{ $surah['numberOfAyahs'] ?? count($surah['ayahs'] ?? []) }} Verses</span>
            <h1 class="font-arabic display-5 text-white mb-1">{{ $surah['name'] ?? '' }}</h1>
            <h4 class="fw-bold text-white-50 mb-0">{{ $surah['englishName'] ?? 'Surah ' . $surahNumber }} &mdash; <small class="text-white">{{ $surah['englishNameTranslation'] ?? '' }}</small></h4>
        </div>
    </div>

    <!-- Bismillah Display (for all surahs except Surah 9 At-Tawbah and Surah 1 where Bismillah is Ayah 1) -->
    @if($surahNumber != 9 && $surahNumber != 1)
        <div class="text-center my-4 py-3 bg-white rounded-4 shadow-sm border">
            <div class="font-arabic fs-1 text-emerald">
                بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ
            </div>
            <small class="text-muted">In the name of Allah, the Entirely Merciful, the Especially Merciful.</small>
        </div>
    @endif

    <!-- Verses Container -->
    <div class="verses-list" id="versesContainer">
        @if(isset($surah['ayahs']) && is_array($surah['ayahs']))
            @foreach($surah['ayahs'] as $index => $ayah)
                @php
                    $ayahNum = $ayah['numberInSurah'];
                    $isBookmarked = in_array($ayahNum, $bookmarks ?? []);
                    $translationText = $translation['ayahs'][$index]['text'] ?? '';
                    $audioUrl = $audio['ayahs'][$index]['audio'] ?? 'https://cdn.islamic.network/quran/audio/128/ar.alafasy/' . $ayah['number'] . '.mp3';
                @endphp

                <div class="card card-glass border-0 p-4 mb-3 ayah-card transition" id="ayah-{{ $ayahNum }}" data-ayah="{{ $ayahNum }}" data-audio="{{ $audioUrl }}">
                    <!-- Ayah Controls Header -->
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge badge-emerald fw-bold px-3 py-2 rounded-pill">
                                {{ $surahNumber }}:{{ $ayahNum }}
                            </span>
                            <small class="text-muted">Juz {{ $ayah['juz'] ?? 1 }} &bull; Page {{ $ayah['page'] ?? 1 }}</small>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <!-- Play Audio Button -->
                            <button class="btn btn-sm btn-light border rounded-pill px-3 d-inline-flex align-items-center gap-1 btn-play-ayah" data-ayah="{{ $ayahNum }}" data-url="{{ $audioUrl }}">
                                <i class="bi bi-play-circle text-emerald fs-6"></i>
                                <span class="small play-label">Listen</span>
                            </button>

                            <!-- AI Recitation Practice -->
                            <a href="{{ route('ai-teacher.index', ['surah' => $surahNumber, 'ayah' => $ayahNum]) }}" class="btn btn-sm btn-outline-warning text-dark fw-semibold rounded-pill px-3" title="Practice reciting this verse with AI">
                                <i class="bi bi-mic-fill text-warning me-1"></i> AI Recite
                            </a>

                            <!-- Bookmark Button -->
                            <button class="btn btn-sm btn-light border rounded-circle btn-bookmark {{ $isBookmarked ? 'text-warning' : 'text-muted' }}" data-surah="{{ $surahNumber }}" data-ayah="{{ $ayahNum }}" title="Bookmark Ayah" style="width: 36px; height: 36px;">
                                <i class="bi {{ $isBookmarked ? 'bi-bookmark-star-fill text-warning' : 'bi-bookmark' }}"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Arabic Text -->
                    <div class="quran-verse-text mb-3 arabic-verse-line">
                        {{ $ayah['text'] }}
                        <span class="ayah-end-marker">{{ $ayahNum }}</span>
                    </div>

                    <!-- Translation Text -->
                    @if($translationText)
                        <div class="translation-text text-secondary border-start border-3 border-emerald ps-3 py-1 bg-light rounded-end small lh-lg">
                            {{ $translationText }}
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <div class="alert alert-warning text-center rounded-4 py-5">
                <i class="bi bi-exclamation-circle fs-1 text-warning mb-2 d-block"></i>
                <h5>Unable to load verses at this moment.</h5>
                <p class="text-secondary small">Please ensure network connection to the Quran API service or try reloading the page.</p>
                <a href="{{ route('quran.index') }}" class="btn btn-emerald rounded-pill px-4 mt-2">Return to Surah List</a>
            </div>
        @endif
    </div>
</div>

<!-- Fixed Floating Audio Bar -->
<div class="audio-player-bar d-none" id="globalAudioBar">
    <div class="container d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-emerald rounded-circle p-0 d-flex align-items-center justify-content-center" id="audioToggleBtn" style="width: 44px; height: 44px;">
                <i class="bi bi-pause-fill fs-4" id="audioToggleIcon"></i>
            </button>
            <div>
                <strong class="d-block text-dark small" id="audioTrackTitle">Surah {{ $surah['englishName'] ?? $surahNumber }} &mdash; Ayah <span id="currentPlayingAyah">1</span></strong>
                <small class="text-muted" id="audioTrackReciter">Sheikh Mishary Rashid Alafasy</small>
            </div>
        </div>

        <div class="d-flex align-items-center gap-3">
            <audio id="mainAudioPlayer" preload="none"></audio>
            <button class="btn btn-sm btn-outline-secondary rounded-pill" id="closeAudioBar">
                <i class="bi bi-x-lg"></i> Close
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        var baseFontSize = 1.85;
        var currentFontSize = baseFontSize;
        var isPlayingContinuous = false;
        var totalAyahs = {{ $surah['numberOfAyahs'] ?? count($surah['ayahs'] ?? []) }};
        var audioPlayer = document.getElementById('mainAudioPlayer');

        // Storage keys for persistent reading & listening memory
        var storageKeyAyah = 'quran_last_ayah_s_{{ $surahNumber }}';
        var storageKeyTime = 'quran_last_time_s_{{ $surahNumber }}';
        var serverSavedAyah = {{ $savedAyahNumber ?? 1 }};
        var localSavedAyah = parseInt(localStorage.getItem(storageKeyAyah), 10);

        // Determine starting Ayah (priority: local browser memory -> database saved progress -> 1)
        var currentAyahIndex = (localSavedAyah && localSavedAyah >= 1 && localSavedAyah <= totalAyahs)
            ? localSavedAyah
            : ((serverSavedAyah >= 1 && serverSavedAyah <= totalAyahs) ? serverSavedAyah : 1);

        // Update button UI & icons
        function updatePlayButtonState() {
            if (isPlayingContinuous && !audioPlayer.paused) {
                $('#playFullIcon').removeClass('bi-play-fill').addClass('bi-pause-fill');
                $('#playFullText').text('Pause Surah');
                $('#btnRestartSurah').removeClass('d-none');
            } else {
                $('#playFullIcon').removeClass('bi-pause-fill').addClass('bi-play-fill');
                if (currentAyahIndex > 1) {
                    $('#playFullText').text('Resume Surah (Ayah ' + currentAyahIndex + ')');
                    $('#btnRestartSurah').removeClass('d-none');
                } else {
                    $('#playFullText').text('Listen Full Surah');
                    $('#btnRestartSurah').addClass('d-none');
                }
            }
        }

        function markStoppedAyahBadge(ayahNumber) {
            $('.last-stopped-badge').remove();
            if (ayahNumber > 1 && ayahNumber <= totalAyahs) {
                var card = $('#ayah-' + ayahNumber);
                if (card.length && !card.find('.last-stopped-badge').length) {
                    card.find('.badge-emerald').after(
                        '<span class="badge bg-warning text-dark rounded-pill fw-semibold ms-1 last-stopped-badge shadow-sm" style="font-size: 0.72rem;"><i class="bi bi-geo-alt-fill me-1"></i>Resume Here</span>'
                    );
                }
            }
        }

        function highlightAyahCard(ayahNumber) {
            $('.ayah-card').removeClass('border-emerald bg-emerald-light bg-opacity-25 active-reciting');
            var card = $('#ayah-' + ayahNumber);
            card.addClass('border-emerald bg-emerald-light bg-opacity-25 active-reciting');
            $('.last-stopped-badge').remove();
        }

        function saveCurrentPosition() {
            if (currentAyahIndex >= 1 && currentAyahIndex <= totalAyahs) {
                localStorage.setItem(storageKeyAyah, currentAyahIndex);
                if (audioPlayer && !isNaN(audioPlayer.currentTime)) {
                    localStorage.setItem(storageKeyTime, Math.floor(audioPlayer.currentTime));
                }
                // Persist to database
                $.post("{{ route('quran.progress.update') }}", {
                    surah_number: {{ $surahNumber }},
                    ayah_number: currentAyahIndex,
                    _token: "{{ csrf_token() }}"
                });
            }
        }

        // Initialize button & badge if previous playback was stopped at Ayah > 1
        if (currentAyahIndex > 1) {
            updatePlayButtonState();
            markStoppedAyahBadge(currentAyahIndex);
        }

        // Font size controllers
        $('#fontIncrease').on('click', function() {
            if (currentFontSize < 3.0) {
                currentFontSize += 0.2;
                $('.quran-verse-text').css('font-size', currentFontSize + 'rem');
            }
        });

        $('#fontDecrease').on('click', function() {
            if (currentFontSize > 1.2) {
                currentFontSize -= 0.2;
                $('.quran-verse-text').css('font-size', currentFontSize + 'rem');
            }
        });

        $('#fontReset').on('click', function() {
            currentFontSize = baseFontSize;
            $('.quran-verse-text').css('font-size', currentFontSize + 'rem');
        });

        // Bookmark toggle via AJAX
        $('.btn-bookmark').on('click', function() {
            var btn = $(this);
            var surah = btn.data('surah');
            var ayah = btn.data('ayah');

            $.ajax({
                url: "{{ route('quran.bookmark.toggle') }}",
                type: "POST",
                data: {
                    surah_number: surah,
                    ayah_number: ayah,
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    if (res.bookmarked) {
                        btn.removeClass('text-muted').addClass('text-warning');
                        btn.find('i').removeClass('bi-bookmark').addClass('bi-bookmark-star-fill text-warning');
                    } else {
                        btn.removeClass('text-warning').addClass('text-muted');
                        btn.find('i').removeClass('bi-bookmark-star-fill text-warning').addClass('bi-bookmark');
                    }
                }
            });
        });

        // Single Ayah Audio Player
        $('.btn-play-ayah').on('click', function() {
            var ayah = $(this).data('ayah');
            var url = $(this).data('url');

            currentAyahIndex = ayah;
            playAyahAudio(ayah, url, false);
        });

        // Play Full Surah / Resume Continuous
        $('#btnPlayFullSurah').on('click', function() {
            if (isPlayingContinuous && !audioPlayer.paused) {
                // Pause current continuous recitation
                audioPlayer.pause();
                isPlayingContinuous = false;
                saveCurrentPosition();
                updatePlayButtonState();
                markStoppedAyahBadge(currentAyahIndex);
                $('#audioToggleIcon').removeClass('bi-pause-fill').addClass('bi-play-fill');
            } else {
                // Resume or start from stopped Ayah
                isPlayingContinuous = true;
                updatePlayButtonState();

                var card = $('#ayah-' + currentAyahIndex);
                var expectedUrl = card.data('audio');

                // If already loaded for currentAyahIndex and paused midway, resume seamlessly
                if (audioPlayer.src && audioPlayer.src === expectedUrl && audioPlayer.currentTime > 0) {
                    audioPlayer.play().catch(function(e) { playCurrentIndex(); });
                    $('#globalAudioBar').removeClass('d-none');
                    $('#audioToggleIcon').removeClass('bi-play-fill').addClass('bi-pause-fill');
                    highlightAyahCard(currentAyahIndex);
                } else {
                    playCurrentIndex();
                }
            }
        });

        // Restart from Ayah 1 button
        $('#btnRestartSurah').on('click', function() {
            currentAyahIndex = 1;
            localStorage.setItem(storageKeyAyah, 1);
            localStorage.setItem(storageKeyTime, 0);
            $('.last-stopped-badge').remove();
            updatePlayButtonState();

            if (isPlayingContinuous) {
                playCurrentIndex();
            } else {
                var card = $('#ayah-1');
                if (card.length) {
                    $('html, body').animate({ scrollTop: card.offset().top - 120 }, 300);
                }
            }
        });

        function playCurrentIndex() {
            var card = $('#ayah-' + currentAyahIndex);
            if (card.length) {
                var url = card.data('audio');
                playAyahAudio(currentAyahIndex, url, true);

                // Scroll smoothly into view
                $('html, body').animate({
                    scrollTop: card.offset().top - 120
                }, 400);
            } else {
                // End of Surah
                isPlayingContinuous = false;
                currentAyahIndex = 1;
                localStorage.setItem(storageKeyAyah, 1);
                localStorage.setItem(storageKeyTime, 0);
                updatePlayButtonState();
                $('#audioToggleIcon').removeClass('bi-pause-fill').addClass('bi-play-fill');
            }
        }

        function playAyahAudio(ayahNumber, audioUrl, continuous) {
            currentAyahIndex = ayahNumber;
            isPlayingContinuous = continuous;

            highlightAyahCard(ayahNumber);
            $('#globalAudioBar').removeClass('d-none');
            $('#currentPlayingAyah').text(ayahNumber);

            audioPlayer.src = audioUrl;
            audioPlayer.play().catch(function(e) { console.log('Playback error:', e); });

            $('#audioToggleIcon').removeClass('bi-play-fill').addClass('bi-pause-fill');
            updatePlayButtonState();

            // Save progress immediately
            saveCurrentPosition();

            audioPlayer.onended = function() {
                if (isPlayingContinuous && currentAyahIndex < totalAyahs) {
                    currentAyahIndex++;
                    saveCurrentPosition();
                    playCurrentIndex();
                } else if (currentAyahIndex >= totalAyahs) {
                    // Full Surah finished
                    isPlayingContinuous = false;
                    currentAyahIndex = 1;
                    localStorage.setItem(storageKeyAyah, 1);
                    localStorage.setItem(storageKeyTime, 0);
                    updatePlayButtonState();
                    $('#audioToggleIcon').removeClass('bi-pause-fill').addClass('bi-play-fill');
                } else {
                    $('#audioToggleIcon').removeClass('bi-pause-fill').addClass('bi-play-fill');
                    updatePlayButtonState();
                    markStoppedAyahBadge(currentAyahIndex);
                }
            };
        }

        // Floating Player Toggle (Play / Pause)
        $('#audioToggleBtn').on('click', function() {
            if (audioPlayer.paused) {
                audioPlayer.play();
                $('#audioToggleIcon').removeClass('bi-play-fill').addClass('bi-pause-fill');
                isPlayingContinuous = true;
                updatePlayButtonState();
            } else {
                audioPlayer.pause();
                $('#audioToggleIcon').removeClass('bi-pause-fill').addClass('bi-play-fill');
                isPlayingContinuous = false;
                saveCurrentPosition();
                updatePlayButtonState();
                markStoppedAyahBadge(currentAyahIndex);
            }
        });

        // Floating Player Close button
        $('#closeAudioBar').on('click', function() {
            audioPlayer.pause();
            isPlayingContinuous = false;
            saveCurrentPosition();
            updatePlayButtonState();
            markStoppedAyahBadge(currentAyahIndex);
            $('#globalAudioBar').addClass('d-none');
            $('.ayah-card').removeClass('border-emerald bg-emerald-light bg-opacity-25 active-reciting');
        });

        // Save position when tab is closed or navigated away
        window.addEventListener('beforeunload', function() {
            saveCurrentPosition();
        });
    });
</script>
@endpush
@endsection
