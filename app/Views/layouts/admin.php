<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Admin Panel — Bayan AI') ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/assets/css/bayan.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .admin-sidebar { min-height: 100vh; background: #02130f; border-right: 1px solid var(--border-glass); }
        .admin-sidebar .nav-link { color: #94a3b8 !important; padding: 12px 18px; border-radius: 8px; margin-bottom: 4px; display: flex; align-items: center; gap: 10px; }
        .admin-sidebar .nav-link:hover, .admin-sidebar .nav-link.active { background: rgba(6, 78, 59, 0.5); color: var(--accent-gold) !important; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 admin-sidebar p-3 d-flex flex-column justify-content-between">
                <div>
                    <a href="/admin" class="d-flex align-items-center gap-2 text-decoration-none mb-4 pb-3 border-bottom border-secondary">
                        <span class="fs-4 text-gold">📖</span>
                        <div>
                            <span class="fw-bold fs-6 text-white">BAYAN <span class="text-gold">ADMIN</span></span>
                            <small class="d-block text-white-50" style="font-size: 0.65rem;">MANAGEMENT HUB</small>
                        </div>
                    </a>

                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/admin') && !str_contains($_SERVER['REQUEST_URI'], '/users') && !str_contains($_SERVER['REQUEST_URI'], '/seo') && !str_contains($_SERVER['REQUEST_URI'], '/analytics') ? 'active' : '' ?>" href="/admin">
                                <i class="bi bi-speedometer2"></i> Executive Hub
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/users') ? 'active' : '' ?>" href="/admin/users">
                                <i class="bi bi-people"></i> Users & Roles
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/seo') ? 'active' : '' ?>" href="/admin/seo">
                                <i class="bi bi-search-heart text-gold"></i> SEO & Console
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/analytics') ? 'active' : '' ?>" href="/admin/analytics">
                                <i class="bi bi-graph-up-arrow"></i> Traffic & Metrics
                            </a>
                        </li>
                    </ul>
                </div>

                <div>
                    <div class="p-2 rounded bg-dark border border-secondary mb-3 text-center">
                        <small class="text-white-50 d-block">Admin Account</small>
                        <span class="text-gold small fw-bold"><?= htmlspecialchars(Auth::user()['name'] ?? 'Admin') ?></span>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="/" class="btn btn-outline-secondary btn-sm"><i class="bi bi-globe me-1"></i> Public Site</a>
                        <a href="/logout" class="btn btn-danger btn-sm"><i class="bi bi-box-arrow-right me-1"></i> Logout</a>
                    </div>
                </div>
            </div>

            <!-- Admin Main Workspace -->
            <div class="col-md-9 col-lg-10 p-4">
                <?= $content ?? '' ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
