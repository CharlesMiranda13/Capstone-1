@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <h2>Dashboard</h2>
    <div class="dashboard-cards">
        <a href="{{ route('admin.manage-users', ['role' => 'all']) }}" class="card">
            <i class="fa-solid fa-users" style="color:blue;"></i>
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
            <p>Clinic</p>
        </a>


        <a href="{{ route('admin.manage-users', ['status' => 'pending']) }}" class="card">
            <i class="fa-solid fa-hourglass-half" style="color:;"></i>
            <h3>{{ $pendingUsers }}</h3>
            <p>Pending Approvals</p>
        </a>
@endsection

</div>
