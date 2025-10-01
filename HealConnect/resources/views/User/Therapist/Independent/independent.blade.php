@extends('layouts.therapist')

@section('title', 'PT Dashboard')
@section('styles')
<link rel="stylesheet" href="{{ asset('css/patients.css') }}">
@endsection

@section('content')
    <div class="welcome-header">
        <h2>Hello, {{ Auth::user()->name ?? 'Therapist' }}!</h2>
        <a href="{{ route('therapist.settings') }}">
            <img src="{{ Auth::user()->profile_picture ?? asset('images/default-profile.png') }}"
                 alt=""
                 class="pic">
        </a>
    </div>

    <main class="therapist-main">
        <div class="left-column">
            <div class="card">
                <h3><i class = "fa fa-calendar"></i>Upcoming Appointments</h3>
            </div>

            <div class="card">
                <h3><i class = "fa fa-folder-open "></i>Total Client</h3>
            </div>

        </div>

        <div class="right-column">
            <div class="notification">
                <h3><i class="fa fa-bell"></i> Notifications</h3>
            </div>
        </div>
</main>
@endsection
