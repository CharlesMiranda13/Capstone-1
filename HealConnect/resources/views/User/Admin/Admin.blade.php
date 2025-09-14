@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('styles')
    <link rel="stylesheet" href="{{ asset('/Css/Admin.css') }}">
    <link rel="stylesheet" href="{{ asset('/Css/style.css') }}">
@endsection

@section('content')
    <h2>Dashboard</h2>
    <div class="dashboard-cards">
        <div class="card">
            <h3>Total Users</h3>
        </div>
        <div class="card">
            <h3>Active Sessions</h3>
        </div>
        <div class="card">
            <h3>New Registrations</h3>
        </div>
        <div class="card">
            <h3>Pending Approvals</h3>
        </div>
    </div>
@endsection
