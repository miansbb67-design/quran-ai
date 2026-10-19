@extends('layouts.customer')

@section('title', 'Student Profile & Settings - Bayan AI Quran')
@section('page_title', 'Account Profile & Preferences')

@section('content')
<div class="container-fluid p-0">
    <div class="row g-4">
        <!-- Profile Information -->
        <div class="col-lg-6">
            <div class="card card-glass border-0 p-4 shadow-sm h-100">
                <div class="d-flex align-items-center gap-2 mb-3 border-bottom pb-2">
                    <i class="bi bi-person-fill text-emerald fs-4"></i>
                    <h5 class="fw-bold text-dark mb-0">Profile Information</h5>
                </div>
                <p class="text-muted small mb-3">Update your account's profile information and email address.</p>

                <form method="post" action="{{ route('profile.update') }}">
                    @csrf
                    @method('patch')

                    <div class="mb-3">
                        <label for="name" class="form-label small fw-semibold">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                        @if($errors->has('name'))
                            <div class="text-danger small mt-1">{{ $errors->first('name') }}</div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label small fw-semibold">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username">
                        @if($errors->has('email'))
                            <div class="text-danger small mt-1">{{ $errors->first('email') }}</div>
                        @endif
                    </div>

                    <div class="d-flex align-items-center gap-3 mt-4">
                        <button type="submit" class="btn btn-emerald rounded-pill px-4">
                            <i class="bi bi-check-lg me-1"></i> Save Changes
                        </button>
                        @if (session('status') === 'profile-updated')
                            <span class="text-success small fw-semibold"><i class="bi bi-check-circle me-1"></i> Saved!</span>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Update Password -->
        <div class="col-lg-6">
            <div class="card card-glass border-0 p-4 shadow-sm h-100">
                <div class="d-flex align-items-center gap-2 mb-3 border-bottom pb-2">
                    <i class="bi bi-shield-lock-fill text-gold fs-4"></i>
                    <h5 class="fw-bold text-dark mb-0">Security & Password</h5>
                </div>
                <p class="text-muted small mb-3">Ensure your account is using a long, random password to stay secure.</p>

                <form method="post" action="{{ route('password.update') }}">
                    @csrf
                    @method('put')

                    <div class="mb-3">
                        <label for="current_password" class="form-label small fw-semibold">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" autocomplete="current-password">
                        @if($errors->updatePassword->has('current_password'))
                            <div class="text-danger small mt-1">{{ $errors->updatePassword->first('current_password') }}</div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label small fw-semibold">New Password</label>
                        <input type="password" class="form-control" id="password" name="password" autocomplete="new-password">
                        @if($errors->updatePassword->has('password'))
                            <div class="text-danger small mt-1">{{ $errors->updatePassword->first('password') }}</div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label small fw-semibold">Confirm New Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
                    </div>

                    <div class="d-flex align-items-center gap-3 mt-4">
                        <button type="submit" class="btn btn-gold rounded-pill px-4 text-white">
                            <i class="bi bi-key-fill me-1"></i> Update Password
                        </button>
                        @if (session('status') === 'password-updated')
                            <span class="text-success small fw-semibold"><i class="bi bi-check-circle me-1"></i> Updated!</span>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
