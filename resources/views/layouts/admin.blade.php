<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Console - Bayan AI Quran')</title>

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
        <!-- Luxury Islamic Admin Sidebar -->
        <aside class="admin-sidebar d-none d-lg-flex flex-column p-3 text-white">
            <!-- Brand & Emblem -->
            <div class="d-flex align-items-center gap-2 mb-3 px-2 pt-1 pb-3 border-bottom border-light border-opacity-10">
                <div class="d-inline-flex align-items-center justify-content-center bg-emerald-grad text-white rounded-3 shadow-sm" style="width: 42px; height: 42px; border: 1px solid rgba(251, 191, 36, 0.4);">
                    <i class="bi bi-shield-lock-fill fs-5 text-gold"></i>
                </div>
                <div class="overflow-hidden">
                    <span class="fw-bold fs-5 text-white d-block lh-1 font-sans">BAYAN ADMIN</span>
                    <small class="text-gold text-uppercase fw-semibold" style="font-size: 0.64rem; letter-spacing: 1.4px;">
                        Control Center
                    </small>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="nav flex-column flex-grow-1">
                <div class="admin-nav-heading">Main Navigation</div>

                <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Overview & Health</span>
                </a>

                <a href="{{ route('admin.analytics.index') }}" class="admin-nav-link {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">
                    <i class="bi bi-graph-up-arrow text-info"></i>
                    <span>Google & Web Analytics</span>
                    <span class="badge bg-emerald text-white ms-auto" style="font-size: 0.65rem;">NEW</span>
                </a>

                <div class="admin-nav-heading">Quran & Content Studio</div>

                <a href="{{ route('admin.hadiths.index') }}" class="admin-nav-link {{ request()->routeIs('admin.hadiths.*') ? 'active' : '' }}">
                    <i class="bi bi-quote text-warning"></i>
                    <span>Daily Hadith Manager</span>
                </a>

                <a href="{{ route('admin.content.index') }}" class="admin-nav-link {{ request()->routeIs('admin.content.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-richtext"></i>
                    <span>Content & Announcements</span>
                </a>

                <div class="admin-nav-heading">System & Users</div>

                <a href="{{ route('admin.users.index') }}" class="admin-nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i>
                    <span>Users & Access Control</span>
                </a>

                <a href="{{ route('admin.settings.index') }}" class="admin-nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="bi bi-sliders2"></i>
                    <span>Settings & AI Voices</span>
                </a>

                <hr class="my-3 border-light border-opacity-10">

                <a href="{{ route('dashboard') }}" class="admin-nav-link text-white-50">
                    <i class="bi bi-arrow-left-circle-fill text-emerald"></i>
                    <span>Return to Student App</span>
                </a>
            </nav>

            <!-- Bottom User Pill -->
            <div class="mt-auto pt-3 border-top border-light border-opacity-10">
                <div class="d-flex align-items-center gap-2 p-2 rounded-3" style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.15);">
                    <div class="rounded-circle bg-emerald-grad text-white d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 36px; height: 36px; font-size: 0.85rem; border: 1px solid rgba(251, 191, 36, 0.3);">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="overflow-hidden">
                        <div class="fw-semibold text-truncate small text-white">{{ Auth::user()->name }}</div>
                        <div class="badge bg-warning text-dark text-uppercase px-2" style="font-size: 0.65rem;">{{ Auth::user()->role }}</div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="ms-auto mb-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-link text-white-50 p-1 hover-text-danger" title="Sign out">
                            <i class="bi bi-box-arrow-right fs-5"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-grow-1 d-flex flex-column min-vh-100" style="min-width: 0;">
            <!-- Top Header -->
            <header class="navbar navbar-expand admin-topbar px-3 px-lg-4 py-2 sticky-top shadow-xs">
                <!-- Mobile toggle -->
                <button class="btn btn-light d-lg-none me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminMobileSidebar">
                    <i class="bi bi-list fs-4 text-emerald"></i>
                </button>

                <!-- Page title & Live Badge -->
                <div class="d-flex align-items-center gap-2">
                    <span class="badge admin-badge-live rounded-pill d-inline-flex align-items-center gap-2">
                        <span class="pulse-indicator"></span> ADMIN CONSOLE
                    </span>
                    <h5 class="mb-0 fw-bold text-dark ms-1 d-none d-md-inline">@yield('page_title', 'Admin Dashboard')</h5>
                </div>

                <!-- Right Header Actions -->
                <div class="ms-auto d-flex align-items-center gap-2 gap-sm-3">
                    <a href="{{ route('admin.analytics.index') }}" class="btn btn-sm btn-light border rounded-pill px-3 d-none d-sm-inline-flex align-items-center gap-1 text-secondary">
                        <i class="bi bi-activity text-emerald"></i>
                        <span class="small fw-semibold">Live Traffic</span>
                    </a>

                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-emerald rounded-pill px-3 shadow-2xs">
                        <i class="bi bi-mortarboard me-1"></i> Student App
                    </a>

                    <div class="dropdown">
                        <button class="btn btn-light rounded-pill d-flex align-items-center gap-2 px-2 py-1 border shadow-2xs" data-bs-toggle="dropdown">
                            <div class="rounded-circle bg-emerald-grad text-white d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                            </div>
                            <span class="d-none d-sm-inline fw-semibold small text-dark me-1">{{ Auth::user()->name }}</span>
                            <i class="bi bi-chevron-down small text-muted"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 rounded-3">
                            <li class="px-3 py-2 border-bottom">
                                <div class="fw-bold small">{{ Auth::user()->name }}</div>
                                <div class="text-muted small">{{ Auth::user()->email }}</div>
                            </li>
                            <li><a class="dropdown-item py-2" href="{{ route('admin.analytics.index') }}"><i class="bi bi-graph-up-arrow me-2 text-info"></i> Google Analytics</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('admin.hadiths.index') }}"><i class="bi bi-quote me-2 text-warning"></i> Hadith Manager</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('admin.settings.index') }}"><i class="bi bi-sliders me-2 text-emerald"></i> Settings & Audio</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i> Profile Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="mb-0">
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
            <div class="offcanvas offcanvas-start admin-sidebar text-white" tabindex="-1" id="adminMobileSidebar">
                <div class="offcanvas-header border-bottom border-light border-opacity-10">
                    <div class="d-flex align-items-center gap-2">
                        <div class="d-inline-flex align-items-center justify-content-center bg-emerald-grad text-white rounded-3 shadow-sm" style="width: 38px; height: 38px;">
                            <i class="bi bi-shield-lock-fill fs-5 text-gold"></i>
                        </div>
                        <span class="fw-bold fs-5 text-white">BAYAN ADMIN</span>
                    </div>
                    <button type="button" class="btn-close btn-close-white text-reset" data-bs-dismiss="offcanvas"></button>
                </div>
                <div class="offcanvas-body d-flex flex-column p-3">
                    <nav class="nav flex-column gap-1">
                        <div class="admin-nav-heading">Main Navigation</div>
                        <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="bi bi-speedometer2"></i>
                            <span>Overview & Health</span>
                        </a>
                        <a href="{{ route('admin.analytics.index') }}" class="admin-nav-link {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">
                            <i class="bi bi-graph-up-arrow text-info"></i>
                            <span>Google & Web Analytics</span>
                        </a>

                        <div class="admin-nav-heading">Quran & Content Studio</div>
                        <a href="{{ route('admin.hadiths.index') }}" class="admin-nav-link {{ request()->routeIs('admin.hadiths.*') ? 'active' : '' }}">
                            <i class="bi bi-quote text-warning"></i>
                            <span>Daily Hadith Manager</span>
                        </a>
                        <a href="{{ route('admin.content.index') }}" class="admin-nav-link {{ request()->routeIs('admin.content.*') ? 'active' : '' }}">
                            <i class="bi bi-file-earmark-richtext"></i>
                            <span>Content & Announcements</span>
                        </a>

                        <div class="admin-nav-heading">System & Users</div>
                        <a href="{{ route('admin.users.index') }}" class="admin-nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i class="bi bi-people-fill"></i>
                            <span>Users & Access Control</span>
                        </a>
                        <a href="{{ route('admin.settings.index') }}" class="admin-nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                            <i class="bi bi-sliders2"></i>
                            <span>Settings & AI Voices</span>
                        </a>
                        <hr class="border-light border-opacity-10 my-3">
                        <a href="{{ route('dashboard') }}" class="admin-nav-link text-white-50">
                            <i class="bi bi-arrow-left-circle-fill text-emerald"></i>
                            <span>Return to Student App</span>
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Page Body -->
            <main class="flex-grow-1 p-3 p-md-4">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4 shadow-sm rounded-3 border-emerald" role="alert">
                        <i class="bi bi-check-circle-fill fs-5 text-emerald"></i>
                        <div>{{ session('success') }}</div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4 shadow-sm rounded-3" role="alert">
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
