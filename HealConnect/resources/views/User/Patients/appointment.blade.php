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
        </form>
    </div>
</main>
@endsection
