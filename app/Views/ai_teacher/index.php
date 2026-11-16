<?php
ob_start();
?>

<!-- Life Deen Subpage Header -->
<div class="container" style="padding-top:2rem; padding-bottom:1rem;">
  <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; border-bottom:1px solid var(--border); padding-bottom:1.5rem; margin-bottom:2rem;">
    <div>
      <span class="badge badge-gold" style="margin-bottom:0.5rem; display:inline-flex; align-items:center; gap:0.4rem;">
        <i class="bi bi-cpu-fill" style="color:var(--gold);"></i> Advanced Gemini AI Recitation Coach
      </span>
      <h1 style="font-size:2rem; font-weight:800; color:var(--text-main); margin:0;">AI Quran Recitation Teacher & Tajweed Hub</h1>
      <p style="font-size:0.95rem; color:var(--text-muted); margin:0.35rem 0 0;">Practice live recitation with instant word-by-word matching, waveform visualization, and Google Gemini AI Tajweed guidance.</p>
    </div>
    <div style="display:flex; gap:0.75rem;">
      <a href="<?= Controller::url('/quran') ?>" class="btn btn-sm btn-outline"><i class="bi bi-book me-1"></i> Open Full Quran</a>
    </div>
  </div>
</div>

<div class="container" style="padding-bottom:4rem;">
  <div style="display:grid; grid-template-columns: 2fr 1fr; gap:2rem; align-items:start;">
    
    <!-- Left: Recitation Workspace -->
    <div>
      <div class="tool-card" style="padding:2rem; margin-bottom:2rem; box-shadow:0 8px 30px rgba(0,0,0,0.06);">
        <!-- Surah, Ayah & Qari Controls -->
        <div style="display:grid; grid-template-columns: 2fr 1fr 1fr; gap:1rem; margin-bottom:1.5rem;">
          <div>
            <label style="display:block; font-size:0.85rem; font-weight:700; margin-bottom:0.4rem; color:var(--text-main);">Choose Surah</label>
            <select id="teacherSurahSelect" class="form-control" style="width:100%; padding:0.65rem; border:1px solid var(--border); border-radius:8px; font-family:inherit;">
              <?php foreach ($surahs as $s): ?>
                <option value="<?= $s['id'] ?>">Surah <?= $s['id'] ?>: <?= htmlspecialchars($s['name_english']) ?> (<?= $s['name_arabic'] ?>)</option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label style="display:block; font-size:0.85rem; font-weight:700; margin-bottom:0.4rem; color:var(--text-main);">Ayah</label>
            <input type="number" id="teacherAyahNum" value="1" min="1" max="7" style="width:100%; padding:0.65rem; border:1px solid var(--border); border-radius:8px; font-family:inherit;">
          </div>
          <div style="display:flex; align-items:flex-end;">
            <button id="btnLoadAyah" class="btn btn-primary" style="width:100%; padding:0.65rem; font-weight:700;">Load Ayah</button>
          </div>
        </div>

        <!-- Verse Display Card -->
        <div style="background:var(--bg-alt); border-radius:16px; padding:2rem; text-align:center; margin-bottom:2rem; border:1px solid var(--gold-border); position:relative;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
            <span id="displayAyahTitle" style="font-size:0.85rem; font-weight:700; color:var(--primary); text-transform:uppercase; letter-spacing:1px;">Surah Al-Fatihah — Ayah 1</span>
            <div style="display:flex; align-items:center; gap:0.5rem;">
              <span style="font-size:0.75rem; color:var(--text-muted);">Qari:</span>
              <select id="refQariSelect" style="padding:0.25rem 0.6rem; border:1px solid var(--border); border-radius:6px; font-size:0.8rem;">
                <?php foreach ($qaris as $q): ?>
                  <option value="<?= htmlspecialchars($q['server_url']) ?>"><?= htmlspecialchars($q['name_english']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <h2 id="displayArabicText" class="arabic-text" style="font-size:2.6rem; line-height:2.2; color:var(--primary-dark); margin:0 0 1rem; font-weight:bold;">بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ</h2>
          <p id="displayTranslation" style="font-size:1.05rem; color:var(--text-muted); margin:0 0 1.5rem;">In the name of Allah, the Entirely Merciful, the Especially Merciful.</p>
          
          <button id="btnPlayReference" class="btn btn-sm btn-outline" style="background:#fff; border-color:var(--gold-border); color:var(--text-main); font-weight:600;">
            <i class="bi bi-play-circle-fill" style="color:var(--primary);"></i> Listen to Reference Tilawat
          </button>
          <audio id="refAudio" preload="none"></audio>
        </div>

        <!-- Advanced Microphone Recording Center with Pulse Animation -->
        <div style="text-align:center; padding:1.5rem 0;">
          <div style="position:relative; display:inline-block;">
            <button id="btnRecordToggle" style="width:90px; height:90px; border-radius:50%; background:var(--gold-gradient); border:none; cursor:pointer; box-shadow:0 8px 30px rgba(212,175,55,0.45); display:inline-flex; align-items:center; justify-content:center; transition:transform 0.2s ease;">
              <i class="bi bi-mic-fill" id="micIcon" style="font-size:2.2rem; color:#000;"></i>
            </button>
          </div>
          <h3 id="recordingStatusText" style="font-size:1.2rem; font-weight:800; margin:1rem 0 0.25rem; color:var(--text-main);">Tap Microphone to Start Recitation</h3>
          <p style="font-size:0.85rem; color:var(--text-muted); margin:0;">Recite clearly into your microphone in Arabic.</p>
        </div>

        <!-- Live Detected Speech Transcript -->
        <div style="background:#fff; border:1px solid var(--border); border-radius:12px; padding:1.25rem; margin-top:1.5rem;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.5rem;">
            <span style="font-size:0.8rem; font-weight:700; color:var(--text-muted); text-transform:uppercase;">Live Speech Recognition (Arabic):</span>
            <span class="badge badge-primary" style="font-size:0.75rem;">ar-SA Engine</span>
          </div>
          <p id="speechTranscriptText" class="arabic-text" style="font-size:1.4rem; color:var(--primary); margin:0; min-height:36px; line-height:1.8;">Waiting for microphone input...</p>
        </div>
      </div>

      <!-- AI Word-by-Word Detailed Feedback Card -->
      <div id="feedbackCard" class="tool-card" style="padding:2rem; display:none; border:2px solid var(--primary); box-shadow:0 12px 35px rgba(0,0,0,0.1);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
          <div>
            <h3 style="font-size:1.35rem; margin:0; color:var(--primary); font-weight:800;">
              <i class="bi bi-patch-check-fill me-1" style="color:var(--gold);"></i> Lafz-Ba-Lafz Jaiza (Word-by-Word Report)
            </h3>
            <span style="font-size:0.85rem; color:var(--text-muted);" id="wordsSummaryText">Sahi Alfaaz: 0 / 0</span>
          </div>
          <span id="accuracyBadge" class="badge badge-gold" style="font-size:1.2rem; padding:0.6rem 1.5rem; font-weight:800; border-radius:9999px;">Accuracy: 0%</span>
        </div>

        <!-- Visual Word Badges -->
        <div style="background:var(--bg-alt); padding:1.5rem; border-radius:12px; margin-bottom:1.5rem; border:1px solid var(--border);">
          <span style="font-size:0.85rem; font-weight:700; color:var(--text-main); display:block; margin-bottom:1rem;">
            🟢 Sabz (Green) = Theek Lafz &nbsp;|&nbsp; 🔴 Surkh (Red) = Ghalat ya Chhoota hua Lafz:
          </span>
          <div id="wordByWordContainer" class="arabic-text" style="display:flex; flex-wrap:wrap; gap:0.75rem; font-size:1.7rem; direction:rtl;"></div>
        </div>

        <!-- Google Gemini AI Tajweed Advice Box (NEW ADVANCED FEATURE) -->
        <div id="geminiAdviceBox" style="background:linear-gradient(135deg, #ecfdf5 0%, #fef8eb 100%); border:1px solid var(--gold-border); border-radius:12px; padding:1.25rem; margin-bottom:1.5rem; display:none;">
          <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.5rem;">
            <i class="bi bi-stars" style="color:var(--gold); font-size:1.2rem;"></i>
            <strong style="color:var(--primary); font-size:0.95rem;">Gemini AI Tajweed Advice:</strong>
          </div>
          <p id="geminiAdviceText" style="font-size:0.9rem; color:var(--text-main); margin:0; line-height:1.6;"></p>
        </div>

        <!-- Detailed Explanations Table -->
        <div style="margin-bottom:1.5rem;">
          <h4 style="font-size:1rem; font-weight:700; color:var(--text-main); margin-bottom:0.75rem;">Lafzi Comparison:</h4>
          <div id="detailedDiffList" style="display:flex; flex-direction:column; gap:0.5rem;"></div>
        </div>

        <p style="font-size:0.85rem; color:var(--text-muted); margin:0; border-top:1px solid var(--border); padding-top:1rem;">
          <strong>Educational Safeguard:</strong> This speech analysis is an educational recitation aid. For certified Tajweed rules (Makharij, Ghunnah), guidance under a certified Qari is recommended.
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
          <i class="bi bi-lightbulb me-1" style="color:var(--gold);"></i> Tajweed Rules Guide
        </h4>
        <ul style="padding-left:1.2rem; font-size:0.85rem; color:var(--text-muted); line-height:1.6; margin:0;">
          <li><strong>Makharij (Pronunciation points):</strong> Throat letters (ح, خ, ع, غ, ء, ه) and Tongue letters.</li>
          <li><strong>Ghunnah:</strong> 2 beats nasal sound on Noon and Meem with Shaddah (نّ, مّ).</li>
          <li><strong>Qalqalah:</strong> Echo sound on bounce letters (ق, ط, ب, ج, د).</li>
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
    document.getElementById('recordingStatusText').innerText = "Tap Microphone to Start Recitation";
    document.getElementById('micIcon').className = 'bi bi-mic-fill';
}

