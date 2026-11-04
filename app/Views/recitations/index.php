<?php
ob_start();
?>

<div class="container py-4 pb-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold text-white mb-2"><i class="bi bi-soundwave text-gold me-2"></i>Quran Tilawat & Qari Audio Library</h1>
        <p class="text-white-50 mx-auto" style="max-width: 650px;">
            Listen to verified, heart-touching Quran recitations by world-renowned Qaris with continuous playback.
        </p>
    </div>

    <!-- Qaris Showcase -->
    <div class="row g-4 mb-5">
        <?php foreach ($qaris as $q): ?>
            <div class="col-md-6 col-lg-4">
                <div class="glass-card p-4 h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge bg-emerald border border-gold text-gold"><?= htmlspecialchars($q['style']) ?></span>
                            <?php if ($q['is_featured']): ?>
                                <span class="badge bg-warning text-dark"><i class="bi bi-star-fill me-1"></i> Featured</span>
                            <?php endif; ?>
                        </div>
                        <h4 class="arabic-text text-gold mb-1"><?= htmlspecialchars($q['name_arabic']) ?></h4>
                        <h5 class="fw-bold text-white mb-2"><?= htmlspecialchars($q['name_english']) ?></h5>
                        <p class="text-white-50 small mb-3">Authentic high-fidelity audio stream powered by EveryAyah canonical archive.</p>
                    </div>

                    <a href="/quran/1?qari=<?= $q['id'] ?>" class="btn btn-gold btn-sm w-100">
                        <i class="bi bi-play-circle me-1"></i> Listen to Recitations
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Surah Audio Navigation -->
    <div class="glass-card p-4 border-gold">
        <h4 class="fw-bold text-white mb-3">Browse Surahs by Recitation</h4>
        <div class="row g-2">
            <?php foreach ($surahs as $s): ?>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <a href="/quran/<?= $s['id'] ?>" class="p-2 rounded bg-dark border border-secondary text-decoration-none d-flex justify-content-between align-items-center">
                        <small class="text-white"><?= $s['id'] ?>. <?= htmlspecialchars($s['name_english']) ?></small>
                        <span class="arabic-text text-gold"><?= htmlspecialchars($s['name_arabic']) ?></span>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
