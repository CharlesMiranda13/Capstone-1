@extends('layouts.admin')

@section('title', 'View Reports')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin-reports.css') }}">
@endsection

@section('content')
<div class="reports-container">
    <div class="reports-header">
        <h2>System Reports</h2>
    </div>

    <!-- Subscription Reports -->
    <div class="report-card">
        <div class="report-card-header">
            <h5><i class="fa-solid fa-credit-card"></i> Subscription Reports</h5>
            <a href="{{ route('admin.subscriptions.index') }}" class="btn-primary-small">View Details</a>
        </div>
        <div class="report-card-body">
            <div class="stats-grid">
                <div class="report-stat">
                    <h3>{{ $subscriptionStats['total'] }}</h3>
                    <p class="text-muted">Total Subscriptions</p>
                </div>
                <div class="report-stat">
                    <h3 class="text-success">{{ $subscriptionStats['active'] }}</h3>
                    <p class="text-muted">Active</p>
                </div>
                <div class="report-stat">
                    <h3 class="text-warning">{{ $subscriptionStats['inactive'] }}</h3>
                    <p class="text-muted">Inactive</p>
                </div>
                <div class="report-stat">
                    <h3 class="text-danger">{{ $subscriptionStats['expired'] }}</h3>
                    <p class="text-muted">Expired</p>
                </div>
                <div class="report-stat">
                    <h3 class="text-primary">{{ $subscriptionStats['pro_solo'] }}</h3>
                    <p class="text-muted">Pro Solo</p>
                </div>
                <div class="report-stat">
                    <h3 class="text-info">{{ $subscriptionStats['pro_clinic'] }}</h3>
                    <p class="text-muted">Pro Clinic</p>
                </div>
            </div>

            <div class="revenue-box">
                <strong>Estimated Monthly Revenue:</strong> 
                ₱{{ number_format(($subscriptionStats['pro_solo'] * 499) + ($subscriptionStats['pro_clinic'] * 999), 2) }}
            </div>
        </div>
    </div>

    <!-- User Statistics -->
    <div class="report-card">
        <div class="report-card-header">
            <h5><i class="fa-solid fa-users"></i> User Statistics</h5>
        </div>
        <div class="report-card-body">
            <div class="stats-grid">
                <div class="report-stat">
                    <h3>{{ $userStats['total_users'] }}</h3>
                    <p class="text-muted">Total Users</p>
                </div>
                <div class="report-stat">
                    <h3 class="text-primary">{{ $userStats['patients'] }}</h3>
                    <p class="text-muted">Patients</p>
                </div>
                <div class="report-stat">
                    <h3 class="text-success">{{ $userStats['therapists'] }}</h3>
                    <p class="text-muted">Therapists</p>
                </div>
                <div class="report-stat">
                    <h3 class="text-info">{{ $userStats['clinics'] }}</h3>
                    <p class="text-muted">Clinics</p>
                </div>
                <div class="report-stat">
                    <h3 class="text-success">{{ $userStats['verified'] }}</h3>
                    <p class="text-muted">Verified Accounts</p>
                </div>
                <div class="report-stat">
                    <h3 class="text-warning">{{ $userStats['pending'] }}</h3>
                    <p class="text-muted">Pending Verification</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Appointment Statistics -->
    <div class="report-card">
        <div class="report-card-header">
            <h5><i class="fa-solid fa-calendar-check"></i> Appointment Statistics</h5>
        </div>
        <div class="report-card-body">
            <div class="stats-grid">
                <div class="report-stat">
                    <h3>{{ $appointmentStats['total'] }}</h3>
                    <p class="text-muted">Total Appointments</p>
                </div>
                <div class="report-stat">
                    <h3 class="text-warning">{{ $appointmentStats['pending'] }}</h3>
                    <p class="text-muted">Pending</p>
                </div>
                <div class="report-stat">
                    <h3 class="text-success">{{ $appointmentStats['confirmed'] }}</h3>
                    <p class="text-muted">Confirmed</p>
                </div>
                <div class="report-stat">
                    <h3 class="text-info">{{ $appointmentStats['completed'] }}</h3>
                    <p class="text-muted">Completed</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="report-card">
        <div class="report-card-header">
            <h5><i class="fa-solid fa-chart-line"></i> Registration Trend (Last 30 Days)</h5>
        </div>
        <div class="report-card-body">
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>New Registrations</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentRegistrations as $reg)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($reg->date)->format('M d, Y') }}</td>
                        <td><span class="badge badge-primary">{{ $reg->count }}</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="empty-state">No recent registrations</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection