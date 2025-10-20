@extends('layouts.patient_layout')

@section('title', 'My Appointments')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/patient_appointment.css') }}">
<link rel="stylesheet" href="{{ asset('css/appointment.css') }}">

@endsection

@section('content')
<main class="appointments-page">
    <section class="appointments-container">
        <h2>My Appointments</h2>

        {{-- Success Message --}}
        @if (session('success'))
            <div class="alert success">
                <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        {{-- Check if there are any appointments --}}
        @if ($appointments->isEmpty())
            <div class="empty-state">
                <i class="fa-regular fa-calendar-xmark"></i>
                <p>You don't have any scheduled appointments yet.</p>
            </div>
        @else
            <div class="appointments-list">
                @foreach ($appointments as $appointment)
                    <div class="appointment-card">
                        <div class="appointment-card-header">
                            <div class="therapist-info">
                                <h3>{{ $appointment->therapist->name ?? 'Unknown Therapist' }}</h3>
                                <span class="status {{ strtolower($appointment->status) }}">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </div>
                        </div>

                        <div class="appointment-info">
                            <p><i class="fa-solid fa-stethoscope"></i> 
                                <strong>Type:</strong> {{ ucfirst($appointment->appointment_type) }}
                            </p>
                            <p><i class="fa-regular fa-calendar"></i> 
                                <strong>Date:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('F j, Y') }}
                            </p>
                            <p><i class="fa-regular fa-clock"></i> 
                                <strong>Time:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}
                            </p>

                            @if ($appointment->notes)
                                <p><i class="fa-regular fa-note-sticky"></i> 
                                    <strong>Notes:</strong> {{ $appointment->notes }}
                                </p>
                            @endif
                        </div>

                        <div class="appointment-actions">
                            @if ($appointment->status === 'pending')
                                <button class="btn btn-cancel">
                                    <i class="fa-solid fa-ban"></i> Cancel Appointment
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</main>
@endsection
