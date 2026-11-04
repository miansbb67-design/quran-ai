<?php
ob_start();
?>

<div class="container py-4 pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary">
        <div>
            <h2 class="fw-bold text-white mb-1">Welcome, <?= htmlspecialchars($user['name']) ?></h2>
            <p class="text-white-50 mb-0">Continue your Quranic study, track recitation attempts, and revise Hifz.</p>
        </div>
        <a href="/quran" class="btn btn-gold btn-sm"><i class="bi bi-book me-1"></i> Continue Reading</a>
    </div>

    <!-- Quick Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="glass-card p-3 d-flex align-items-center gap-3">
                <span class="fs-1 text-gold"><i class="bi bi-clock-history"></i></span>
                <div>
                    <h5 class="fw-bold text-white mb-0"><?= $user['daily_goal_minutes'] ?? 15 ?> Mins</h5>
                    <small class="text-white-50">Daily Reading Goal</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card p-3 d-flex align-items-center gap-3">
                <span class="fs-1 text-success"><i class="bi bi-patch-check"></i></span>
                <div>
                    <h5 class="fw-bold text-white mb-0"><?= $hifzStats['memorized'] ?? 0 ?> Portions</h5>
                    <small class="text-white-50">Memorized in Hifz</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card p-3 d-flex align-items-center gap-3">
                <span class="fs-1 text-warning"><i class="bi bi-mic"></i></span>
                <div>
                    <h5 class="fw-bold text-white mb-0"><?= count($recentPractices) ?> Sessions</h5>
                    <small class="text-white-50">AI Recitations</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Last Reading & Saved Bookmarks -->
        <div class="col-lg-6">
            <div class="glass-card p-4 border-gold mb-4">
                <h5 class="fw-bold text-white mb-3"><i class="bi bi-bookmark-star text-gold me-2"></i>Last Read Position</h5>
                <?php if ($lastRead): ?>
                    <div class="p-3 rounded bg-dark border border-secondary d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white mb-1">Surah <?= htmlspecialchars($lastRead['name_english']) ?> (<?= htmlspecialchars($lastRead['name_arabic']) ?>)</h6>
                            <small class="text-white-50">Ayah <?= $lastRead['ayah_number'] ?> &bull; <?= date('M d, H:i', strtotime($lastRead['last_read_at'])) ?></small>
                        </div>
                        <a href="/quran/<?= $lastRead['surah_id'] ?>" class="btn btn-gold btn-sm px-3">Resume</a>
                    </div>
                <?php else: ?>
                    <p class="text-white-50 small mb-0">You haven't opened any Surah yet. <a href="/quran" class="text-gold">Browse Quran</a></p>
                <?php endif; ?>
            </div>

            <div class="glass-card p-4">
                <h5 class="fw-bold text-white mb-3"><i class="bi bi-bookmarks text-gold me-2"></i>Saved Bookmarks</h5>
                <?php if (empty($bookmarks)): ?>
                    <p class="text-white-50 small mb-0">No saved bookmarks. While reading Quran, click the bookmark icon on any ayah!</p>
                <?php else: ?>
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($bookmarks as $bm): ?>
                            <li class="pb-2 mb-2 border-bottom border-secondary d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-white">Surah <?= htmlspecialchars($bm['name_english']) ?> — Ayah <?= $bm['ayah_number'] ?></span>
                                </div>
                                <a href="/quran/<?= $bm['surah_id'] ?>#ayah-<?= $bm['ayah_number'] ?>" class="btn btn-outline-gold btn-sm">Read</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent AI Recitation Practices -->
        <div class="col-lg-6">
            <div class="glass-card p-4 border-gold h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-white mb-0"><i class="bi bi-mic text-gold me-2"></i>Recent AI Recitations</h5>
                    <a href="/ai-teacher" class="btn btn-outline-gold btn-sm">Practice More</a>
                </div>

                <?php if (empty($recentPractices)): ?>
                    <p class="text-white-50 small mb-0">No speech recitation attempts yet. Use your microphone to practice reciting!</p>
                <?php else: ?>
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($recentPractices as $p): ?>
                            <li class="pb-3 mb-3 border-bottom border-secondary d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white mb-0">Surah <?= htmlspecialchars($p['name_english']) ?> — Ayah <?= $p['ayah_number'] ?></h6>
                                    <small class="text-white-50"><?= date('M d, Y H:i', strtotime($p['created_at'])) ?></small>
                                </div>
                                <span class="badge <?= ($p['status'] === 'passed') ? 'bg-success' : 'bg-warning text-dark' ?>">
                                    <?= round($p['accuracy_score']) ?>% Match
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
