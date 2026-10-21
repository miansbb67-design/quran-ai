<x-guest-layout>
    <div class="mb-4 text-center">
        <h4 class="fw-bold text-emerald mb-1">Create Student Account</h4>
        <p class="text-muted small">Begin reciting and memorizing the Holy Quran today</p>
    </div>

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

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label small fw-semibold text-secondary">Full Name</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                <input id="name" class="form-control border-start-0" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Your name">
            </div>
        </div>

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label small fw-semibold text-secondary">Email Address</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                <input id="email" class="form-control border-start-0" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="name@example.com">
            </div>
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label small fw-semibold text-secondary">Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                <input id="password" class="form-control border-start-0 border-end-0" type="password" name="password" required autocomplete="new-password" placeholder="At least 8 characters">
                <button class="btn btn-light border border-start-0 text-muted" type="button" id="togglePassBtn" title="Toggle password visibility">
                    <i class="bi bi-eye" id="togglePassIcon"></i>
                </button>
            </div>
        </div>

        <!-- Confirm Password -->
        <div class="mb-4">
            <label for="password_confirmation" class="form-label small fw-semibold text-secondary">Confirm Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-shield-check text-muted"></i></span>
                <input id="password_confirmation" class="form-control border-start-0 border-end-0" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Re-type password">
                <button class="btn btn-light border border-start-0 text-muted" type="button" id="toggleConfirmPassBtn" title="Toggle password visibility">
                    <i class="bi bi-eye" id="toggleConfirmPassIcon"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn btn-emerald w-100 rounded-pill py-2 fw-semibold shadow-sm mb-3">
            <i class="bi bi-person-plus me-1"></i> Create Free Account
        </button>

        <div class="text-center small text-muted">
            Already registered? 
            <a href="{{ route('login') }}" class="text-emerald fw-semibold text-decoration-none">Sign in here</a>
        </div>
    </form>

    <script>
        function setupToggle(btnId, inputId, iconId) {
            var btn = document.getElementById(btnId);
            if (btn) {
                btn.addEventListener('click', function() {
                    var input = document.getElementById(inputId);
                    var icon = document.getElementById(iconId);
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('bi-eye');
                        icon.classList.add('bi-eye-slash');
                    } else {
                        input.type = 'password';
                        icon.classList.remove('bi-eye-slash');
                        icon.classList.add('bi-eye');
                    }
                });
            }
        }
        setupToggle('togglePassBtn', 'password', 'togglePassIcon');
        setupToggle('toggleConfirmPassBtn', 'password_confirmation', 'toggleConfirmPassIcon');
    </script>
</x-guest-layout>
