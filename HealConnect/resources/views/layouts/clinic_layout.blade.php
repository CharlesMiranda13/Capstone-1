<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Clinic Dashboard')</title>

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
        <a href="{{ route('clinic.home') }}" class="{{request()->routeIs('clinic.home') ? 'active' : '' }}">
            <i class="fa-solid fa-house"></i> Home
        </a>
        <a href="{{ route('clinic.employees') }}" class="{{request()->routeIs('clinic.employees') ? 'active' : '' }}">
            <i class="fa-solid fa-user-md"></i> Employees
        </a>        
        <a href="{{ route('therapist.client') }}" class="{{ request()->routeIs('therapist.client') ? 'active' : '' }}">
            <i class="fa-solid fa-users"></i> Clients
        </a>
        <a href="{{ route('clinic.appointments') }}" class="{{request()->routeIs('clinic.appointments') ? 'active' : '' }}">
            <i class="fa-regular fa-calendar"></i> Appointment
        </a>
        <a href="{{ route('messages') }}" class="{{ request()->routeIs('messages') ? 'active' : '' }}">
            <i class="fa-regular fa-message"></i> Messages
        </a>
        <a href="{{ route('clinic.services') }}" class="{{request()->routeIs('clinic.services') ? 'active' : '' }}">
            <i class="fa-regular fa-dumbbell"></i> Services
        </a>
        <a href="{{ route('clinic.records') }}" class="{{request()->routeIs('clinic.records') ? 'active' : '' }}">
            <i class="fa-regular fa-file-lines"></i> Records
        </a>
        <a href="{{ route('clinic.settings') }}" class="{{request()->routeIs('clinic.settings') ? 'active' : '' }}">
            <i class="fa-solid fa-user-md"></i> Profile
        </a>
        
        <form action="{{ route('clinic.logout') }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </button>
        </form>
    </div>

    <div class ="main-content">
        @yield('content')
    </div>
</body>
</html>
