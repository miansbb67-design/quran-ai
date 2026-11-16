<?php
ob_start();
?>

<!-- Hero Section with Life Deen Live Prayer Strip -->
<section class="hero" style="position:relative; overflow:hidden; padding:3.5rem 0 2.5rem;">
  <div class="container hero-content">
    <div class="hero-ai-badge">
      <i class="bi bi-mic-fill" style="color:var(--gold);"></i> Powered by Real-Time Speech AI & Authentic Qaris
    </div>
    
    <h1 class="hero-title" style="font-size:2.8rem; line-height:1.2; margin-bottom:1rem;">
      Islamic Tools for Everyday Life & <span class="highlight">Quran AI Teacher</span>
    </h1>
    
    <p class="hero-subtitle" style="max-width:750px; margin:0 auto 2rem; font-size:1.1rem; color:var(--text-muted); line-height:1.6;">
      Simple, useful and accessible Islamic utilities, complete Holy Quran reader with 114 Surahs, world-class Qari recitations, digital Tasbeeh, Zakat calculator, and intelligent AI speech pronunciation feedback.
    </p>

    <!-- Hero Search Bar (Life Deen Feature) -->
    <div style="max-width:560px; margin:0 auto 2rem; position:relative;">
      <input type="text" id="mainToolSearch" placeholder="Search tools, Surahs (e.g. AI Teacher, Al-Fatihah, Tasbeeh, Zakat)..." style="width:100%; padding:0.9rem 1.4rem 0.9rem 3rem; border-radius:9999px; border:2px solid var(--gold-border); background:#fff; font-size:1rem; box-shadow:0 4px 20px rgba(0,0,0,0.06); font-family:inherit;">
      <i class="bi bi-search" style="position:absolute; left:1.2rem; top:50%; transform:translateY(-50%); color:var(--gold); font-size:1.1rem;"></i>
    </div>

    <!-- Hero Actions -->
    <div class="hero-actions" style="display:flex; justify-content:center; gap:1rem; flex-wrap:wrap; margin-bottom:2.5rem;">
      <a href="<?= Controller::url('/ai-teacher') ?>" class="btn btn-lg btn-primary" style="display:inline-flex; align-items:center; gap:0.5rem; background:var(--primary); font-weight:700; padding:0.85rem 1.8rem; border-radius:9999px;">
        <i class="bi bi-mic-fill"></i> Try AI Quran Teacher &rarr;
      </a>
      <a href="<?= Controller::url('/quran') ?>" class="btn btn-lg btn-outline" style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.85rem 1.8rem; border-radius:9999px;">
        <i class="bi bi-book"></i> Read Holy Quran
      </a>
      <a href="<?= Controller::url('/recitations') ?>" class="btn btn-lg btn-outline" style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.85rem 1.8rem; border-radius:9999px;">
        <i class="bi bi-headphones"></i> Listen to Qaris
      </a>
    </div>

    <!-- Live Spiritual Banner & Hijri Date Strip -->
    <div style="background:#fff; border:1px solid var(--gold-border); border-radius:16px; padding:1.25rem 2rem; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1.5rem; box-shadow:0 4px 15px rgba(212,175,55,0.08);">
      <div style="display:flex; align-items:center; gap:1rem;">
        <span style="font-size:2rem; color:var(--primary);">🕌</span>
        <div>
          <strong style="color:var(--primary); font-size:1.1rem; display:block;" id="livePrayerName">Next Prayer: Dhuhr</strong>
          <span style="font-size:0.85rem; color:var(--text-muted);" id="livePrayerTime">Prayer Times Synchronized</span>
        </div>
      </div>
      <div style="display:flex; align-items:center; gap:1.5rem;">
        <div style="text-align:right;">
          <strong style="color:var(--text-main); font-size:1rem; display:block;" id="currentDateStr"><?= date('l, d F Y') ?></strong>
          <span style="font-size:0.85rem; color:var(--gold); font-weight:700;">Holy Quran & Islamic Utilities</span>
        </div>
        <div style="border-left:1px solid var(--border); padding-left:1.5rem;">
          <span class="arabic-text" style="font-size:1.5rem; color:var(--primary); font-weight:bold;">الْحَمْدُ لِلَّهِ</span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Most Popular Islamic Utilities (Life Deen Grid) -->
