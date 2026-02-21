@extends('layouts.patient_layout')

@section('title', 'My Appointments')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/patient_appointment.css') }}">
@endsection

@section('content')
<main class="appointments-page">
    <div class="bg-white rounded-4 shadow-sm p-4 w-100">
        <div class="page-header-row mb-4">
            <h2 class="page-title-new">My Appointments</h2>
            <p class="page-subtitle">View your scheduled and past appointments</p>
        </div>
        <section class="appointments-container">

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
            <div class="hc-table-container hc-table-responsive">
                <table class="hc-table">
                    <thead>
                        <tr>
                            <th>Therapist</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Time</th>     
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($appointments as $appointment)
                        <tr>
                            <td data-label="Therapist">{{ $appointment->provider->name ?? 'Unknown Therapist' }}</td>

                            <td data-label="Type">{{ ucfirst($appointment->appointment_type) }}</td>
                            
                            <td data-label="Date">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('F j, Y') }}</td>
                            
                            <td data-label="Time">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</td>

                            <td data-label="Status">
                                <span class="hc-badge 
                                            @if($appointment->status == 'pending') hc-badge-warning
                                            @elseif($appointment->status == 'approved') hc-badge-success
                                            @elseif($appointment->status == 'rejected') hc-badge-danger
                                            @elseif($appointment->status == 'completed') hc-badge-info
                                            @endif">
                                            {{ ucfirst($appointment->status) }}
                                        </span>
                            </td>

                            <td data-label="Actions">
                                @if ($appointment->status === 'pending')
                                <form action="{{ route('patient.appointments.cancel', $appointment->id) }}" 
                                    method="POST" 
                                    style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn btn-sm btn-danger"
                                            onclick="return confirm('Cancel this appointment?');">
                                        <i class="fa-solid fa-ban"></i> Cancel
                                    </button>
                                </form>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
        </section>
    </div>
</main>
@endsection