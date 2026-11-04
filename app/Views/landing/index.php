<?php
ob_start();
?>

<!-- Hero Section -->
<section class="py-5 text-center position-relative" style="background: radial-gradient(circle at 50% 30%, rgba(6, 78, 59, 0.45), transparent 70%);">
    <div class="container py-lg-5">
        <div class="badge px-3 py-2 rounded-pill mb-3" style="background: rgba(212, 175, 55, 0.15); border: 1px solid rgba(212, 175, 55, 0.4); color: #d4af37;">
            <i class="bi bi-stars me-1"></i> The Modern Islamic Quran Learning Experience
        </div>
        
        <h1 class="display-3 fw-bold mb-3">
            Learn the Quran with <span class="text-gold">Confidence</span>
        </h1>
        
        <p class="lead text-white-50 mx-auto mb-4" style="max-width: 720px;">
            Read authentic Uthmani Quran, listen to world-renowned Qaris, test your recitation with real-time AI speech feedback, and organize your daily Hifz memorization.
        </p>

        <div class="d-flex flex-wrap justify-content-center gap-3 mb-5">
            <a href="/register" class="btn btn-gold btn-lg px-4"><i class="bi bi-person-plus-fill me-2"></i>Start Learning Free</a>
            <a href="/recitations" class="btn btn-outline-gold btn-lg px-4"><i class="bi bi-play-circle me-2"></i>Listen to Quran</a>
            <a href="/ai-teacher" class="btn btn-emerald btn-lg px-4 text-white" style="background: #059669; border: 1px solid #10b981;"><i class="bi bi-mic-fill me-2 text-warning"></i>Try AI Recitation</a>
        </div>

        <!-- Arabic Bismillah Calligraphy -->
        <div class="my-4">
            <p class="arabic-text fs-2 text-gold opacity-75">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</p>
        </div>
    </div>
</section>

<!-- Core Features Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h6 class="text-gold text-uppercase fw-bold letter-spacing-1">Complete Ecosystem</h6>
            <h2 class="fw-bold">Everything You Need for Quranic Mastery</h2>
        </div>

        <div class="row g-4">
            <!-- Feature 1: Quran Reader -->
            <div class="col-md-6 col-lg-3">
                <div class="glass-card p-4 h-100 text-center">
                    <div class="fs-1 text-gold mb-3"><i class="bi bi-book"></i></div>
                    <h5 class="fw-bold text-white mb-2">Complete Quran</h5>
                    <p class="text-white-50 small mb-3">
                        All 114 Surahs and 30 Juz with authentic Uthmani script, translations, and verse-by-verse audio sync.
                    </p>
                    <a href="/quran" class="text-gold small fw-bold text-decoration-none">Read Now <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>

            <!-- Feature 2: AI Recitation Teacher -->
            <div class="col-md-6 col-lg-3">
                <div class="glass-card p-4 h-100 text-center border-gold">
                    <div class="fs-1 text-gold mb-3"><i class="bi bi-mic"></i></div>
                    <h5 class="fw-bold text-white mb-2">AI Quran Teacher</h5>
                    <p class="text-white-50 small mb-3">
                        Recite through your microphone and get instant word-by-word difference detection and pronunciation suggestions.
                    </p>
                    <a href="/ai-teacher" class="text-gold small fw-bold text-decoration-none">Practice Recitation <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>

            <!-- Feature 3: Authentic Tilawat -->
            <div class="col-md-6 col-lg-3">
                <div class="glass-card p-4 h-100 text-center">
                    <div class="fs-1 text-gold mb-3"><i class="bi bi-soundwave"></i></div>
                    <h5 class="fw-bold text-white mb-2">Authentic Qaris</h5>
                    <p class="text-white-50 small mb-3">
                        Listen to Sheikh Mishary Alafasy, AbdulBaset, Al-Husary, and Sudais in crystal-clear audio quality.
                    </p>
                    <a href="/recitations" class="text-gold small fw-bold text-decoration-none">Browse Qaris <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>

            <!-- Feature 4: Hifz Tracker -->
            <div class="col-md-6 col-lg-3">
                <div class="glass-card p-4 h-100 text-center">
                    <div class="fs-1 text-gold mb-3"><i class="bi bi-calendar-check"></i></div>
                    <h5 class="fw-bold text-white mb-2">Hifz Revision Tracker</h5>
                    <p class="text-white-50 small mb-3">
                        Set memorization targets, track revision cycles, and maintain learning streaks without losing progress.
                    </p>
                    <a href="/hifz" class="text-gold small fw-bold text-decoration-none">Track Memorization <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Qaris Preview Section -->
