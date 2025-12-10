<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="pusher-key" content="{{ config('broadcasting.connections.pusher.key') }}">
    <meta name="pusher-cluster" content="{{ config('broadcasting.connections.pusher.options.cluster') }}">
    <meta name="user-role" content="admin">
    <meta name="unread-counts-url" content="{{ route('admin.unread-counts') }}">

    <title>@yield('title', 'Admin Dashboard')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('Css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('Css/Admin.css') }}">
    @yield('styles')
</head>
<body>
    {{-- Hamburger Button --}}
    <button class="hamburger-btn">
        <span></span>
        <span></span>
        <span></span>
    </button>

    {{-- Overlay --}}
    <div class="sidebar-overlay"></div>

    <div class="sidebar">
        <div class="logo">
            <a href="{{ route('admin.dashboard') }}">
                <div class="logo-circle">
                    <img src="{{ asset('images/logo.jpg') }}" alt="HealConnect Logo">
                </div>
                <div class="logo-text">
                    <span class="heal-blue">Heal</span><span class="connect-green">Connect</span>
                </div>
            </a>
        </div>
        <hr>
        <a href="{{ route('admin.dashboard') }}" class ="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' :'' }}" >
            <i class="fa-solid fa-gauge"></i> Dashboard</a>
        <a href="{{ route('admin.manage-users') }}" class = "sidebar-item {{ request()->routeIs('admin.manage-users') ? 'active' : ''}}">
            <i class="fa-solid fa-users"></i> Manage Users <span class="notification-badge" id="new-users-badge">0</span></a>
        <a href="{{ route('admin.viewreports') }}"class = "sidebar-item {{ request()->routeIs('admin.viewreports') ? 'active' : ''}}">
            <i class="fa-solid fa-chart-bar"></i> System Reports</a>
        <a href="{{ route('admin.contact_messages') }}"class = "sidebar-item {{ request()->routeIs('admin.contact_messages') ? 'active' : ''}}">
            <i class="fa-solid fa-area-chart"></i> User Concern <span class="notification-badge" id="new-concerns-badge">0</span></a>
        <a href="{{ route('admin.setting') }}" class = "sidebar-item {{ request()->routeIs('admin.setting') ? 'active' : ''}}">
            <i class="fa-solid fa-cog"></i> Settings</a>

        <form action="{{ route('admin.logout') }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </button>
        </form>
    </div>

    <div class="admin-main">
        @yield('content')
    </div>
    <script src="{{ asset('js/modal.js') }}"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="{{ asset('js/notifications.js') }}"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
    @yield('scripts')
</body>
</html>