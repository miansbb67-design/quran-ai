<?php
ob_start();
?>

<!-- Life Deen Subpage Header -->
<div class="container" style="padding-top:2rem; padding-bottom:1rem;">
  <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; border-bottom:1px solid var(--border); padding-bottom:1.5rem; margin-bottom:2rem;">
    <div>
      <span class="badge badge-gold" style="margin-bottom:0.5rem; display:inline-block;"><i class="bi bi-mic me-1"></i> Interactive Voice AI</span>
      <h1 style="font-size:2rem; font-weight:800; color:var(--text-main); margin:0;">AI Quran Recitation Teacher</h1>
      <p style="font-size:0.95rem; color:var(--text-muted); margin:0.35rem 0 0;">Practice Quran recitation into your microphone with instant word-by-word alignment and feedback.</p>
    </div>
    <div>
      <a href="<?= Controller::url('/quran') ?>" class="btn btn-sm btn-outline"><i class="bi bi-book me-1"></i> Open Full Quran</a>
    </div>
  </div>
</div>

<div class="container" style="padding-bottom:4rem;">
  <div style="display:grid; grid-template-columns: 2fr 1fr; gap:2rem; align-items:start;">
    
    <!-- Left: Recitation Workspace -->
    <div>
      <div class="tool-card" style="padding:2rem; margin-bottom:2rem;">
        <!-- Surah & Ayah Pickers -->
        <div style="display:grid; grid-template-columns: 2fr 1fr 1fr; gap:1rem; margin-bottom:2rem;">
          <div>
            <label style="display:block; font-size:0.85rem; font-weight:600; margin-bottom:0.4rem; color:var(--text-main);">Choose Surah</label>
            <select id="teacherSurahSelect" class="form-control" style="width:100%; padding:0.6rem; border:1px solid var(--border); border-radius:8px; font-family:inherit;">
              <?php foreach ($surahs as $s): ?>
                <option value="<?= $s['id'] ?>">Surah <?= $s['id'] ?>: <?= htmlspecialchars($s['name_english']) ?> (<?= $s['name_arabic'] ?>)</option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label style="display:block; font-size:0.85rem; font-weight:600; margin-bottom:0.4rem; color:var(--text-main);">Ayah</label>
            <input type="number" id="teacherAyahNum" value="1" min="1" max="7" style="width:100%; padding:0.6rem; border:1px solid var(--border); border-radius:8px; font-family:inherit;">
          </div>
          <div style="display:flex; align-items:flex-end;">
            <button id="btnLoadAyah" class="btn btn-primary" style="width:100%; padding:0.65rem;">Load Ayah</button>
          </div>
        </div>

        <!-- Verse Display Card -->
        <div style="background:var(--bg-alt); border-radius:12px; padding:2rem; text-align:center; margin-bottom:2rem; border:1px solid var(--gold-border);">
          <span id="displayAyahTitle" style="display:block; font-size:0.85rem; font-weight:700; color:var(--primary); margin-bottom:0.75rem; text-transform:uppercase; letter-spacing:1px;">Surah Al-Fatihah — Ayah 1</span>
          <h2 id="displayArabicText" class="arabic-text" style="font-size:2.4rem; line-height:2; color:var(--primary-dark); margin:0 0 1rem; font-weight:bold;">بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ</h2>
          <p id="displayTranslation" style="font-size:1rem; color:var(--text-muted); margin:0 0 1.25rem;">In the name of Allah, the Entirely Merciful, the Especially Merciful.</p>
          
          <button id="btnPlayReference" class="btn btn-sm btn-outline" style="background:#fff;">
            <i class="bi bi-volume-up me-1"></i> Listen to Reference Qari (Mishary Alafasy)
          </button>
          <audio id="refAudio" preload="none"></audio>
        </div>

        <!-- Microphone Recording Center -->
        <div style="text-align:center; padding:1.5rem 0;">
          <button id="btnRecordToggle" style="width:84px; height:84px; border-radius:50%; background:var(--gold-gradient); border:none; cursor:pointer; box-shadow:0 8px 24px rgba(212,175,55,0.4); display:inline-flex; align-items:center; justify-content:center; transition:transform 0.2s ease;">
            <i class="bi bi-mic-fill" id="micIcon" style="font-size:2rem; color:#000;"></i>
          </button>
          <h3 id="recordingStatusText" style="font-size:1.15rem; font-weight:700; margin:1rem 0 0.25rem; color:var(--text-main);">Tap Microphone to Recite</h3>
          <p style="font-size:0.85rem; color:var(--text-muted); margin:0;">Allow microphone permission when prompted by your browser.</p>
        </div>

        <!-- Speech Transcript -->
        <div style="background:#fff; border:1px solid var(--border); border-radius:8px; padding:1rem; margin-top:1.5rem;">
          <span style="font-size:0.8rem; font-weight:600; color:var(--text-muted); display:block; margin-bottom:0.35rem;">Detected Speech (Arabic):</span>
          <p id="speechTranscriptText" class="arabic-text" style="font-size:1.3rem; color:var(--primary); margin:0;">Waiting for microphone input...</p>
        </div>
      </div>

      <!-- AI Word-by-Word Feedback Card -->
      <div id="feedbackCard" class="tool-card" style="padding:2rem; display:none; border:2px solid var(--primary);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
          <h3 style="font-size:1.2rem; margin:0; color:var(--primary); font-weight:700;">
            <i class="bi bi-check-circle-fill me-1"></i> Recitation Accuracy Report
          </h3>
          <span id="accuracyBadge" class="badge badge-gold" style="font-size:0.9rem; padding:0.35rem 0.85rem;">Accuracy: 0%</span>
        </div>

        <div style="background:var(--bg-alt); padding:1.25rem; border-radius:8px; margin-bottom:1rem;">
          <span style="font-size:0.8rem; font-weight:600; color:var(--text-muted); display:block; margin-bottom:0.75rem;">Word Alignment:</span>
          <div id="wordByWordContainer" class="arabic-text" style="display:flex; flex-wrap:wrap; gap:0.5rem; font-size:1.4rem;"></div>
        </div>

        <p style="font-size:0.85rem; color:var(--text-muted); margin:0;">
          <strong>Educational Safeguard:</strong> This speech analysis is an educational aid. Certified Tajweed rules require guidance from a human Quran teacher.
        </p>
      </div>
    </div>

    <!-- Right: Sidebar Info & Tips -->
    <div>
      <!-- Recent History Card -->
      <div class="tool-card" style="padding:1.5rem; margin-bottom:1.5rem;">
        <h4 style="font-size:1.05rem; font-weight:700; color:var(--text-main); margin:0 0 1rem; border-bottom:1px solid var(--border); padding-bottom:0.75rem;">
          <i class="bi bi-clock-history me-1" style="color:var(--gold);"></i> Recent Practices
        </h4>
        <?php if (empty($recentSessions)): ?>
          <p style="font-size:0.85rem; color:var(--text-muted); margin:0;">No practice sessions recorded yet. Start reciting to see your log!</p>
        <?php else: ?>
          <ul style="list-style:none; padding:0; margin:0;">
            <?php foreach ($recentSessions as $session): ?>
              <li style="display:flex; justify-content:space-between; align-items:center; padding:0.6rem 0; border-bottom:1px solid var(--border);">
                <div>
                  <strong style="font-size:0.85rem; display:block; color:var(--text-main);"><?= htmlspecialchars($session['name_english']) ?> (Ayah <?= $session['ayah_number'] ?>)</strong>
                  <span style="font-size:0.75rem; color:var(--text-muted);"><?= date('M d, H:i', strtotime($session['created_at'])) ?></span>
                </div>
                <span class="badge badge-primary"><?= round($session['accuracy_score']) ?>%</span>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>

      <!-- Recitation Advice -->
      <div class="tool-card" style="padding:1.5rem;">
        <h4 style="font-size:1.05rem; font-weight:700; color:var(--text-main); margin:0 0 1rem;">
          <i class="bi bi-lightbulb me-1" style="color:var(--gold);"></i> Practice Guidelines
        </h4>
        <ul style="padding-left:1.2rem; font-size:0.85rem; color:var(--text-muted); line-height:1.6; margin:0;">
          <li>Listen to the reference Qari first to align with the correct pace.</li>
          <li>Recite in a quiet room for optimal speech recognition.</li>
          <li>Focus on clear articulation from the Arabic Makharij (exit points).</li>
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

