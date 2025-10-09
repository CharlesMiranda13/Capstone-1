@extends('layouts.patient_layout')

@section('title', 'Book Appointment')
@section('styles')
<link rel="stylesheet" href="{{ asset('css/appointment.css') }}">
@endsection

@section('content')
<main class="appointment-main">
    <div class="appointment-content">
        <h2>Book an Appointment</h2>

        <form action="{{ route('patient.appointments.store') }}" method="POST" class="appointment-form">
            @csrf

            <!-- Appointment Type -->
            <div class="form-group">
                <label for="appointment_type">Appointment Type</label>
                <select name="appointment_type" id="appointment_type" required>
                    <option value="">-- Select Type --</option>
                    <option value="online">Online Therapy</option>
                    <option value="home">Home Therapy</option>
                    <option value="clinic">In-Clinic</option>
                </select>
            </div>

            <!-- Therapist -->
            <div class="form-group">
                <label for="therapist_id">Select Therapist</label>
                <select name="therapist_id" id="therapist_id" required>
                    @foreach($therapists as $therapist)
                        <option value="{{ $therapist->id }}">
                            {{ $therapist->name }} ({{ $therapist->specialization }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Appointment Date -->
            <div class="form-group">
                <label for="appointment_date">Preferred Date</label>
                <input type="date" name="appointment_date" min="{{ date('Y-m-d') }}" required>
            </div>

            <!-- Appointment Time -->
            <div class="form-group">
                <label for="appointment_time">Preferred Time</label>
                <input type="time" name="appointment_time" required>
            </div>

            <!-- Notes -->
            <div class="form-group">
                <label for="notes">Additional Notes (Optional)</label>
                <textarea name="notes" id="notes" rows="3" placeholder="Include any details or preferences..."></textarea>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">Submit Appointment Request</button>
        </form>
    </div>
</main>
@endsection
