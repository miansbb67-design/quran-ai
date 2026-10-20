@extends('layouts.customer')

@section('title', 'AI Voice Studio - Bayan AI Quran Teacher')
@section('page_title', 'AI Recitation Coach & Voice Studio')

@section('content')
<div class="container-fluid p-0">

    <!-- Top Selection Banner -->
    <div class="card card-glass border-0 p-4 mb-3 bg-emerald-grad text-white rounded-4 shadow-sm">
        <div class="row align-items-center g-3">
            <div class="col-lg-5">
                <span class="badge badge-gold mb-2"><i class="bi bi-cpu-fill me-1"></i> Interactive Voice AI</span>
                <h3 class="fw-bold mb-1">Recitation Analysis Studio</h3>
                <p class="text-white-50 mb-0 small">Select any verse, speak into your microphone, and receive instant word-by-word accuracy, Tajweed, and spoken teacher feedback.</p>
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

    <!-- AI Voice Teacher Toolbar & Settings Strip -->
    <div class="card card-glass border-0 p-3 mb-4 rounded-4 shadow-sm bg-white">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <!-- Teacher Spoken Feedback Toggle -->
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" role="switch" id="toggleSpokenFeedback" checked>
                    <label class="form-check-label small fw-bold text-dark" for="toggleSpokenFeedback">
                        <i class="bi bi-volume-up-fill text-emerald me-1"></i> Spoken Teacher Guidance
                    </label>
                </div>

                <div class="vr d-none d-md-block" style="height: 24px;"></div>

                <!-- Language Selector -->
                <div class="d-flex align-items-center gap-2">
                    <span class="small text-muted fw-semibold"><i class="bi bi-translate me-1"></i> Voice Language:</span>
                    <select id="teacherVoiceLanguage" class="form-select form-select-sm fw-semibold" style="width: auto;">
                        <option value="en" selected>English</option>
                        <option value="ur">اردو (Urdu)</option>
                        <option value="ar">العربية (Arabic)</option>
                    </select>
                </div>

                <div class="vr d-none d-md-block" style="height: 24px;"></div>

                <!-- Auto-speak on error -->
                <div class="form-check mb-0">
                    <input class="form-check-input" type="checkbox" id="toggleAutoSpeak" checked>
                    <label class="form-check-label small text-secondary" for="toggleAutoSpeak">
                        Auto-read mistakes aloud
                    </label>
                </div>
            </div>

            <!-- Privacy & History Clear -->
            <div class="d-flex align-items-center gap-2">
                <span id="speechStatusIndicator" class="badge bg-light text-muted border px-2 py-1">
                    <i class="bi bi-mic-fill text-secondary me-1"></i> Mic Ready (ar-SA)
                </span>
                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3" id="btnClearSession" title="Delete practice history for this session (Privacy compliance)">
                    <i class="bi bi-trash3 me-1"></i> Clear History
                </button>
            </div>
        </div>
    </div>

    <!-- Main Practice Studio -->
    <div class="row g-4">
        <!-- Left: Target Verse & Recitation Stage -->
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
                        <button type="button" class="btn btn-sm btn-outline-emerald rounded-pill px-3 d-inline-flex align-items-center gap-1 shadow-sm" id="btnPlayReference">
                            <i class="bi bi-volume-up-fill fs-6" id="refAudioIcon"></i>
                            <span class="small" id="refAudioLabel">Listen to Qari</span>
                        </button>
                        <audio id="referenceAudioPlayer" preload="none"></audio>
                        <!-- Word Audio Player -->
                        <audio id="wordAudioPlayer" preload="none"></audio>
                    </div>
                </div>

                <!-- Interactive Arabic Verse Box (Word-by-Word Synchronized) -->
                <div class="bg-light rounded-4 p-4 text-center mb-3 border position-relative">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted fst-italic"><i class="bi bi-cursor me-1"></i> Click any word to hear verified Qari audio & practice</small>
                        <span class="badge bg-white text-muted border" id="verseWordCount">4 words</span>
                    </div>

                    <div class="quran-verse-text mb-3" id="targetArabicText" style="font-size: 2.2rem; min-height: 80px;">
                        <!-- Synchronized word spans will be injected here -->
                        بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ
                    </div>
                    <div class="text-secondary small fst-italic" id="targetTranslationText">
                        "In the name of Allah, the Entirely Merciful, the Especially Merciful."
                    </div>
                </div>

                <!-- Selected Word Quick Inspector Card (Appears when student clicks any word in the verse) -->
                <div id="selectedWordCard" class="card border border-emerald border-opacity-25 rounded-4 p-3 mb-3 bg-emerald-light shadow-sm d-none">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div class="d-flex align-items-center gap-3">
                            <div class="font-arabic fs-2 text-emerald fw-bold" id="inspectorArabicWord">بِسْمِ</div>
                            <div>
                                <div class="small fw-bold text-dark" id="inspectorTranslit">Bismi</div>
                                <div class="small text-muted" id="inspectorTranslation">In the name of</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-sm btn-gold rounded-pill px-3 fw-bold" id="btnInspectorPlay">
                                <i class="bi bi-volume-up-fill me-1"></i> Play Word
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-emerald rounded-pill px-3" id="btnInspectorSpeak">
                                <i class="bi bi-chat-quote-fill me-1"></i> Speak Teacher
                            </button>
                            <button type="button" class="btn btn-sm btn-emerald rounded-pill px-3" id="btnInspectorPractice">
                                <i class="bi bi-mic me-1"></i> Practice Word
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Voice Recording Stage -->
                <div class="text-center py-4 bg-white rounded-4 border p-3">
                    <div class="mb-2">
                        <span class="badge bg-emerald-light text-emerald border border-emerald-border px-3 py-1 rounded-pill small">
                            <i class="bi bi-lightning-charge-fill text-gold me-1"></i> Auto-Stop: Automatically analyzes when Ayah is finished
                        </span>
                    </div>

                    <div class="mb-3 position-relative d-inline-block">
                        <button type="button" id="btnRecord" class="btn rounded-circle d-inline-flex align-items-center justify-content-center shadow-lg transition" style="width: 86px; height: 86px; background: var(--emerald-gradient); color: white; border: 4px solid #d4af37; cursor: pointer;">
                            <i class="bi bi-mic-fill fs-1" id="micIcon"></i>
                        </button>
                        <span id="recordingRipple" class="position-absolute top-0 start-0 w-100 h-100 rounded-circle border border-danger d-none"></span>
                    </div>
                    
                    <h5 class="fw-bold text-dark mb-1" id="micStatusHeading">Click Microphone to Recite</h5>
                    <p class="text-muted small mb-3" id="micStatusDesc">Recite clearly in Arabic. The AI automatically detects when you complete the Ayah.</p>

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
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="fw-bold text-dark mb-0">Word Alignment Analysis</h6>
                        <small class="text-muted">Click pill to inspect</small>
                    </div>
                    <p class="text-muted small mb-2">Color key: <span class="badge bg-success-subtle text-success border me-1">Matched</span> <span class="badge bg-warning-subtle text-warning-emphasis border me-1">Uncertain</span> <span class="badge bg-danger-subtle text-danger border">Needs Review</span></p>
                    <div class="p-3 bg-white rounded-3 border text-end min-h-80" id="wordAlignmentContainer">
                        <span class="text-muted small fst-italic d-block text-center py-3">Recite the verse to view word breakdown</span>
                    </div>
                </div>

                <!-- Pronunciation Corrections & Mistakes Breakdown Panel -->
                <div id="mistakesContainer" class="mb-4 d-none"></div>

                <!-- Teacher Feedback Advice -->
                <div class="p-3 rounded-4 bg-emerald-light border border-emerald-border mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-info-circle-fill text-emerald fs-5"></i>
                            <strong class="text-emerald small">Teacher's Note</strong>
                        </div>
                        <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none text-emerald small" id="btnSpeakTeacherNote" title="Listen to Teacher's Note">
                            <i class="bi bi-volume-up-fill me-1"></i> Listen
                        </button>
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

