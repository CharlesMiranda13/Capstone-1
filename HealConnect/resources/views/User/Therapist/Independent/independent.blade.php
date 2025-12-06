@extends('layouts.therapist')

@section('title', 'Therapist Dashboard')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/therapist.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('content')
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
    <h2>Hello Therapist, {{ Auth::user()->name ?? 'Therapist' }}!</h2>
    <a href="{{ route('therapist.settings') }}">
        <img src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) 
        : asset('images/logo1.png') }}"
        alt="Profile Picture"
        class="pic">
    </a>
</div>

<main class="therapist-main">
    <div class="dashboard-cards">
        <div class="card appointments-card">
            <h3><i class="fa fa-calendar"></i> Upcoming Appointments</h3>
            <div class="appointments-list">
                @forelse ($appointments as $appointment)
                    <div class="appointment-item">
                        <p class="patient-name">{{ $appointment->patient->name }}</p>
                        <p class="appointment-date">
                            {{ \Carbon\Carbon::parse($appointment->appointment_date . ' ' . $appointment->appointment_time)->format('F j, Y - g:i A') }}
                        </p>
                        <p class="appointment-type">
                            Type: {{ ucfirst($appointment->appointment_type) }}
                        </p>
                    </div>
                @empty
                    <p class="empty-state">No upcoming appointments.</p>
                @endforelse
            </div>
        </div>

        <div class="card client-card">
            <h3><i class="fa fa-folder-open"></i> Total Clients</h3>
            <div class="client-count">
                <h2>{{ $appointmentCount ?? 0 }}</h2>
                <p>Active Clients</p>
            </div>
        </div>
    </div>

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
