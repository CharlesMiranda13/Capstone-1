@extends('layouts.patient_layout')

@section('title', 'PT - Profile')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<main class="therapist-profile">
    <div class="container">

        <div class="header">
            <h2>{{ $therapist->name }}'s Profile</h2>
        </div>

        {{-- PROFILE CARD --}}
        <section class="profile-card">

            {{-- LEFT: PROFILE INFO --}}
            <div class="profile-left">
                <div class="profile-pic">
                    <img src="{{ $therapist->profile_picture 
                        ? asset('storage/' . $therapist->profile_picture) 
                        : asset('images/default-therapist.png') }}" 
                        alt="{{ $therapist->name }}">
                </div>

                <h3>{{ $therapist->name }}</h3>
                <p class="role">{{ ucfirst($therapist->role) }}</p>

                <p class="bio">
                    {{ $therapist->description ?? 'A compassionate and dedicated therapist ready to assist you.' }}
                </p>

                <div class="contact-info">
                    <p><i class="fa-solid fa-location-dot"></i> {{ $therapist->location ?? 'Location not specified' }}</p>
                    <p><i class="fa-solid fa-envelope"></i> {{ $therapist->email }}</p>
                    <p><i class="fa-solid fa-phone"></i> {{ $therapist->phone ?? 'Phone not specified' }}</p>
                    <p><i class="fa-solid fa-briefcase"></i> 
                        {{ $therapist->experience_years ? $therapist->experience_years . ' years experience' : 'Experience not specified' }}
                    </p>
                </div>
                
                <div class ="app"> 
                    <a href="{{ route('patient.appointments.create', $therapist->id) }}" class="btn-book">
                        <i class="fa-solid fa-calendar-check"></i> Book Appointment
                    </a>

                    <a href="{{ route('patient.appointments.create', $therapist->id) }}" class="btn-book">
                        <i class="fa-solid fa-comments"></i> Messege Therapist
                    </a>
                </div>
            </div>

            {{-- RIGHT: DETAILS --}}
            <div class="profile-right">

                {{-- SPECIALIZATIONS --}}
                <div class="card-section">
                    <h4><i class="fa-solid fa-user-md"></i> Specializations</h4>
                    @if($therapist->specialization)
                        <ul class="specializations">
                            @foreach(explode(',', $therapist->specialization) as $spec)
                                <li>{{ trim($spec) }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p>N/A</p>
                    @endif
                </div>

                {{-- AVAILABILITY --}}
                <div class="card-section">
                    <h4><i class="fa-solid fa-calendar-days"></i> Availability</h4>
                    @if($therapist->availability && count($therapist->availability) > 0)
                        <ul class="availability">
                            @foreach($therapist->availability as $slot)
                                <li>
                                    <span class="date">{{ \Carbon\Carbon::parse($slot['date'])->format('F j, Y') }}</span>
                                    <span class="time">
                                        {{ \Carbon\Carbon::parse($slot['start_time'])->format('g:i A') }} - 
                                        {{ \Carbon\Carbon::parse($slot['end_time'])->format('g:i A') }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p>No availability set.</p>
                    @endif
                </div>

                <div class="services-offered">
                    <h4><i class="fa-solid fa-concierge-bell"></i> Services Offered</h4>
                    @if(!empty($servicesList))
                        <ul class="services-list">
                            @foreach ($servicesList as $service)
                                <li>{{ trim($service) }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p>No appointment types added yet.</p>
                    @endif

            </div>
        </section>
    </div>
</main>
@endsection