<section class="py-5" style="background: rgba(6, 78, 59, 0.15);">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h6 class="text-gold text-uppercase fw-bold">Authentic Voices</h6>
                <h2 class="fw-bold mb-0">Featured Qaris</h2>
            </div>
            <a href="/recitations" class="btn btn-outline-gold btn-sm">View All Qaris</a>
        </div>

        <div class="row g-4">
            <?php foreach ($featuredQaris as $qari): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="glass-card p-4 d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: rgba(212, 175, 55, 0.15); border: 1px solid rgba(212, 175, 55, 0.4);">
                            <i class="bi bi-person-fill fs-3 text-gold"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-white mb-1"><?= htmlspecialchars($qari['name_english']) ?></h6>
                            <p class="arabic-text text-gold mb-1 fs-6"><?= htmlspecialchars($qari['name_arabic']) ?></p>
                            <span class="badge bg-dark border border-secondary text-white-50" style="font-size: 0.75rem;"><?= htmlspecialchars($qari['style']) ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Popular Surahs Quick Access -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h6 class="text-gold text-uppercase fw-bold">Quick Recitation</h6>
            <h2 class="fw-bold">Frequently Read Surahs</h2>
        </div>

        <div class="row g-3">
            <?php foreach ($popularSurahs as $s): ?>
                <div class="col-sm-6 col-md-3">
                    <a href="/quran/<?= $s['id'] ?>" class="glass-card p-3 d-flex justify-content-between align-items-center text-decoration-none">
                        <div>
                            <span class="badge bg-emerald border border-gold text-gold me-2">#<?= $s['id'] ?></span>
                            <span class="fw-bold text-white"><?= htmlspecialchars($s['name_english']) ?></span>
                            <small class="d-block text-white-50"><?= $s['total_ayahs'] ?> Verses</small>
                        </div>
                        <span class="arabic-text text-gold fs-4"><?= htmlspecialchars($s['name_arabic']) ?></span>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Ethical AI Quran Learning & Tajweed Disclosure -->
<section class="py-5">
    <div class="container">
        <div class="glass-card p-4 p-lg-5 border-gold">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <span class="badge bg-warning text-dark fw-bold mb-2"><i class="bi bi-shield-lock-fill me-1"></i> Religious & Academic Integrity</span>
                    <h3 class="fw-bold text-white mb-3">Honest & Responsible AI Assistance</h3>
                    <p class="text-white-50 mb-0">
                        Our speech analysis compares your recitation against canonical Uthmani text to highlight omissions, word substitutions, and pacing. While it provides an invaluable daily practice coach, verified Tajweed certification requires instruction under a qualified human Quran teacher.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="/ai-teacher" class="btn btn-gold btn-lg px-4"><i class="bi bi-mic me-1"></i> Test Microphone</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Banner -->
<section class="py-5 text-center">
    <div class="container py-4">
        <h2 class="fw-bold mb-3">Begin Your Quran Journey Today</h2>
        <p class="text-white-50 mb-4 mx-auto" style="max-width: 600px;">
            Create your free student account, set your daily reading goals, and experience intelligent Quran recitation practice.
        </p>
        <a href="/register" class="btn btn-gold btn-lg px-5">Join Bayan AI Now</a>
    </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
