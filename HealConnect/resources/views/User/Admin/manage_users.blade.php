@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('content')
    <div class="admin-main-manage-users">
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

        <table class="user-table table table-bordered">
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
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ ucfirst($user->role) }}</td>
                        <td>{{ $user->status ?? 'Pending' }}</td>   
                        <td>
                        {{-- View --}}
                            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-info btn-sm">View</a>

                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-del btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
