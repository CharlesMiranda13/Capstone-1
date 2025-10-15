@extends('layouts.therapist')

@section('title', 'Therapist Profile')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<main class="therapist-profile">
    <div class="container">

        {{-- HEADER --}}
        <div class="header">
            <h2>Your Profile</h2>
            <p>Manage your details and keep your profile up to date.</p>
        </div>

        {{-- PROFILE CARD --}}
        <section class="profile-card">

            {{-- LEFT: PHOTO & BASIC INFO --}}
            <div class="profile-left">
                <div class="profile-pic">
                    <img 
                        src="{{ Auth::user()->profile_picture 
                            ? asset('storage/' . Auth::user()->profile_picture) 
                            : asset('images/default-therapist.png') }}" 
                        alt="Profile Picture">
                </div>

                <h3>{{ Auth::user()->name }}</h3>
                <p class="role">{{ ucfirst(Auth::user()->role) }}</p>
                <p class="bio">{{ Auth::user()->description ?? 'A compassionate and dedicated therapist ready to assist you.' }}</p>

                <div class="contact-info">
                    <p><i class="fa-solid fa-location-dot"></i> {{ Auth::user()->address ?? 'Location not specified' }}</p>
                    <p><i class="fa-solid fa-envelope"></i> {{ Auth::user()->email }}</p>
                    <p><i class="fa-solid fa-phone"></i> {{ Auth::user()->phone ?? 'Phone not specified' }}</p>
                </div>
                              
                <div class="card-section">
                    <h4><i class="fa-solid fa-hand-holding-medical"></i> Offered Appointment Types</h4>
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
            </div>

            {{-- RIGHT: DETAILS --}}
            <div class="profile-right">

                {{-- SPECIALIZATIONS --}}
                <div class="card-section">
                    <h4><i class="fa-solid fa-user-md"></i> Specializations</h4>
                    @if(Auth::user()->specialization)
                        <ul class="specializations">
                            @foreach(explode(',', Auth::user()->specialization) as $spec)
                                <li>{{ trim($spec) }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p>N/A</p>
                    @endif
                </div>

                {{-- AVAILABILITY --}}
                @if(isset($availability) && count($availability) > 0)
                <div class="card-section">
                    <h4><i class="fa-solid fa-calendar-days"></i> Availability</h4>
                    <ul class="availability">
                        @foreach($availability as $slot)
                            <li>
                                <span class="date">{{ \Carbon\Carbon::parse($slot['date'])->format('F j, Y') }}</span>
                                <span class="time">
                                    {{ \Carbon\Carbon::parse($slot['start_time'])->format('g:i A') }} -
                                    {{ \Carbon\Carbon::parse($slot['end_time'])->format('g:i A') }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </section>
    </div>
</main>
@endsection
