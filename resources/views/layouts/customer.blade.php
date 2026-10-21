<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Student Portal - Bayan AI Quran')</title>

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom Design System -->
    <link rel="stylesheet" href="{{ asset('css/quran-app.css') }}">
    <!-- Chart.js 4.4 -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @stack('styles')
</head>
<body class="bg-sand min-vh-100 d-flex flex-column">

    <div class="d-flex flex-grow-1">
        <!-- Sidebar Navigation -->
        <aside class="app-sidebar d-none d-lg-flex flex-column p-3">
            <div class="d-flex align-items-center gap-2 mb-4 px-2">
                <div class="d-inline-flex align-items-center justify-content-center bg-emerald-grad text-white rounded-3 p-2 shadow-sm" style="width: 40px; height: 40px;">
                    <i class="bi bi-book-half fs-4 text-gold"></i>
                </div>
                <div>
                    <span class="fw-bold fs-5 text-emerald d-block lh-1">BAYAN</span>
                    <small class="text-muted text-uppercase tracking-wider" style="font-size: 0.65rem; letter-spacing: 1.5px;">Student Portal</small>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="nav flex-column flex-grow-1">
                <a href="{{ route('dashboard') }}" class="sidebar-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-fill"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('quran.index') }}" class="sidebar-nav-link {{ request()->routeIs('quran.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-bookmark-fill"></i>
                    <span>Quran Reader</span>
                </a>

                <a href="{{ route('ai-teacher.index') }}" class="sidebar-nav-link {{ request()->routeIs('ai-teacher.*') ? 'active' : '' }}">
                    <i class="bi bi-mic-fill"></i>
                    <span>AI Voice Teacher</span>
                    <span class="badge bg-gold-grad text-white rounded-pill ms-auto px-2" style="font-size: 0.7rem;">AI</span>
                </a>

                <a href="{{ route('hifz.index') }}" class="sidebar-nav-link {{ request()->routeIs('hifz.*') ? 'active' : '' }}">
                    <i class="bi bi-award-fill"></i>
                    <span>Hifz Memorization</span>
                </a>

                <a href="{{ route('progress.index') }}" class="sidebar-nav-link {{ request()->routeIs('progress.*') ? 'active' : '' }}">
                    <i class="bi bi-graph-up-arrow"></i>
                    <span>Progress & Stats</span>
                </a>

                <a href="{{ route('quran.bookmarks') }}" class="sidebar-nav-link {{ request()->routeIs('quran.bookmarks') ? 'active' : '' }}">
                    <i class="bi bi-bookmark-star-fill"></i>
                    <span>My Bookmarks</span>
                </a>

                <a href="{{ route('hadith.index') }}" class="sidebar-nav-link {{ request()->routeIs('hadith.*') ? 'active' : '' }}">
                    <i class="bi bi-book-half"></i>
                    <span>Daily Hadith</span>
                </a>

                <a href="{{ route('profile.edit') }}" class="sidebar-nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <i class="bi bi-person-circle"></i>
                    <span>Profile & Settings</span>
                </a>
            </nav>

            <!-- Bottom User Pill -->
            <div class="mt-auto pt-3 border-top">
                <div class="d-flex align-items-center gap-2 p-2 rounded-3 bg-light">
                    <div class="rounded-circle bg-emerald-grad text-white d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; font-size: 0.85rem;">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="overflow-hidden">
                        <div class="fw-semibold text-truncate small text-dark">{{ Auth::user()->name }}</div>
                        <div class="text-muted text-uppercase" style="font-size: 0.65rem;">{{ Auth::user()->role }}</div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="ms-auto">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-link text-muted p-1" title="Sign out">
                            <i class="bi bi-box-arrow-right fs-5"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-grow-1 d-flex flex-column min-vh-100" style="min-width: 0;">
            <!-- Top Header -->
            <header class="navbar navbar-expand bg-white border-bottom px-3 px-lg-4 py-2 sticky-top shadow-sm">
                <!-- Mobile toggle -->
                <button class="btn btn-light d-lg-none me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
                    <i class="bi bi-list fs-4"></i>
                </button>

                <!-- Page title or breadcrumb -->
                <div class="d-flex align-items-center gap-2">
                    <h5 class="mb-0 fw-bold text-emerald">@yield('page_title', 'Student Portal')</h5>
                </div>

                <!-- Right Header Actions -->
                <div class="ms-auto d-flex align-items-center gap-2 gap-md-3">
                    @php
                        $userProgress = Auth::user()->readingProgress;
                        $streak = $userProgress ? $userProgress->current_streak_days : 0;
                    @endphp
                    <div class="badge badge-gold d-flex align-items-center gap-1 px-3 py-2 shadow-sm" title="Active Daily Streak">
                        <i class="bi bi-fire text-danger fs-6"></i>
                        <span><strong>{{ $streak }}</strong> Day Streak</span>
                    </div>

                    <a href="{{ route('home') }}" class="btn btn-sm btn-outline-secondary d-none d-md-inline-flex align-items-center gap-1 rounded-pill" target="_blank">
                        <i class="bi bi-box-arrow-up-right"></i>
                        <span>Website</span>
                    </a>

                    <!-- User Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-light rounded-pill d-flex align-items-center gap-2 px-2 py-1 border" data-bs-toggle="dropdown">
                            <div class="rounded-circle bg-emerald-grad text-white d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                            </div>
                            <span class="d-none d-sm-inline fw-semibold small text-dark me-1">{{ Auth::user()->name }}</span>
                            <i class="bi bi-chevron-down small text-muted"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                            <li class="px-3 py-2 border-bottom">
                                <div class="fw-bold small">{{ Auth::user()->name }}</div>
                                <div class="text-muted small">{{ Auth::user()->email }}</div>
                            </li>
                            <li><a class="dropdown-item py-2" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i> Profile Settings</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('progress.index') }}"><i class="bi bi-graph-up me-2"></i> Learning Statistics</a></li>
                            @if(Auth::user()->isAdmin())
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item py-2 text-warning fw-semibold" href="{{ route('admin.dashboard') }}"><i class="bi bi-shield-lock me-2"></i> Admin Panel</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger py-2">
                                        <i class="bi bi-box-arrow-right me-2"></i> Sign Out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Offcanvas Mobile Sidebar -->
            <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar">
                <div class="offcanvas-header border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <div class="d-inline-flex align-items-center justify-content-center bg-emerald-grad text-white rounded-3 p-2 shadow-sm" style="width: 38px; height: 38px;">
                            <i class="bi bi-book-half fs-4 text-gold"></i>
                        </div>
                        <span class="fw-bold fs-5 text-emerald">BAYAN</span>
                    </div>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
                </div>
                <div class="offcanvas-body d-flex flex-column">
                    <nav class="nav flex-column gap-1">
                        <a href="{{ route('dashboard') }}" class="sidebar-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="bi bi-grid-fill"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('quran.index') }}" class="sidebar-nav-link {{ request()->routeIs('quran.*') ? 'active' : '' }}">
                            <i class="bi bi-journal-bookmark-fill"></i>
                            <span>Quran Reader</span>
                        </a>
                        <a href="{{ route('ai-teacher.index') }}" class="sidebar-nav-link {{ request()->routeIs('ai-teacher.*') ? 'active' : '' }}">
                            <i class="bi bi-mic-fill"></i>
                            <span>AI Voice Teacher</span>
                        </a>
                        <a href="{{ route('hifz.index') }}" class="sidebar-nav-link {{ request()->routeIs('hifz.*') ? 'active' : '' }}">
                            <i class="bi bi-award-fill"></i>
                            <span>Hifz Memorization</span>
                        </a>
                        <a href="{{ route('progress.index') }}" class="sidebar-nav-link {{ request()->routeIs('progress.*') ? 'active' : '' }}">
                            <i class="bi bi-graph-up-arrow"></i>
                            <span>Progress & Stats</span>
                        </a>
                        <a href="{{ route('quran.bookmarks') }}" class="sidebar-nav-link {{ request()->routeIs('quran.bookmarks') ? 'active' : '' }}">
                            <i class="bi bi-bookmark-star-fill"></i>
                            <span>My Bookmarks</span>
                        </a>
                        <a href="{{ route('hadith.index') }}" class="sidebar-nav-link {{ request()->routeIs('hadith.*') ? 'active' : '' }}">
                            <i class="bi bi-book-half"></i>
                            <span>Daily Hadith</span>
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Page Body -->
            <main class="flex-grow-1 p-3 p-md-4">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4 shadow-sm" role="alert">
                        <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                        <div>{{ session('success') }}</div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4 shadow-sm" role="alert">
                        <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
                        <div>{{ session('error') }}</div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery 3.7.1 -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @stack('scripts')
</body>
</html>
