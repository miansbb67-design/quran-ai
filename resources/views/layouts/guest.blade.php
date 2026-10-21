<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Bayan AI Quran') }} - Account Portal</title>

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom Design System -->
    <link rel="stylesheet" href="{{ asset('css/quran-app.css') }}">
</head>
<body class="min-vh-100 d-flex flex-column justify-content-center py-5" style="background: radial-gradient(circle at 50% 0%, rgba(16, 185, 129, 0.08) 0%, #f8fafc 75%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5 col-xl-4">
                <!-- Branding Header -->
                <div class="text-center mb-4">
                    <a href="{{ route('home') }}" class="d-inline-flex align-items-center gap-2 text-decoration-none">
                        <div class="d-inline-flex align-items-center justify-content-center bg-emerald-grad text-white rounded-3 p-2 shadow-sm" style="width: 48px; height: 48px;">
                            <i class="bi bi-book-half fs-3 text-gold"></i>
                        </div>
                        <div class="text-start">
                            <span class="fw-bold fs-4 text-emerald d-block lh-1">BAYAN</span>
                            <small class="text-muted text-uppercase tracking-wider" style="font-size: 0.65rem; letter-spacing: 1.5px;">AI Quran Teacher</small>
                        </div>
                    </a>
                </div>

                <!-- Auth Card -->
                <div class="card card-glass border-0 shadow-sm p-4 p-md-5 bg-white" style="border-top: 4px solid #10b981 !important; overflow: hidden; border-radius: 1.25rem !important; box-shadow: 0 10px 25px -5px rgba(6, 78, 59, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.02) !important;">
                    {{ $slot }}
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('home') }}" class="small text-muted text-decoration-none">
                        <i class="bi bi-arrow-left me-1"></i> Return to Homepage
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