<!-- Modal: Targeted Single-Word Practice Gym -->
<div class="modal fade" id="singleWordModal" tabindex="-1" aria-labelledby="singleWordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-emerald-grad text-white rounded-top-4 border-0">
                <h6 class="modal-title fw-bold" id="singleWordModalLabel">
                    <i class="bi bi-mic me-1 text-gold"></i> Targeted Word Practice
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <p class="text-muted small mb-2">Practice reciting only this specific word until pronounced accurately:</p>
                
                <div class="single-word-gym my-3">
                    <div class="font-arabic text-emerald fw-bold mb-2" id="modalTargetWord" style="font-size: 3rem; line-height: 1.2;">
                        --
                    </div>
                    <div class="text-muted small" id="modalWordTajweedTip">
                        Checking Tajweed rules...
                    </div>
                </div>

                <div class="d-flex justify-content-center gap-2 mb-3">
                    <button type="button" class="btn btn-sm btn-outline-emerald rounded-pill px-3" id="btnModalPlayQari">
                        <i class="bi bi-volume-up-fill me-1"></i> Hear Qari Pronunciation
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" id="btnModalSpeakAdvice">
                        <i class="bi bi-chat-quote me-1"></i> Spoken Advice
                    </button>
                </div>

                <!-- Modal mic button -->
                <div class="my-3">
                    <button type="button" id="btnModalRecord" class="btn rounded-circle d-inline-flex align-items-center justify-content-center shadow" style="width: 72px; height: 72px; background: var(--emerald-gradient); color: white; border: 3px solid #d4af37;">
                        <i class="bi bi-mic-fill fs-2" id="modalMicIcon"></i>
                    </button>
                </div>
                <div class="small fw-bold text-dark" id="modalMicStatus">Click microphone and recite this word</div>
                <div class="font-arabic fs-4 text-secondary mt-2" id="modalRecognizedWord"></div>

                <!-- Result alert box in modal -->
                <div id="modalResultBox" class="mt-3 d-none"></div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">Close</button>
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
        var isListening = false;
        var activeRecognition = null;
        var activeModalRecognition = null;
        var isModalRecording = false;
        var autoStopAyahTimer = null;
        var silenceTimer = null;
        var modalSilenceTimer = null;
        var transcribedText = "";
        var refAudioUrl = "";
        var currentAyahWords = [];
        var targetVerseText = "";
        var targetWordsClean = [];
        var activeSelectedWord = null;
        var latestAnalysis = null;
        var currentMistakes = [];

        // Helper: normalize Arabic letters and strip diacritics
        function normalizeArabicText(text) {
            if (!text) return '';
            return text.toString()
                .replace(/[\u064B-\u065F\u0670\u06D6-\u06DC\u06DF-\u06E4\u06E7\u06E8\u06EA-\u06ED]/g, '')
                .replace(/[أإآٱ]/g, 'ا')
                .replace(/ة/g, 'ه')
                .replace(/ى/g, 'ي')
                .replace(/ـ/g, '')
                .replace(/\s+/g, ' ')
                .trim();
        }

        // Helper: Levenshtein similarity
        function wordSimilarityJS(s1, s2) {
            if (s1 === s2) return 1.0;
            if (!s1 || !s2) return 0.0;
            var len1 = s1.length, len2 = s2.length;
            var maxLen = Math.max(len1, len2);
            if (maxLen === 0) return 1.0;
            var matrix = [];
            for (var i = 0; i <= len1; i++) matrix[i] = [i];
            for (var j = 0; j <= len2; j++) matrix[0][j] = j;
            for (var i = 1; i <= len1; i++) {
                for (var j = 1; j <= len2; j++) {
                    var cost = (s1[i - 1] === s2[j - 1]) ? 0 : 1;
                    matrix[i][j] = Math.min(
                        matrix[i - 1][j] + 1,
                        matrix[i - 1][j] + 1,
                        matrix[i - 1][j - 1] + cost
                    );
                }
            }
            return Math.max(0, 1 - (matrix[len1][len2] / maxLen));
        }

        // Single Word Modal Object
        var singleWordModal = new bootstrap.Modal(document.getElementById('singleWordModal'));

        // Fresh SpeechRecognition factory & start handler
        function startRecitationListening() {
            if (isListening) return;

            const SpeechRec = window.SpeechRecognition || window.webkitSpeechRecognition;
            if (!SpeechRec) {
                alert('Voice recognition is not supported in this browser. Please use Chrome, Edge, or Safari, or test with the manual Arabic input.');
                $('#manualInputContainer').removeClass('d-none');
                return;
            }

            // Clean up any stale instances
            if (activeRecognition) {
                try { activeRecognition.abort(); } catch(e) {}
                activeRecognition = null;
            }

            // Function to launch the instance after permissions
            function launchInstance() {
                try {
                    activeRecognition = new SpeechRec();
                    activeRecognition.continuous = true;
                    activeRecognition.interimResults = true;
                    activeRecognition.lang = 'ar-SA';
                    activeRecognition.maxAlternatives = 1;

                    transcribedText = "";
                    var accumulatedFinal = "";

                    activeRecognition.onstart = function() {
                        isListening = true;
                        $('#btnRecord').addClass('pulse-recording').css('background', '#ef4444');
                        $('#recordingRipple').removeClass('d-none');
                        $('#micIcon').removeClass('bi-mic-fill').addClass('bi-stop-fill');
                        $('#micStatusHeading').text('Listening to your recitation...');
                        $('#micStatusDesc').html('<span class="text-success fw-bold"><i class="bi bi-mic"></i> Reciting...</span> Will stop automatically when you complete the Ayah.');
                        $('#speechStatusIndicator').html('<i class="bi bi-record-circle text-danger me-1"></i> Recording (ar-SA)...').removeClass('bg-light text-muted bg-success-subtle text-success').addClass('bg-danger-subtle text-danger');
                        transcribedText = "";
                        $('#liveTranscript').text('Listening in Arabic... Say the verse now...').removeClass('text-danger text-muted').addClass('text-dark');
                    };

                    activeRecognition.onresult = function(event) {
                        var interim = '';
                        for (var i = event.resultIndex; i < event.results.length; ++i) {
                            if (event.results[i].isFinal) {
                                accumulatedFinal += ' ' + event.results[i][0].transcript;
                            } else {
                                interim += ' ' + event.results[i][0].transcript;
                            }
                        }
                        transcribedText = (accumulatedFinal + ' ' + interim).trim();
                        $('#liveTranscript').text(transcribedText).addClass('text-dark fw-bold font-arabic fs-5');

                        // Check if Ayah recitation has finished to automatically stop!
                        checkAyahCompletion(transcribedText);
                    };

                    activeRecognition.onerror = function(event) {
                        console.warn('Speech error:', event.error);
                        if (event.error === 'not-allowed' || event.error === 'service-not-allowed') {
                            $('#speechStatusIndicator').html('<i class="bi bi-slash-circle text-danger me-1"></i> Mic Denied').addClass('bg-danger-subtle text-danger');
                            alert('Microphone access is not allowed. Please allow microphone permissions in your browser or use the manual Arabic text input.');
                            $('#manualInputContainer').removeClass('d-none');
                            stopRecitationListening(false);
                        } else if (event.error === 'no-speech') {
                            // Waiting for speech, keep open
                        }
                    };

                    activeRecognition.onend = function() {
                        if (isListening) {
                            stopRecitationListening(true);
                        }
                    };

                    activeRecognition.start();
                } catch(e) {
                    console.error("Speech start error:", e);
                    stopRecitationListening(false);
                    alert("Could not start microphone: " + e.message);
                }
            }

            // Probe permissions if possible to avoid silent failures
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({ audio: true }).then(function(stream) {
                    stream.getTracks().forEach(t => t.stop());
                    launchInstance();
                }).catch(function(err) {
                    console.warn("GetUserMedia prompt warning:", err);
                    launchInstance();
                });
            } else {
                launchInstance();
            }
        }

        // Intelligent Auto-Stop: detects when the user finishes reciting the Ayah
        function checkAyahCompletion(currentSpokenText) {
            if (!currentSpokenText || currentSpokenText.trim().length === 0) return;

            clearTimeout(autoStopAyahTimer);
            clearTimeout(silenceTimer);

            var spokenTokens = normalizeArabicText(currentSpokenText).split(/\s+/).filter(Boolean);
            var targetTokens = targetWordsClean.length > 0
                ? targetWordsClean
                : normalizeArabicText($('#targetArabicText').text()).split(/\s+/).filter(Boolean);

            var targetCount = targetTokens.length;
            var spokenCount = spokenTokens.length;

            // 1. Check if the final target word was spoken
            var reachedLastTargetWord = false;
            if (targetCount > 0 && spokenCount > 0) {
                var lastTargetWord = targetTokens[targetCount - 1];
                for (var k = Math.max(0, spokenCount - 3); k < spokenCount; k++) {
                    if (wordSimilarityJS(spokenTokens[k], lastTargetWord) >= 0.70) {
                        reachedLastTargetWord = true;
                        break;
                    }
                }
            }

            var wordCountMet = (targetCount > 0 && spokenCount >= targetCount);

            if (reachedLastTargetWord || wordCountMet) {
                // User reached the end of the Ayah!
                $('#speechStatusIndicator').html('<i class="bi bi-check2-circle text-success me-1"></i> Ayah completed! Auto-analyzing...').removeClass('bg-danger-subtle text-danger').addClass('bg-success-subtle text-success');
                $('#micStatusHeading').text('Ayah Recited! Processing...');
                $('#micStatusDesc').html('<span class="text-success fw-bold"><i class="bi bi-check-circle"></i> Verse finished. Evaluating recitation...</span>');

                // Buffer of 750ms to catch any final waqf/vowel, then auto-stop
                autoStopAyahTimer = setTimeout(function() {
                    if (isListening) {
                        stopRecitationListening(true);
                    }
                }, 750);
            } else if (spokenCount >= 1) {
                // 2. Silence detection: if user pauses for 2.3 seconds after speaking at least 1 word
                silenceTimer = setTimeout(function() {
                    if (isListening) {
                        $('#speechStatusIndicator').html('<i class="bi bi-hourglass text-success me-1"></i> Recitation pause. Analyzing...').removeClass('bg-danger-subtle text-danger').addClass('bg-success-subtle text-success');
                        stopRecitationListening(true);
                    }
                }, 2300);
            }
        }

        // Stop Recitation and Trigger Submission
        function stopRecitationListening(shouldSubmit) {
            clearTimeout(autoStopAyahTimer);
            clearTimeout(silenceTimer);

            if (!isListening && !activeRecognition) return;

            isListening = false;
            var textToAnalyze = transcribedText ? transcribedText.trim() : "";

            if (activeRecognition) {
                try {
                    activeRecognition.stop();
                } catch(e) {}
                activeRecognition = null;
            }

            // Update UI buttons and badges
            $('#btnRecord').removeClass('pulse-recording').css('background', 'var(--emerald-gradient)');
            $('#recordingRipple').addClass('d-none');
            $('#micIcon').removeClass('bi-stop-fill').addClass('bi-mic-fill');

            if (shouldSubmit && textToAnalyze.length > 0) {
                $('#speechStatusIndicator').html('<i class="bi bi-hourglass-split text-emerald me-1"></i> Analyzing Audio...').removeClass('bg-danger-subtle text-danger bg-success-subtle text-success').addClass('bg-light text-muted');
                $('#micStatusHeading').text('Analyzing your recitation...');
                $('#micStatusDesc').text('Computing phonetic alignment and Tajweed rules with the Quranic text...');
                submitRecitation(textToAnalyze);
            } else {
                $('#speechStatusIndicator').html('<i class="bi bi-mic-fill text-secondary me-1"></i> Mic Ready (ar-SA)').removeClass('bg-danger-subtle text-danger bg-success-subtle text-success').addClass('bg-light text-muted');
                $('#micStatusHeading').text('Click Microphone to Recite');
                $('#micStatusDesc').text('Recite clearly in Arabic. The AI automatically detects when you complete the Ayah.');
                if (shouldSubmit && textToAnalyze.length === 0) {
                    $('#liveTranscript').text('No speech recognized. Please verify your mic and recite again.').addClass('text-danger');
                }
            }
        }

        // Record Button click - toggle on/off
        $('#btnRecord').off('click').on('click', function(e) {
            e.preventDefault();
            if (!isListening) {
                startRecitationListening();
            } else {
                stopRecitationListening(true);
            }
        });

        function stopModalRecordingUI() {
            isModalRecording = false;
            $('#btnModalRecord').removeClass('pulse-recording').css('background', 'var(--emerald-gradient)');
            $('#modalMicIcon').removeClass('bi-stop-fill').addClass('bi-mic-fill');
        }

        // Modal mic button
        $('#btnModalRecord').on('click', function() {
            if (!modalRecognition) {
                alert('Speech recognition is not supported in this browser.');
                return;
            }
            if (!isModalRecording) {
                try {
                    modalRecognition.start();
                } catch(e) {
                    console.log('Modal start error:', e);
                }
            } else {
                modalRecognition.stop();
                stopModalRecordingUI();
            }
        });

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

        // Clear Session History (Privacy compliance)
        $('#btnClearSession').on('click', function() {
            if (!sessionId) {
                alert('No active session history to clear.');
                return;
            }
            if (confirm('Are you sure you want to delete all practice history and attempts for this session?')) {
                $.post("{{ route('ai-teacher.delete-session-history') }}", {
                    session_id: sessionId,
                    _token: "{{ csrf_token() }}"
                }, function(res) {
                    resetAssessmentUI();
                    alert(res.message || 'Session history deleted.');
                    startNewSession();
                }).fail(function() {
                    alert('Could not delete session history.');
                });
            }
        });

        // Play Reference Qari Audio
        var refAudio = document.getElementById('referenceAudioPlayer');
        var wordAudio = document.getElementById('wordAudioPlayer');

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

        // Play individual word audio
        function playWordAudioUrl(audioUrl) {
            if (!audioUrl) return;
            if (!refAudio.paused) {
                refAudio.pause();
                $('#refAudioIcon').removeClass('bi-pause-fill').addClass('bi-volume-up-fill');
                $('#refAudioLabel').text('Listen to Qari');
            }
            wordAudio.src = audioUrl;
            wordAudio.play();
        }

        // Voice-Based Error Explanation via Web SpeechSynthesis API
        function speakTeacherExplanation(text, lang, onEndCallback) {
            if (!window.speechSynthesis) {
                console.log('SpeechSynthesis API not supported.');
                if (onEndCallback) onEndCallback();
                return;
            }

            // Cancel any ongoing speech
            window.speechSynthesis.cancel();

            var utterance = new SpeechSynthesisUtterance(text);
            var selectedLang = lang || $('#teacherVoiceLanguage').val() || 'en';

            if (selectedLang === 'ur') {
                utterance.lang = 'ur-PK';
            } else if (selectedLang === 'ar') {
                utterance.lang = 'ar-SA';
            } else {
                utterance.lang = 'en-US';
            }

            utterance.rate = 0.95;
            utterance.pitch = 1.0;

            // Highlight status during speech
            $('#speechStatusIndicator').html('<i class="bi bi-volume-up-fill text-success me-1"></i> Teacher Speaking...').addClass('bg-success-subtle text-success');

            utterance.onend = function() {
                $('#speechStatusIndicator').html('<i class="bi bi-mic-fill text-secondary me-1"></i> Mic Ready (ar-SA)').removeClass('bg-success-subtle text-success').addClass('bg-light text-muted');
                if (onEndCallback) onEndCallback();
            };

            utterance.onerror = function() {
                $('#speechStatusIndicator').html('<i class="bi bi-mic-fill text-secondary me-1"></i> Mic Ready (ar-SA)').removeClass('bg-success-subtle text-success').addClass('bg-light text-muted');
                if (onEndCallback) onEndCallback();
            };

            window.speechSynthesis.speak(utterance);
        }

        // Teacher's Note button speak
        $('#btnSpeakTeacherNote').on('click', function() {
            var note = $('#feedbackMessage').text().trim();
            speakTeacherExplanation(note, $('#teacherVoiceLanguage').val());
        });

        // AJAX: Load Ayah data
        function loadAyahData(surah, ayah) {
            resetAssessmentUI();
            $('#targetArabicText').html('<div class="spinner-border text-emerald" role="status"></div>');
            $('#selectedWordCard').addClass('d-none');

            $.get('/ai-teacher/ayah/' + surah + '/' + ayah, function(res) {
                if (res.ayah) {
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

                    // Store target text and words for auto-stop completion matching
                    targetVerseText = res.ayah.text;
                    targetWordsClean = normalizeArabicText(res.ayah.text).split(/\s+/).filter(Boolean);

                    // Store words
                    currentAyahWords = res.words && res.words.length > 0 ? res.words : [];
                    renderInteractiveVerse(res.ayah.text, currentAyahWords);

                    // Pre-fill manual input with target text as convenience for testing
                    $('#manualArabicInput').val(res.ayah.text);
                }
            }).fail(function() {
                $('#targetArabicText').text('Unable to load verse. Please verify network connection.');
            });
        }

        // Render interactive Quranic text with word-level span wrappers
        function renderInteractiveVerse(verseText, words) {
            var wordsCount = words && words.length > 0 ? words.length : (verseText.trim().split(/\s+/).length);
            $('#verseWordCount').text(wordsCount + ' words');

            var html = '';
            if (words && words.length > 0) {
                words.forEach(function(w) {
                    html += '<span class="quran-word-item" id="qword-' + w.position + '" data-pos="' + w.position + '" data-word="' + w.text_uthmani + '" data-audio="' + (w.audio_url || '') + '" data-translit="' + (w.transliteration || '') + '" data-trans="' + (w.translation || '') + '">' +
                        w.text_uthmani +
                    '</span> ';
                });
            } else {
                var tokens = verseText.trim().split(/\s+/);
                tokens.forEach(function(tok, idx) {
                    var pos = idx + 1;
                    html += '<span class="quran-word-item" id="qword-' + pos + '" data-pos="' + pos + '" data-word="' + tok + '" data-audio="" data-translit="" data-trans="">' +
                        tok +
                    '</span> ';
                });
            }

            $('#targetArabicText').html(html);

            // Bind click on word spans
            $('.quran-word-item').on('click', function() {
                $('.quran-word-item').removeClass('qword-selected');
                $(this).addClass('qword-selected');

                var wordText = $(this).data('word');
                var audioUrl = $(this).data('audio');
                var translit = $(this).data('translit') || '';
                var trans = $(this).data('trans') || '';
                var pos = $(this).data('pos');

                activeSelectedWord = {
                    text: wordText,
                    audio: audioUrl,
                    translit: translit,
                    trans: trans,
                    pos: pos
                };

                // Populate and reveal the selected word quick inspector
                $('#inspectorArabicWord').text(wordText);
                $('#inspectorTranslit').text(translit || ('Word #' + pos));
                $('#inspectorTranslation').text(trans || 'Quranic Word');
                $('#selectedWordCard').removeClass('d-none');

                // If audio url available, play word audio immediately
                if (audioUrl) {
                    playWordAudioUrl(audioUrl);
                }
            });
        }

        // Quick Word Inspector Actions
        $('#btnInspectorPlay').on('click', function() {
            if (activeSelectedWord && activeSelectedWord.audio) {
                playWordAudioUrl(activeSelectedWord.audio);
            } else if (refAudioUrl) {
                playWordAudioUrl(refAudioUrl);
            } else {
                alert('Audio for this word is not available.');
            }
        });

        $('#btnInspectorSpeak').on('click', function() {
            if (!activeSelectedWord) return;
            var lang = $('#teacherVoiceLanguage').val();
            var text = "";
            if (lang === 'ur') {
                text = "یہ لفظ " + activeSelectedWord.text + " ہے۔ تلاوت سنیں اور دہرائیں۔";
            } else if (lang === 'ar') {
                text = "هذه كلمة " + activeSelectedWord.text + ". استمع للقارئ وأعد النطق.";
            } else {
                text = "The word is " + activeSelectedWord.text + ". Listen to the Qari and practice.";
            }
            speakTeacherExplanation(text, lang);
        });

        $('#btnInspectorPractice').on('click', function() {
            if (!activeSelectedWord) return;
            openSingleWordPractice(activeSelectedWord.text, activeSelectedWord.audio, "Practice this word carefully.");
        });

        // Open Targeted Single-Word Practice Gym Modal
        function openSingleWordPractice(targetWord, audioUrl, tip) {
            $('#modalTargetWord').text(targetWord);
            $('#modalWordTajweedTip').text(tip || 'Pronounce clearly according to Tajweed makhraj.');
            $('#modalRecognizedWord').text('');
            $('#modalResultBox').addClass('d-none').html('');
            $('#modalMicStatus').text('Click microphone and recite this word');

            // Wire up modal mic button with fresh instance
            $('#btnModalRecord').off('click').on('click', function(e) {
                e.preventDefault();
                const SpeechRec = window.SpeechRecognition || window.webkitSpeechRecognition;
                if (!SpeechRec) {
                    alert('Speech recognition is not supported in this browser.');
                    return;
                }

                if (isModalRecording) {
                    if (activeModalRecognition) {
                        try { activeModalRecognition.stop(); } catch(e) {}
                    }
                    stopModalRecordingUI();
                    return;
                }

                try {
                    if (activeModalRecognition) {
                        try { activeModalRecognition.abort(); } catch(e) {}
                    }

                    activeModalRecognition = new SpeechRec();
                    activeModalRecognition.continuous = false;
                    activeModalRecognition.interimResults = false;
                    activeModalRecognition.lang = 'ar-SA';
                    activeModalRecognition.maxAlternatives = 1;

                    activeModalRecognition.onstart = function() {
                        isModalRecording = true;
                        $('#btnModalRecord').addClass('pulse-recording').css('background', '#ef4444');
                        $('#modalMicIcon').removeClass('bi-mic-fill').addClass('bi-stop-fill');
                        $('#modalMicStatus').text('Listening for this single word...');
                        $('#modalRecognizedWord').text('');
                        $('#modalResultBox').addClass('d-none');
                    };

                    activeModalRecognition.onresult = function(event) {
                        var spokenWord = event.results[0][0].transcript.trim();
                        $('#modalRecognizedWord').text(spokenWord);
                        stopModalRecordingUI();
                        submitSingleWordPractice(spokenWord);
                    };

                    activeModalRecognition.onerror = function(event) {
                        console.warn('Modal speech error:', event.error);
                        stopModalRecordingUI();
                        $('#modalMicStatus').text('Could not hear word. Click mic and try again.');
                    };

                    activeModalRecognition.onend = function() {
                        stopModalRecordingUI();
                    };

                    activeModalRecognition.start();
                } catch(err) {
                    console.error("Modal start error:", err);
                    stopModalRecordingUI();
                }
            });

            $('#singleWordModal').off('hidden.bs.modal').on('hidden.bs.modal', function() {
                if (activeModalRecognition) {
                    try { activeModalRecognition.abort(); } catch(e) {}
                    activeModalRecognition = null;
                }
                stopModalRecordingUI();
            });

            singleWordModal.show();
        }

        // Submit Single Word Practice
        function submitSingleWordPractice(spokenWord) {
            var targetWord = $('#modalTargetWord').text().trim();
            $('#modalMicStatus').text('Analyzing word accuracy...');

            $.post("{{ route('ai-teacher.analyze-word') }}", {
                target_word: targetWord,
                recognized_word: spokenWord,
                surah_number: currentSurah,
                ayah_number: currentAyah,
                _token: "{{ csrf_token() }}"
            }, function(res) {
                var isMatched = res.matched;
                var score = Math.round(res.similarity * 100);

                if (isMatched) {
                    $('#modalResultBox').html(
                        '<div class="alert alert-success border-0 rounded-4 p-3 shadow-sm">' +
                            '<i class="bi bi-check-circle-fill text-success fs-3 d-block mb-1"></i>' +
                            '<strong class="d-block text-dark">Masha\'Allah! Word Pronounced Correctly!</strong>' +
                            '<small class="text-success">' + (res.teacher_tip || 'Accurate pronunciation.') + ' (' + score + '% match)</small>' +
                        '</div>'
                    ).removeClass('d-none');
                    $('#modalMicStatus').text('Passed! Excellent!');

                    // Update the word in the verse to matched!
                    $('.quran-word-item').each(function() {
                        if ($(this).text().trim() === targetWord) {
                            $(this).removeClass('qword-error qword-uncertain').addClass('qword-matched');
                        }
                    });

                    // Voice praise
                    var lang = $('#teacherVoiceLanguage').val();
                    var praise = res['spoken_explanation_' + lang] || res.spoken_explanation_en || 'Excellent! Correct pronunciation.';
                    speakTeacherExplanation(praise, lang);
                } else {
                    $('#modalResultBox').html(
                        '<div class="alert alert-warning border-0 rounded-4 p-3 shadow-sm">' +
                            '<i class="bi bi-exclamation-triangle-fill text-warning fs-3 d-block mb-1"></i>' +
                            '<strong class="d-block text-dark">Keep Trying!</strong>' +
                            '<small class="text-secondary">' + (res.teacher_tip || 'Listen to the Qari and repeat.') + '</small>' +
                        '</div>'
                    ).removeClass('d-none');
                    $('#modalMicStatus').text('Try again for higher accuracy');
                }
            }).fail(function() {
                $('#modalResultBox').html('<div class="alert alert-danger p-2 small">Error analyzing word.</div>').removeClass('d-none');
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
            latestAnalysis = data.analysis;
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

            // Reset word states in the verse box
            $('.quran-word-item').removeClass('qword-matched qword-uncertain qword-error');

            // Word breakdown & mistakes collection
            var wordsHtml = '';
            currentMistakes = [];

            if (analysis.word_analysis && analysis.word_analysis.length > 0) {
                analysis.word_analysis.forEach(function(item) {
                    var target = item.target_word || item.word || '';
                    var heard = item.recognized_word || '';
                    var status = item.status || (item.matched ? 'correct' : 'incorrect');
                    var position = item.position;

                    // Match with verse span
                    var $verseWordSpan = $('#qword-' + position);

                    var cls = 'word-matched';
                    var statusLabel = '<i class="bi bi-check-lg text-success"></i> Correct';

                    // Find corresponding audio URL from currentAyahWords
                    var wordAudioUrl = "";
                    if (currentAyahWords && currentAyahWords[position - 1]) {
                        wordAudioUrl = currentAyahWords[position - 1].audio_url || "";
                    }

                    if (status === 'correct') {
                        $verseWordSpan.addClass('qword-matched');
                    } else if (status === 'uncertain') {
                        cls = 'word-uncertain';
                        $verseWordSpan.addClass('qword-uncertain');
                        statusLabel = '<i class="bi bi-exclamation-triangle-fill text-warning"></i> Heard: "' + (heard || '...') + '"';
                        currentMistakes.push({
                            target: target,
                            heard: heard || '(Unclear)',
                            status: 'uncertain',
                            position: position,
                            audioUrl: wordAudioUrl,
                            makhraj: item.makhraj || 'Oral Articulation',
                            tajweed: item.tajweed_rule || 'Tartil',
                            advice: item.teacher_tip || 'Check the makhraj (articulation point) and elongation.',
                            spoken_en: item.spoken_explanation_en || ('In the word ' + target + ', pay attention to pronunciation.'),
                            spoken_ur: item.spoken_explanation_ur || ('لفظ ' + target + ' کے تلفظ پر توجہ دیں۔'),
                            spoken_ar: item.spoken_explanation_ar || ('في كلمة ' + target + '، يُرجى الانتباه للمخرج.')
                        });
                    } else if (status === 'incorrect') {
                        cls = 'word-missing';
                        $verseWordSpan.addClass('qword-error');
                        statusLabel = '<i class="bi bi-x-circle-fill text-danger"></i> Heard: "' + (heard || 'Wrong') + '"';
                        currentMistakes.push({
                            target: target,
                            heard: heard || '(Mispronounced)',
                            status: 'incorrect',
                            position: position,
                            audioUrl: wordAudioUrl,
                            makhraj: item.makhraj || 'Oral Articulation',
                            tajweed: item.tajweed_rule || 'Tartil',
                            advice: item.teacher_tip || 'Listen to the reference Qari audio and repeat this word.',
                            spoken_en: item.spoken_explanation_en || ('You made a mistake in this word. The correct word is ' + target + '.'),
                            spoken_ur: item.spoken_explanation_ur || ('آپ نے اس لفظ میں غلطی کی ہے۔ صحیح لفظ ' + target + ' ہے۔'),
                            spoken_ar: item.spoken_explanation_ar || ('لقد حدث خطأ في نطق هذه الكلمة. الكلمة الصحيحة هي ' + target + '.')
                        });
                    } else if (status === 'missing') {
                        cls = 'word-missing';
                        $verseWordSpan.addClass('qword-error');
                        statusLabel = '<i class="bi bi-dash-circle-fill text-danger"></i> Missed';
                        currentMistakes.push({
                            target: target,
                            heard: '(Omitted / Skipped)',
                            status: 'missing',
                            position: position,
                            audioUrl: wordAudioUrl,
                            makhraj: item.makhraj || 'Sequence',
                            tajweed: item.tajweed_rule || 'Recitation Order',
                            advice: 'Do not rush; pronounce every word in sequence without omission.',
                            spoken_en: item.spoken_explanation_en || ('The word ' + target + ' was skipped.'),
                            spoken_ur: item.spoken_explanation_ur || ('لفظ ' + target + ' چھوٹ گیا ہے۔'),
                            spoken_ar: item.spoken_explanation_ar || ('تم تخطي كلمة ' + target + '.')
                        });
                    } else if (status === 'repeated') {
                        cls = 'word-uncertain';
                        $verseWordSpan.addClass('qword-uncertain');
                        statusLabel = '<i class="bi bi-arrow-repeat text-warning"></i> Repeated';
                        currentMistakes.push({
                            target: target,
                            heard: heard + ' (Repeated)',
                            status: 'repeated',
                            position: position,
                            audioUrl: wordAudioUrl,
                            makhraj: item.makhraj || 'Pacing',
                            tajweed: item.tajweed_rule || 'Waqf & Ibtida',
                            advice: 'Avoid repeating words consecutively unless stopping and restarting properly.',
                            spoken_en: item.spoken_explanation_en || ('The word ' + target + ' was repeated.'),
                            spoken_ur: item.spoken_explanation_ur || ('لفظ ' + target + ' کو دہرایا گیا ہے۔'),
                            spoken_ar: item.spoken_explanation_ar || ('تم تكرار كلمة ' + target + '.')
                        });
                    } else if (status === 'extra') {
                        cls = 'word-uncertain';
                        statusLabel = '<i class="bi bi-plus-circle text-muted"></i> Extra Word';
                    }

                    wordsHtml += '<div class="word-feedback ' + cls + '" data-pos="' + position + '" style="cursor: pointer;">' +
                        '<span class="arabic-word">' + (target || heard) + '</span>' +
                        '<span class="heard-status">' + statusLabel + '</span>' +
                    '</div>';
                });
            } else {
                wordsHtml = '<span class="text-muted small fst-italic d-block text-center py-3">No word breakdown available.</span>';
            }
            $('#wordAlignmentContainer').html(wordsHtml);

            // Bind click on word pills in alignment container to trigger inspector
            $('.word-feedback').on('click', function() {
                var pos = $(this).data('pos');
                var $targetSpan = $('#qword-' + pos);
                if ($targetSpan.length) {
                    $targetSpan.trigger('click');
                }
            });

            // Render Dedicated Pronunciation Corrections Breakdown
            renderMistakesPanel(currentMistakes);

            $('#micStatusHeading').text('Recitation Evaluated!');
            $('#micStatusDesc').text('Review your word breakdown above or click any word to practice individually.');

            // Auto-speak voice teacher explanation if enabled
            if ($('#toggleSpokenFeedback').is(':checked') && $('#toggleAutoSpeak').is(':checked') && currentMistakes.length > 0) {
                var lang = $('#teacherVoiceLanguage').val();
                var firstMistake = currentMistakes[0];
                var spokenMsg = firstMistake['spoken_' + lang] || firstMistake.spoken_en;
                speakTeacherExplanation(spokenMsg, lang);
            }
        }

        // Render Mistakes Breakdown Panel with Spoken Teacher Controls
        function renderMistakesPanel(mistakes) {
            if (mistakes.length > 0) {
                var mistakesHtml = '<div class="card border border-warning border-opacity-50 rounded-4 p-3 bg-white shadow-sm">' +
                    '<div class="d-flex flex-wrap align-items-center justify-content-between mb-3 pb-2 border-bottom gap-2">' +
                        '<div>' +
                            '<h6 class="fw-bold text-dark mb-0"><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>Pronunciation Corrections (' + mistakes.length + ' ' + (mistakes.length === 1 ? 'word needs' : 'words need') + ' practice)</h6>' +
                            '<small class="text-secondary">Click "Speak" to hear voice teacher guidance or "Practice" to repeat that word:</small>' +
                        '</div>' +
                        '<button type="button" class="btn btn-sm btn-gold rounded-pill px-3 fw-bold shadow-sm" id="btnSpeakAllCorrections">' +
                            '<i class="bi bi-volume-up-fill me-1"></i> Listen to All Corrections' +
                        '</button>' +
                    '</div>' +
                    '<div class="table-responsive">' +
                        '<table class="table table-sm table-bordered align-middle mb-0">' +
                            '<thead class="table-light small text-muted">' +
                                '<tr>' +
                                    '<th class="text-end" style="min-width: 110px;">Quran Word</th>' +
                                    '<th class="text-end" style="min-width: 110px;">Heard</th>' +
                                    '<th>Status & Tajweed</th>' +
                                    '<th style="min-width: 140px;">Actions</th>' +
                                '</tr>' +
                            '</thead>' +
                            '<tbody>';

                mistakes.forEach(function(m, idx) {
                    var badgeCls = m.status === 'uncertain'
                        ? 'bg-warning-subtle text-warning-emphasis border-warning-subtle'
                        : 'bg-danger-subtle text-danger border-danger-subtle';

                    mistakesHtml += '<tr id="mistake-row-' + idx + '">' +
                        '<td class="font-arabic fs-4 text-emerald fw-bold text-end" style="line-height:1.2;">' + m.target + '</td>' +
                        '<td class="font-arabic fs-5 text-danger text-end" style="line-height:1.2;">' + m.heard + '</td>' +
                        '<td>' +
                            '<span class="badge border ' + badgeCls + ' mb-1">' + m.status.toUpperCase() + '</span>' +
                            '<div class="small fw-semibold text-dark">' + m.tajweed + '</div>' +
                            '<div class="small text-secondary">' + m.advice + '</div>' +
                        '</td>' +
                        '<td>' +
                            '<div class="d-flex flex-wrap gap-1">' +
                                '<button type="button" class="btn btn-xs btn-outline-emerald rounded-pill btn-row-play" data-idx="' + idx + '" title="Play Verified Qari Audio">' +
                                    '<i class="bi bi-volume-up-fill"></i> Qari' +
                                '</button>' +
                                '<button type="button" class="btn btn-xs btn-outline-primary rounded-pill btn-row-speak" data-idx="' + idx + '" title="Speak Voice Teacher Explanation">' +
                                    '<i class="bi bi-chat-quote-fill"></i> Speak' +
                                '</button>' +
                                '<button type="button" class="btn btn-xs btn-emerald rounded-pill btn-row-practice" data-idx="' + idx + '" title="Practice Repeating This Word">' +
                                    '<i class="bi bi-mic"></i> Practice' +
                                '</button>' +
                            '</div>' +
                        '</td>' +
                    '</tr>';
                });

                mistakesHtml += '</tbody></table></div></div>';
                $('#mistakesContainer').html(mistakesHtml).removeClass('d-none');

                // Bind Master "Listen to All Corrections" Button
                $('#btnSpeakAllCorrections').on('click', function() {
                    speakAllMistakesSequentially(0);
                });

                // Row action buttons
                $('.btn-row-play').on('click', function() {
                    var idx = $(this).data('idx');
                    var m = mistakes[idx];
                    if (m && m.audioUrl) {
                        playWordAudioUrl(m.audioUrl);
                    } else if (refAudioUrl) {
                        playWordAudioUrl(refAudioUrl);
                    }
                });

                $('.btn-row-speak').on('click', function() {
                    var idx = $(this).data('idx');
                    var m = mistakes[idx];
                    if (m) {
                        var lang = $('#teacherVoiceLanguage').val();
                        var text = m['spoken_' + lang] || m.spoken_en;
                        speakTeacherExplanation(text, lang);
                    }
                });

                $('.btn-row-practice').on('click', function() {
                    var idx = $(this).data('idx');
                    var m = mistakes[idx];
                    if (m) {
                        openSingleWordPractice(m.target, m.audioUrl, m.advice);
                    }
                });
            } else {
                $('#mistakesContainer').html(
                    '<div class="alert alert-success border-0 rounded-4 p-3 shadow-sm d-flex align-items-center gap-2">' +
                        '<i class="bi bi-check-circle-fill text-success fs-4"></i>' +
                        '<div>' +
                            '<strong class="d-block text-dark">Masha\'Allah! Accurate Recitation!</strong>' +
                            '<small class="text-success">Every word in this verse was pronounced accurately with correct Tajweed sequence.</small>' +
                        '</div>' +
                    '</div>'
                ).removeClass('d-none');
            }
        }

        // Speak all mistakes sequentially with pauses
        function speakAllMistakesSequentially(index) {
            if (!currentMistakes || index >= currentMistakes.length) return;
            var m = currentMistakes[index];
            var lang = $('#teacherVoiceLanguage').val();
            var text = m['spoken_' + lang] || m.spoken_en;

            // Highlight the row being spoken
            $('tr[id^="mistake-row-"]').removeClass('table-warning');
            $('#mistake-row-' + index).addClass('table-warning');

            speakTeacherExplanation(text, lang, function() {
                setTimeout(function() {
                    speakAllMistakesSequentially(index + 1);
                }, 700);
            });
        }

        function resetAssessmentUI() {
            clearTimeout(autoStopAyahTimer);
            clearTimeout(silenceTimer);
            if (isListening) {
                stopRecitationListening(false);
            }
            $('#scoreDisplay').text('--%').removeClass('text-success text-warning text-danger').addClass('text-emerald');
            $('#scoreProgressBar').css('width', '0%');
            $('#resultLabel').text('Awaiting Recitation').removeClass('text-success text-warning text-danger');
            $('#wordAlignmentContainer').html('<span class="text-muted small fst-italic d-block text-center py-3">Recite the verse to view word breakdown</span>');
            $('#mistakesContainer').html('').addClass('d-none');
            $('#feedbackMessage').text('Press the microphone button and recite the Holy verse clearly. Listen to the reference Qari first if needed.');
            $('#micStatusHeading').text('Click Microphone to Recite');
            $('#micStatusDesc').text('Recite clearly in Arabic. The AI automatically detects when you complete the Ayah.');
            $('#speechStatusIndicator').html('<i class="bi bi-mic-fill text-secondary me-1"></i> Mic Ready (ar-SA)').removeClass('bg-danger-subtle bg-success-subtle bg-warning-subtle text-danger text-success text-warning').addClass('bg-light text-muted');
            $('.quran-word-item').removeClass('qword-matched qword-uncertain qword-error qword-selected');
            $('#selectedWordCard').addClass('d-none');
            currentMistakes = [];
        }
    });
</script>
@endpush
@endsection
