<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('Public/Css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('Public/Css/Admin.css') }}">
</head>
<body>
    <div class="sidebar">
        <h1>
            <span class="heal-blue">Heal</span><span class="connect-green">Connect</span>
        </h1>
        <hr>
        <a href="{{ url('/admin/manage-users') }}"><i class="fa-solid fa-users"></i> Manage Users</a>
        <a href="{{ url('/admin/view-reports') }}"><i class="fa-solid fa-chart-bar"></i> View Reports</a>
        <a href="{{ url('/admin/settings') }}"><i class="fa-solid fa-cog"></i> Settings</a>
        <a href="{{ url('/logout') }}"><i class="fa-solid fa-sign-out"></i> Logout</a>
    </div>

    <div class="admin-main">
        @yield('content')
    </div>
</body>
</html>
