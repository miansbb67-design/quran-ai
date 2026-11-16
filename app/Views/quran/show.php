<?php
ob_start();
?>

<!-- Life Deen Surah Detail Reading View -->
<div class="container" style="padding-top:2rem; padding-bottom:1rem;">
  <!-- Header Card with Surah Selector -->
  <div class="tool-card" style="padding:2rem; text-align:center; margin-bottom:2rem; position:relative;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
      <a href="<?= Controller::url('/quran/' . max(1, $surah['id'] - 1)) ?>" class="btn btn-sm btn-outline <?= ($surah['id'] <= 1) ? 'disabled' : '' ?>">
        &larr; Previous Surah
      </a>

      <div>
        <select onchange="location = this.value;" style="padding:0.5rem 1rem; border:1px solid var(--border); border-radius:8px; font-weight:700; color:var(--primary); font-family:inherit;">
          <?php foreach ($allSurahs as $sItem): ?>
            <option value="<?= Controller::url('/quran/' . $sItem['id']) ?>" <?= ($sItem['id'] == $surah['id']) ? 'selected' : '' ?>>
              Surah <?= $sItem['id'] ?>: <?= htmlspecialchars($sItem['name_english']) ?> (<?= $sItem['name_arabic'] ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <a href="<?= Controller::url('/quran/' . min(114, $surah['id'] + 1)) ?>" class="btn btn-sm btn-outline <?= ($surah['id'] >= 114) ? 'disabled' : '' ?>">
        Next Surah &rarr;
      </a>
    </div>

    <h1 class="arabic-text" style="font-size:3rem; color:var(--primary); margin:0 0 0.5rem; font-weight:bold;"><?= htmlspecialchars($surah['name_arabic']) ?></h1>
    <h2 style="font-size:1.6rem; font-weight:800; color:var(--text-main); margin:0 0 0.5rem;">
      Surah <?= htmlspecialchars($surah['name_english']) ?> (<?= htmlspecialchars($surah['name_translation']) ?>)
    </h2>
    <p style="font-size:0.9rem; color:var(--text-muted); margin:0 0 1.5rem;">
      <span class="badge badge-primary"><?= htmlspecialchars($surah['revelation_type']) ?></span> &bull;
      <span><?= $surah['total_ayahs'] ?> Verses</span> &bull;
      <span>Juz <?= $surah['juz_number'] ?></span> &bull;
      <span>Page <?= $surah['page_number'] ?></span>
    </p>

    <!-- Qari Selector & Tilawat Controller -->
    <div style="display:flex; justify-content:center; align-items:center; gap:1rem; flex-wrap:wrap; border-top:1px solid var(--border); padding-top:1.5rem;">
      <div style="display:flex; align-items:center; gap:0.5rem;">
        <span style="font-size:0.85rem; font-weight:600; color:var(--text-muted);"><i class="bi bi-person-fill"></i> Qari:</span>
        <select id="qariSelect" style="padding:0.4rem 0.8rem; border:1px solid var(--border); border-radius:6px; font-family:inherit;">
          <?php foreach ($qaris as $q): ?>
            <option value="<?= htmlspecialchars($q['server_url']) ?>"><?= htmlspecialchars($q['name_english']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <button id="btnPlayAll" class="btn btn-sm btn-primary" style="padding:0.45rem 1.25rem;">
        <i class="bi bi-play-fill"></i> Play Surah
      </button>
      <button id="btnStopAudio" class="btn btn-sm btn-outline" disabled>
        <i class="bi bi-stop-fill"></i> Stop
      </button>
      <a href="<?= Controller::url('/ai-teacher?surah=' . $surah['id']) ?>" class="btn btn-sm btn-outline" style="background:var(--gold-light); border-color:var(--gold-border); color:#855d04; font-weight:700;">
        <i class="bi bi-mic me-1"></i> Practice with AI Teacher
      </a>
    </div>
  </div>

  <!-- Bismillah Header -->
  <?php if ($surah['id'] != 9 && $surah['id'] != 1): ?>
    <div style="text-align:center; padding:1.5rem 0 2rem;">
      <p class="arabic-text" style="font-size:2.2rem; color:var(--primary); margin:0;">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</p>
    </div>
  <?php endif; ?>

  <!-- Verses Container -->
  <div class="tool-card" id="ayahsContainer" style="padding:2rem;">
    <div style="text-align:center; padding:3rem 0;" id="loader">
      <div style="font-size:1.5rem; margin-bottom:0.75rem;">📖</div>
      <p style="color:var(--text-muted); font-size:0.95rem;">Loading authentic Uthmani verses and translation...</p>
    </div>
  </div>
</div>

<!-- Sticky Bottom Floating Audio Player Bar (Life Deen Clean Design) -->
<div id="floatingPlayer" style="display:none; position:fixed; bottom:0; left:0; right:0; background:#fff; border-top:2px solid var(--gold); box-shadow:0 -8px 24px rgba(0,0,0,0.1); padding:0.85rem 1.5rem; z-index:1000;">
  <div class="container" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem;">
    <div style="display:flex; align-items:center; gap:1rem;">
      <button id="floatingPlayPause" style="width:40px; height:40px; border-radius:50%; background:var(--primary); color:#fff; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center;">
        <i class="bi bi-pause-fill fs-5" id="playIcon"></i>
      </button>
      <div>
        <strong id="currentAyahInfo" style="display:block; font-size:0.9rem; color:var(--text-main);">Surah <?= htmlspecialchars($surah['name_english']) ?> — Ayah 1</strong>
        <span style="font-size:0.8rem; color:var(--text-muted);">Tilawat: Continuous Murattal</span>
      </div>
    </div>

    <div style="display:flex; align-items:center; gap:0.75rem;">
      <button class="btn btn-sm btn-outline" id="btnPrevAyah"><i class="bi bi-skip-start-fill"></i></button>
      <button class="btn btn-sm btn-outline" id="btnNextAyah"><i class="bi bi-skip-end-fill"></i></button>
      <button class="btn btn-sm btn-outline" id="btnClosePlayer" style="color:#dc2626;"><i class="bi bi-x-lg"></i></button>
    </div>
  </div>
</div>

<audio id="quranAudio" preload="none"></audio>

<script>
const surahId = <?= $surah['id'] ?>;
let ayahsData = [];
let currentAyahIndex = 0;
let isPlaying = false;
const audio = document.getElementById('quranAudio');
const qariSelect = document.getElementById('qariSelect');
const floatingPlayer = document.getElementById('floatingPlayer');
const playIcon = document.getElementById('playIcon');
const currentAyahInfo = document.getElementById('currentAyahInfo');

fetch(`<?= Controller::url('/api/surah/' . $surah['id']) ?>`)
    .then(r => r.json())
    .then(res => {
        if (!res.success) throw new Error(res.error);
        ayahsData = res.ayahs;
        renderAyahs(ayahsData);
    })
    .catch(err => {
        document.getElementById('ayahsContainer').innerHTML = `
            <div style="text-align:center; color:#dc2626; padding:2rem;">
                <i class="bi bi-exclamation-triangle"></i> Failed to load verses: ${err.message}
            </div>
        `;
    });

function renderAyahs(ayahs) {
    let html = '';
    ayahs.forEach((ayah, index) => {
        html += `
            <div class="ayah-box" id="ayah-${ayah.number}" style="border-bottom:1px solid var(--border); padding:1.75rem 0;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
                    <span class="badge badge-primary" style="font-size:0.8rem;">Ayah ${ayah.number}</span>
                    <div style="display:flex; gap:0.5rem;">
                        <button class="btn btn-sm btn-outline play-single-ayah" data-index="${index}" title="Play Ayah">
                            <i class="bi bi-play-fill"></i> Listen
                        </button>
                        <button class="btn btn-sm btn-outline toggle-bookmark-btn" data-ayah="${ayah.number}" title="Save Ayah">
                            <i class="bi bi-bookmark"></i>
                        </button>
                    </div>
                </div>
                <p class="arabic-text" style="font-size:2.2rem; text-align:right; color:var(--primary-dark); line-height:2.2; margin:0 0 1rem; font-weight:bold;">${ayah.text}</p>
                <p style="font-size:1rem; color:var(--text-muted); line-height:1.6; margin:0;">${ayah.translation || ''}</p>
            </div>
        `;
    });
    document.getElementById('ayahsContainer').innerHTML = html;
    attachAyahEvents();
}

function attachAyahEvents() {
    document.querySelectorAll('.play-single-ayah').forEach(btn => {
        btn.addEventListener('click', function() {
            const index = parseInt(this.getAttribute('data-index'));
            playAyahByIndex(index);
        });
    });

    document.querySelectorAll('.toggle-bookmark-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const ayah = this.getAttribute('data-ayah');
            fetch(`<?= Controller::url('/api/quran/bookmark') ?>`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `surah_id=${surahId}&ayah_number=${ayah}&csrf_token=<?= $csrfToken ?>`
            })
            .then(r => r.json())
            .then(res => {
                if (res.bookmarked) {
                    btn.style.background = 'var(--gold)';
                    btn.style.color = '#fff';
                } else {
                    btn.style.background = 'none';
                    btn.style.color = 'inherit';
                }
            });
        });
    });
}

