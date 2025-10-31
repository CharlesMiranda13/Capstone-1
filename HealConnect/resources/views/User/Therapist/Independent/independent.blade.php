@extends('layouts.therapist')

@section('title', 'PT Dashboard')
@section('styles')
<link rel="stylesheet" href="{{ asset('css/therapist.css') }}">
@endsection

@section('content')
    <div class="welcome-header">
        <h2>Hello, {{ Auth::user()->name ?? 'Therapist' }}!</h2>
        <a href="{{ route('therapist.settings') }}">
            <img src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) 
            : asset('images/default-profile.png') }}"
            alt="Profile Picture"
            class="pic">
        </a>
    </div>
    <main class="therapist-main">
        <div class="left-column">
            {{-- Upcoming Appointments --}}
            <div class="card appointments-card">
                <h3><i class="fa fa-calendar"></i> Upcoming Appointments</h3>
                <div class="appointments-list">
                    @forelse ($appointments as $appointment)
                        <div class="appointment-item">
                            <p class="patient-name">{{ $appointment->patient->name }}</p>
                            <p class="appointment-date">
                                {{ \Carbon\Carbon::parse($appointment->appointment_date . ' ' . $appointment->appointment_time)->format('F j, Y - g:i A') }}
                            </p>
                            <p class = "appointment-type">
                                Type: {{ ucfirst($appointment->appointment_type) }}
                            </p>
                        </div>
                    @empty
                    <p class="empty-state">No upcoming appointments.</p>
                    @endforelse
                </div>
            </div>

            {{-- Total Clients --}}
            <div class="card client-card">
                <h3><i class="fa fa-folder-open"></i> Total Clients</h3>
                <div class="client-count">
                    <h2>{{ $appointmentCount?? 0 }}</h2>
                    <p>Active Clients</p>
                </div>
            </div>
        </div>
</main>
@endsection
