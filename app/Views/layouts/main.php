<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Life Deen - Islamic Tools for Everyday Life | Quran AI') ?></title>
    
    <!-- Meta Tags -->
    <meta name="description" content="<?= htmlspecialchars($seo['meta_description'] ?? 'Simple, useful and accessible Islamic tools, Quran reader, AI Quran Teacher, and spiritual planners.') ?>">
    <meta name="keywords" content="<?= htmlspecialchars($seo['meta_keywords'] ?? 'Quran recitation, AI Quran Teacher, Islamic tools, prayer times, qibla compass, lifedeen') ?>">
    <meta name="robots" content="index, follow">

    <!-- Life Deen Premium Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Official Life Deen Architecture Stylesheets -->
    <link rel="stylesheet" href="<?= Controller::url('/assets/css/lifedeen-style.css') ?>">
    <link rel="stylesheet" href="<?= Controller::url('/assets/css/lifedeen-components.css') ?>">
    <link rel="stylesheet" href="<?= Controller::url('/assets/css/lifedeen-tools.css') ?>">
    <link rel="stylesheet" href="<?= Controller::url('/assets/css/lifedeen-mobile.css') ?>">

    <style>
        .arabic-text { font-family: 'Amiri', serif; direction: rtl; }
        .hero-ai-badge { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.4rem 1rem; border-radius: 9999px; background: rgba(212, 175, 55, 0.15); border: 1px solid var(--gold-border); color: #855d04; font-weight: 700; font-size: 0.85rem; margin-bottom: 1.25rem; }

        /* Top Bar Professional Styling */
        .top-bar {
            background: linear-gradient(135deg, var(--primary-dark, #022c22) 0%, var(--primary, #064e3b) 100%);
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.82rem;
            padding: 0.45rem 0;
            border-bottom: 1px solid rgba(212, 175, 55, 0.25);
        }
        .top-bar-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .top-bar-links {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .top-bar-links a {
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            transition: color 0.2s ease;
        }
        .top-bar-links a:hover {
            color: var(--gold, #d4af37);
        }
    </style>
</head>
<body>

  <!-- Top Notification Bar -->
  <div class="top-bar">
    <div class="container top-bar-content">
      <span>Welcome to Life Deen - Quran Reader, Authentic Tilawat & AI Quran Teacher.</span>
      <div class="top-bar-links">
        <a href="<?= Controller::url('/quran') ?>">Read Quran</a>
        <a href="<?= Controller::url('/ai-teacher') ?>" style="color:var(--gold); font-weight:700;"><i class="bi bi-mic me-1"></i>AI Teacher</a>
      </div>
    </div>
  </div>

  <!-- Header -->
  <header class="site-header" id="siteHeader">
    <div class="container navbar">
      <!-- Brand Logo -->
      <a href="<?= Controller::url('/') ?>" class="brand-link" aria-label="Life Deen Homepage">
        <div class="brand-emblem" style="background:var(--primary); color:#fff; width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.5rem; font-weight:bold; border:2px solid var(--gold);">
          📖
        </div>
        <div class="brand-text">
          <span class="brand-name">Life <span>Deen</span></span>
          <span class="brand-tag">Quran AI & Islamic Tools</span>
        </div>
      </a>

      <!-- Desktop Nav Links -->
      <nav class="nav-links" aria-label="Main Navigation">
        <a href="<?= Controller::url('/') ?>" class="nav-link <?= ($_SERVER['REQUEST_URI'] == '/' || str_ends_with($_SERVER['REQUEST_URI'], '/quran-ai/')) ? 'active' : '' ?>">Home</a>
        <a href="<?= Controller::url('/quran') ?>" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/quran') ? 'active' : '' ?>">Quran Reader</a>
        <a href="<?= Controller::url('/ai-teacher') ?>" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/ai-teacher') ? 'active' : '' ?>" style="color:var(--primary); font-weight:700;">
          <span style="background:var(--gold-gradient); -webkit-background-clip:text; -webkit-text-fill-color:transparent; font-weight:800;">★ AI Teacher</span>
        </a>
        <a href="<?= Controller::url('/recitations') ?>" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/recitations') ? 'active' : '' ?>">Qari Tilawat</a>
        <a href="<?= Controller::url('/hifz') ?>" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/hifz') ? 'active' : '' ?>">Hifz Tracker</a>
      </nav>

      <!-- Right Header Actions -->
      <div class="nav-actions">
        <?php if (Auth::check()): ?>
            <?php if (Auth::isAdmin()): ?>
                <a href="<?= Controller::url('/admin') ?>" class="btn btn-sm btn-outline">Admin Panel</a>
            <?php else: ?>
                <a href="<?= Controller::url('/dashboard') ?>" class="btn btn-sm btn-outline">Dashboard</a>
            <?php endif; ?>
            <a href="<?= Controller::url('/logout') ?>" class="btn btn-sm btn-outline" style="color:#dc2626; border-color:#fca5a5;">Logout</a>
        <?php else: ?>
            <a href="<?= Controller::url('/login') ?>" class="btn btn-sm btn-outline">Sign In</a>
            <a href="<?= Controller::url('/register') ?>" class="btn btn-sm btn-primary">Start Free</a>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="site-main" id="mainContent">
    <?= $content ?? '' ?>
  </main>

  <!-- Footer -->
  <footer class="site-footer">
    <div class="container footer-content">
      <!-- Col 1: Brand -->
      <div class="footer-col brand-col">
        <div class="brand-link">
          <div class="brand-emblem" style="background:var(--primary); color:#fff; width:38px; height:38px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.2rem; border:1px solid var(--gold);">
            📖
          </div>
          <div class="brand-text">
            <span class="brand-name">Life <span>Deen</span></span>
            <span class="brand-tag">Quran AI Teacher</span>
          </div>
        </div>
        <p class="footer-desc">
          Life Deen is a modern Islamic utility platform built to provide accurate, peaceful, and accessible tools for everyday worship, Holy Quran reading, and AI voice recitation guidance.
        </p>
      </div>

      <!-- Col 2: Core Islamic Tools -->
      <div class="footer-col">
        <h4 class="footer-title">Quran & AI Tools</h4>
        <ul class="footer-links">
          <li><a href="<?= Controller::url('/quran') ?>">Read Holy Quran (114 Surahs)</a></li>
          <li><a href="<?= Controller::url('/ai-teacher') ?>" style="font-weight:700; color:#f6e6b4;">AI Quran Recitation Teacher</a></li>
          <li><a href="<?= Controller::url('/recitations') ?>">Authentic Qari Recitations</a></li>
          <li><a href="<?= Controller::url('/hifz') ?>">Hifz & Revision Planner</a></li>
        </ul>
      </div>

      <!-- Col 3: Popular Surahs -->
      <div class="footer-col">
        <h4 class="footer-title">Popular Surahs</h4>
        <ul class="footer-links">
          <li><a href="<?= Controller::url('/quran/1') ?>">Surah Al-Fatihah</a></li>
          <li><a href="<?= Controller::url('/quran/36') ?>">Surah Ya-Sin</a></li>
          <li><a href="<?= Controller::url('/quran/55') ?>">Surah Ar-Rahman</a></li>
          <li><a href="<?= Controller::url('/quran/67') ?>">Surah Al-Mulk</a></li>
        </ul>
      </div>

      <!-- Col 4: Platform -->
      <div class="footer-col">
        <h4 class="footer-title">Platform</h4>
        <ul class="footer-links">
          <li><a href="<?= Controller::url('/login') ?>">Sign In / Account</a></li>
          <li><a href="<?= Controller::url('/register') ?>">Register Free</a></li>
          <li><a href="<?= Controller::url('/sitemap.xml') ?>">XML Sitemap</a></li>
        </ul>
      </div>
    </div>

    <!-- Copyright -->
    <div class="footer-bottom">
      <div class="container footer-bottom-content">
        <p>&copy; <?= date('Y') ?> <strong>Life Deen - Quran AI</strong>. All rights reserved. Authentic Uthmani Text & AI Recitation Assistant.</p>
      </div>
    </div>
  </footer>

</body>
</html>
