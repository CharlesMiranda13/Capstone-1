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
            <a href="{{ route('subscribe.show', 'pro solo') }}">Renew Now</a>
        </div>
    @elseif($daysRemaining <= 0)
        <div class="subscription-expired">
            <i class="fa fa-exclamation-circle"></i>
            <div>
                <strong>Subscription Expired!</strong>
                Your subscription ended on {{ $subscriptionEnd->format('M d, Y') }}. Renew to continue accessing all features.
            </div>
            <a href="{{ route('subscribe.show', 'pro solo') }}">Renew Now</a>
        </div>
    @endif
@elseif(auth()->user()->subscription_status === 'expired')
    <div class="subscription-expired">
        <i class="fa fa-exclamation-circle"></i>
        <div>
            <strong>Subscription Expired!</strong>
            Your subscription has ended. Renew to continue accessing all features.
        </div>
        <a href="{{ route('subscribe.show', 'pro solo') }}">Renew Now</a>
    </div>
@elseif(auth()->user()->subscription_status === 'inactive' || !auth()->user()->plan)
    @php
        $customerCount = auth()->user()->customer_count ?? 0;
    @endphp
    @if($customerCount >= 2)
        <div class="subscription-expired">
            <i class="fa fa-exclamation-triangle"></i>
            <div>
                <strong>Trial Limit Reached!</strong>
                You currently have {{ $customerCount }} patients (the maximum allowed on the free trial). You must upgrade to accept more.
            </div>
            <a href="{{ route('subscribe.show', 'pro solo') }}">Upgrade Now</a>
        </div>
    @else
        <div class="subscription-warning" style="background-color: #e9f5ff; color: #0056b3; border-left: 5px solid #0056b3;">
            <i class="fa fa-info-circle" style="color: #0056b3;"></i>
            <div>
                <strong>Free Trial Active</strong>
                You have used {{ $customerCount }} out of your 2 free patient slots. Subscribe to unlock unlimited patients and all premium features.
            </div>
            <a href="{{ route('subscribe.show', 'pro solo') }}" style="background-color: #0056b3; color: white;">View Plans</a>
        </div>
    @endif
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
        <div class="card">
            <i class="fa fa-calendar-check" style="color:#28a745;"></i>
            <h3>{{ count($appointments) }}</h3>
            <p>Upcoming Appointments</p>
        </div>
        <div class="card">
            <i class="fa-solid fa-users"></i>
            <h3>{{ $appointmentCount ?? 0 }}</h3>
            <p>Total Patients</p>
        </div>
        <div class="card">
            <i class="fa-solid fa-hourglass-half" style="color:orange;"></i>
            <h3>{{ $pendingCount ?? 0 }}</h3>
            <p>Pending Appointments</p>
        </div>
    </div>

    <!-- Upcoming Appointments List -->
    <div class="card appointments-card">
        <h3><i class="fa fa-calendar"></i> Upcoming Appointments</h3>
        <div class="appointments-list">
            @forelse ($appointments as $appointment)
                <div class="appointment-item">
                    <p><strong>Patient:</strong> {{ $appointment->patient->name }}</p>
                    <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_date . ' ' . $appointment->appointment_time)->format('F j, Y - g:i A') }}</p>
                    <p><strong>Type:</strong> {{ ucfirst($appointment->appointment_type) }}</p>
                </div>
            @empty
                <p class="empty-state">No upcoming appointments.</p>
            @endforelse
        </div>
    </div>

    <div class="analytics-overview card">
        <h3>Analytics Overview</h3>
        <div class="analytics-section">
            <div class="analytics-card">
                <h4>Appointments Overview (Yearly)</h4>
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
            labels: @json($monthlyLabels),
            values: @json($monthlyValues),
        },
        appointmentTypeData: {
            labels: {!! json_encode($appointmentTypes->keys() ?? []) !!},
            values: {!! json_encode($appointmentTypes->values() ?? []) !!}
        }
    };
</script>
<script src="{{ asset('js/therapist_dashboard.js') }}"></script>
@endsection
