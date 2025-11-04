@if (!request()->has('embed'))
    @extends('layouts.therapist')

    @section('title', 'Patient Profile')

    @section('styles')
        <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
    @endsection

    @section('content')
@endif

<main class="therapist-profile">
    <div class="container">
        <div class="header">
            <h2>{{ $patient->name }}'s Profile</h2>
        </div>

        <section class="profile-card">
            {{-- LEFT SIDE --}}
            <div class="profile-left">
                <div class="profile-pic">
                    <img src="{{ $patient->profile_picture ? asset('storage/' . $patient->profile_picture) : asset('images/default-patient.png') }}" 
                        alt="{{ $patient->name }}">
                </div>

                <h3>{{ $patient->name }}</h3>
                <p class="role">Patient</p>

                <div class="contact-info">
                    <p><i class="fa-solid fa-envelope"></i> {{ $patient->email }}</p>
                    <p><i class="fa-solid fa-phone"></i> {{ $patient->phone ?? 'Phone not specified' }}</p>
                    <p><i class="fa-solid fa-location-dot"></i> {{ $patient->address ?? 'Address not specified' }}</p>
                    <p><i class="fa-solid fa-calendar"></i> 
                        Date of Birth: {{ $patient->dob ? \Carbon\Carbon::parse($patient->dob)->format('F j, Y') : 'Not provided' }}
                    </p>
                </div>

                <div class="app">
                    <a href="{{ route('messages', ['receiver_id' => $patient->id]) }}" class="btn-book">
                        <i class="fa-solid fa-comments"></i> Message Therapist
                    </a>
                </div>
            </div>

            {{-- RIGHT SIDE --}}
            <div class="profile-right">
                <div class="card-section">
                    <h4><i class="fa-solid fa-notes-medical"></i> Health Information</h4>
                    @if($patient->medical_notes)
                        <p>{{ $patient->medical_notes }}</p>
                    @else
                        <p>No health information provided.</p>
                    @endif
                </div>

                <div class="card-section">
                    <h4><i class="fa-solid fa-calendar-days"></i> Appointment History</h4>
                    @if(isset($appointments) && count($appointments) > 0)
                        <ul class="availability">
                            @foreach($appointments as $appt)
                                <li>
                                    <strong>{{ ucfirst($appt->appointment_type) }}</strong> - 
                                    {{ \Carbon\Carbon::parse($appt->appointment_date)->format('F j, Y') }} 
                                    ({{ ucfirst($appt->status) }})
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p>No past appointments found.</p>
                    @endif
                </div>
            </div>
        </section>
    </div>
</main>

@if (!request()->has('embed'))
    @endsection
@endif
