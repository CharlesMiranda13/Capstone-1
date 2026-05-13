@extends('layouts.app')

@section('title', 'HealConnect - Therapists & Clinics')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/modals.css') }}">
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<div class="page-header-row">
    <h2 class="page-title-new">Verified Therapists & Clinics</h2>
    <p class="page-subtitle">Browse and book appointments with verified healthcare professionals</p>
</div>

<main class="Therapist-main">

    <div class="filters-wrapper">
        <div class="filters-left">

            {{-- Category Filter --}}
            <div class="filter-row-inline">
                <span class="service-label">Category</span>
                <div class="hc-tabs-wrapper">
                    <a href="{{ route('ptlist') }}" 
                        class="hc-tab-item {{ request('category') == '' ? 'active' : '' }}">All</a>
                    <a href="{{ route('ptlist', ['category' => 'independent']) }}" 
                        class="hc-tab-item {{ request('category') == 'independent' ? 'active' : '' }}">Independent</a>
                    <a href="{{ route('ptlist', ['category' => 'clinic']) }}" 
                        class="hc-tab-item {{ request('category') == 'clinic' ? 'active' : '' }}">Clinic</a>
                </div>
            </div>

            {{-- Services Filter --}}
            <div class="filter-row-inline">
                <span class="service-label">Services</span>
                <div class="hc-tabs-wrapper">
                    <a href="{{ route('ptlist', ['service' => 'home']) }}" 
                        class="hc-tab-item {{ request('service') == 'home' ? 'active' : '' }}">In-home</a>
                    <a href="{{ route('ptlist', ['service' => 'online']) }}" 
                        class="hc-tab-item {{ request('service') == 'online' ? 'active' : '' }}">Online</a>
                    <a href="{{ route('ptlist', ['service' => 'clinic']) }}" 
                        class="hc-tab-item {{ request('service') == 'clinic' ? 'active' : '' }}">Clinic</a>
                </div>
            </div>
        </div>

        {{-- Search Bar --}}
        <div class="search-container">
            <form action="{{ route('ptlist') }}" method="GET" class="search-form">
                <div class="search-input-wrap">
                    <input type="text" 
                           name="search" 
                           placeholder="Search therapists..." 
                           value="{{ request('search') }}"
                           class="search-input">
                    <button type="submit" class="search-btn">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    @if($therapists->count() > 0)
        <div class="therapist-cards-container">
            @foreach($therapists as $therapist)
                <div class="therapist-card">
                    
                    @if($therapist->role === 'clinic' && $therapist->clinic_type)
                        <span class="clinic-type-badge {{ $therapist->clinic_type }}">
                            {{ ucfirst($therapist->clinic_type) }}
                        </span>
                    @endif

                    {{-- Card Body --}}
                    <div class="therapist-card-body">
                        <div class="therapist-logo">
                            @if($therapist->profile_picture)
                                <img src="{{ asset('storage/' .$therapist->profile_picture) }}" alt="{{ $therapist->name }}">
                            @else
                                <img src="{{ asset('images/logo1.png') }}" alt="Default Therapist">
                            @endif
                        </div>
                        
                        <h3 class="therapist-name">{{ $therapist->name }}</h3>
                        <p class="therapist-role">{{ ucfirst($therapist->role_display) }}</p>
                        
                        <p class="therapist-description">
                            {{ $therapist->description ?? 'A compassionate and dedicated therapist ready to assist you.' }}
                        </p>

                        <p class="therapist-location">
                            <i class="fa-solid fa-location-dot"></i>
                            {{ $therapist->address ?? 'Location not specified' }}
                        </p>

                        <div class="card-spacer"></div>
                    </div>

                    {{-- Card Footer --}}
                    <div class="therapist-card-footer">
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
                            <button type="button" class="btn-profile view-profile-btn" 
                                    data-id="{{ $therapist->id }}" 
                                    data-url="{{ route('view_profile', $therapist->id) }}">
                                View Profile
                            </button>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>

        <div class="pagination">
            {{ $therapists->links() }}
        </div>
    @else
        <p class="no-results">No verified therapists or clinics registered at the moment.</p>
    @endif
</main>

{{-- Profile Modal --}}
<div id="profileModal" class="modal-overlay">
    <div class="modal-container modal-lg">
        <div class="modal-header">
            <h3>Therapist Profile</h3>
            <button class="modal-close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body" id="modalProfileContent">
            {{-- Content loaded via AJAX --}}
            <div class="loading-spinner">
                <i class="fas fa-spinner fa-spin"></i> Loading profile...
            </div>
        </div>
    </div>
</div>
@endsection