<section class="section" id="toolsSection">
  <div class="container">
    <div class="section-header">
      <span class="section-badge">Essential Tools</span>
      <h2 class="section-title">Featured Islamic & Quran Utilities</h2>
      <p class="section-subtitle">Practical, beautifully designed Islamic tools and AI learning companions.</p>
    </div>

    <div class="tools-grid">
      <!-- Tool 1: AI Quran Teacher (Star Tool) -->
      <div class="tool-card tool-item" data-keywords="ai quran teacher voice mic recitation tajweed pronunciation" style="border:2px solid var(--gold); position:relative; box-shadow:0 8px 30px rgba(212,175,55,0.15);">
        <div style="position:absolute; top:-12px; right:20px; background:var(--gold-gradient); color:#000; font-size:0.75rem; font-weight:800; padding:3px 12px; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.1);">FEATURED AI</div>
        <div class="tool-card-header">
          <div class="tool-icon-box" style="background:var(--primary-subtle); color:var(--primary);">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path><path d="M19 10v2a7 7 0 0 1-14 0v-2"></path><line x1="12" y1="19" x2="12" y2="23"></line><line x1="8" y1="23" x2="16" y2="23"></line></svg>
          </div>
          <span class="badge badge-gold">AI Voice Coach</span>
        </div>
        <h3>AI Quran Teacher</h3>
        <p>Practice Quran recitation into your microphone, receive AI-assisted alignment, detect omitted words, and improve pronunciation.</p>
        <div class="tool-card-footer">
          <span class="badge badge-primary">Voice AI</span>
          <a href="<?= Controller::url('/ai-teacher') ?>" class="btn btn-sm btn-primary">Start Practicing &rarr;</a>
        </div>
      </div>

      <!-- Tool 2: Complete Quran Reader -->
      <div class="tool-card tool-item" data-keywords="quran reader 114 surahs uthmani translation ayah audio">
        <div class="tool-card-header">
          <div class="tool-icon-box">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
          </div>
          <span class="badge badge-gold">114 Surahs</span>
        </div>
        <h3>Holy Quran Reader</h3>
        <p>Read all 114 Surahs with authentic Uthmani script, multi-language translations, ayah-by-ayah audio player, and personal bookmarks.</p>
        <div class="tool-card-footer">
          <span class="badge badge-primary">Reading</span>
          <a href="<?= Controller::url('/quran') ?>" class="btn btn-sm btn-primary">Open Quran &rarr;</a>
        </div>
      </div>

      <!-- Tool 3: Qari Recitations Library -->
      <div class="tool-card tool-item" data-keywords="qari tilawat audio alafasy abdulsamad sudais husary mp3">
        <div class="tool-card-header">
          <div class="tool-icon-box">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 18v-6a9 9 0 0 1 18 0v6"></path><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"></path></svg>
          </div>
          <span class="badge badge-gold">Verified Murattal</span>
        </div>
        <h3>Qari Tilawat Library</h3>
        <p>Stream world-renowned Qaris: Sheikh Mishary Alafasy, AbdulBaset AbdulSamad, Al-Husary, and Abdur-Rahman As-Sudais.</p>
        <div class="tool-card-footer">
          <span class="badge badge-primary">Tilawat</span>
          <a href="<?= Controller::url('/recitations') ?>" class="btn btn-sm btn-primary">Listen Tilawat &rarr;</a>
        </div>
      </div>

      <!-- Tool 4: Interactive Digital Tasbeeh Counter -->
      <div class="tool-card tool-item" data-keywords="tasbeeh dhikr counter subhanallah alhamdulillah allahuakbar">
        <div class="tool-card-header">
          <div class="tool-icon-box" style="background:#fef3c7; color:#b45309;">
            <i class="bi bi-hand-index-thumb fs-4"></i>
          </div>
          <span class="badge badge-gold">Live Widget</span>
        </div>
        <h3>Digital Tasbeeh & Dhikr Counter</h3>
        <p>Sensory counter with instant click counting, vibration feedback, and Prophetic remembrance presets.</p>
        
        <!-- Live Embedded Tasbeeh Counter Widget -->
        <div style="background:var(--bg-alt); padding:1rem; border-radius:12px; text-align:center; margin:1rem 0;">
          <div style="font-size:2rem; font-weight:800; color:var(--primary); margin-bottom:0.5rem;" id="tasbeehCountDisplay">33</div>
          <div style="display:flex; justify-content:center; gap:0.5rem;">
            <button type="button" id="btnCountTasbeeh" style="background:var(--primary); color:#fff; border:none; padding:0.4rem 1.25rem; border-radius:8px; font-weight:700; cursor:pointer;">
              Count (+1)
            </button>
            <button type="button" id="btnResetTasbeeh" style="background:none; border:1px solid var(--border); padding:0.4rem 0.75rem; border-radius:8px; font-size:0.8rem; cursor:pointer;">
              Reset
            </button>
          </div>
        </div>

        <div class="tool-card-footer">
          <span class="badge badge-primary">Dhikr</span>
          <span style="font-size:0.8rem; color:var(--text-muted);">Instant Active</span>
        </div>
      </div>

      <!-- Tool 5: Hifz Revision Tracker -->
      <div class="tool-card tool-item" data-keywords="hifz memorization tracker planner revision streaks">
        <div class="tool-card-header">
          <div class="tool-icon-box">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
          </div>
          <span class="badge badge-gold">Revision Schedule</span>
        </div>
        <h3>Hifz Memorization Tracker</h3>
        <p>Set daily memorization goals, schedule retention revision dates, and track your active memorized portions with confidence scores.</p>
        <div class="tool-card-footer">
          <span class="badge badge-primary">Hifz</span>
          <a href="<?= Controller::url('/hifz') ?>" class="btn btn-sm btn-primary">Open Tracker &rarr;</a>
        </div>
      </div>

      <!-- Tool 6: Zakat Quick Calculator -->
      <div class="tool-card tool-item" data-keywords="zakat calculator gold silver nisab cash charity finance">
        <div class="tool-card-header">
          <div class="tool-icon-box">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2"></rect><line x1="8" y1="6" x2="16" y2="6"></line><line x1="16" y1="14" x2="16" y2="18"></line></svg>
          </div>
          <span class="badge badge-gold">Pillar of Islam</span>
        </div>
        <h3>Zakat Quick Estimator</h3>
        <p>Calculate 2.5% Zakat liability across your cash, gold, and savings instantly with accurate nisab thresholds.</p>
        
        <div style="background:var(--bg-alt); padding:0.85rem; border-radius:8px; margin:0.75rem 0;">
          <input type="number" id="zakatAmountInput" placeholder="Enter wealth / cash amount..." style="width:100%; padding:0.4rem 0.6rem; border:1px solid var(--border); border-radius:6px; font-size:0.85rem; margin-bottom:0.4rem;">
          <div style="font-size:0.85rem; color:var(--primary); font-weight:700;" id="zakatResult">Zakat Payable (2.5%): $0</div>
        </div>

        <div class="tool-card-footer">
          <span class="badge badge-primary">Finance</span>
          <span style="font-size:0.8rem; color:var(--text-muted);">Instant 2.5% Formula</span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Frequently Read Surahs Quick Grid -->
