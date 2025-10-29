<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Patient Dashboard')</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('Css/patients.css') }}">
    <link rel="stylesheet" href="{{ asset('Css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('Css/settings.css') }}">

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
        <a href="{{ route('patient.home') }}"class="{{ request()->routeIs('patient.home') ? 'active' : '' }}">
            <i class="fa-solid fa-house"></i> Home
        </a>

        <a href="{{ route('patient.appointments.index') }}" class="{{ request()->routeIs('patient.appointments.index') ? 'active' : '' }}">
            <i class="fa-regular fa-calendar"></i> Appointment
        </a>

        <a href="{{ route('patient.records') }}" class="{{ request()->routeIs('patients.records') ? 'active' : '' }}">
            <i class="fa-regular fa-file-lines"></i> Records
        </a>

        <a href="{{ route('messages') }}" class="{{ request()->routeIs('messages') ? 'active' : '' }}">
            <i class="fa-regular fa-message"></i> Messages
        </a>

        <a href="{{ route('patient.therapists') }}" class="{{ request()->routeIs('patient.therapists') ? 'active' : '' }}">
            <i class="fa-solid fa-user-md"></i> Therapists
        </a>
        
        <form action="{{ route('patient.logout') }}" method="POST" style="display:inline;">
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
