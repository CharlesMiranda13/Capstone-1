<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="pusher-key" content="{{ config('broadcasting.connections.pusher.key') }}">
    <meta name="pusher-cluster" content="{{ config('broadcasting.connections.pusher.options.cluster') }}">
    <meta name="user-id" content="{{ auth()->id() }}">
    <meta name="unread-counts-url" content="{{ route('clinic.unread.counts') }}">

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
            <a href="{{ route('clinic.home') }}">
                <div class="logo-circle">
                    <img src="{{ asset('images/logo.jpg') }}" alt="HealConnect Logo">
                </div>
                <div class="logo-text">
                    <span class="heal-blue">Heal</span><span class="connect-green">Connect</span>
                </div>
            </a>
        </div>
        <hr>
        <a href="{{ route('clinic.home') }}" class="sidebar-item {{request()->routeIs('clinic.home') ? 'active' : '' }}">
            <i class="fa-solid fa-house"></i> Home
        </a>
        <a href="{{ route('clinic.employees') }}" class="sidebar-item {{request()->routeIs('clinic.employees') ? 'active' : '' }}">
            <i class="fa-solid fa-user-md"></i> Employees
        </a>        
        <a href="{{ route('therapist.client') }}" class="sidebar-item {{ request()->routeIs('therapist.client') ? 'active' : '' }}">
            <i class="fa-solid fa-users"></i> Clients
        </a>
        <a href="{{ route('clinic.appointments') }}" class="sidebar-item {{request()->routeIs('clinic.appointments') ? 'active' : '' }}">
            <i class="fa-regular fa-calendar"></i> Appointment
            <span class="notification-badge" id="appointments-badge">0</span>
        </a>
        <a href="{{ route('messages') }}" class="sidebar-item {{ request()->routeIs('messages') ? 'active' : '' }}">
            <i class="fa-regular fa-message"></i> Messages
            <span class="notification-badge" id="messages-badge">0</span>
        </a>
        <a href="{{ route('clinic.services') }}" class="sidebar-item {{request()->routeIs('clinic.services') ? 'active' : '' }}">
            <i class="fa-solid fa-clock"></i> Services & Schedule
        </a>
        <a href="{{ route('clinic.profile') }}" class="sidebar-item {{request()->routeIs('clinic.profile') ? 'active' : '' }}">
            <i class="fa-solid fa-user-md"></i> Profile
        </a>
        
        <form action="{{ route('logout') }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </button>
        </form>
    </div>

    <div class ="main-content">
        @yield('content')
    </div>
    
    {{-- Scripts --}}
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="{{ asset('js/include.js') }}"></script>
    <script src="{{ asset('js/modal.js') }}"></script>
    <script src="{{ asset('js/notifications.js') }}"></script>
    @yield('scripts')
</body>
</html>