const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
if (SpeechRecognition) {
    recognition = new SpeechRecognition();
    recognition.lang = 'ar-SA';
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
        document.getElementById('recordingStatusText').innerText = "Microphone Notice: " + event.error;
        stopRecordingUI();
    };

    recognition.onend = function() {
        stopRecordingUI();
    };
} else {
    document.getElementById('speechTranscriptText').innerText = "Web Speech API is supported on Google Chrome and Microsoft Edge.";
}

const btnRecord = document.getElementById('btnRecordToggle');
btnRecord.addEventListener('click', function() {
    if (!recognition) {
        alert("Please use Google Chrome or Microsoft Edge for microphone speech recitation.");
        return;
    }

    if (!isRecording) {
        try {
            recognition.start();
            isRecording = true;
            document.getElementById('recordingStatusText').innerText = "Listening... Recite the Ayah now!";
            document.getElementById('micIcon').className = 'bi bi-stop-circle-fill text-danger';
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
    document.getElementById('recordingStatusText').innerText = "Tap Microphone to Recite";
    document.getElementById('micIcon').className = 'bi bi-mic-fill';
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
        feedbackCard.style.display = 'block';

        const badge = document.getElementById('accuracyBadge');
        badge.innerText = `Accuracy: ${res.accuracy}%`;

        let wordsHtml = '';
        res.word_diffs.forEach(w => {
            if (w.status === 'correct') {
                wordsHtml += `<span style="background:#ecfdf5; color:#047857; border:1px solid #a7f3d0; padding:0.2rem 0.6rem; border-radius:6px; margin:2px;">${w.word}</span>`;
            } else {
                wordsHtml += `<span style="background:#fef2f2; color:#b91c1c; border:1px solid #fecaca; padding:0.2rem 0.6rem; border-radius:6px; margin:2px;" title="Check pronunciation">${w.word}</span>`;
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
            document.getElementById('feedbackCard').style.display = 'none';
            document.getElementById('speechTranscriptText').innerText = "Waiting for microphone input...";
        });
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
