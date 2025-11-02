<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Therapist Dashboard')</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('Css/therapist.css') }}">
    <link rel="stylesheet" href="{{ asset('Css/style.css') }}">
    

    @yield('styles')
</head>
<body>
    <div class="page-logo">
        <img src="{{ asset('images/logo1.png') }}" alt="Logo">
    </div>
    <div class="sidebar">
        <div class="logo">
            <div class="logo-circle">
                <img src="{{ asset('images/logo.jpg') }}" alt="HealConnect Logo">
            </div>
            <div class="logo-text">
                <span class="heal-blue">Heal</span><span class="connect-green">Connect</span>
            </div>
        </div>
        <hr>
        <a href="{{ route('therapist.home') }}"class="{{ request()->routeIs('therapist.home') ? 'active' : '' }}">
            <i class="fa-solid fa-house"></i> Home
        </a>

        <a href="{{ route('therapist.appointments') }}" class="{{ request()->routeIs('therapist.appointments') ? 'active' : '' }}">
            <i class="fa-regular fa-calendar"></i> Appointment
        </a>

        <a href="{{ route('therapist.records') }}" class="{{ request()->routeIs('therapist.records') ? 'active' : '' }}">
            <i class="fa-regular fa-file-lines"></i> Records
        </a>

        <a href="{{ route('messages') }}" class="{{ request()->routeIs('messages') ? 'active' : '' }}">
            <i class="fa-regular fa-message"></i> Messages
        </a>

        <a href="{{ route('therapist.client') }}" class="{{ request()->routeIs('therapist.client') ? 'active' : '' }}">
            <i class="fa-solid fa-users"></i> Clients
        </a>

        <a href="{{ route('therapist.availability') }}" class="{{ request()->routeIs('therapist.availability') ? 'active' : '' }}">
            <i class="fa-solid fa-clock"></i> Services & Schedule
        </a>

        <a href="{{ route('therapist.profile') }}" class="{{ request()->routeIs('therapist.profile') ? 'active' : '' }}">
            <i class="fa-solid fa-user-md"></i>  Profile
        </a>        
        <form action="{{ route('therapist.logout') }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </button>
        </form>
    </div>

    {{-- Main Content --}}
    <div class="main-content">
        @yield('content')
    </div>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    @yield('scripts')
</body>
</html>