function playAyahByIndex(index) {
    if (index < 0 || index >= ayahsData.length) return;
    currentAyahIndex = index;
    const ayah = ayahsData[index];
    const serverUrl = qariSelect.value;
    const audioUrl = `${serverUrl}${ayah.audio_num}`;

    document.querySelectorAll('.ayah-box').forEach(el => {
        el.style.background = 'none';
        el.style.borderLeft = 'none';
    });
    const activeEl = document.getElementById(`ayah-${ayah.number}`);
    if (activeEl) {
        activeEl.style.background = 'var(--primary-subtle)';
        activeEl.style.borderLeft = '4px solid var(--primary)';
        activeEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    currentAyahInfo.innerText = `Surah <?= htmlspecialchars($surah['name_english']) ?> — Ayah ${ayah.number}`;
    floatingPlayer.style.display = 'block';
    document.getElementById('btnStopAudio').removeAttribute('disabled');

    audio.src = audioUrl;
    audio.play();
    isPlaying = true;
    playIcon.className = 'bi bi-pause-fill';
}

audio.addEventListener('ended', function() {
    if (currentAyahIndex + 1 < ayahsData.length) {
        playAyahByIndex(currentAyahIndex + 1);
    } else {
        isPlaying = false;
        playIcon.className = 'bi bi-play-fill';
    }
});

document.getElementById('btnPlayAll').addEventListener('click', () => playAyahByIndex(0));

document.getElementById('btnStopAudio').addEventListener('click', () => {
    audio.pause();
    audio.currentTime = 0;
    isPlaying = false;
    floatingPlayer.style.display = 'none';
    document.querySelectorAll('.ayah-box').forEach(el => {
        el.style.background = 'none';
        el.style.borderLeft = 'none';
    });
});

document.getElementById('floatingPlayPause').addEventListener('click', () => {
    if (isPlaying) {
        audio.pause();
        isPlaying = false;
        playIcon.className = 'bi bi-play-fill';
    } else {
        audio.play();
        isPlaying = true;
        playIcon.className = 'bi bi-pause-fill';
    }
});

document.getElementById('btnNextAyah').addEventListener('click', () => {
    if (currentAyahIndex + 1 < ayahsData.length) playAyahByIndex(currentAyahIndex + 1);
});

document.getElementById('btnPrevAyah').addEventListener('click', () => {
    if (currentAyahIndex - 1 >= 0) playAyahByIndex(currentAyahIndex - 1);
});

document.getElementById('btnClosePlayer').addEventListener('click', () => {
    audio.pause();
    floatingPlayer.style.display = 'none';
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