function stopRecordingAndAnalyze(recitedText) {
    stopRecordingUI();
    document.getElementById('recordingStatusText').innerText = "Analyzing recitation with AI...";

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

        document.getElementById('wordsSummaryText').innerText = `Sahi Alfaaz: ${res.matched_words} / ${res.total_words} (${res.accuracy}%)`;

        let wordsHtml = '';
        let diffListHtml = '';

        res.word_diffs.forEach((w, i) => {
            if (w.status === 'correct') {
                wordsHtml += `<span style="background:#ecfdf5; color:#047857; border:2px solid #10b981; padding:0.35rem 0.9rem; border-radius:10px; display:inline-flex; align-items:center; gap:0.4rem;" title="Sahi"><i class="bi bi-check-circle-fill" style="font-size:1.1rem;"></i> ${w.original}</span>`;
                diffListHtml += `<div style="background:#f0fdf4; border-left:4px solid #10b981; padding:0.75rem 1rem; border-radius:8px; font-size:0.95rem; color:#166534; display:flex; justify-content:space-between; align-items:center;"><span><strong>Lafz #${i+1}:</strong> ${w.original}</span> <span style="font-weight:700;"><i class="bi bi-check-lg"></i> Bilkul Sahi (${w.confidence}%)</span></div>`;
            } else {
                wordsHtml += `<span style="background:#fef2f2; color:#b91c1c; border:2px solid #ef4444; padding:0.35rem 0.9rem; border-radius:10px; display:inline-flex; align-items:center; gap:0.4rem;" title="Ghalat"><i class="bi bi-x-circle-fill" style="font-size:1.1rem;"></i> ${w.original}</span>`;
                diffListHtml += `<div style="background:#fef2f2; border-left:4px solid #ef4444; padding:0.75rem 1rem; border-radius:8px; font-size:0.95rem; color:#991b1b; display:flex; justify-content:space-between; align-items:center;"><span><strong>Lafz #${i+1}:</strong> Asal: <strong>${w.original}</strong> | Parha: <em>${w.recited}</em></span> <span style="font-weight:700;"><i class="bi bi-arrow-repeat"></i> Dobara Practice Karein</span></div>`;
            }
        });

        document.getElementById('wordByWordContainer').innerHTML = wordsHtml;
        document.getElementById('detailedDiffList').innerHTML = diffListHtml;

        // Gemini AI Advice Box
        const geminiBox = document.getElementById('geminiAdviceBox');
        if (res.ai_advice) {
            document.getElementById('geminiAdviceText').innerText = res.ai_advice;
            geminiBox.style.display = 'block';
        } else {
            geminiBox.style.display = 'none';
        }

        document.getElementById('recordingStatusText').innerText = "Analysis Complete!";
        feedbackCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
    })
    .catch(err => {
        console.error(err);
        document.getElementById('recordingStatusText').innerText = "Failed to evaluate recitation.";
    });
}

// Reference Audio Player
document.getElementById('btnPlayReference').addEventListener('click', function() {
    const audio = document.getElementById('refAudio');
    const qariUrl = document.getElementById('refQariSelect').value;
    audio.src = `${qariUrl}${currentAyahData.audioNum}`;
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
