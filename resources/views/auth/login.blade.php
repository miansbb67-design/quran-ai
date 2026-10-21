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
                <input id="email" class="form-control border-start-0" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="name@example.com">
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
                <input id="password" class="form-control border-start-0 border-end-0" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
                <button class="btn btn-light border border-start-0 text-muted" type="button" id="togglePasswordBtn" title="Show or hide password">
                    <i class="bi bi-eye" id="togglePasswordIcon"></i>
                </button>
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

    <script>

        document.getElementById('togglePasswordBtn').addEventListener('click', function() {
            var passInput = document.getElementById('password');
            var passIcon = document.getElementById('togglePasswordIcon');
            if (passInput.type === 'password') {
                passInput.type = 'text';
                passIcon.classList.remove('bi-eye');
                passIcon.classList.add('bi-eye-slash');
            } else {
                passInput.type = 'password';
                passIcon.classList.remove('bi-eye-slash');
                passIcon.classList.add('bi-eye');
            }
        });
    </script>
</x-guest-layout>
