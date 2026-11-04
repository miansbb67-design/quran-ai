<?php
ob_start();
?>

<div class="container py-5" style="min-height: 75vh;">
    <div class="row justify-content-center align-items-center">
        <div class="col-md-6 col-lg-5">
            <div class="glass-card p-4 p-lg-5 shadow-lg border-gold">
                <div class="text-center mb-4">
                    <span class="fs-1 text-gold">📖</span>
                    <h3 class="fw-bold mt-2">Welcome Back</h3>
                    <p class="text-white-50 small">Sign in to your Bayan AI Quran account</p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger py-2 small" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="/login">
                    <?= $csrfField ?>

                    <div class="mb-3">
                        <label for="email" class="form-label text-white-50 small">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-secondary text-gold"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control bg-dark text-white border-secondary" id="email" name="email" required placeholder="name@example.com">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label text-white-50 small">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-secondary text-gold"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control bg-dark text-white border-secondary" id="password" name="password" required placeholder="••••••••">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-gold w-100 py-2 fw-bold mb-3">Sign In</button>

                    <div class="text-center">
                        <span class="text-white-50 small">Don't have an account?</span>
                        <a href="/register" class="text-gold small fw-bold text-decoration-none ms-1">Create Account</a>
                    </div>
                </form>

                <div class="mt-4 pt-3 border-top border-secondary text-center">
                    <small class="text-white-50">Demo Admin: <code class="text-warning">admin@bayan.test</code> / <code class="text-warning">Admin@123456</code></small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
