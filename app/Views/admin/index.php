<?php
ob_start();
?>

<div class="mb-4 pb-3 border-bottom border-secondary d-flex justify-content-between align-items-center">
    <div>
        <h3 class="fw-bold text-white mb-1">Executive Dashboard</h3>
        <p class="text-white-50 mb-0">Real-time platform overview for Bayan AI Quran Teacher</p>
    </div>
    <span class="badge bg-success px-3 py-2"><i class="bi bi-shield-check me-1"></i> System Operational</span>
</div>

<!-- Key Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="glass-card p-3 border-gold text-center">
            <span class="fs-2 text-gold fw-bold d-block"><?= $stats['total_users'] ?></span>
            <small class="text-white-50 text-uppercase fw-bold" style="font-size: 0.72rem;">Registered Students</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="glass-card p-3 text-center">
            <span class="fs-2 text-success fw-bold d-block"><?= $stats['total_practices'] ?></span>
            <small class="text-white-50 text-uppercase fw-bold" style="font-size: 0.72rem;">AI Recitations</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="glass-card p-3 text-center">
            <span class="fs-2 text-warning fw-bold d-block"><?= $stats['total_hifz_records'] ?></span>
            <small class="text-white-50 text-uppercase fw-bold" style="font-size: 0.72rem;">Hifz Portions</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="glass-card p-3 text-center">
            <span class="fs-2 text-info fw-bold d-block"><?= $stats['total_events'] ?></span>
            <small class="text-white-50 text-uppercase fw-bold" style="font-size: 0.72rem;">Analytics Events</small>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Students -->
    <div class="col-lg-6">
        <div class="glass-card p-4 border-gold">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-white mb-0">Recent Registrations</h5>
                <a href="/admin/users" class="btn btn-outline-gold btn-sm">View All</a>
            </div>

            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead>
                        <tr class="text-white-50 small">
                            <th>Name</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentUsers as $u): ?>
                            <tr>
                                <td>
                                    <strong class="text-white"><?= htmlspecialchars($u['name']) ?></strong>
                                    <small class="d-block text-white-50"><?= htmlspecialchars($u['email']) ?></small>
                                </td>
                                <td><span class="badge bg-secondary"><?= ucfirst($u['role']) ?></span></td>
                                <td><span class="badge bg-success"><?= ucfirst($u['status']) ?></span></td>
                                <td><small class="text-white-50"><?= date('M d', strtotime($u['created_at'])) ?></small></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent AI Recitations -->
    <div class="col-lg-6">
        <div class="glass-card p-4 border-gold">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-white mb-0">Recent Recitation Practice</h5>
                <span class="badge bg-dark border border-secondary text-gold">Live Log</span>
            </div>

            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead>
                        <tr class="text-white-50 small">
                            <th>Student</th>
                            <th>Surah</th>
                            <th>Score</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentPractices)): ?>
                            <tr><td colspan="4" class="text-center text-white-50 py-3">No recitation sessions recorded yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($recentPractices as $rp): ?>
                                <tr>
                                    <td class="text-white"><?= htmlspecialchars($rp['user_name']) ?></td>
                                    <td><?= htmlspecialchars($rp['name_english']) ?>:<?= $rp['ayah_number'] ?></td>
                                    <td><strong><?= round($rp['accuracy_score']) ?>%</strong></td>
                                    <td>
                                        <span class="badge <?= ($rp['status'] === 'passed') ? 'bg-success' : 'bg-warning text-dark' ?>">
                                            <?= ucfirst($rp['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/admin.php';
?>
