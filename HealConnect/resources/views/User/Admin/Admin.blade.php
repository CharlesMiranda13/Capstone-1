@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="admin-main">
    <h2>Dashboard</h2>
    <div class="dashboard-cards">
        <a href="{{ route('admin.manage-users', ['role' => 'all']) }}" class="card">
            <i class="fa-solid fa-users" style="color:#007bff;"></i>
            <h3>{{ $totalUsers }}</h3>
            <p>Total Users</p>
        </a>
        <a href="{{ route('admin.manage-users', ['role' => 'patient']) }}" class="card">
            <i class="fa-solid fa-user"></i>
            <h3>{{ $totalPatients }}</h3>
            <p>Patients</p>
        </a>
        <a href="{{ route('admin.manage-users', ['role' => 'therapist']) }}" class="card">
            <i class="fa-solid fa-user-doctor"></i>
            <h3>{{ $totalTherapists }}</h3>
            <p>Therapists</p>
        </a>
        <a href="{{ route('admin.manage-users', ['role' => 'clinic']) }}" class="card">
            <i class="fa-solid fa-circle-h" style="color:red;"></i>
            <h3>{{ $totalClinics }}</h3>
            <p>Clinics</p>
        </a>
        <a href="{{ route('admin.manage-users', ['status' => 'pending']) }}" class="card">
            <i class="fa-solid fa-hourglass-half"></i>
            <h3>{{ $pendingUsers }}</h3>
            <p>Pending Approvals</p>
        </a>
    </div>
    <div class="analytics-overview">
        <h3>Analytics Overview</h3>
        <div class="analytics-section">
            <div class="analytics-card">
                <h4>Monthly New Users</h4>
                <canvas id="monthlyChart"></canvas>
            </div>
            <div class="analytics-card">
                <h4>Appointments by Type</h4>
                <canvas id="appointmentChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    window.dashboardata = {
        usergrowth: @json($monthlyData),
        appointmenttypes: @json($appointmentTypes)
    };
</script>
<script src="{{ asset('js/admin_dashboard.js') }}"></script>   
@endsection 


