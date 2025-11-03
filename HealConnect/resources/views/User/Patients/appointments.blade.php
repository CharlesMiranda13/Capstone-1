@extends('layouts.patient_layout')

@section('title', 'My Appointments')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/patient_appointment.css') }}">
@endsection

@section('content')
<main class="appointments-page">
    <section class="appointments-container">
        <h2>My Appointments</h2>

        {{-- Success Message --}}
        @if (session('success'))
            <div class="alert success">
                <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        {{-- Check if there are any appointments --}}
        @if ($appointments->isEmpty())
            <div class="empty-state">
                <i class="fa-regular fa-calendar-xmark"></i>
                <p>You don't have any scheduled appointments yet.</p>
            </div>
        @else
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Therapist</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Notes</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($appointments as $appointment)
                    <tr>
                        <td>{{ $appointment->provider->name ?? 'Unknown Therapist' }}</td>

                        <td>{{ ucfirst($appointment->appointment_type) }}</td>
                        <td>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('F j, Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</td>

                        <td>
                            @if ($appointment->notes)
                                {{ $appointment->notes }}
                            @else
                                <em>No notes</em>
                            @endif
                        </td>

                        <td>
                            <span class="status {{ strtolower($appointment->status) }}">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </td>

                        <td>
                            @if ($appointment->status === 'pending')
                                <button class="btn btn-sm btn-danger">
                                    <i class="fa-solid fa-ban"></i> Cancel
                                </button>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        @endif
    </section>
</main>
@endsection
