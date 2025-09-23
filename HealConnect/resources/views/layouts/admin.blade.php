<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('Css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('Css/Admin.css') }}">
    @yield('styles')
</head>
<body>
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
        <a href="{{ route('admin.dashboard') }}" class ="{{ request()->routeIs('admin.dashboard') ? 'active' :'' }}" >
            <i class="fa-solid fa-gauge"></i> Dashboard</a>
        <a href="{{ route('admin.manage-users') }}" class = "{{ request()->routeIs('admin.manage-users') ? 'active' : ''}}">
            <i class="fa-solid fa-users"></i> Manage Users</a>
        <a href="{{ route('admin.viewreports') }}"class = "{{ request()->routeIs('admin.viewreports') ? 'active' : ''}}">
            <i class="fa-solid fa-chart-bar"></i> View Reports</a>
        <a href="{{ route('admin.setting') }}" class = "{{ request()->routeIs('admin.setting') ? 'active' : ''}}">
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
</body>
</html>
