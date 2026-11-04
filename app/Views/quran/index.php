<?php
ob_start();
?>

<div class="container py-4">
    <!-- Header with Search -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pb-3 border-bottom border-secondary">
        <div>
            <h2 class="fw-bold text-white mb-1">The Holy Quran <span class="text-gold">(القرآن الكريم)</span></h2>
            <p class="text-white-50 mb-0">Browse all 114 Surahs with authentic Uthmani script and audio recitations.</p>
        </div>

        <div class="d-flex gap-2">
            <input type="text" id="surahSearch" class="form-control bg-dark text-white border-secondary" placeholder="Search Surah by name or number..." style="min-width: 280px;">
        </div>
    </div>

    <?php if ($lastRead): ?>
        <div class="glass-card p-3 mb-4 border-gold d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <span class="fs-2 text-gold"><i class="bi bi-bookmark-star"></i></span>
                <div>
                    <small class="text-gold text-uppercase fw-bold">Continue Reading</small>
                    <h6 class="text-white mb-0">Surah <?= htmlspecialchars($lastRead['name_english']) ?> (Ayah <?= $lastRead['ayah_number'] ?>)</h6>
                </div>
            </div>
            <a href="/quran/<?= $lastRead['surah_id'] ?>" class="btn btn-gold btn-sm px-3">Resume</a>
        </div>
    <?php endif; ?>

    <!-- Surah Grid List -->
    <div class="row g-3" id="surahsGrid">
        <?php foreach ($surahs as $s): ?>
            <div class="col-sm-6 col-md-4 col-lg-3 surah-card-item" data-name="<?= strtolower($s['name_english'] . ' ' . $s['name_arabic']) ?>" data-id="<?= $s['id'] ?>">
                <a href="/quran/<?= $s['id'] ?>" class="glass-card p-3 h-100 d-flex flex-column justify-content-between text-decoration-none">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge bg-emerald border border-gold text-gold">#<?= $s['id'] ?></span>
                        <span class="arabic-text text-gold fs-4"><?= htmlspecialchars($s['name_arabic']) ?></span>
                    </div>
                    <div>
                        <h6 class="fw-bold text-white mb-1"><?= htmlspecialchars($s['name_english']) ?></h6>
                        <div class="d-flex justify-content-between text-white-50 small">
                            <span><?= htmlspecialchars($s['name_translation']) ?></span>
                            <span><?= $s['total_ayahs'] ?> Verses</span>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
document.getElementById('surahSearch').addEventListener('input', function(e) {
    const val = e.target.value.toLowerCase().trim();
    document.querySelectorAll('.surah-card-item').forEach(card => {
        const name = card.getAttribute('data-name');
        const id = card.getAttribute('data-id');
        if (name.includes(val) || id === val) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
