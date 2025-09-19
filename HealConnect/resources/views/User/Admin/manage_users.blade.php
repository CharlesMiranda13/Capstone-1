@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('styles')
    <link rel="stylesheet" href="{{ asset('/Css/Admin.css') }}">
    <link rel="stylesheet" href="{{ asset('/Css/style.css') }}">
@endsection

@section('content')
    <h2>Manage Users</h2>

    <div class="filters">
        <input type="text" placeholder="Search users...">
        <select>
            <option value="all">All</option>
            <option value="patient">Patients</option>
            <option value="therapist">Therapists</option>
        </select>
    </div>

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
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ ucfirst($user->role) }}</td>
                    <td>{{ $user->status ?? 'pending' }}</td>   
                    <td>
                        {{-- ✅ Verify --}}
                        <form action="{{ route('admin.users.verify', $user->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success">Verify</button>
                        </form>

                        {{-- ✅ Decline --}}
                        <form action="{{ route('admin.users.decline', $user->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-warning">Decline</button>
                        </form>

                        {{-- ✅ Edit --}}
                       <a href="{{ route('admin.users.edit', $user->id) }}" style="display:inline; background:#4CAF50; color:white; padding:6px 12px; 
                        border-radius:5px; text-decoration:none; font-size:14px;" >Edit</a>

                        {{-- ✅ Delete --}}
                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger"
                                onclick="return confirm('Are you sure you want to delete this user?')">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
