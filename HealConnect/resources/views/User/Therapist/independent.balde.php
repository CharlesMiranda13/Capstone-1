@extends('layouts.patient_layout')

@section('title', 'PT Dashboard')

@section('styles')
    <link rel="stylesheet" href="{{ asset('Css/patient.css') }}">
@endsection

@section('content')
<main class="pt-main">
    <div class="main-content left-column">
        <div class="welcome-header">
            <h2>Hello, {{ Auth::user()->name ?? 'Patient' }}!</h2>
        </div>

        <div class="card">
            <h3>Upcoming Appointments</h3>
        </div>
        <div class="card">
            <h3>Recent Medical Records</h3>
        </div>
    </div>

    <div class="right-column">
        <div class="notification">
            <h3><i class="fa fa-bell"></i> Notifications</h3>
        </div>
    </div>
</main>
@endsection
