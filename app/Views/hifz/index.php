<?php
ob_start();
?>

<div class="container py-4 pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary">
        <div>
            <h2 class="fw-bold text-white mb-1"><i class="bi bi-calendar-check text-gold me-2"></i>Quran Hifz & Revision Tracker</h2>
            <p class="text-white-50 mb-0">Organize your daily Quran memorization goals, revision dates, and retention confidence.</p>
        </div>
        <button class="btn btn-gold btn-sm" data-bs-toggle="modal" data-bs-target="#addHifzModal">
            <i class="bi bi-plus-circle me-1"></i> Add Memorization Target
        </button>
    </div>

    <!-- Statistics Overview -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="glass-card p-3 text-center">
                <span class="fs-4 text-gold fw-bold d-block"><?= $stats['total_items'] ?? 0 ?></span>
                <small class="text-white-50">Total Targets</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-3 text-center">
                <span class="fs-4 text-success fw-bold d-block"><?= $stats['memorized_count'] ?? 0 ?></span>
                <small class="text-white-50">Memorized (Hifz)</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-3 text-center">
                <span class="fs-4 text-warning fw-bold d-block"><?= $stats['learning_count'] ?? 0 ?></span>
                <small class="text-white-50">In Progress</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-3 text-center">
                <span class="fs-4 text-danger fw-bold d-block"><?= $stats['revision_count'] ?? 0 ?></span>
                <small class="text-white-50">Needs Revision</small>
            </div>
        </div>
    </div>

    <!-- Targets List -->
    <div class="glass-card p-4 border-gold">
        <h5 class="fw-bold text-white mb-3">Your Memorized & Active Sections</h5>

        <?php if (empty($hifzList)): ?>
            <div class="text-center py-5">
                <i class="bi bi-journal-bookmark fs-1 text-gold mb-3 d-block"></i>
                <h6 class="text-white">No memorization targets added yet</h6>
                <p class="text-white-50 small mb-3">Start by adding a Surah or ayah range to build your Hifz revision schedule.</p>
                <button class="btn btn-outline-gold btn-sm" data-bs-toggle="modal" data-bs-target="#addHifzModal">Add First Target</button>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead>
                        <tr class="text-white-50 small">
                            <th>Surah</th>
                            <th>Ayah Range</th>
                            <th>Status</th>
                            <th>Next Revision</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($hifzList as $item): ?>
                            <tr>
                                <td>
                                    <strong class="text-white"><?= htmlspecialchars($item['name_english']) ?></strong>
                                    <span class="arabic-text text-gold ms-2"><?= htmlspecialchars($item['name_arabic']) ?></span>
                                </td>
                                <td>Ayah <?= $item['start_ayah'] ?> – <?= $item['end_ayah'] ?></td>
                                <td>
                                    <span class="badge <?= ($item['status'] === 'memorized') ? 'bg-success' : (($item['status'] === 'learning') ? 'bg-warning text-dark' : 'bg-danger') ?>">
                                        <?= ucfirst(str_replace('_', ' ', $item['status'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <small class="text-white-50"><?= $item['next_revision_date'] ? date('M d, Y', strtotime($item['next_revision_date'])) : 'Not scheduled' ?></small>
                                </td>
                                <td>
                                    <button class="btn btn-outline-gold btn-sm mark-memorized-btn" data-id="<?= $item['id'] ?>" data-status="memorized">
                                        <i class="bi bi-check-circle"></i> Memorized
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add Target Modal -->
<div class="modal fade" id="addHifzModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white border border-gold">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold">Add Hifz Target</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="/hifz/store" method="POST">
                <?= $csrfField ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-white-50 small">Surah</label>
                        <select name="surah_id" class="form-select bg-dark text-white border-secondary" required>
                            <?php foreach ($surahs as $s): ?>
                                <option value="<?= $s['id'] ?>">Surah <?= $s['id'] ?>: <?= htmlspecialchars($s['name_english']) ?> (<?= $s['total_ayahs'] ?> Verses)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label text-white-50 small">Start Ayah</label>
                            <input type="number" name="start_ayah" class="form-control bg-dark text-white border-secondary" value="1" min="1" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-white-50 small">End Ayah</label>
                            <input type="number" name="end_ayah" class="form-control bg-dark text-white border-secondary" value="10" min="1" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white-50 small">Initial Status</label>
                        <select name="status" class="form-select bg-dark text-white border-secondary">
                            <option value="learning">Currently Learning</option>
                            <option value="memorized">Already Memorized</option>
                            <option value="needs_revision">Needs Urgent Revision</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-gold">Save Target</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.mark-memorized-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        fetch('/api/hifz/update-status', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${id}&status=memorized&csrf_token=<?= $csrfToken ?>`
        })
        .then(r => r.json())
        .then(() => location.reload());
    });
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
