<x-guest-layout>
    <div class="mb-4 text-center">
        <h4 class="fw-bold text-emerald mb-1">Student Sign In</h4>
        <p class="text-muted small">Continue your Quran recitation and memorization</p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success small py-2 mb-3 rounded-3">
            {{ session('status') }}
        </div>
    @endif

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="alert alert-danger small py-2 mb-3 rounded-3">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label small fw-semibold text-secondary">Email Address</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                <input id="email" class="form-control border-start-0" type="email" name="email" value="{{ old('email', 'student@quran-ai.com') }}" required autofocus autocomplete="username">
            </div>
        </div>

        <!-- Password -->
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="password" class="form-label small fw-semibold text-secondary mb-0">Password</label>
                @if (Route::has('password.request'))
                    <a class="small text-emerald text-decoration-none" href="{{ route('password.request') }}">
                        Forgot password?
                    </a>
                @endif
            </div>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                <input id="password" class="form-control border-start-0" type="password" name="password" value="Password123!" required autocomplete="current-password">
            </div>
        </div>

        <!-- Remember Me -->
        <div class="form-check mb-4">
            <input id="remember_me" type="checkbox" class="form-check-input" name="remember" checked>
            <label for="remember_me" class="form-check-label small text-secondary">
                Remember me on this browser
            </label>
        </div>

        <button type="submit" class="btn btn-emerald w-100 rounded-pill py-2 fw-semibold shadow-sm mb-3">
            <i class="bi bi-box-arrow-in-right me-1"></i> Sign In to Learning Portal
        </button>

        <div class="text-center small text-muted">
            Don't have an account? 
            <a href="{{ route('register') }}" class="text-emerald fw-semibold text-decoration-none">Create free account</a>
        </div>
    </form>

    <!-- Demo Accounts Quick Fill Pill -->
    <div class="mt-4 pt-3 border-top text-center">
        <small class="text-muted d-block mb-2">Seed Demo Accounts (Click to fill):</small>
        <div class="d-flex justify-content-center gap-1 flex-wrap">
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-0 px-2" style="font-size: 0.72rem;" onclick="fillDemo('student@quran-ai.com')">Student</button>
            <button type="button" class="btn btn-sm btn-outline-warning text-dark rounded-pill py-0 px-2" style="font-size: 0.72rem;" onclick="fillDemo('admin@quran-ai.com')">Admin</button>
            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill py-0 px-2" style="font-size: 0.72rem;" onclick="fillDemo('owner@quran-ai.com')">Owner</button>
        </div>
    </div>

    <script>
        function fillDemo(email) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = 'Password123!';
        }
    </script>
</x-guest-layout>
