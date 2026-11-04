<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Bayan AI Quran Teacher — Learn, Recite & Memorize Holy Quran') ?></title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="<?= htmlspecialchars($seo['meta_description'] ?? 'Master Quran recitation, Tajweed, and Hifz with authentic Qari voices and real-time AI pronunciation feedback.') ?>">
    <meta name="keywords" content="<?= htmlspecialchars($seo['meta_keywords'] ?? 'Quran, AI Quran, Tajweed, Hifz, Tilawat, Alafasy') ?>">
    <meta name="robots" content="<?= htmlspecialchars($seo['robots'] ?? 'index, follow') ?>">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?= htmlspecialchars($title ?? 'Bayan AI Quran Teacher') ?>">
    <meta property="og:description" content="<?= htmlspecialchars($seo['meta_description'] ?? 'Master Quran recitation, Tajweed, and Hifz with authentic Qaris.') ?>">
    <meta property="og:image" content="<?= htmlspecialchars($seo['og_image'] ?? '/assets/images/og-bayan.jpg') ?>">
    <meta property="og:type" content="website">

    <!-- Google Fonts & Bootstrap 5.3 & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Custom Theme CSS -->
    <link rel="stylesheet" href="/assets/css/bayan.css">

    <!-- Schema.org JSON-LD -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "EducationalOrganization",
      "name": "Bayan AI Quran Teacher",
      "url": "https://xyz.careernexaa.com",
      "description": "Comprehensive Islamic Quranic learning platform with AI Tajweed feedback, authentic Qari recitations, and Hifz tracker."
    }
    </script>
</head>
<body>

    <!-- Navigation Header -->
    <nav class="navbar navbar-expand-lg bayan-navbar sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="/">
                <span class="fs-3 text-gold">📖</span>
                <div>
                    <span class="fw-bold fs-5 text-white">BAYAN <span class="text-gold">AI</span></span>
                    <small class="d-block text-white-50" style="font-size: 0.68rem; letter-spacing: 1px;">QURAN TEACHER</small>
                </div>
            </a>
            
            <button class="navbar-toggler border-gold text-gold" type="button" data-bs-toggle="collapse" data-bs-target="#bayanNav">
                <i class="bi bi-list fs-2 text-gold"></i>
            </button>

            <div class="collapse navbar-collapse" id="bayanNav">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="/">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="/quran">Read Quran</a></li>
                    <li class="nav-item"><a class="nav-link" href="/ai-teacher"><i class="bi bi-mic text-gold me-1"></i>AI Teacher</a></li>
                    <li class="nav-item"><a class="nav-link" href="/recitations">Recitations</a></li>
                    <li class="nav-item"><a class="nav-link" href="/hifz">Hifz Tracker</a></li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <?php if (Auth::check()): ?>
                        <?php if (Auth::isAdmin()): ?>
                            <a href="/admin" class="btn btn-outline-gold btn-sm"><i class="bi bi-speedometer2 me-1"></i>Admin Panel</a>
                        <?php else: ?>
                            <a href="/dashboard" class="btn btn-outline-gold btn-sm"><i class="bi bi-person-circle me-1"></i>Dashboard</a>
                        <?php endif; ?>
                        <a href="/logout" class="btn btn-danger btn-sm px-3">Logout</a>
                    <?php else: ?>
                        <a href="/login" class="btn btn-outline-gold btn-sm">Sign In</a>
                        <a href="/register" class="btn btn-gold btn-sm">Start Free</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Dynamic Content -->
    <main>
        <?= $content ?? '' ?>
    </main>

    <!-- Comprehensive Islamic Footer -->
    <footer class="bayan-footer mt-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="fs-3 text-gold">📖</span>
                        <span class="fw-bold fs-5 text-white">BAYAN <span class="text-gold">AI</span></span>
                    </div>
                    <p class="text-white-50 small mb-3">
                        A dedicated platform to learn, recite, understand, and memorize the Holy Quran with authentic recitations and AI pronunciation guidance.
                    </p>
                    <p class="text-gold small mb-0"><i class="bi bi-patch-check-fill me-1"></i> Authentic Uthmani Text & Verified Qari Recitations</p>
                </div>
                <div class="col-lg-2 col-6">
                    <h6 class="text-gold fw-bold mb-3">Quran Links</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="/quran" class="text-white-50 text-decoration-none hover-gold">All 114 Surahs</a></li>
                        <li class="mb-2"><a href="/quran/1" class="text-white-50 text-decoration-none">Surah Al-Fatihah</a></li>
                        <li class="mb-2"><a href="/quran/36" class="text-white-50 text-decoration-none">Surah Ya-Sin</a></li>
                        <li class="mb-2"><a href="/quran/55" class="text-white-50 text-decoration-none">Surah Ar-Rahman</a></li>
                        <li class="mb-2"><a href="/quran/67" class="text-white-50 text-decoration-none">Surah Al-Mulk</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-6">
                    <h6 class="text-gold fw-bold mb-3">Learning Modules</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="/ai-teacher" class="text-white-50 text-decoration-none">AI Recitation Coach</a></li>
                        <li class="mb-2"><a href="/recitations" class="text-white-50 text-decoration-none">Qari Audio Library</a></li>
                        <li class="mb-2"><a href="/hifz" class="text-white-50 text-decoration-none">Hifz Revision Tracker</a></li>
                        <li class="mb-2"><a href="/dashboard" class="text-white-50 text-decoration-none">Student Dashboard</a></li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h6 class="text-gold fw-bold mb-3">Reliability & Ethics</h6>
                    <p class="text-white-50 small">
                        Quran text is sourced from verified canonical Uthmani scripts. Speech analysis acts as an educational tool and does not replace human certified Tajweed instructors.
                    </p>
                    <div class="mt-3 text-white-50 small">
                        &copy; <?= date('Y') ?> Bayan AI Quran Teacher. All rights reserved.
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5.3 & GSAP Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
</body>
</html>
