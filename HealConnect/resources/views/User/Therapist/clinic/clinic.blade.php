@extends('layouts.clinic_layout')

@section('title', 'Clinic Dashboard')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/therapist.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('content')
<div class="welcome-header">
    <h2>Welcome, {{ $clinic->name ?? 'Clinic' }}!</h2>
    <a href="{{ route('therapist.settings') }}">
        <img src="{{ $clinic->profile_picture ? asset('storage/' . $clinic->profile_picture) 
            : asset('images/logo1.png') }}"
            alt="Profile Picture"
            class="pic">
    </a>
</div>

<main class="clinic-main">
    <div class="dashboard-cards">
        <div class="card">
            <i class="fa-solid fa-user-doctor" style="color:#007bff;"></i>
            <h3>{{ $totalTherapists }}</h3>
            <p>Therapists</p>
        </div>
        <div class="card">
            <i class="fa-solid fa-users" style="color:#28a745;"></i>
            <h3>{{ $totalEmployees }}</h3>
            <p>Employees</p>
        </div>
        <div class="card">
            <i class="fa-solid fa-users"></i>
            <h3>{{ $totalPatients }}</h3>
            <p>Patients</p>
        </div>
        <div class="card">
            <i class="fa-solid fa-calendar-check"></i>
            <h3>{{ $totalAppointments }}</h3>
            <p>Total Appointments</p>
        </div>
        <div class="card">
            <i class="fa-solid fa-hourglass-half" style="color:orange;"></i>
            <h3>{{ $pendingAppointments }}</h3>
            <p>Pending Appointments</p>
        </div>
    </div>

    <!-- Upcoming Appointments -->
    <div class="card appointments-card">
        <h3><i class="fa fa-calendar"></i> Upcoming Appointments</h3>
        <div class="appointments-list">
            @forelse ($appointments as $appointment)
                <div class="appointment-item">
                    <p><strong>Patient:</strong> {{ $appointment->patient->name }}</p>
                    <p><strong>Therapist:</strong> {{ $appointment->therapist->name ?? 'Unassigned' }}</p>
                    <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('F j, Y - g:i A') }}</p>
                    <p><strong>Type:</strong> {{ ucfirst($appointment->appointment_type) }}</p>
                </div>
            @empty
                <p class="empty-state">No upcoming appointments.</p>
            @endforelse
        </div>
    </div>

    <!-- Analytics Overview -->
    <div class="analytics-overview card">
        <h3>Analytics Overview</h3>
        <div class="analytics-section">
            <div class="analytics-card">
                <h4>Appointments by Therapist</h4>
                <canvas id="therapistChart"></canvas>
            </div>
            <div class="analytics-card">
                <h4>Appointments by Type</h4>
                <canvas id="typeChart"></canvas>
            </div>
        </div>
    </div>
</main>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    window.dashboardData = {
        therapistData: {
            labels: {!! json_encode($therapistNames ?? []) !!},
            values: {!! json_encode($therapistAppointmentsCount ?? []) !!}
        },
        appointmentTypeData: {
            labels: {!! json_encode($appointmentTypes->keys() ?? []) !!},
            values: {!! json_encode($appointmentTypes->values() ?? []) !!}
        }
    };
</script>
<script src="{{ asset('js/therapist_dashboard.js') }}"></script>
@endsection
