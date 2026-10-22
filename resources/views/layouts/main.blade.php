<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Bayan AI Quran Teacher - Recite, Learn & Memorize')</title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom Design System -->
    <link rel="stylesheet" href="{{ asset('css/quran-app.css') }}">
    @stack('styles')
</head>
<body class="bg-sand d-flex flex-column min-vh-100">

    <!-- Top Announcement Bar (if active) -->
    @php
        $topAnnouncement = \App\Models\Announcement::active()->latest()->first();
    @endphp
    @if($topAnnouncement)
        <div class="bg-emerald-grad text-white text-center py-2 px-3 small d-flex align-items-center justify-content-center gap-2">
            <span class="badge badge-gold"><i class="bi bi-stars"></i> New</span>
            <span><strong>{{ $topAnnouncement->title }}:</strong> {{ Str::limit($topAnnouncement->content, 90) }}</span>
            <a href="{{ route('home') }}#announcements" class="text-white text-decoration-underline ms-2">Details</a>
        </div>
    @endif

    <!-- Main Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top py-3 shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                <div class="d-inline-flex align-items-center justify-content-center bg-emerald-grad text-white rounded-3 p-2 shadow-sm" style="width: 42px; height: 42px;">
                    <i class="bi bi-book-half fs-4 text-gold"></i>
                </div>
                <div>
                    <span class="fw-bold fs-5 text-emerald d-block lh-1">BAYAN</span>
                    <small class="text-muted text-uppercase tracking-wider" style="font-size: 0.65rem; letter-spacing: 1.5px;">AI Quran Teacher</small>
                </div>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-lg-3">
                    <li class="nav-item">
                        <a class="nav-link fw-medium {{ request()->routeIs('home') ? 'active text-emerald fw-semibold' : '' }}" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium" href="{{ route('home') }}#features">AI Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium" href="{{ route('home') }}#how-it-works">How It Works</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium" href="{{ route('home') }}#faq">FAQs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium" href="{{ route('page.show', 'about') }}">About Us</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    @auth
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-dark text-warning border-warning px-3 rounded-pill shadow-sm">
                                <i class="bi bi-shield-lock-fill me-1"></i> Admin Panel
                            </a>
                        @endif
                        <a href="{{ route('dashboard') }}" class="btn btn-emerald px-4 rounded-pill">
                            <i class="bi bi-grid-fill me-1"></i> Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-emerald rounded-pill px-3">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-gold rounded-pill px-4 shadow-sm">
                            <i class="bi bi-person-plus me-1"></i> Join Free
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-dark-grad text-white pt-5 pb-4 border-top border-success border-opacity-25 mt-auto">
        <div class="container">
            <div class="row g-4 mb-4">
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white bg-opacity-10 text-white rounded-3 p-2" style="width: 40px; height: 40px;">
                            <i class="bi bi-book-half fs-4 text-gold"></i>
                        </div>
                        <span class="fw-bold fs-5 text-white">BAYAN AI</span>
                    </div>
                    <p class="text-white-50 small mb-3">
                        An intelligent Islamic learning platform empowering individuals to recite the Holy Quran with proper pronunciation and memorize sacred verses through advanced voice feedback.
                    </p>
                    <div class="font-arabic fs-4 text-gold opacity-75">
                        وَرَتِّلِ الْقُرْآنَ تَرْتِيلًا
                    </div>
                    <small class="text-white-50">"And recite the Quran with measured recitation." (73:4)</small>
                </div>

                <div class="col-lg-2 col-md-6">
                    <h6 class="text-gold fw-bold mb-3 text-uppercase small" style="letter-spacing: 1px;">Platform</h6>
                    <ul class="list-unstyled small text-white-50 d-flex flex-column gap-2">
                        <li><a href="{{ route('login') }}" class="text-white-50 text-decoration-none">Interactive Quran</a></li>
                        <li><a href="{{ route('login') }}" class="text-white-50 text-decoration-none">AI Recitation Coach</a></li>
                        <li><a href="{{ route('login') }}" class="text-white-50 text-decoration-none">Hifz Spaced Repetition</a></li>
                        <li><a href="{{ route('login') }}" class="text-white-50 text-decoration-none">Progress Analytics</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h6 class="text-gold fw-bold mb-3 text-uppercase small" style="letter-spacing: 1px;">Information</h6>
                    <ul class="list-unstyled small text-white-50 d-flex flex-column gap-2">
                        <li><a href="{{ route('page.show', 'about') }}" class="text-white-50 text-decoration-none">About The Mission</a></li>
                        <li><a href="{{ route('page.show', 'privacy-policy') }}" class="text-white-50 text-decoration-none">Privacy & Voice Data</a></li>
                        <li><a href="{{ route('page.show', 'terms-of-service') }}" class="text-white-50 text-decoration-none">Terms of Service</a></li>
                        <li><a href="{{ route('home') }}#faq" class="text-white-50 text-decoration-none">Frequently Asked Questions</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h6 class="text-gold fw-bold mb-3 text-uppercase small" style="letter-spacing: 1px;">Reciters Supported</h6>
                    <p class="small text-white-50 mb-2">Authentic audio from acclaimed reciters including Mishary Rashid Alafasy, Abdul Basit, and Mahmoud Khalil Al-Husary.</p>
                    <div class="d-flex gap-2">
                        <span class="badge bg-white bg-opacity-10 text-white fw-normal">Hafs 'an 'Asim</span>
                        <span class="badge bg-white bg-opacity-10 text-white fw-normal">Uthmani Script</span>
                    </div>
                </div>
            </div>

            <div class="border-top border-white border-opacity-10 pt-4 d-flex flex-column flex-md-row align-items-center justify-content-between gap-3 text-white-50 small">
                <div>
                    &copy; {{ date('Y') }} Bayan AI Quran Teacher. All rights reserved.
                </div>
                <div class="d-flex gap-3">
                    <a href="{{ route('page.show', 'privacy-policy') }}" class="text-white-50 text-decoration-none">Privacy Policy</a>
                    <a href="{{ route('page.show', 'terms-of-service') }}" class="text-white-50 text-decoration-none">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery 3.7.1 -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @stack('scripts')
</body>
</html>
