<?php
ob_start();
?>

<div class="container py-4 pb-5">
    <!-- Surah Header Banner -->
    <div class="glass-card p-4 mb-4 border-gold text-center position-relative">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="/quran/<?= max(1, $surah['id'] - 1) ?>" class="btn btn-outline-gold btn-sm <?= ($surah['id'] <= 1) ? 'disabled' : '' ?>">
                <i class="bi bi-chevron-left me-1"></i> Prev
            </a>

            <div class="dropdown">
                <button class="btn btn-emerald border border-gold text-white btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    Surah <?= $surah['id'] ?>: <?= htmlspecialchars($surah['name_english']) ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-dark" style="max-height: 300px; overflow-y: auto;">
                    <?php foreach ($allSurahs as $sItem): ?>
                        <li><a class="dropdown-item <?= ($sItem['id'] == $surah['id']) ? 'active bg-success' : '' ?>" href="/quran/<?= $sItem['id'] ?>"><?= $sItem['id'] ?>. <?= htmlspecialchars($sItem['name_english']) ?> (<?= $sItem['name_arabic'] ?>)</a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <a href="/quran/<?= min(114, $surah['id'] + 1) ?>" class="btn btn-outline-gold btn-sm <?= ($surah['id'] >= 114) ? 'disabled' : '' ?>">
                Next <i class="bi bi-chevron-right ms-1"></i>
            </a>
        </div>

        <h1 class="arabic-text text-gold display-4 mb-2"><?= htmlspecialchars($surah['name_arabic']) ?></h1>
        <h3 class="fw-bold text-white mb-1"><?= htmlspecialchars($surah['name_english']) ?> (<?= htmlspecialchars($surah['name_translation']) ?>)</h3>
        <p class="text-white-50 small mb-3">
            <span class="badge bg-dark border border-secondary text-gold me-2"><?= htmlspecialchars($surah['revelation_type']) ?></span>
            <span><?= $surah['total_ayahs'] ?> Verses</span> &bull; 
            <span>Juz <?= $surah['juz_number'] ?></span> &bull; 
            <span>Page <?= $surah['page_number'] ?></span>
        </p>

        <!-- Qari Selection & Audio Control Bar -->
        <div class="d-flex flex-wrap justify-content-center align-items-center gap-3 pt-3 border-top border-secondary">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-volume-up text-gold"></i>
                <select id="qariSelect" class="form-select form-select-sm bg-dark text-white border-secondary" style="width: auto;">
                    <?php foreach ($qaris as $q): ?>
                        <option value="<?= htmlspecialchars($q['server_url']) ?>" data-id="<?= $q['id'] ?>"><?= htmlspecialchars($q['name_english']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button id="btnPlayAll" class="btn btn-gold btn-sm px-3"><i class="bi bi-play-fill me-1"></i> Play Surah</button>
            <button id="btnStopAudio" class="btn btn-outline-secondary btn-sm px-3 text-white-50" disabled><i class="bi bi-stop-fill me-1"></i> Stop</button>
            
            <a href="/ai-teacher?surah=<?= $surah['id'] ?>" class="btn btn-emerald border border-gold text-white btn-sm px-3">
                <i class="bi bi-mic text-gold me-1"></i> Practice Reciting This Surah
            </a>
        </div>
    </div>

    <!-- Bismillah for all Surahs except At-Tawbah (#9) -->
    <?php if ($surah['id'] != 9 && $surah['id'] != 1): ?>
        <div class="text-center py-3 mb-4">
            <p class="arabic-text text-gold fs-2">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</p>
        </div>
    <?php endif; ?>

    <!-- Ayahs Container (Loaded via Async Verified Source) -->
    <div id="ayahsContainer" class="glass-card p-4 mb-5">
        <div class="text-center py-5" id="loader">
            <div class="spinner-border text-gold" role="status"></div>
            <p class="text-white-50 mt-3">Loading authentic Uthmani verses...</p>
        </div>
    </div>
</div>

<!-- Sticky Bottom Floating Audio Player -->
<div class="sticky-audio-bar d-none" id="floatingPlayer">
    <div class="container d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <button id="floatingPlayPause" class="btn btn-gold btn-sm rounded-circle" style="width: 40px; height: 40px;">
                <i class="bi bi-pause-fill fs-5" id="playIcon"></i>
            </button>
            <div>
                <small class="text-gold fw-bold d-block" id="currentAyahInfo">Surah <?= htmlspecialchars($surah['name_english']) ?> — Ayah 1</small>
                <small class="text-white-50" id="currentQariName">Mishary Rashid Alafasy</small>
            </div>
        </div>

        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-outline-gold btn-sm" id="btnPrevAyah"><i class="bi bi-skip-start-fill"></i></button>
            <button class="btn btn-outline-gold btn-sm" id="btnNextAyah"><i class="bi bi-skip-end-fill"></i></button>
            <button class="btn btn-outline-danger btn-sm" id="btnClosePlayer"><i class="bi bi-x-lg"></i></button>
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

// 1. Fetch authentic verses from backend API
fetch(`<?= Controller::url('/api/surah/' . $surah['id']) ?>`)
    .then(r => r.json())
    .then(res => {
        if (!res.success) throw new Error(res.error);
        ayahsData = res.ayahs;
        renderAyahs(ayahsData);
    })
    .catch(err => {
        document.getElementById('ayahsContainer').innerHTML = `
            <div class="alert alert-danger text-center">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> Failed to load verses: ${err.message}
            </div>
        `;
    });

function renderAyahs(ayahs) {
    let html = '';
    ayahs.forEach((ayah, index) => {
        html += `
            <div class="ayah-box" id="ayah-${ayah.number}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-dark border border-secondary text-gold rounded-pill px-3 py-1">
                        Ayah ${ayah.number}
                    </span>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-gold btn-sm play-single-ayah" data-index="${index}" title="Play Ayah">
                            <i class="bi bi-play-fill"></i>
                        </button>
                        <button class="btn btn-outline-secondary btn-sm toggle-bookmark-btn" data-ayah="${ayah.number}" title="Save Ayah">
                            <i class="bi bi-bookmark"></i>
                        </button>
                    </div>
                </div>
                <p class="arabic-text text-white text-end fs-3 mb-3">${ayah.text}</p>
                <p class="text-white-50 small mb-0">${ayah.translation || ''}</p>
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
                    btn.classList.replace('btn-outline-secondary', 'btn-warning');
                } else {
                    btn.classList.replace('btn-warning', 'btn-outline-secondary');
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

    // Highlight active ayah
    document.querySelectorAll('.ayah-box').forEach(el => el.classList.remove('playing-now'));
    const activeEl = document.getElementById(`ayah-${ayah.number}`);
    if (activeEl) {
        activeEl.classList.add('playing-now');
        activeEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    currentAyahInfo.innerText = `Surah <?= htmlspecialchars($surah['name_english']) ?> — Ayah ${ayah.number}`;
    floatingPlayer.classList.remove('d-none');
    document.getElementById('btnStopAudio').removeAttribute('disabled');

    audio.src = audioUrl;
    audio.play();
    isPlaying = true;
    playIcon.className = 'bi bi-pause-fill fs-5';
}

// Next Ayah on audio finished
audio.addEventListener('ended', function() {
    if (currentAyahIndex + 1 < ayahsData.length) {
        playAyahByIndex(currentAyahIndex + 1);
    } else {
        isPlaying = false;
        playIcon.className = 'bi bi-play-fill fs-5';
    }
});

document.getElementById('btnPlayAll').addEventListener('click', () => playAyahByIndex(0));

document.getElementById('btnStopAudio').addEventListener('click', () => {
    audio.pause();
    audio.currentTime = 0;
    isPlaying = false;
    floatingPlayer.classList.add('d-none');
    document.querySelectorAll('.ayah-box').forEach(el => el.classList.remove('playing-now'));
});

document.getElementById('floatingPlayPause').addEventListener('click', () => {
    if (isPlaying) {
        audio.pause();
        isPlaying = false;
        playIcon.className = 'bi bi-play-fill fs-5';
    } else {
        audio.play();
        isPlaying = true;
        playIcon.className = 'bi bi-pause-fill fs-5';
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
    floatingPlayer.classList.add('d-none');
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>

