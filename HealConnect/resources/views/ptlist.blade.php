@extends('layouts.app')

@section('title', 'HealConnect - Therapists & Clinics')

@section('content')
<main class="Therapist-main">
    <div class="ptlist">
    <h2 class="therapist-title">Verified Therapists / Clinics </h2>

    <div class="filter-tabs">
        <span class="service-label">Category:</span>
        <a href="{{ route('ptlist') }}" 
           class="{{ request('category') == '' ? 'active' : '' }}">All</a> |
        <a href="{{ route('ptlist', ['category' => 'independent']) }}" 
           class="{{ request('category') == 'independent' ? 'active' : '' }}">Independent Therapist</a> |
        <a href="{{ route('ptlist', ['category' => 'clinic']) }}" 
           class="{{ request('category') == 'clinic' ? 'active' : '' }}">Clinic</a>
    </div>

    {{-- Services Tabs --}}
    <div class="filter-services">
        <span class="service-label">Services:</span>
        <a href="{{ route('ptlist', ['service' => 'home']) }}" 
           class="{{ request('service') == 'home' ? 'active' : '' }}">In-home</a> |
        <a href="{{ route('ptlist', ['service' => 'online']) }}" 
           class="{{ request('service') == 'online' ? 'active' : '' }}">Online</a> |
        <a href="{{ route('ptlist', ['service' => 'clinic']) }}" 
           class="{{ request('service') == 'clinic' ? 'active' : '' }}">Clinic</a>
    </div>

    @if($therapists->count() > 0)
        <div class="therapist-cards-container">
            @foreach($therapists as $therapist)
                <div class="therapist-card">
                    <div class="therapist-logo">
                        @if($therapist->profile_picture)
                            <img src="{{ asset('storage/' .$therapist->profile_picture) }}" alt="{{ $therapist->name }}">
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
                        {{ $therapist->address ?? 'Location not specified' }}
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
                        <a href="{{ url('/logandsign') }}" class="btn-book">Book Now</a>
                        <a href="{{ route('view_profile', $therapist->id) }}" class="btn-profile">View Profile</a>
                    </div>
                </div>
            @endforeach

        <div class="pagination">
            {{ $therapists->links() }}
        </div>
    @else
        <p style="text-align: center;">No verified therapists or clinics registered at the moment.</p>
    @endif
</main>
@endsection
