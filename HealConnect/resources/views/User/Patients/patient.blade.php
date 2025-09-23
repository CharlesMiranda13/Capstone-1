@extends('layouts.patient_layout')

@section('title', 'Patient Dashboard')

@section('content')
    <div class="welcome-header">
        <h2>Hello, {{ Auth::user()->name ?? 'Patient' }}!</h2>
        <a href="{{ route('patient.settings') }}">
            <img src="{{ Auth::user()->profile_picture ?? asset('images/default-profile.png') }}"
                 alt=""
                 class="pic">
        </a>
    </div>

    <main class="patient-main">
        <div class="left-column">
            <div class="card">
                <h3><i class = "fa fa-calendar"></i>Upcoming Appointments</h3>
            </div>

            <div class="card">
                <h3><i class = "fa fa-folder-open "></i>Recent Medical Records</h3>
            </div>

        </div>

        <div class="right-column">
            <div class="notification">
                <h3><i class="fa fa-bell"></i> Notifications</h3>
            </div>
        </div>
    </main>
@endsection
