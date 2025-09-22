<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Patient Dashboard')</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('Css/patients.css') }}">
    <link rel="stylesheet" href="{{ asset('Css/style.css') }}">
    

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
        <a href="{{ route('patient.home') }}"><i class="fa-solid fa-house"></i> Home</a>
        <a href="{{ route('patient.appointments') }}"><i class="fa-regular fa-calendar"></i> Appointment</a>
        <a href="{{ route('patient.records') }}"><i class="fa-regular fa-file-lines"></i> Records</a>
        <a href="{{ route('patient.messages') }}"><i class="fa-regular fa-message"></i> Messages</a>
        <a href="{{ route('patient.home') }}"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>

    {{-- Main Content --}}
    <div class="main-content">
        @yield('content')
    </div>
</body>
</html>
