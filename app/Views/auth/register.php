<?php
ob_start();
?>

<div class="container py-5" style="min-height: 80vh;">
    <div class="row justify-content-center align-items-center">
        <div class="col-md-7 col-lg-5">
            <div class="glass-card p-4 p-lg-5 shadow-lg border-gold">
                <div class="text-center mb-4">
                    <span class="fs-1 text-gold">📖</span>
                    <h3 class="fw-bold mt-2">Start Learning Free</h3>
                    <p class="text-white-50 small">Join Bayan AI Quran Teacher & track your recitation</p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger py-2 small" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="/register">
                    <?= $csrfField ?>

                    <div class="mb-3">
                        <label for="name" class="form-label text-white-50 small">Full Name</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-secondary text-gold"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control bg-dark text-white border-secondary" id="name" name="name" required placeholder="Ahmed Ali">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label text-white-50 small">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-secondary text-gold"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control bg-dark text-white border-secondary" id="email" name="email" required placeholder="ahmed@example.com">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label text-white-50 small">Password (Min 8 chars)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-secondary text-gold"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control bg-dark text-white border-secondary" id="password" name="password" minlength="8" required placeholder="••••••••">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label text-white-50 small">Confirm Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-secondary text-gold"><i class="bi bi-shield-check"></i></span>
                            <input type="password" class="form-control bg-dark text-white border-secondary" id="password_confirmation" name="password_confirmation" minlength="8" required placeholder="••••••••">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-gold w-100 py-2 fw-bold mb-3">Create Free Account</button>

                    <div class="text-center">
                        <span class="text-white-50 small">Already registered?</span>
                        <a href="/login" class="text-gold small fw-bold text-decoration-none ms-1">Sign In</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
