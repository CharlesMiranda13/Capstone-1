@extends('layouts.patient_layout')

@section('title', 'List of Therapists & Clinics')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/tts.css') }}">
@endsection

@section('content')
<div class="ptlist">
    <h2 class="therapist-title">Therapists / Clinics Near You</h2>

    <div class="filter-tabs">
        <a href="{{ route('patient.therapists') }}" 
           class="{{ request('category') == '' ? 'active' : '' }}">All</a> |
        <a href="{{ route('patient.therapists', ['category' => 'independent']) }}" 
           class="{{ request('category') == 'independent' ? 'active' : '' }}">Independent Therapist</a> |
        <a href="{{ route('patient.therapists', ['category' => 'clinic']) }}" 
           class="{{ request('category') == 'clinic' ? 'active' : '' }}">Clinic</a>
    </div>

    {{-- Services Tabs --}}
    <div class="filter-services">
        <span class="service-label">Services:</span>
        <a href="{{ route('patient.therapists', ['service' => 'home']) }}" 
           class="{{ request('service') == 'home' ? 'active' : '' }}">In-home</a> |
        <a href="{{ route('patient.therapists', ['service' => 'online']) }}" 
           class="{{ request('service') == 'online' ? 'active' : '' }}">Online</a> |
        <a href="{{ route('patient.therapists', ['service' => 'clinic']) }}" 
           class="{{ request('service') == 'clinic' ? 'active' : '' }}">Clinic</a>
    </div>


    {{-- Therapist List --}}
    @if($therapists->count() > 0)
        <div class="therapist-cards-container">
            @foreach($therapists as $therapist)
                <div class="therapist-card">
                    <div class="therapist-logo">
                        @if($therapist->profile_image)
                            <img src="{{ asset('storage/' .Auth::user()->profile_picture) }}" alt="{{ $therapist->name }}">
                        @else
                            <img src="{{ asset('images/default-therapist.png') }}" alt="Default Therapist">
                        @endif
                    </div>

                    <h3 class="therapist-name">{{ $therapist->name }}</h3>
                    <p class="therapist-role">{{ ucfirst($therapist->role) }}</p>
                    <p class="therapist-description">
                        {{ $therapist->description ?? 'A compassionate and dedicated therapist ready to assist you.' }}
                    </p>

                    <p class="therapist-location">
                        <i class="fa-solid fa-location-dot"></i>
                        {{ $therapist->location ?? 'Location not specified' }}
                    </p>


                    {{-- Availability --}}
                    @if($therapist->availability && count($therapist->availability) > 0)
                        <span class="availability-status available">
                            <i class="fa-solid fa-circle"></i> Has Availability
                        </span>
                    @else
                        <span class="availability-status unavailable">
                            <i class="fa-solid fa-circle"></i> No Availability
                        </span>
                    @endif

                    <div class="therapist-actions">
                        <a href="{{ route('patient.appointments.create', $therapist->id) }}" class="btn-book">Book Now</a>
                        <a href="{{ route('patient.appointments.create', $therapist->id) }}" class="btn-profile">View Profile</a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pagination">
            {{ $therapists->links() }}
        </div>
    @else
        <p class="no-results">No therapists or clinics available.</p>
    @endif
</div>
@endsection
