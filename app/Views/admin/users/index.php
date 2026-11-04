<?php
ob_start();
?>

<div class="mb-4 pb-3 border-bottom border-secondary d-flex justify-content-between align-items-center">
    <div>
        <h3 class="fw-bold text-white mb-1">User & Role Management</h3>
        <p class="text-white-50 mb-0">View registered students, administrators, and manage account statuses.</p>
    </div>
</div>

<div class="glass-card p-4 border-gold">
    <!-- Filter Bar -->
    <form method="GET" action="/admin/users" class="row g-2 mb-4">
        <div class="col-md-6">
            <input type="text" name="q" class="form-control bg-dark text-white border-secondary" placeholder="Search by student name or email..." value="<?= htmlspecialchars($search ?? '') ?>">
        </div>
        <div class="col-md-3">
            <select name="role" class="form-select bg-dark text-white border-secondary">
                <option value="">All Roles</option>
                <option value="student" <?= ($role === 'student') ? 'selected' : '' ?>>Student</option>
                <option value="admin" <?= ($role === 'admin') ? 'selected' : '' ?>>Administrator</option>
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-gold w-100"><i class="bi bi-filter me-1"></i> Filter Users</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-dark table-hover mb-0 align-middle">
            <thead>
                <tr class="text-white-50 small">
                    <th>User Details</th>
                    <th>Role</th>
                    <th>Goal</th>
                    <th>Status</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td>
                            <strong class="text-white"><?= htmlspecialchars($u['name']) ?></strong>
                            <small class="d-block text-white-50"><?= htmlspecialchars($u['email']) ?></small>
                        </td>
                        <td>
                            <span class="badge <?= ($u['role'] === 'admin') ? 'bg-danger' : 'bg-primary' ?>"><?= ucfirst($u['role']) ?></span>
                        </td>
                        <td><?= $u['daily_goal_minutes'] ?? 15 ?> min/day</td>
                        <td>
                            <span class="badge <?= ($u['status'] === 'active') ? 'bg-success' : 'bg-danger' ?>" id="status-badge-<?= $u['id'] ?>">
                                <?= ucfirst($u['status']) ?>
                            </span>
                        </td>
                        <td><small class="text-white-50"><?= date('M d, Y', strtotime($u['created_at'])) ?></small></td>
                        <td>
                            <?php if ($u['id'] != Auth::id()): ?>
                                <button class="btn btn-outline-warning btn-sm toggle-status-btn" data-id="<?= $u['id'] ?>" data-status="<?= ($u['status'] === 'active') ? 'suspended' : 'active' ?>">
                                    <?= ($u['status'] === 'active') ? 'Suspend' : 'Activate' ?>
                                </button>
                            <?php else: ?>
                                <small class="text-white-50">Current Admin</small>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.querySelectorAll('.toggle-status-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        const nextStatus = this.getAttribute('data-status');
        fetch('/admin/users/status', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `user_id=${id}&status=${nextStatus}&csrf_token=<?= $csrfToken ?>`
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) location.reload();
            else alert(res.error || "Action failed");
        });
    });
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/admin.php';
?>
