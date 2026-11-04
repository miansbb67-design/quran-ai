<?php
ob_start();
?>

<div class="mb-4 pb-3 border-bottom border-secondary d-flex justify-content-between align-items-center">
    <div>
        <h3 class="fw-bold text-white mb-1">Traffic & System Analytics</h3>
        <p class="text-white-50 mb-0">First-party privacy-conscious event metrics & activity logs.</p>
    </div>
</div>

<!-- Daily Visits Chart Card -->
<div class="glass-card p-4 border-gold mb-4">
    <h5 class="fw-bold text-white mb-3"><i class="bi bi-graph-up text-gold me-2"></i>7-Day Traffic Activity</h5>
    <div style="height: 280px;">
        <canvas id="trafficChart" 
            data-dates='<?= json_encode(array_column($dailyVisits, "date")) ?>' 
            data-totals='<?= json_encode(array_column($dailyVisits, "total")) ?>'>
        </canvas>
    </div>
</div>

<div class="row g-4">
    <!-- Event Distribution -->
    <div class="col-lg-5">
        <div class="glass-card p-4 border-gold h-100">
            <h5 class="fw-bold text-white mb-3">Event Breakdown</h5>
            <ul class="list-group list-group-flush bg-transparent">
                <?php foreach ($eventCounts as $ev): ?>
                    <li class="list-group-item bg-transparent text-white d-flex justify-content-between align-items-center border-secondary px-0">
                        <span><i class="bi bi-dot text-gold fs-4"></i> <?= htmlspecialchars($ev['event_type']) ?></span>
                        <span class="badge bg-emerald border border-gold text-gold"><?= $ev['count'] ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <!-- Live Event Log -->
    <div class="col-lg-7">
        <div class="glass-card p-4 border-gold">
            <h5 class="fw-bold text-white mb-3">Recent Activity Stream</h5>
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead>
                        <tr class="text-white-50 small">
                            <th>Type</th>
                            <th>Page</th>
                            <th>User</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentEvents as $re): ?>
                            <tr>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($re['event_type']) ?></span></td>
                                <td><small class="text-white"><?= htmlspecialchars($re['page_url']) ?></small></td>
                                <td><small class="text-white-50"><?= htmlspecialchars($re['user_name'] ?? 'Guest') ?></small></td>
                                <td><small class="text-white-50"><?= date('H:i:s', strtotime($re['created_at'])) ?></small></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const canvas = document.getElementById('trafficChart');
    if (canvas) {
        const dates = JSON.parse(canvas.getAttribute('data-dates') || '[]');
        const totals = JSON.parse(canvas.getAttribute('data-totals') || '[]');

        new Chart(canvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: dates.length ? dates : ['Today'],
                datasets: [{
                    label: 'Page Views & Events',
                    data: totals.length ? totals : [1],
                    borderColor: '#d4af37',
                    backgroundColor: 'rgba(212, 175, 55, 0.1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { color: '#94a3b8' }, grid: { color: 'rgba(255,255,255,0.05)' } },
                    y: { ticks: { color: '#94a3b8' }, grid: { color: 'rgba(255,255,255,0.05)' } }
                }
            }
        });
    }
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/admin.php';
?>
