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
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ ucfirst($user->role) }}</td>
                    <td>{{ $user->is_active ? 'Active' : 'Inactive' }}</td>
                    <td>
                        <button class="btn btn-edit">Edit</button>
                        <button class="btn btn-delete">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>



@endsection
    