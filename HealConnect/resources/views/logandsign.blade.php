@extends('layouts.app')

@section('title', 'HealConnect - login & register')

@section('content')
<link rel="stylesheet" href="{{ asset('css/Logandreg.css') }}">

<main>
    @php
        $plan = request()->query('plan');
    @endphp

    @if($plan)
        <div class="selected-plan" style="text-align:center; margin: 15px auto; padding:12px; border:1px solid #ccc; border-radius:8px; width: fit-content; background:#f9f9f9;">
            <strong>You selected the {{ ucfirst($plan) }} Plan.</strong>
        </div>
    @endif

    <div class="tab-container">
        <div class="tab-header">
            <button class="tab-btn active" data-tab="login">Login</button>
            <button class="tab-btn" data-tab="register">Register</button>
        </div>

        <!-- Login Tab -->
        <div id="login" class="tab-content active">
            <div class="login-container">
                <h2>Login to HealConnect</h2>

                <form action="{{ route('login') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label>Password:</label>
                        <div class="password-wrapper">
                            <input type="password" name="password" id="login-password" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('login-password', this)" tabindex="-1">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path class="eye-open" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle class="eye-open" cx="12" cy="12" r="3"></circle>
                                    <line class="eye-closed" style="display:none;" x1="1" y1="1" x2="23" y2="23"></line>
                                </svg>
                            </button>
                        </div>
                    </div>


                    <div style="text-align:center; margin-top:3px; margin-bottom:10px;">
                        <a href="#" class="openForgotBtn" style="font-size:14px; text-decoration:none; color:#007bff;">
                            Forgot Password?
                        </a>
                    </div>

                    <button type="submit">Login</button>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            {{ $errors->first() }}
                        </div>
                    @endif
                </form>
            </div>
        </div>

        <!-- Register Tab -->
        <div id="register" class="tab-content">
            <h2 style="text-align:center; margin-bottom: 24px;">Choose Account Type</h2>
            <div class="role-selector-container">
                <!-- Independent PT -->
                <a href="{{ url('register/therapist') }}{{ $plan ? '?plan='.$plan : '' }}" class="role-card pt-card">
                    <div class="role-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="role-info">
                        <h3>Independent PT</h3>
                        <p>Solo physical therapist managing individual practice.</p>
                    </div>
                    <div class="role-action">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </a>

                <!-- Clinic PT -->
                <a href="{{ url('register/clinic') }}{{ $plan ? '?plan='.$plan : '' }}" class="role-card clinic-card">
                    <div class="role-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="role-info">
                        <h3>Clinic PT</h3>
                        <p>For organizations managing multiple therapists.</p>
                    </div>
                    <div class="role-action">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </a>

                <!-- Patient -->
                <a href="{{ url('register/patient') }}{{ $plan ? '?plan='.$plan : '' }}" class="role-card patient-card">
                    <div class="role-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <div class="role-info">
                        <h3>Patient</h3>
                        <p>Seeking physical therapy services and guidance.</p>
                    </div>
                    <div class="role-action">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </a>
            </div>
        </div>
    </div>
</main>

<!-- Forgot Password Modal -->
<div id="forgotModal" class="forgot-modal">
    <div class="forgot-modal-content">
        <span class="close">&times;</span>
        <h2>Reset Password</h2>

        {{-- If password reset email sent --}}
        @if (session('status'))
            <p class="success-msg">{{ session('status') }}</p>
        {{-- If password has been successfully reset --}}
        @elseif (session('password_reset_success'))
            <p class="success-msg">{{ session('password_reset_success') }}</p>
        {{-- Show the form if nothing is sent yet --}}
        @else
            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="form-group">
                    <label for="forgot-email">Enter your email:</label>
                    <input type="email" id="forgot-email" name="email" required placeholder="Email@gmail.com">
                </div>

                <button type="submit" class="modal-submit-btn">Send Reset Link</button>
            </form>
        @endif
    </div>
</div>

<script src="{{ asset('js/include.js') }}"></script>
<script src="{{ asset('js/modal.js') }}"></script>
@endsection
