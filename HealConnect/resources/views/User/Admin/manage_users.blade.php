@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('styles')
@endsection

@section('content')
    <div class="page-header-row">
        <h2 class="page-title-new">Manage Users</h2>
        <p class="page-subtitle">View, search, and manage all registered users</p>
    </div>

    @php
        $roleTitle = 'All Users';
        if(request('role') == 'patient') $roleTitle = 'Patient Users';
        elseif(request('role') == 'therapist') $roleTitle = 'Therapist Users';
        elseif(request('role') == 'clinic') $roleTitle = 'Clinic Users';
    @endphp
    <div class="print-only-title">
        HealConnect - {{ $roleTitle }}
    </div>

    <div class="search-filter-new">
        <form method="GET" action="{{ route('admin.manage-users') }}" class="search-filter-form-new">
            <div class="search-input-wrapper">
                <i class="fa fa-search search-icon-inside"></i>
                <input type="text" name="search" placeholder="Search users..." value="{{ request('search') }}" class="search-input-new">
            </div>
            <select name="role" class="filter-select-new" onchange="this.form.submit()">
                <option value="all"      {{ request('role') == 'all'       ? 'selected' : '' }}>All Roles</option>
                <option value="patient"  {{ request('role') == 'patient'   ? 'selected' : '' }}>Patients</option>
                <option value="therapist"{{ request('role') == 'therapist' ? 'selected' : '' }}>Therapists</option>
                <option value="clinic"   {{ request('role') == 'clinic'    ? 'selected' : '' }}>Clinics</option>
            </select>
            <select name="status" class="filter-select-new" onchange="this.form.submit()">
                <option value="all"      {{ request('status') == 'all'       ? 'selected' : '' }}>All Status</option>
                <option value="Active"   {{ request('status') == 'Active'    ? 'selected' : '' }}>Active</option>
                <option value="Pending"  {{ request('status') == 'Pending'   ? 'selected' : '' }}>Pending</option>
                <option value="Expired"  {{ request('status') == 'Expired'   ? 'selected' : '' }}>Expired</option>
                <option value="Declined" {{ request('status') == 'Declined'  ? 'selected' : '' }}>Declined</option>
            </select>
            <button type="submit" class="hc-btn hc-btn-primary hc-btn-search">
                <i class="fa fa-search"></i> Search
            </button>
        </form>
        <button type="button" class="hc-btn hc-btn-secondary text-nowrap-btn" onclick="window.print()">
            <i class="fa fa-print"></i> Print
        </button>
    </div>

    <div class="hc-table-container hc-table-responsive">
        <table class="hc-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th class="print-only">Contact Number</th>
                    <th class="hide-on-print">Role</th>
                    <th class="hide-on-print">Status</th>
                    <th class="hide-on-print">Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>#{{ $user->id }}</td>
                    <td>
                        <div class="user-info">
                            <span class="fw-bold">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td>{{ $user->email }}</td>
                    <td class="print-only">{{ $user->phone ?? 'N/A' }}</td>
                    <td class="hide-on-print">
                        <span class="role-badge">{{ ucfirst($user->role_display) }}</span>
                    </td>
                    <td class="hide-on-print">
                        <span class="hc-badge 
                            @if($user->status == 'Pending' || $user->status == 'Re-verification Pending') hc-badge-warning
                            @elseif($user->status == 'Active') hc-badge-success
                            @elseif($user->status == 'Declined') hc-badge-danger
                            @elseif($user->status == 'Expired') hc-badge-expired
                            @endif">
                        {{ $user->status }}
                        </span>
                    </td>   
                    <td class="hide-on-print">
                        <div class="hc-dropdown">
                            <button class="hc-dropdown-toggle">Actions</button>
                            <div class="hc-dropdown-menu">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="hc-dropdown-item">
                                    <i class="fa fa-eye"></i> View Profile
                                </a>
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="action-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="hc-dropdown-item hc-dropdown-item-danger"
                                        onclick="return confirm('Are you sure you want to delete this user?');">
                                        <i class="fa fa-trash"></i> Delete User
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4 d-flex justify-content-center">
        {{ $users->withQueryString()->links('pagination.custom') }}
    </div>
@endsection
