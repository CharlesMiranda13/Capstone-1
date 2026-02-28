@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('content')
    <div class="page-header-row">
        <h2 class="page-title-new">Manage Users</h2>
        <p class="page-subtitle">View, search, and manage all registered users</p>
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
            <button type="submit" class="hc-btn hc-btn-primary hc-btn-search">
                <i class="fa fa-search"></i> Search
            </button>
        </form>
    </div>

    <div class="hc-table-container hc-table-responsive">
        <table class="hc-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
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
                    <td>
                        <span class="role-badge">{{ ucfirst($user->role_display) }}</span>
                    </td>
                    <td>
                        <span class="hc-badge 
                            @if($user->status == 'Pending') hc-badge-warning
                            @elseif($user->status == 'Active') hc-badge-success
                            @elseif($user->status == 'Declined') hc-badge-danger
                            @endif">
                        {{ $user->status }}
                        </span>
                    </td>   
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-primary btn-sm">
                                <i class="fa fa-eye"></i> View
                            </a>

                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-del btn-sm"
                                    onclick="return confirm('Are you sure you want to delete this user?');">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </form>
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
