@extends('layouts.app')

@section('title', 'HealConnect - login & register')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/Logandreg.css') }}">

    <main>
        <div class="tab-container">
            <div class="tab-header">
                <button class="tab-btn active" data-tab="login">Login</button>
                <button class="tab-btn" data-tab="register">Register</button>
            </div>

            <!-- Login tab -->
            <div id="login" class="tab-content active">
                <div class="login-container">
                    <h2>Login to HealConnect</h2>
                    <form action="#" method="post">
                        <div class="form-group">
                            <label for="username">Email:</label>
                            <input type="text" id="username" name="username" required>
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
                        <a href="{{ url('register/therapist') }}">Independent PT</a>
                    </div>
                    <div class="User2">
                        <a href="{{ url('register/clinic') }}">Clinic PT</a>
                    </div>
                    <div class="User3">
                        <a href="{{ url('register/patient') }}">Patient</a>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="{{ asset('js/tabswitching.js') }}"></script>
@endsection
