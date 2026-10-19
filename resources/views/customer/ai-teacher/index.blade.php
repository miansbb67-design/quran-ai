@extends('layouts.customer')

@section('title', 'AI Voice Studio - Bayan AI Quran Teacher')
@section('page_title', 'AI Recitation Coach & Voice Studio')

@section('content')
<div class="container-fluid p-0">

    <!-- Top Selection Banner -->
    <div class="card card-glass border-0 p-4 mb-4 bg-emerald-grad text-white rounded-4 shadow-sm">
        <div class="row align-items-center g-3">
            <div class="col-lg-5">
                <span class="badge badge-gold mb-2"><i class="bi bi-cpu-fill me-1"></i> Interactive Voice AI</span>
                <h3 class="fw-bold mb-1">Recitation Analysis Studio</h3>
                <p class="text-white-50 mb-0 small">Select any verse, speak into your microphone, and receive instant word-by-word accuracy feedback.</p>
            </div>

            <div class="col-lg-7">
                <div class="row g-2 align-items-center bg-white bg-opacity-10 p-3 rounded-4 border border-white border-opacity-25">
                    <!-- Surah select -->
                    <div class="col-sm-6">
                        <label class="form-label text-white small fw-semibold mb-1">Select Surah</label>
                        <select id="selectSurah" class="form-select form-select-sm fw-semibold">
                            @if(is_array($surahs))
                                @php $preselectedSurah = request('surah', $readingProgress->last_surah_number ?? 1); @endphp
                                @foreach($surahs as $s)
                                    <option value="{{ $s['number'] }}" data-ayahs="{{ $s['numberOfAyahs'] }}" {{ $s['number'] == $preselectedSurah ? 'selected' : '' }}>
                                        {{ $s['number'] }}. {{ $s['englishName'] }} ({{ $s['name'] }})
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Ayah select -->
                    <div class="col-sm-3 col-6">
                        <label class="form-label text-white small fw-semibold mb-1">Ayah</label>
                        @php $preselectedAyah = request('ayah', $readingProgress->last_ayah_number ?? 1); @endphp
                        <input type="number" id="selectAyah" class="form-control form-control-sm text-center fw-bold" value="{{ $preselectedAyah }}" min="1" max="286">
                    </div>

                    <!-- Load Button -->
                    <div class="col-sm-3 col-6">
                        <label class="form-label text-transparent d-block small mb-1">Action</label>
                        <button type="button" id="btnLoadAyah" class="btn btn-gold btn-sm w-100 rounded-pill fw-bold shadow-sm">
                            <i class="bi bi-arrow-clockwise me-1"></i> Load Verse
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Practice Studio -->
    <div class="row g-4">
        <!-- Left: Target Verse & Mic -->
        <div class="col-lg-7">
            <!-- Target Verse Card -->
            <div class="card card-glass border-0 p-4 mb-4 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge badge-emerald px-3 py-2 rounded-pill fw-bold" id="currentVerseBadge">
                            Surah 1 : Ayah 1
                        </span>
                        <span class="text-muted small" id="currentSurahName">Al-Faatiha</span>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <!-- Qari reference audio -->
                        <button type="button" class="btn btn-sm btn-outline-emerald rounded-pill px-3 d-inline-flex align-items-center gap-1" id="btnPlayReference">
                            <i class="bi bi-volume-up-fill fs-6" id="refAudioIcon"></i>
                            <span class="small" id="refAudioLabel">Listen to Qari</span>
                        </button>
                        <audio id="referenceAudioPlayer" preload="none"></audio>
                    </div>
                </div>

                <!-- Arabic Verse Box -->
                <div class="bg-light rounded-4 p-4 text-center mb-3 border">
                    <div class="quran-verse-text mb-3" id="targetArabicText" style="font-size: 2.2rem; min-height: 80px;">
                        بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ
                    </div>
                    <div class="text-secondary small fst-italic" id="targetTranslationText">
                        "In the name of Allah, the Entirely Merciful, the Especially Merciful."
                    </div>
                </div>

                <!-- Voice Recording Stage -->
                <div class="text-center py-4 bg-white rounded-4 border p-3">
                    <div class="mb-3">
                        <button type="button" id="btnRecord" class="btn rounded-circle d-inline-flex align-items-center justify-content-center shadow-lg transition" style="width: 84px; height: 84px; background: var(--emerald-gradient); color: white; border: 4px solid #d4af37;">
                            <i class="bi bi-mic-fill fs-1" id="micIcon"></i>
                        </button>
                    </div>
                    
                    <h5 class="fw-bold text-dark mb-1" id="micStatusHeading">Click Microphone to Recite</h5>
                    <p class="text-muted small mb-3" id="micStatusDesc">Recite clearly in Arabic. Speak naturally close to your device.</p>

                    <!-- Real-time transcript preview -->
                    <div class="p-3 bg-light rounded-3 border mb-3 text-center" id="transcriptBox" style="min-height: 48px;">
                        <span class="text-muted small fst-italic" id="liveTranscript">Live voice transcript will appear here as you recite...</span>
                    </div>

                    <!-- Browser fallback text input toggle -->
                    <div class="text-end">
                        <button type="button" class="btn btn-sm btn-link text-muted p-0 text-decoration-none small" id="toggleManualInput">
                            <i class="bi bi-keyboard me-1"></i> Need manual text input?
                        </button>
                    </div>

                    <div id="manualInputContainer" class="d-none mt-3 text-start">
                        <label class="form-label small fw-semibold text-muted">Arabic Transcribed Text (Manual Input / Fallback)</label>
                        <div class="input-group">
                            <input type="text" id="manualArabicInput" class="form-control text-end font-arabic fs-5" placeholder="أدخل الآية المنطوقة هنا...">
                            <button type="button" class="btn btn-emerald px-4" id="btnSubmitManual">Analyze Text</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: AI Evaluation & Feedback Studio -->
        <div class="col-lg-5">
            <div class="card card-glass border-0 p-4 shadow-sm h-100">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <h5 class="fw-bold text-emerald mb-0"><i class="bi bi-stars text-gold me-2"></i>AI Assessment</h5>
                    <span class="badge bg-light text-muted border" id="attemptBadge">Attempt #0</span>
                </div>

                <!-- Score Meter Card -->
                <div class="card border-0 bg-light p-4 rounded-4 text-center mb-4">
                    <div class="display-4 fw-bold text-emerald mb-1" id="scoreDisplay">--%</div>
                    <div class="fw-semibold text-secondary small text-uppercase" id="resultLabel">Awaiting Recitation</div>
                    <div class="progress mt-3" style="height: 10px; border-radius: 5px;">
                        <div class="progress-bar bg-emerald-grad" id="scoreProgressBar" role="progressbar" style="width: 0%"></div>
                    </div>
                </div>

                <!-- Word-by-Word Alignment Breakdown -->
                <div class="mb-4">
                    <h6 class="fw-bold text-dark mb-2">Word Alignment Analysis</h6>
                    <p class="text-muted small mb-2">Color key: <span class="badge bg-success-subtle text-success border me-1">Matched</span> <span class="badge bg-warning-subtle text-warning-emphasis border me-1">Uncertain</span> <span class="badge bg-danger-subtle text-danger border">Needs Review</span></p>
                    <div class="p-3 bg-white rounded-3 border text-end min-h-80" id="wordAlignmentContainer">
                        <span class="text-muted small fst-italic d-block text-center py-3">Recite the verse to view word breakdown</span>
                    </div>
                </div>

                <!-- Teacher Feedback Advice -->
                <div class="p-3 rounded-4 bg-emerald-light border border-emerald-border mb-4">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="bi bi-info-circle-fill text-emerald fs-5"></i>
                        <strong class="text-emerald small">Teacher's Note</strong>
                    </div>
                    <p class="text-secondary small mb-0" id="feedbackMessage">
                        Press the microphone button and recite the Holy verse clearly. Listen to the reference Qari first if you would like to review the correct pronunciation and pauses.
                    </p>
                </div>

                <!-- Action Controls: Retry & Next Ayah -->
                <div class="mt-auto d-flex gap-2">
                    <button type="button" class="btn btn-outline-emerald rounded-pill px-3 flex-grow-1" id="btnRetry">
                        <i class="bi bi-arrow-repeat me-1"></i> Retry Verse
                    </button>
                    <button type="button" class="btn btn-emerald rounded-pill px-3 flex-grow-1" id="btnNextAyah">
                        <span>Next Ayah</span> <i class="bi bi-chevron-right ms-1"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        var currentSurah = parseInt($('#selectSurah').val()) || 1;
        var currentAyah = parseInt($('#selectAyah').val()) || 1;
        var sessionId = null;
        var isRecording = false;
        var recognition = null;
        var transcribedText = "";
        var refAudioUrl = "";
        var startTime = Date.now();

        // Initialize Web Speech Recognition
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (SpeechRecognition) {
            recognition = new SpeechRecognition();
            recognition.continuous = true;
            recognition.interimResults = true;
            recognition.lang = 'ar-SA'; // Arabic (Saudi Arabia)

            recognition.onstart = function() {
                isRecording = true;
                $('#btnRecord').addClass('pulse-recording').css('background', '#ef4444');
                $('#micIcon').removeClass('bi-mic-fill').addClass('bi-stop-fill');
                $('#micStatusHeading').text('Listening to your recitation...');
                $('#micStatusDesc').text('Recite the verse now. Click again when finished.');
                transcribedText = "";
                $('#liveTranscript').text('Listening...');
            };

            recognition.onresult = function(event) {
                var interimTranscript = '';
                for (var i = event.resultIndex; i < event.results.length; ++i) {
                    if (event.results[i].isFinal) {
                        transcribedText += event.results[i][0].transcript;
                    } else {
                        interimTranscript += event.results[i][0].transcript;
                    }
                }
                var display = transcribedText || interimTranscript;
                $('#liveTranscript').text(display).addClass('text-dark fw-bold font-arabic fs-5');
            };

            recognition.onerror = function(event) {
                console.log('Speech error:', event.error);
                stopRecordingUI();
                if (event.error === 'not-allowed') {
                    alert('Microphone access is not allowed by browser. You can use the manual Arabic text input option.');
                    $('#manualInputContainer').removeClass('d-none');
                }
            };

            recognition.onend = function() {
                if (isRecording) {
                    stopRecordingUI();
                    if (transcribedText.trim().length > 0) {
                        submitRecitation(transcribedText);
                    }
                }
            };
        } else {
            console.log('Web Speech API not supported in this browser.');
            $('#micStatusDesc').text('Voice recognition requires Chrome/Edge/Safari. You can also test using manual input.');
            $('#manualInputContainer').removeClass('d-none');
        }

        // Start a Practice Session on page load
        startNewSession();

        // Load the initial verse
        loadAyahData(currentSurah, currentAyah);

        // Surah or Ayah change
        $('#btnLoadAyah').on('click', function() {
            currentSurah = parseInt($('#selectSurah').val());
            currentAyah = parseInt($('#selectAyah').val()) || 1;
            loadAyahData(currentSurah, currentAyah);
        });

        // Record Button click
        $('#btnRecord').on('click', function() {
            if (!recognition) {
                $('#manualInputContainer').removeClass('d-none');
                $('#manualArabicInput').focus();
                return;
            }

            if (!isRecording) {
                try {
                    recognition.start();
                } catch(e) {
                    console.log('Start error:', e);
                }
            } else {
                recognition.stop();
                stopRecordingUI();
                if (transcribedText.trim().length > 0) {
                    submitRecitation(transcribedText);
                }
            }
        });

        function stopRecordingUI() {
            isRecording = false;
            $('#btnRecord').removeClass('pulse-recording').css('background', 'var(--emerald-gradient)');
            $('#micIcon').removeClass('bi-stop-fill').addClass('bi-mic-fill');
            $('#micStatusHeading').text('Analyzing your recitation...');
            $('#micStatusDesc').text('Computing phonetic alignment with the Quranic text...');
        }

        // Manual text fallback submit
        $('#toggleManualInput').on('click', function() {
            $('#manualInputContainer').toggleClass('d-none');
        });

        $('#btnSubmitManual').on('click', function() {
            var text = $('#manualArabicInput').val().trim();
            if (text.length > 0) {
                submitRecitation(text);
            } else {
                alert('Please enter Arabic words to analyze.');
            }
        });

        // Next Ayah Button
        $('#btnNextAyah').on('click', function() {
            var maxAyahs = parseInt($('#selectSurah option:selected').data('ayahs')) || 286;
            if (currentAyah < maxAyahs) {
                currentAyah++;
                $('#selectAyah').val(currentAyah);
                loadAyahData(currentSurah, currentAyah);
            } else {
                alert('You have reached the end of this Surah.');
            }
        });

        // Retry Button
        $('#btnRetry').on('click', function() {
            resetAssessmentUI();
            $('#transcriptBox').html('<span class="text-muted small fst-italic">Live voice transcript will appear here as you recite...</span>');
        });

        // Play Reference Qari Audio
        var refAudio = document.getElementById('referenceAudioPlayer');
        $('#btnPlayReference').on('click', function() {
            if (!refAudioUrl) return;

            if (refAudio.paused) {
                refAudio.src = refAudioUrl;
                refAudio.play();
                $('#refAudioIcon').removeClass('bi-volume-up-fill').addClass('bi-pause-fill');
                $('#refAudioLabel').text('Pause Qari');
            } else {
                refAudio.pause();
                $('#refAudioIcon').removeClass('bi-pause-fill').addClass('bi-volume-up-fill');
                $('#refAudioLabel').text('Listen to Qari');
            }
        });

        refAudio.onended = function() {
            $('#refAudioIcon').removeClass('bi-pause-fill').addClass('bi-volume-up-fill');
            $('#refAudioLabel').text('Listen to Qari');
        };

        // AJAX: Load Ayah data
        function loadAyahData(surah, ayah) {
            resetAssessmentUI();
            $('#targetArabicText').html('<div class="spinner-border text-emerald" role="status"></div>');

            $.get('/ai-teacher/ayah/' + surah + '/' + ayah, function(res) {
                if (res.ayah) {
                    $('#targetArabicText').text(res.ayah.text);
                    $('#currentVerseBadge').text('Surah ' + surah + ' : Ayah ' + ayah);
                    $('#currentSurahName').text($('#selectSurah option:selected').text().trim());

                    if (res.translation && res.translation.text) {
                        $('#targetTranslationText').text('"' + res.translation.text + '"');
                    } else {
                        $('#targetTranslationText').text('');
                    }

                    if (res.audio && res.audio.audio) {
                        refAudioUrl = res.audio.audio;
                        $('#btnPlayReference').prop('disabled', false);
                    } else {
                        refAudioUrl = "";
                    }

                    // Pre-fill manual input with target text as convenience for testing
                    $('#manualArabicInput').val(res.ayah.text);
                }
            }).fail(function() {
                $('#targetArabicText').text('Unable to load verse. Please verify network connection.');
            });
        }

        // AJAX: Start a Session
        function startNewSession() {
            $.post("{{ route('ai-teacher.start-session') }}", {
                surah_number: currentSurah,
                start_ayah: currentAyah,
                end_ayah: currentAyah,
                session_type: 'recitation',
                _token: "{{ csrf_token() }}"
            }, function(res) {
                if (res.session_id) {
                    sessionId = res.session_id;
                }
            });
        }

        // AJAX: Submit Recitation for Analysis
        function submitRecitation(recognized) {
            if (!sessionId) {
                startNewSession();
            }

            $.post("{{ route('ai-teacher.analyze') }}", {
                session_id: sessionId,
                surah_number: currentSurah,
                ayah_number: currentAyah,
                recognized_text: recognized,
                _token: "{{ csrf_token() }}"
            }, function(res) {
                displayAssessment(res);
            }).fail(function(xhr) {
                console.log('Analysis error:', xhr);
                alert('Analysis failed. Please check connection.');
            });
        }

        // Render Assessment Results
        function displayAssessment(data) {
            var analysis = data.analysis;
            var score = Math.round(analysis.confidence_score);

            $('#scoreDisplay').text(score + '%');
            $('#scoreProgressBar').css('width', score + '%');

            if (score >= 85) {
                $('#scoreDisplay').removeClass('text-danger text-warning').addClass('text-success');
                $('#scoreProgressBar').removeClass('bg-danger bg-warning').addClass('bg-success');
                $('#resultLabel').text('Excellent Recitation!').removeClass('text-danger text-warning').addClass('text-success');
            } else if (score >= 60) {
                $('#scoreDisplay').removeClass('text-danger text-success').addClass('text-warning');
                $('#scoreProgressBar').removeClass('bg-danger bg-success').addClass('bg-warning');
                $('#resultLabel').text('Good Attempt - Room for Improvement').removeClass('text-danger text-success').addClass('text-warning');
            } else {
                $('#scoreDisplay').removeClass('text-success text-warning').addClass('text-danger');
                $('#scoreProgressBar').removeClass('bg-success bg-warning').addClass('bg-danger');
                $('#resultLabel').text('Needs Practice').removeClass('text-success text-warning').addClass('text-danger');
            }

            $('#attemptBadge').text('Attempt #' + data.attempt_number);
            $('#feedbackMessage').text(analysis.feedback_message);

            // Word breakdown
            var wordsHtml = '';
            if (analysis.word_analysis && analysis.word_analysis.length > 0) {
                analysis.word_analysis.forEach(function(item) {
                    var cls = item.matched ? 'word-matched' : (item.status === 'uncertain' ? 'word-uncertain' : 'word-missing');
                    wordsHtml += '<span class="word-feedback ' + cls + '">' + item.word + '</span> ';
                });
            }
            $('#wordAlignmentContainer').html(wordsHtml);

            $('#micStatusHeading').text('Recitation Evaluated!');
            $('#micStatusDesc').text('Review your word breakdown above or try again for a higher score.');
        }

        function resetAssessmentUI() {
            $('#scoreDisplay').text('--%').removeClass('text-success text-warning text-danger').addClass('text-emerald');
            $('#scoreProgressBar').css('width', '0%');
            $('#resultLabel').text('Awaiting Recitation').removeClass('text-success text-warning text-danger');
            $('#wordAlignmentContainer').html('<span class="text-muted small fst-italic d-block text-center py-3">Recite the verse to view word breakdown</span>');
            $('#feedbackMessage').text('Press the microphone button and recite the Holy verse clearly. Listen to the reference Qari first if needed.');
            $('#micStatusHeading').text('Click Microphone to Recite');
            $('#micStatusDesc').text('Recite clearly in Arabic. Speak naturally close to your device.');
        }
    });
</script>
@endpush
@endsection
