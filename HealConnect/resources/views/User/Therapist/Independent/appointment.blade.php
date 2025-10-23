@extends('layouts.therapist')

@section('title', 'Appointments')
@section('styles')
<link rel="stylesheet" href="{{ asset('css/therapist_appointment.css') }}">
<link rel="stylesheet" href="{{ asset('css/appointment.css') }}">
@endsection

@section('content')
<main class="appointments-main">
    <div class="container">
        <h2 style = "text-align:center">My Appointments</h2>

        <div class="search-filter">
            <form method="GET" action="{{ route('therapist.appointments') }}" class="search-filter-form">
                <input type="text" name="search" placeholder="Search by patient name or type..." 
                value="{{ request('search') }}"
                class="search-input">

            <select name="status" class="filter-select" onchange="this.form.submit()">
                <option value="">Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
            
            <select name=type class="filter-select" onchange="this.form.submit()">
                <option value="">All Types</option>
                <option value="in-person" {{ request('type') == 'in-person' ? 'selected' : '' }}>In-Person</option>
                <option value="online" {{ request('type') == 'online' ? 'selected' : '' }}>Online</option>
            </select>

            <button type="submit" class="btn-search">
                <i class="fa fa-search"></i>
            </button>
            </form>
        </div>


        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($appointments->isEmpty())
            <p>No appointments yet.</p>
        @else
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Notes</th>
                        <th>Refferal</th>
                        <th>Status</th>
                        <th>Action</th>
                        <th>View Profile</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($appointments as $appointment)
                        <tr>
                            <td>{{ $appointment->patient->name }}</td>
                            <td>{{ ucfirst($appointment->appointment_type) }}</td>
                            <td>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('F j, Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</td>
                            <td>{{ $appointment->notes ?? 'N/A' }}</td>
                            <td>
                                @if($appointment->referral)
                                    <a href="{{ asset('storage/referrals/' . $appointment->referrals) }}" target="_blank">View Referral</a>
                                @else
                                    N/A
                                @endif
                            <td>
                                <span class="badge 
                                    @if($appointment->status == 'pending') bg-warning
                                    @elseif($appointment->status == 'approved') bg-success
                                    @elseif($appointment->status == 'rejected') bg-danger
                                    @elseif($appointment->status == 'completed') bg-primary
                                    @endif">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </td>
                            <td class="d-flex align-items-center gap-2">
                                

                                @if($appointment->appointment_type === 'online' && $appointment->status === 'approved')
                                    <a href="{{ $appointment->session_link }}" target="_blank" class="btn btn-sm btn-success">
                                        <i class="fa fa-video"></i> Join Session
                                    </a>
                                @endif

                                @if($appointment->status === 'completed')
                                    <a href="{{ route('therapist.session.notes', $appointment->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fa fa-file-medical"></i> Add Notes
                                    </a>
                                 @endif

                                <form action="{{ route('therapist.appointments.updateStatus', $appointment->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                        <option value="" disabled selected>Change Status</option>
                                        <option value="approved">Approve</option>
                                        <option value="rejected">Reject</option>
                                        <option value="completed">Complete</option>
                                    </select>
                                </form>
                                
                            </td>
                            <td>
                                <a href="{{ route('therapist.patients.profile', $appointment->patient->id) }}" class="btn btn-sm btn-info">
                                    <i class="fa fa-user"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        @endif
    </div>
</main>
@endsection