<section class="section section-alt">
  <div class="container">
    <div class="section-header">
      <span class="section-badge">Quran Direct Access</span>
      <h2 class="section-title">Popular Surahs for Daily Recitation</h2>
      <p class="section-subtitle">Quickly jump to frequently recited chapters of the Holy Quran with audio recitation.</p>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(260px, 1fr)); gap:1rem;">
      <?php foreach ($popularSurahs as $s): ?>
        <a href="<?= Controller::url('/quran/' . $s['id']) ?>" class="tool-card" style="padding:1.25rem; display:flex; justify-content:space-between; align-items:center; text-decoration:none; transition:transform 0.2s ease, box-shadow 0.2s ease;">
          <div>
            <span class="badge badge-primary" style="font-size:0.75rem; margin-bottom:0.35rem; display:inline-block;">Surah <?= $s['id'] ?></span>
            <h4 style="font-size:1.05rem; margin:0; color:var(--text-main); font-weight:700;"><?= htmlspecialchars($s['name_english']) ?></h4>
            <span style="font-size:0.8rem; color:var(--text-muted);"><?= $s['total_ayahs'] ?> Verses</span>
          </div>
          <span class="arabic-text" style="font-size:1.6rem; color:var(--primary); font-weight:bold;"><?= htmlspecialchars($s['name_arabic']) ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- How Life Deen & Quran AI Empowers Your Day -->
