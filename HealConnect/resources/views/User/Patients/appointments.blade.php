@extends('layouts.patient_layout')

@section('title', 'My Appointments')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/appointment.css') }}">
@endsection

@section('content')
<main class="appointments">
    <div class="container">
        <div class="header">
            <h2> My Appointments</h2>
        </div>

        {{-- Success Message --}}
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- Check if there are any appointments --}}
        @if ($appointments->isEmpty())
            <p class="no-appointments">You have no scheduled appointments yet.</p>
        @else
            <div class="appointments-list">
                @foreach ($appointments as $appointment)
                    <div class="appointment-card">
                        <div class="appointment-header">
                            <h3>{{ $appointment->therapist->name ?? 'Unknown Therapist' }}</h3>
                            <span class="status {{ strtolower($appointment->status) }}">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </div>

                        <div class="appointment-details">
                            <p><strong>Type:</strong> {{ ucfirst($appointment->appointment_type) }}</p>
                            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('F j, Y') }}</p>
                            <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</p>
                            @if ($appointment->notes)
                                <p><strong>Notes:</strong> {{ $appointment->notes }}</p>
                            @endif
                        </div>

                        {{-- Optional actions --}}
                        <div class="appointment-actions">
                            @if ($appointment->status === 'pending')
                                <button class="btn btn-cancel">Cancel</button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</main>
@endsection
