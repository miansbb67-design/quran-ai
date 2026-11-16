<?php
ob_start();
?>

<!-- Life Deen Quran Reader Header -->
<div class="container" style="padding-top:2rem; padding-bottom:1rem;">
  <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; border-bottom:1px solid var(--border); padding-bottom:1.5rem; margin-bottom:2rem;">
    <div>
      <span class="badge badge-gold" style="margin-bottom:0.5rem; display:inline-block;"><i class="bi bi-book me-1"></i> The Holy Quran</span>
      <h1 style="font-size:2rem; font-weight:800; color:var(--text-main); margin:0;">Complete Quran Reader (القرآن الكريم)</h1>
      <p style="font-size:0.95rem; color:var(--text-muted); margin:0.35rem 0 0;">Read all 114 Surahs with authentic Uthmani script and word-by-word Qari tilawat.</p>
    </div>
    <div>
      <input type="text" id="surahSearch" placeholder="Search Surah by name or number..." style="padding:0.65rem 1.25rem; border:1px solid var(--border); border-radius:9999px; width:280px; font-family:inherit;">
    </div>
  </div>
</div>

<div class="container" style="padding-bottom:4rem;">
  <?php if ($lastRead): ?>
    <div class="tool-card" style="margin-bottom:2rem; display:flex; justify-content:space-between; align-items:center; background:var(--gold-light); border-color:var(--gold-border);">
      <div style="display:flex; align-items:center; gap:1rem;">
        <span style="font-size:1.8rem; color:var(--gold);">🔖</span>
        <div>
          <span style="font-size:0.75rem; font-weight:700; color:var(--gold); text-transform:uppercase;">Continue Reading</span>
          <h3 style="font-size:1.1rem; margin:0; color:var(--text-main);">Surah <?= htmlspecialchars($lastRead['name_english']) ?> (Ayah <?= $lastRead['ayah_number'] ?>)</h3>
        </div>
      </div>
      <a href="<?= Controller::url('/quran/' . $lastRead['surah_id']) ?>" class="btn btn-sm btn-primary">Resume Reading &rarr;</a>
    </div>
  <?php endif; ?>

  <!-- Surahs Grid (Life Deen Clean Card Layout) -->
  <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(260px, 1fr)); gap:1.25rem;" id="surahsGrid">
    <?php foreach ($surahs as $s): ?>
      <div class="surah-card-item" data-name="<?= strtolower($s['name_english'] . ' ' . $s['name_arabic']) ?>" data-id="<?= $s['id'] ?>">
        <a href="<?= Controller::url('/quran/' . $s['id']) ?>" class="tool-card" style="padding:1.25rem; display:flex; flex-direction:column; justify-content:space-between; height:100%; text-decoration:none;">
          <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1rem;">
            <span class="badge badge-primary">#<?= $s['id'] ?></span>
            <span class="arabic-text" style="font-size:1.6rem; color:var(--primary); font-weight:bold;"><?= htmlspecialchars($s['name_arabic']) ?></span>
          </div>
          <div>
            <h4 style="font-size:1.1rem; font-weight:700; color:var(--text-main); margin:0 0 0.25rem;"><?= htmlspecialchars($s['name_english']) ?></h4>
            <div style="display:flex; justify-content:space-between; font-size:0.8rem; color:var(--text-muted);">
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
