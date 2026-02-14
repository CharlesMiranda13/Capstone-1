@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('content')
    <h2>Manage Users</h2>
    <div class="filters mb-3">
        <form method="GET" action="{{ route('admin.manage-users') }}" class="d-flex gap-2">
            <input type="text" name="search" placeholder="Search users..." value="{{ request('search') }}">
            <select name="role" onchange="this.form.submit()">
                <option value="all" {{ request('role') == 'all' ? 'selected' : '' }}>All</option>
                <option value="patient" {{ request('role') == 'patient' ? 'selected' : '' }}>Patients</option>
                <option value="therapist" {{ request('role') == 'therapist' ? 'selected' : '' }}>Therapists</option>
                <option value="clinic" {{ request('role') == 'clinic' ? 'selected' : '' }}>Clinics</option>
            </select>
            <button type="submit" class="btn-search">
                <i class="fa fa-search"></i>
            </button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="user-table">
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
                        <span class="badge 
                            @if($user->status == 'Pending') bg-warning
                            @elseif($user->status == 'Active') bg-success
                            @elseif($user->status == 'Declined') bg-danger
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
    <div class="mt-3">
        {{ $users->withQueryString()->links() }}
    </div>
@endsection