<section class="section">
  <div class="container">
    <div class="section-header">
      <span class="section-badge">Learning Flow</span>
      <h2 class="section-title">How Quran AI Teacher Works</h2>
      <p class="section-subtitle">Step-by-step guidance designed to make Quran reading and pronunciation effortless.</p>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:1.5rem;">
      <div class="tool-card" style="text-align:center; padding:2rem;">
        <div style="width:52px; height:52px; border-radius:50%; background:var(--primary); color:#fff; display:flex; align-items:center; justify-content:center; margin:0 auto 1.25rem; font-weight:bold; font-size:1.3rem;">1</div>
        <h3 style="font-size:1.2rem; margin-bottom:0.5rem;">Choose Surah & Ayah</h3>
        <p style="font-size:0.9rem; color:var(--text-muted); margin:0;">Select any verse from the 114 Surahs and listen to the authentic reference Qari recording.</p>
      </div>

      <div class="tool-card" style="text-align:center; padding:2rem; border-color:var(--primary); box-shadow:0 8px 24px rgba(6,78,59,0.1);">
        <div style="width:52px; height:52px; border-radius:50%; background:var(--gold-gradient); color:#000; display:flex; align-items:center; justify-content:center; margin:0 auto 1.25rem; font-weight:bold; font-size:1.3rem;">2</div>
        <h3 style="font-size:1.2rem; margin-bottom:0.5rem;">Recite via Microphone</h3>
        <p style="font-size:0.9rem; color:var(--text-muted); margin:0;">Tap the microphone button and recite the Ayah naturally. Speech analysis captures your recitation in real time.</p>
      </div>

      <div class="tool-card" style="text-align:center; padding:2rem;">
        <div style="width:52px; height:52px; border-radius:50%; background:var(--primary); color:#fff; display:flex; align-items:center; justify-content:center; margin:0 auto 1.25rem; font-weight:bold; font-size:1.3rem;">3</div>
        <h3 style="font-size:1.2rem; margin-bottom:0.5rem;">Word-by-Word Feedback</h3>
        <p style="font-size:0.9rem; color:var(--text-muted); margin:0;">Receive instant comparison highlighting correctly matched words and words needing another attempt.</p>
      </div>
    </div>
  </div>
</section>

<!-- Ethical Religious Guidance Notice (Life Deen Standard) -->
<section class="section section-alt">
  <div class="container">
    <div class="tool-card" style="background:#fff; border-left:4px solid var(--gold); padding:1.75rem;">
      <h3 style="font-size:1.15rem; color:var(--primary); margin-bottom:0.5rem; display:flex; align-items:center; gap:0.5rem;">
        <i class="bi bi-shield-check" style="color:var(--gold);"></i> Religious & Academic Integrity
      </h3>
      <p style="font-size:0.9rem; color:var(--text-muted); margin:0; line-height:1.6;">
        Quranic text is sourced strictly from canonical Uthmani script standards. Voice evaluation acts as an educational aid to detect word omissions and reading differences. It does not replace formal certified Tajweed training under a qualified human Qari.
      </p>
    </div>
  </div>
</section>

<!-- Final Registration CTA -->
<section class="section" style="text-align:center; background:linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color:#fff; padding:4.5rem 1rem;">
  <div class="container">
    <h2 style="font-size:2.4rem; color:#fff; font-weight:800; margin-bottom:1rem;">Start Your Quran Journey Today</h2>
    <p style="font-size:1.15rem; color:rgba(255,255,255,0.85); max-width:620px; margin:0 auto 2.25rem;">
      Create a free account to track your daily reading goals, bookmarks, and AI recitation practice progress.
    </p>
    <a href="<?= Controller::url('/register') ?>" class="btn btn-lg" style="background:var(--gold-gradient); color:#000; font-weight:700; padding:1rem 2.75rem; border:none; border-radius:9999px; font-size:1.1rem; box-shadow:0 6px 20px rgba(0,0,0,0.25);">
      Create Free Account &rarr;
    </a>
  </div>
</section>

<script>
// 1. Live Interactive Tool Search Filter
document.getElementById('mainToolSearch').addEventListener('input', function(e) {
  const q = e.target.value.toLowerCase().trim();
  document.querySelectorAll('.tool-item').forEach(card => {
    const k = card.getAttribute('data-keywords') || '';
    const text = card.innerText.toLowerCase();
    if (k.includes(q) || text.includes(q)) {
      card.style.display = 'flex';
    } else {
      card.style.display = 'none';
    }
  });
});

// 2. Interactive Digital Tasbeeh Counter
let tasbeehCount = 33;
const tasbeehDisplay = document.getElementById('tasbeehCountDisplay');
document.getElementById('btnCountTasbeeh').addEventListener('click', function() {
  tasbeehCount++;
  tasbeehDisplay.innerText = tasbeehCount;
  if (navigator.vibrate) navigator.vibrate(40);
});
document.getElementById('btnResetTasbeeh').addEventListener('click', function() {
  tasbeehCount = 0;
  tasbeehDisplay.innerText = tasbeehCount;
});

// 3. Live Quick Zakat Estimator
document.getElementById('zakatAmountInput').addEventListener('input', function(e) {
  const val = parseFloat(e.target.value) || 0;
  const zakat = (val * 0.025).toFixed(2);
  document.getElementById('zakatResult').innerText = `Zakat Payable (2.5%): $${zakat}`;
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
