<?php
ob_start();
?>

<div class="mb-4 pb-3 border-bottom border-secondary d-flex justify-content-between align-items-center">
    <div>
        <h3 class="fw-bold text-white mb-1">SEO & Search Console Management</h3>
        <p class="text-white-50 mb-0">Control Google & Bing meta tags, Open Graph previews, and robots.txt file.</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Google Search Console Verifications -->
    <div class="col-lg-6">
        <div class="glass-card p-4 border-gold">
            <h5 class="fw-bold text-white mb-3"><i class="bi bi-google text-gold me-2"></i>Search Engine Verification</h5>
            <form method="POST" action="/admin/seo/update">
                <?= $csrfField ?>
                <div class="mb-3">
                    <label class="form-label text-white-50 small">Google Search Console Verification Token</label>
                    <input type="text" name="google_verification" class="form-control bg-dark text-white border-secondary" placeholder="e.g. google-site-verification=xyz..." value="<?= htmlspecialchars($settings['google_verification'] ?? '') ?>">
                    <small class="text-white-50" style="font-size: 0.72rem;">Meta tag content for domain verification.</small>
                </div>
                <button type="submit" class="btn btn-gold btn-sm"><i class="bi bi-check-circle me-1"></i> Save Verifications</button>
            </form>
        </div>
    </div>

    <!-- Live robots.txt Editor -->
    <div class="col-lg-6">
        <div class="glass-card p-4 border-gold">
            <h5 class="fw-bold text-white mb-3"><i class="bi bi-robot text-gold me-2"></i>Live robots.txt Configuration</h5>
            <form method="POST" action="/admin/seo/robots">
                <?= $csrfField ?>
                <div class="mb-3">
                    <textarea name="robots_content" class="form-control bg-dark text-white border-secondary font-monospace" rows="4"><?= htmlspecialchars($robotsContent) ?></textarea>
                </div>
                <button type="submit" class="btn btn-outline-gold btn-sm"><i class="bi bi-file-earmark-code me-1"></i> Save robots.txt</button>
            </form>
        </div>
    </div>
</div>

<!-- Route Metadata Editor -->
<div class="glass-card p-4 border-gold">
    <h5 class="fw-bold text-white mb-3"><i class="bi bi-card-text text-gold me-2"></i>Page-Specific Meta Tags</h5>
    <div class="table-responsive">
        <table class="table table-dark table-hover mb-0 align-middle">
            <thead>
                <tr class="text-white-50 small">
                    <th>Page Route</th>
                    <th>Meta Title</th>
                    <th>Meta Description</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($routes as $r): ?>
                    <tr>
                        <td><code class="text-warning"><?= htmlspecialchars($r['route_path']) ?></code></td>
                        <td><small class="text-white"><?= htmlspecialchars($r['meta_title']) ?></small></td>
                        <td><small class="text-white-50"><?= substr(htmlspecialchars($r['meta_description']), 0, 70) ?>...</small></td>
                        <td>
                            <button class="btn btn-outline-gold btn-sm edit-seo-btn" 
                                data-id="<?= $r['id'] ?>" 
                                data-path="<?= htmlspecialchars($r['route_path']) ?>"
                                data-title="<?= htmlspecialchars($r['meta_title']) ?>" 
                                data-desc="<?= htmlspecialchars($r['meta_description']) ?>"
                                data-keywords="<?= htmlspecialchars($r['meta_keywords'] ?? '') ?>"
                                data-bs-toggle="modal" 
                                data-bs-target="#editSeoModal">
                                Edit
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Edit SEO Modal -->
<div class="modal fade" id="editSeoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark text-white border border-gold">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold" id="seoModalTitle">Edit Page SEO</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/admin/seo/update">
                <?= $csrfField ?>
                <input type="hidden" name="route_id" id="modalRouteId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-white-50 small">Meta Title (Max 60 chars recommended)</label>
                        <input type="text" name="meta_title" id="modalMetaTitle" class="form-control bg-dark text-white border-secondary" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white-50 small">Meta Description (Max 160 chars recommended)</label>
                        <textarea name="meta_description" id="modalMetaDesc" class="form-control bg-dark text-white border-secondary" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white-50 small">Keywords</label>
                        <input type="text" name="meta_keywords" id="modalMetaKeywords" class="form-control bg-dark text-white border-secondary">
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-gold">Update SEO</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.edit-seo-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('modalRouteId').value = this.getAttribute('data-id');
        document.getElementById('seoModalTitle').innerText = 'Edit SEO: ' + this.getAttribute('data-path');
        document.getElementById('modalMetaTitle').value = this.getAttribute('data-title');
        document.getElementById('modalMetaDesc').value = this.getAttribute('data-desc');
        document.getElementById('modalMetaKeywords').value = this.getAttribute('data-keywords');
    });
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/admin.php';
?>
