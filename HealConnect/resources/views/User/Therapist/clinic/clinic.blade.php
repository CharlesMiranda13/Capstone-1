@extends('layouts.clinic_layout')

@section('title', 'Clinic Dashboard')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/therapist.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('content')

{{-- Subscription Expiry Warning --}}
@if(auth()->user()->subscription_status === 'active' && auth()->user()->subscription_started_at)
    @php
        $subscriptionEnd = \Carbon\Carbon::parse(auth()->user()->subscription_started_at)->addMonth();
        $daysRemaining = now()->diffInDays($subscriptionEnd, false);
    @endphp
    
    @if($daysRemaining <= 7 && $daysRemaining > 0)
        <div class="subscription-warning">
            <i class="fa fa-exclamation-triangle"></i>
            <div>
                <strong>Subscription Expiring Soon!</strong>
                Your {{ ucfirst(auth()->user()->plan) }} subscription will expire in {{ ceil($daysRemaining) }} day{{ ceil($daysRemaining) > 1 ? 's' : '' }} on {{ $subscriptionEnd->format('M d, Y') }}.
            </div>
            <a href="{{ route('pricing.index') }}">Renew Now</a>
        </div>
    @elseif($daysRemaining <= 0)
        <div class="subscription-expired">
            <i class="fa fa-exclamation-circle"></i>
            <div>
                <strong>Subscription Expired!</strong>
                Your subscription ended on {{ $subscriptionEnd->format('M d, Y') }}. Renew to continue accessing all features.
            </div>
            <a href="{{ route('pricing.index') }}">Renew Now</a>
        </div>
    @endif
@elseif(auth()->user()->subscription_status === 'expired')
    <div class="subscription-expired">
        <i class="fa fa-exclamation-circle"></i>
        <div>
            <strong>Subscription Expired!</strong>
            Your subscription has ended. Renew to continue accessing all features.
        </div>
        <a href="{{ route('pricing.index') }}">Renew Now</a>
    </div>
@elseif(auth()->user()->subscription_status === 'inactive' || !auth()->user()->plan)
    <div class="subscription-expired">
        <i class="fa fa-info-circle"></i>
        <div>
            <strong>No Active Subscription</strong>
            Subscribe to a plan to unlock all features and start connecting with patients.
        </div>
        <a href="{{ route('pricing.index') }}">View Plans</a>
    </div>
@endif
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
                    <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_date . ' ' . $appointment->appointment_time)
                                                ->format('F j, Y - g:i A') }}
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
                <h4>Appointments This Month</h4>
                <canvas id="monthlyChart"></canvas>
            </div>
            <div class="analytics-card">
                <h4>Appointments by Type</h4>
                <canvas id="appointmentChart"></canvas>
            </div>
        </div>
    </div>
</main>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    window.dashboardData = {
        usergrowth: {
            labels: {!! json_encode(range(1, now()->daysInMonth)) !!},
            values: {!! json_encode($monthlyData ?? []) !!}
        },
        appointmentTypeData: {
            labels: {!! json_encode($appointmentTypes->keys() ?? []) !!},
            values: {!! json_encode($appointmentTypes->values() ?? []) !!}
        }
    };
</script>
<script src="{{ asset('js/therapist_dashboard.js') }}"></script>
@endsection
