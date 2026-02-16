@extends('layouts.bootstrap')

@section('title', 'Login')

@section('content')
<div class="row justify-content-center align-items-center min-vh-75">
    <div class="col-md-5 col-lg-4">
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-gradient rounded-circle mb-3" style="width: 80px; height: 80px;">
                <i class="bi bi-person-fill text-white" style="font-size: 2.5rem;"></i>
            </div>
            <h2 class="fw-bold text-dark">Welcome Back!</h2>
            <p class="text-muted">Sign in to continue to Academia</p>
        </div>

        <div class="card shadow-lg border-0">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email Address -->
                    <div class="mb-4">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope me-1"></i>{{ __('Email Address') }}
                        </label>
                        <input id="email" type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="Enter your email">
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label for="password" class="form-label">
                            <i class="bi bi-lock me-1"></i>{{ __('Password') }}
                        </label>
                        <input id="password" type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Enter your password">
                        @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="mb-4 d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="remember_me" name="remember">
                            <label class="form-check-label" for="remember_me">{{ __('Remember Me') }}</label>
                        </div>
                        @if (Route::has('password.request'))
                        <a class="text-decoration-none small fw-medium" href="{{ route('password.request') }}">
                            {{ __('Forgot Password?') }}
                        </a>
                        @endif
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-box-arrow-in-right me-2"></i>{{ __('Sign In') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="text-center mt-4">
            <p class="text-muted mb-0">Don't have an account?
                <a href="{{ route('register') }}" class="fw-semibold text-decoration-none">Create Account</a>
            </p>
        </div>
    </div>
</div>
@endsection