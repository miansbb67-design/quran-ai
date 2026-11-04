<?php
ob_start();
?>

<div class="container py-4 pb-5">
    <div class="text-center mb-5">
        <div class="badge px-3 py-2 rounded-pill mb-2" style="background: rgba(212, 175, 55, 0.15); border: 1px solid rgba(212, 175, 55, 0.4); color: #d4af37;">
            <i class="bi bi-mic me-1"></i> Interactive Quranic Pronunciation Coach
        </div>
        <h1 class="fw-bold text-white mb-2">AI Quran Recitation Teacher</h1>
        <p class="text-white-50 mx-auto" style="max-width: 650px;">
            Select an ayah, listen to an authentic reference Qari, and recite via microphone to receive immediate word-by-word difference detection.
        </p>
    </div>

    <div class="row g-4">
        <!-- Practice Workspace Column -->
        <div class="col-lg-8">
            <div class="glass-card p-4 border-gold mb-4">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small">Select Surah</label>
                        <select id="teacherSurahSelect" class="form-select bg-dark text-white border-secondary">
                            <?php foreach ($surahs as $s): ?>
                                <option value="<?= $s['id'] ?>">Surah <?= $s['id'] ?>: <?= htmlspecialchars($s['name_english']) ?> (<?= $s['name_arabic'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-white-50 small">Ayah Number</label>
                        <input type="number" id="teacherAyahNum" class="form-control bg-dark text-white border-secondary" value="1" min="1" max="7">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button id="btnLoadAyah" class="btn btn-outline-gold w-100"><i class="bi bi-arrow-repeat me-1"></i> Load Ayah</button>
                    </div>
                </div>

                <!-- Ayah Display Box -->
                <div class="p-4 rounded-3 text-center mb-4" style="background: rgba(3, 28, 22, 0.7); border: 1px solid var(--border-glass);">
                    <small class="text-gold fw-bold text-uppercase d-block mb-2" id="displayAyahTitle">Surah Al-Fatihah — Ayah 1</small>
                    <h2 class="arabic-text text-white display-5 mb-3" id="displayArabicText">بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ</h2>
                    <p class="text-white-50 mb-3" id="displayTranslation">In the name of Allah, the Entirely Merciful, the Especially Merciful.</p>
                    
                    <button id="btnPlayReference" class="btn btn-outline-gold btn-sm"><i class="bi bi-volume-up me-1"></i> Listen to Reference Qari</button>
                    <audio id="refAudio" preload="none"></audio>
                </div>

                <!-- Recitation Microphone & Recording Section -->
                <div class="text-center py-3">
                    <div class="mb-3">
                        <button id="btnRecordToggle" class="btn btn-gold btn-lg rounded-circle p-4 shadow-lg position-relative" style="width: 90px; height: 90px;">
                            <i class="bi bi-mic-fill fs-1" id="micIcon"></i>
                        </button>
                    </div>
                    <h5 class="fw-bold text-white mb-1" id="recordingStatusText">Tap to Recite Ayah</h5>
                    <p class="text-white-50 small mb-0">Allow microphone permission in your browser when prompted.</p>
                </div>

                <!-- Live Speech Transcript Box -->
                <div class="mt-4 p-3 rounded bg-dark border border-secondary">
                    <label class="form-label text-white-50 small mb-1">Your Recitation (Recognized Speech):</label>
                    <p class="arabic-text text-warning fs-4 mb-0" id="speechTranscriptText">Waiting for microphone recitation...</p>
                </div>
            </div>

            <!-- AI Word-by-Word Feedback Card -->
            <div class="glass-card p-4 border-gold d-none" id="feedbackCard">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-white mb-0"><i class="bi bi-patch-check-fill text-gold me-2"></i>Recitation Analysis</h5>
                    <span class="badge fs-6 px-3 py-2" id="accuracyBadge">Accuracy: 0%</span>
                </div>

                <div class="p-3 rounded bg-dark mb-3">
                    <label class="text-white-50 small mb-2">Word-by-word Alignment:</label>
                    <div id="wordByWordContainer" class="d-flex flex-wrap gap-2 direction-rtl" style="direction: rtl;"></div>
                </div>

                <div class="alert alert-info py-2 small mb-0">
                    <i class="bi bi-info-circle me-1"></i> <strong>Academic & Tajweed Notice:</strong> This speech analysis is an educational recitation aid. Tajweed rules (Ghunnah, Qalqalah, Ikhfa) require qualified human verification.
                </div>
            </div>
        </div>

        <!-- Sidebar / Recent Practice Column -->
        <div class="col-lg-4">
            <div class="glass-card p-4 border-gold mb-4">
                <h6 class="text-gold fw-bold text-uppercase mb-3"><i class="bi bi-clock-history me-1"></i> Recent Practices</h6>
                <?php if (empty($recentSessions)): ?>
                    <p class="text-white-50 small mb-0">No recorded practice sessions yet. Start reciting to see your history!</p>
                <?php else: ?>
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($recentSessions as $session): ?>
                            <li class="pb-3 mb-3 border-bottom border-secondary d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white mb-0"><?= htmlspecialchars($session['name_english']) ?> (Ayah <?= $session['ayah_number'] ?>)</h6>
                                    <small class="text-white-50"><?= date('M d, H:i', strtotime($session['created_at'])) ?></small>
                                </div>
                                <span class="badge <?= ($session['status'] === 'passed') ? 'bg-success' : 'bg-warning text-dark' ?>">
                                    <?= round($session['accuracy_score']) ?>%
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <div class="glass-card p-4">
                <h6 class="text-gold fw-bold text-uppercase mb-3"><i class="bi bi-lightbulb me-1"></i> Recitation Tips</h6>
                <ul class="text-white-50 small ps-3 mb-0">
                    <li class="mb-2">Recite in a quiet room with minimal background noise.</li>
                    <li class="mb-2">Listen to the reference Qari recitation first to align pacing.</li>
                    <li class="mb-2">Pronounce letters clearly from their correct exit points (Makharij).</li>
                    <li>Repeat difficult ayahs until your match score reaches 90%+.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
let isRecording = false;
let recognition = null;
let currentAyahData = {
    surahId: 1,
    ayahNum: 1,
    text: "بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ",
    audioNum: "001001.mp3"
};

// Check Web Speech API support
const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
if (SpeechRecognition) {
    recognition = new SpeechRecognition();
    recognition.lang = 'ar-SA'; // Arabic recitation recognition
    recognition.continuous = false;
    recognition.interimResults = true;

    recognition.onresult = function(event) {
        let transcript = '';
        for (let i = event.resultIndex; i < event.results.length; ++i) {
            transcript += event.results[i][0].transcript;
        }
        document.getElementById('speechTranscriptText').innerText = transcript;

        if (event.results[0].isFinal) {
            stopRecordingAndAnalyze(transcript);
        }
    };

    recognition.onerror = function(event) {
        console.error("Speech Recognition Error:", event.error);
        document.getElementById('recordingStatusText').innerText = "Microphone error: " + event.error;
        stopRecordingUI();
    };

    recognition.onend = function() {
        stopRecordingUI();
    };
} else {
    document.getElementById('speechTranscriptText').innerText = "Web Speech API is not supported in this browser. Please use Google Chrome or Microsoft Edge.";
}

const btnRecord = document.getElementById('btnRecordToggle');
btnRecord.addEventListener('click', function() {
    if (!recognition) {
        alert("Microphone speech recognition requires Google Chrome or Microsoft Edge.");
        return;
    }

    if (!isRecording) {
        try {
            recognition.start();
            isRecording = true;
            document.getElementById('recordingStatusText').innerText = "Listening... Recite the Ayah now!";
            document.getElementById('micIcon').className = 'bi bi-stop-circle-fill fs-1 text-danger';
            document.getElementById('speechTranscriptText').innerText = "Listening...";
        } catch(e) {
            console.error(e);
        }
    } else {
        recognition.stop();
        stopRecordingUI();
    }
});

function stopRecordingUI() {
    isRecording = false;
    document.getElementById('recordingStatusText').innerText = "Tap to Recite Ayah";
    document.getElementById('micIcon').className = 'bi bi-mic-fill fs-1';
}

function stopRecordingAndAnalyze(recitedText) {
    stopRecordingUI();
    document.getElementById('recordingStatusText').innerText = "Analyzing recitation...";

    fetch(`<?= Controller::url('/api/ai-teacher/evaluate') ?>`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `surah_id=${currentAyahData.surahId}&ayah_number=${currentAyahData.ayahNum}&recognized_text=${encodeURIComponent(recitedText)}&target_text=${encodeURIComponent(currentAyahData.text)}&csrf_token=<?= $csrfToken ?>`
    })
    .then(r => r.json())
    .then(res => {
        if (!res.success) {
            alert(res.error || "Analysis failed");
            return;
        }

        const feedbackCard = document.getElementById('feedbackCard');
        feedbackCard.classList.remove('d-none');

        const badge = document.getElementById('accuracyBadge');
        badge.innerText = `Accuracy: ${res.accuracy}%`;
        badge.className = `badge fs-6 px-3 py-2 ${res.accuracy >= 80 ? 'bg-success' : 'bg-warning text-dark'}`;

        let wordsHtml = '';
        res.word_diffs.forEach(w => {
            if (w.status === 'correct') {
                wordsHtml += `<span class="badge bg-success-subtle text-success border border-success fs-5 p-2">${w.word}</span>`;
            } else {
                wordsHtml += `<span class="badge bg-danger-subtle text-danger border border-danger fs-5 p-2" title="Check pronunciation">${w.word}</span>`;
            }
        });
        document.getElementById('wordByWordContainer').innerHTML = wordsHtml;
        document.getElementById('recordingStatusText').innerText = "Analysis Complete!";
    })
    .catch(err => {
        console.error(err);
        document.getElementById('recordingStatusText').innerText = "Failed to evaluate recitation.";
    });
}

// Reference Audio Player
document.getElementById('btnPlayReference').addEventListener('click', function() {
    const audio = document.getElementById('refAudio');
    audio.src = `https://everyayah.com/data/Alafasy_128kbps/${currentAyahData.audioNum}`;
    audio.play();
});

// Load Ayah details
document.getElementById('btnLoadAyah').addEventListener('click', function() {
    const sId = document.getElementById('teacherSurahSelect').value;
    const aNum = document.getElementById('teacherAyahNum').value;

    fetch(`<?= Controller::url('/api/surah/') ?>` + sId)
        .then(r => r.json())
        .then(res => {
            const ayah = res.ayahs.find(a => a.number == aNum) || res.ayahs[0];
            currentAyahData = {
                surahId: sId,
                ayahNum: ayah.number,
                text: ayah.text,
                audioNum: ayah.audio_num
            };
            document.getElementById('displayAyahTitle').innerText = `Surah ${sId} — Ayah ${ayah.number}`;
            document.getElementById('displayArabicText').innerText = ayah.text;
            document.getElementById('displayTranslation').innerText = ayah.translation;
            document.getElementById('feedbackCard').classList.add('d-none');
            document.getElementById('speechTranscriptText').innerText = "Waiting for microphone recitation...";
        });
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>

