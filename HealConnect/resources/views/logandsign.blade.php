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
                        <input type="password" name="password" required>
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
            <h2 style="text-align:center;">Register as</h2>
            <div class="Users">
                <div class="User1">
                    <a href="{{ url('register/therapist') }}{{ $plan ? '?plan='.$plan : '' }}">Independent PT</a>
                </div>
                <div class="User2">
                    <a href="{{ url('register/clinic') }}{{ $plan ? '?plan='.$plan : '' }}">Clinic PT</a>
                </div>
                <div class="User3">
                    <a href="{{ url('register/patient') }}{{ $plan ? '?plan='.$plan : '' }}">Patient</a>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Forgot Password Modal -->
<div id="forgotModal" class="forgot-modal">
    <div class="forgot-modal-content">
        <span class="close">&times;</span>
        <h2>Reset Password</h2>

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="form-group">
                <label for="forgot-email">Enter your email:</label>
                <input type="email" id="forgot-email" name="email" required placeholder="Email@gmail.com" >
            </div>

            <p>Please check your email for the password reset link after submitting.</p>

            <button type="submit" class="modal-submit-btn">Send Reset Link</button>

            @if (session('status'))
                <p class="success-msg">{{ session('status') }}</p>
            @endif
        </form>
    </div>
</div>

<script src="{{ asset('js/include.js') }}"></script>
<script src="{{ asset('js/modal.js') }}"></script>
@endsection
