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
        <a href="{{ route('admin.manage-users') }}"><i class="fa-solid fa-users"></i> Manage Users</a>
        <a href="{{ url('/admin/view-reports') }}"><i class="fa-solid fa-chart-bar"></i> View Reports</a>
        <a href="{{ url('/admin/settings') }}"><i class="fa-solid fa-cog"></i> Settings</a>
        <a href="{{ url('/logout') }}"><i class="fa-solid fa-sign-out"></i> Logout</a>
    </div>

    <div class="admin-main">
        @yield('content')
    </div>
</body>
</html>
