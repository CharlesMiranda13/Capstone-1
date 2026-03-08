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
            <a href="{{ route('subscribe.show', 'pro clinic') }}">Renew Now</a>
        </div>
    @elseif($daysRemaining <= 0)
        <div class="subscription-expired">
            <i class="fa fa-exclamation-circle"></i>
            <div>
                <strong>Subscription Expired!</strong>
                Your subscription ended on {{ $subscriptionEnd->format('M d, Y') }}. Renew to continue accessing all features.
            </div>
            <a href="{{ route('subscribe.show', 'pro clinic') }}">Renew Now</a>
        </div>
    @endif
@elseif(auth()->user()->subscription_status === 'expired')
    <div class="subscription-expired">
        <i class="fa fa-exclamation-circle"></i>
        <div>
            <strong>Subscription Expired!</strong>
            Your subscription has ended. Renew to continue accessing all features.
        </div>
        <a href="{{ route('subscribe.show', 'pro clinic') }}">Renew Now</a>
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
            <a href="{{ route('subscribe.show', 'pro clinic') }}">Upgrade Now</a>
        </div>
    @else
        <div class="subscription-warning" style="background-color: #e9f5ff; color: #0056b3; border-left: 5px solid #0056b3;">
            <i class="fa fa-info-circle" style="color: #0056b3;"></i>
            <div>
                <strong>Free Trial Active</strong>
                You have used {{ $customerCount }} out of your 2 free patient slots. Subscribe to unlock unlimited patients and all premium features.
            </div>
            <a href="{{ route('subscribe.show', 'pro clinic') }}" style="background-color: #0056b3; color: white;">View Plans</a>
        </div>
    @endif
@endif

{{-- Business Permit Expiry Warning --}}
@if($clinic->role === 'clinic' && $clinic->business_permit_expiry)
    @php
        $expiryDate = $clinic->business_permit_expiry;
    @endphp

    @if($clinic->isBusinessPermitExpired())
        <div class="subscription-expired" style="margin-top: 10px; background-color: #fff5f5; border-left: 5px solid #fc8181; color: #c53030;">
            <i class="fa fa-file-contract"></i>
            <div>
                <strong>Business Permit Expired!</strong>
                Your business permit expired on {{ $expiryDate->format('M d, Y') }}. Please upload a new permit in settings to maintain your account verification.
            </div>
            <a href="{{ route('therapist.settings') }}" style="background-color: #c53030; color: white;">Update Permit</a>
        </div>
    @elseif($clinic->isBusinessPermitExpiringSoon())
        @php
            $daysUntilExpiry = ceil(now()->diffInDays($expiryDate, false));
        @endphp
        <div class="subscription-warning" style="margin-top: 10px; background-color: #fffaf0; border-left: 5px solid #f6ad55; color: #9c4221;">
            <i class="fa fa-clock"></i>
            <div>
                <strong>Business Permit Expiring Soon!</strong>
                Your business permit will expire in {{ $daysUntilExpiry }} day{{ $daysUntilExpiry > 1 ? 's' : '' }} on {{ $expiryDate->format('M d, Y') }}.
            </div>
            <a href="{{ route('therapist.settings') }}" style="background-color: #f6ad55; color: white;">Update Permit</a>
        </div>
    @endif
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
                    <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_date . ' ' . $appointment->appointment_time)
                                                ->format('F j, Y - g:i A') }}</p>
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
            labels: {!! json_encode(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']) !!},
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
