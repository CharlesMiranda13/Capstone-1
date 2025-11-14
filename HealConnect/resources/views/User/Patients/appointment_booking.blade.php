@extends('layouts.patient_layout')

@section('title', 'Book an Appointment')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/booking.css') }}">
@endsection

@section('content')
<main class="therapist-profile">
    <div class="container">
        <div class="header">
            <h2>Book an Appointment</h2>
        </div>

        <section class="profile-card">
            {{-- LEFT: Therapist Info --}}
            <div class="profile-left">
                <div class="profile-pic">
                    <img src="{{ $therapist->profile_picture ? asset('storage/' . $therapist->profile_picture) : asset('images/default-therapist.png') }}" 
                        alt="{{ $therapist->name }}">
                </div>

                <h3>{{ $therapist->name }}</h3>
                <p class="role">{{ ucfirst($therapist->role) }}</p>
                <p class="bio">{{ $therapist->description ?? 'A compassionate and dedicated therapist ready to assist you.' }}</p>

                <div class="contact-info">
                    <p><i class="fa-solid fa-location-dot"></i> {{ $therapist->address ?? 'Location not specified' }}</p>
                    <p><i class="fa-solid fa-envelope"></i> {{ $therapist->email }}</p>
                    <p><i class="fa-solid fa-phone"></i> {{ $therapist->phone ?? 'Phone not specified' }}</p>
                </div>

                <div class="card-section">
                    <h4><i class="fa-solid fa-hand-holding-medical"></i> Offered Service Types</h4>
                    @if(!empty($servicesList))
                        <ul class="services-list">
                            @foreach($servicesList as $service)
                                <li>{{ trim($service) }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p>No appointment types added yet.</p>
                    @endif
                </div>
            </div>

            {{-- RIGHT: Booking Form --}}
            <div class="profile-right">
                <div class="card-section">
                    <h4><i class="fa-solid fa-calendar-check"></i> Schedule an Appointment</h4>

                    <form action="{{ route('patient.appointments.store') }}" method="POST" class="booking-form" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="therapist_id" value="{{ $therapist->id }}">

                        {{-- Appointment Type --}}
                        <label for="appointment_type">Select Appointment Type:</label>
                        <select name="appointment_type" id="appointment_type" required>
                            <option value="">-- Choose Type --</option>
                            @foreach($servicesList as $service)
                                <option value="{{ $service }}">{{ ucfirst($service) }}</option>
                            @endforeach
                        </select>

                        {{-- Available Date --}}
                        <label for="appointment_date">Select Available Date:</label>
                        <select name="appointment_date" id="appointment_date" required>
                            <option value="">-- Choose Date --</option>
                            @foreach($availabilities as $availability)
                                <option value="{{ $availability['date'] }}">
                                    {{ \Carbon\Carbon::parse($availability['date'])->format('F j, Y (l)') }}
                                </option>
                            @endforeach
                        </select>


                        <label for="appointment_time">Select Available Time:</label>
                        <select name="appointment_time" id="appointment_time" required>
                            <option value="">-- Select Date First --</option>
                        </select>

                        <label for="notes">Notes (Optional):</label>
                        <textarea name="notes" placeholder="Add any details or requests..."></textarea>

                        <label for="referral">Referral:</label>
                        <input type="file" name="referral" accept=".pdf,.doc,.docx,.jpg,.png">

                        <button type="submit" class="btn-submit">Book Appointment</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
</main>
<script id="availabilities-data" type="application/json">
    {!! json_encode($availabilities) !!}
</script>
<script id="booked-times-data" type="application/json">
    {!! json_encode($bookedTimes) !!}
</script>
@endsection
@section('scripts')
<script src="{{ asset('js/booking.js') }}"></script>
@endsection

