@extends('layouts.patient_layout')

@section('title', 'Dashboard')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/patients.css') }}">
@endsection

@section('content')
    <div class="welcome-header">
        <h2>Hello, {{ Auth::user()->name ?? 'Patient' }}!</h2>
        <a href="{{ route('patient.settings') }}">
            <img src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) 
            : asset('images/default-profile.png') }}"
            alt="Profile Picture"
            class="pic">
        </a>
    </div>

    <main class="patient-main">
        <div class="left-column">
            <div class="card appointments-card">
                <h3><i class = "fa fa-calendar"></i>Upcoming Appointments</h3>
                <div class="appointments-list">
                    @forelse ($appointments as $appointment)
                    <div class ="appointment-item">
                        <p class ="therapist-name">{{$appointment->provider->name}}</p>
                        <p class ="appointment-date">
                            {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('F j, Y - g:i A') }}
                        </p>
                        <p class = "appointment-type">
                            Type: {{ ucfirst($appointment->appointment_type) }}
                        </p>
                    </div>
                @empty
                <p class="empty-state"> No appointments scheduled.</p>
                @endforelse
                </div>
            </div>

            <div class="medical-card">
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
