<?php
ob_start();
?>

<!-- Hero Section (Life Deen Exact Clone Style) -->
<section class="hero" style="position:relative; overflow:hidden;">
  <div class="container hero-content">
    <div class="hero-ai-badge">
      <i class="bi bi-mic-fill" style="color:var(--gold);"></i> Powered by Real-Time Speech AI & Authentic Qaris
    </div>
    
    <h1 class="hero-title">
      Islamic Tools for Everyday Life & <span class="highlight">Quran AI Teacher</span>
    </h1>
    
    <p class="hero-subtitle">
      Simple, useful and accessible Islamic utilities, complete Holy Quran reader with 114 Surahs, world-class Qari recitations, and intelligent AI speech pronunciation feedback.
    </p>

    <!-- Hero CTA Buttons -->
    <div class="hero-actions" style="display:flex; justify-content:center; gap:1rem; flex-wrap:wrap; margin-bottom:2.5rem;">
      <a href="<?= Controller::url('/ai-teacher') ?>" class="btn btn-lg btn-primary" style="display:inline-flex; align-items:center; gap:0.5rem; background:var(--primary); font-weight:700;">
        <i class="bi bi-mic"></i> Try AI Quran Teacher &rarr;
      </a>
      <a href="<?= Controller::url('/quran') ?>" class="btn btn-lg btn-outline" style="display:inline-flex; align-items:center; gap:0.5rem;">
        <i class="bi bi-book"></i> Read Holy Quran
      </a>
      <a href="<?= Controller::url('/recitations') ?>" class="btn btn-lg btn-outline" style="display:inline-flex; align-items:center; gap:0.5rem;">
        <i class="bi bi-headphones"></i> Listen to Qaris
      </a>
    </div>

    <!-- Quick Stats Strip (Life Deen Style) -->
    <div class="hero-stats" style="display:flex; justify-content:center; gap:2rem; flex-wrap:wrap; border-top:1px solid var(--border); padding-top:2rem;">
      <div>
        <strong style="display:block; font-size:1.5rem; color:var(--primary);">114</strong>
        <span style="font-size:0.85rem; color:var(--text-muted);">Complete Surahs</span>
      </div>
      <div>
        <strong style="display:block; font-size:1.5rem; color:var(--gold);">6+</strong>
        <span style="font-size:0.85rem; color:var(--text-muted);">Authentic Qaris</span>
      </div>
      <div>
        <strong style="display:block; font-size:1.5rem; color:var(--primary);">Instant</strong>
        <span style="font-size:0.85rem; color:var(--text-muted);">Voice Speech Feedback</span>
      </div>
      <div>
        <strong style="display:block; font-size:1.5rem; color:var(--primary);">100%</strong>
        <span style="font-size:0.85rem; color:var(--text-muted);">Free & Ad-Free Focus</span>
      </div>
    </div>
  </div>
</section>

<!-- Most Popular Islamic Utilities (Life Deen Clone Grid) -->
<section class="section">
  <div class="container">
    <div class="section-header">
      <span class="section-badge">Essential Tools</span>
      <h2 class="section-title">Featured Islamic & Quran Utilities</h2>
      <p class="section-subtitle">Practical, beautifully designed Islamic tools and AI learning companions.</p>
    </div>

    <div class="tools-grid">
      <!-- Tool 1: AI Quran Teacher (Star Tool) -->
      <div class="tool-card" style="border:2px solid var(--gold); position:relative;">
        <div style="position:absolute; top:-12px; right:20px; background:var(--gold-gradient); color:#000; font-size:0.75rem; font-weight:800; padding:2px 10px; border-radius:12px;">FEATURED AI</div>
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
      <div class="tool-card">
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
      <div class="tool-card">
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

      <!-- Tool 4: Hifz Revision Tracker -->
      <div class="tool-card">
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
    </div>
  </div>
</section>

<!-- Frequently Read Surahs Quick Grid -->
<section class="section section-alt">
  <div class="container">
    <div class="section-header">
      <span class="section-badge">Quran Direct Access</span>
      <h2 class="section-title">Popular Surahs for Daily Recitation</h2>
      <p class="section-subtitle">Quickly jump to frequently recited chapters of the Holy Quran.</p>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(260px, 1fr)); gap:1rem;">
      <?php foreach ($popularSurahs as $s): ?>
        <a href="<?= Controller::url('/quran/' . $s['id']) ?>" class="tool-card" style="padding:1.25rem; display:flex; justify-content:space-between; align-items:center; text-decoration:none;">
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
        <div style="width:48px; height:48px; border-radius:50%; background:var(--primary); color:#fff; display:flex; align-items:center; justify-content:center; margin:0 auto 1.25rem; font-weight:bold; font-size:1.25rem;">1</div>
        <h3 style="font-size:1.2rem; margin-bottom:0.5rem;">Choose Surah & Ayah</h3>
        <p style="font-size:0.9rem; color:var(--text-muted); margin:0;">Select any verse from the 114 Surahs and listen to the authentic reference Qari recording.</p>
      </div>

      <div class="tool-card" style="text-align:center; padding:2rem; border-color:var(--primary);">
        <div style="width:48px; height:48px; border-radius:50%; background:var(--gold-gradient); color:#000; display:flex; align-items:center; justify-content:center; margin:0 auto 1.25rem; font-weight:bold; font-size:1.25rem;">2</div>
        <h3 style="font-size:1.2rem; margin-bottom:0.5rem;">Recite via Microphone</h3>
        <p style="font-size:0.9rem; color:var(--text-muted); margin:0;">Tap the microphone button and recite the Ayah naturally. Speech analysis captures your recitation in real time.</p>
      </div>

      <div class="tool-card" style="text-align:center; padding:2rem;">
        <div style="width:48px; height:48px; border-radius:50%; background:var(--primary); color:#fff; display:flex; align-items:center; justify-content:center; margin:0 auto 1.25rem; font-weight:bold; font-size:1.25rem;">3</div>
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
<section class="section" style="text-align:center; background:linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color:#fff; padding:4rem 1rem;">
  <div class="container">
    <h2 style="font-size:2.2rem; color:#fff; font-weight:800; margin-bottom:1rem;">Start Your Quran Journey Today</h2>
    <p style="font-size:1.1rem; color:rgba(255,255,255,0.8); max-width:600px; margin:0 auto 2rem;">
      Create a free account to track your daily reading goals, bookmarks, and AI recitation practice progress.
    </p>
    <a href="<?= Controller::url('/register') ?>" class="btn btn-lg" style="background:var(--gold-gradient); color:#000; font-weight:700; padding:0.9rem 2.5rem; border:none; border-radius:9999px;">
      Create Free Account &rarr;
    </a>
  </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
