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
                                    <a href="{{ asset('storage/referrals/' . $appointment->refferals) }}" target="_blank">View Referral</a>
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
                            <td>
                                <form action="{{ route('therapist.appointments.updateStatus', $appointment->id) }}" method="POST">
                                    @csrf
                                    <select name="status" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                        <option value="">Change</option>
                                        <option value="approved">Approve</option>
                                        <option value="rejected">Reject</option>
                                        <option value="completed">Complete</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        @endif
    </div>
</main>
@endsection
