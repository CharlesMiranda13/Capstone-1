@extends('layouts.app')

@section('title', 'HealConnect - login & register')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/Logandreg.css') }}">

    <main>
        @php
            $plan = request()->query('plan');
        @endphp

        {{-- Show selected plan notice if available --}}
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

            <!-- Login tab -->
            <div id="login" class="tab-content active">
                <div class="login-container">
                    <h2>Login to HealConnect</h2>
                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        <button type="submit">Login</button>
                    </form>
                </div>
            </div>

            <!-- Register tab -->
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
    <script src="{{ asset('js/include.js') }}"></script>
@endsection
