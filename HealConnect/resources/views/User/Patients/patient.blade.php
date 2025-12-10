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
            : asset('images/logo1.png') }}"
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
                        <p class ="therapist-name"><strong>Dr. {{$appointment->provider->name}}</strong></p>
                        <p class ="appointment-date">
                            {{ \Carbon\Carbon::parse($appointment->appointment_date . ' ' . $appointment->appointment_time)->format('F j, Y - g:i A') }}
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
                <h3><i class="fa fa-folder-open"></i>Recent Medical Records</h3>
                <div class="medical-records-list scrollable">
                    @forelse ($recentRecords as $record)
                        <div class="record-item">
                            <p class="record-update">
                                <strong>Dr. {{ $record->therapist->name }}</strong> updated your 
                                <span class="record-type">{{ $record->record_type }}</span>
                            </p>
                            <p class="record-date">
                                {{ $record->created_at->format('F j, Y - g:i A') }}
                            </p>
                        </div>
                    @empty
                        <p class="empty-state">No medical records yet.</p>
                    @endforelse
                </div>
                <a href="{{ route('patient.records') }}" class="view-all-link">View All Records →</a>
            </div>
        </div>

        <div class="right-column">
            <div class="notification">
                <h3><i class="fa fa-sticky-note"></i> Note</h3>
                <div class="notes-content">
                    @if (!empty($latestProgressNote))
                        <div class="progress-note-card">
                            <p class="note-from">
                                <strong>From: Dr. {{ $latestProgressRecord->therapist->name ?? 'Your Therapist' }}</strong>
                            </p>
                            <p class="note-date-header">
                                {{ $latestProgressNote['date'] }}
                            </p>
                            <p class="note-text">
                                {{ $latestProgressNote['note'] }}
                            </p>
                        </div>
                    @else
                        <p class="empty-state">No progress notes yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </main>
@endsection
