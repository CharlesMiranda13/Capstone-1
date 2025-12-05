@extends('layouts.patient_layout')

@section('title', 'List of Therapists & Clinics')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/tts.css') }}">
@endsection

@section('content')
<main class="Therapist-main">

    {{-- Page Title --}}
    <div class="ptlist">
        <h2 class="therapist-title">VERIFIED THERAPISTS / CLINICS</h2>
    </div>
    
    <div class="filters-wrapper">

        <div class="filters-left">

            {{-- Category Tabs --}}
            <div class="filter-tabs">
                <span class="service-label">Category:</span>
                <a href="{{ route('patient.therapists') }}" 
                    class="{{ request('category') == '' ? 'active' : '' }}">All</a> |
                <a href="{{ route('patient.therapists', ['category' => 'independent']) }}" 
                    class="{{ request('category') == 'independent' ? 'active' : '' }}">Independent</a> |
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

        </div>

        <div class="search-container">
            <form action="{{ route('patient.therapists') }}" method="GET" class="search-form">
                <input type="text" 
                       name="search" 
                       placeholder="Search by name or specialization..." 
                       value="{{ request('search') }}"
                       class="search-input">

                <button type="submit" class="search-btn">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>

                {{-- Advanced Filters Toggle --}}
                <button type="button" class="filter-toggle-btn" onclick="toggleAdvancedFilters()">
                    <i class="fa-solid fa-filter"></i> Filters
                </button>
            </form>
        </div>

    </div>

    <div class="advanced-filters" id="advancedFilters" style="display: none;">
        <form action="{{ route('patient.therapists') }}" method="GET" class="advanced-filters-form">
            
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            @if(request('service'))
                <input type="hidden" name="service" value="{{ request('service') }}">
            @endif
            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif

            <div class="filter-row">
                {{-- City Filter --}}
                <div class="filter-group">
                    <label for="city">City/Municipality</label>
                    <input type="text" 
                           id="city" 
                           name="city" 
                           placeholder="e.g., Makati City"
                           value="{{ request('city') }}"
                           class="filter-input">
                </div>

                {{-- Province Filter --}}
                <div class="filter-group">
                    <label for="province">Province</label>
                    <input type="text" 
                           id="province" 
                           name="province" 
                           placeholder="e.g., Metro Manila"
                           value="{{ request('province') }}"
                           class="filter-input">
                </div>

                {{-- Region Filter --}}
                <div class="filter-group">
                    <label for="region">Region</label>
                    <select id="region" name="region" class="filter-select">
                        <option value="">All Regions</option>
                        <option value="NCR" {{ request('region') == 'NCR' ? 'selected' : '' }}>NCR - National Capital Region</option>
                        <option value="CAR" {{ request('region') == 'CAR' ? 'selected' : '' }}>CAR - Cordillera Administrative Region</option>
                        <option value="Region I" {{ request('region') == 'Region I' ? 'selected' : '' }}>Region I - Ilocos Region</option>
                        <option value="Region II" {{ request('region') == 'Region II' ? 'selected' : '' }}>Region II - Cagayan Valley</option>
                        <option value="Region III" {{ request('region') == 'Region III' ? 'selected' : '' }}>Region III - Central Luzon</option>
                        <option value="Region IV-A" {{ request('region') == 'Region IV-A' ? 'selected' : '' }}>Region IV-A - CALABARZON</option>
                        <option value="Region IV-B" {{ request('region') == 'Region IV-B' ? 'selected' : '' }}>Region IV-B - MIMAROPA</option>
                        <option value="Region V" {{ request('region') == 'Region V' ? 'selected' : '' }}>Region V - Bicol Region</option>
                        <option value="Region VI" {{ request('region') == 'Region VI' ? 'selected' : '' }}>Region VI - Western Visayas</option>
                        <option value="Region VII" {{ request('region') == 'Region VII' ? 'selected' : '' }}>Region VII - Central Visayas</option>
                        <option value="Region VIII" {{ request('region') == 'Region VIII' ? 'selected' : '' }}>Region VIII - Eastern Visayas</option>
                        <option value="Region IX" {{ request('region') == 'Region IX' ? 'selected' : '' }}>Region IX - Zamboanga Peninsula</option>
                        <option value="Region X" {{ request('region') == 'Region X' ? 'selected' : '' }}>Region X - Northern Mindanao</option>
                        <option value="Region XI" {{ request('region') == 'Region XI' ? 'selected' : '' }}>Region XI - Davao Region</option>
                        <option value="Region XII" {{ request('region') == 'Region XII' ? 'selected' : '' }}>Region XII - SOCCSKSARGEN</option>
                        <option value="Region XIII" {{ request('region') == 'Region XIII' ? 'selected' : '' }}>Region XIII - Caraga</option>
                        <option value="BARMM" {{ request('region') == 'BARMM' ? 'selected' : '' }}>BARMM - Bangsamoro Autonomous Region</option>
                    </select>
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-apply-filters">
                    <i class="fa-solid fa-check"></i> Apply Filters
                </button>
                <a href="{{ route('patient.therapists') }}" class="btn-clear-filters">
                    <i class="fa-solid fa-times"></i> Clear All
                </a>
            </div>
        </form>
    </div>

    {{-- Active Filters Display --}}
    @if(request('city') || request('province') || request('region'))
        <div class="active-filters">
            <span class="filter-label">Active Location Filters:</span>
            
            @if(request('city'))
                <span class="filter-tag">
                    City: {{ request('city') }}
                    <a href="{{ route('patient.therapists', array_merge(request()->except('city'))) }}" class="remove-filter">×</a>
                </span>
            @endif
            
            @if(request('province'))
                <span class="filter-tag">
                    Province: {{ request('province') }}
                    <a href="{{ route('patient.therapists', array_merge(request()->except('province'))) }}" class="remove-filter">×</a>
                </span>
            @endif
            
            @if(request('region'))
                <span class="filter-tag">
                    Region: {{ request('region') }}
                    <a href="{{ route('patient.therapists', array_merge(request()->except('region'))) }}" class="remove-filter">×</a>
                </span>
            @endif
        </div>
    @endif

    {{-- Therapist List --}}
    @if($therapists->count() > 0)
        <div class="therapist-cards-container">

            @foreach($therapists as $therapist)
                <div class="therapist-card">
                    <div class="therapist-logo">
                        @if($therapist->profile_picture)
                            <img src="{{ asset('storage/' .$therapist->profile_picture) }}" alt="{{ $therapist->name }}">
                        @else
                            <img src="{{ asset('images/logo1.png') }}" alt="Default Therapist">
                        @endif
                    </div>

                    @if($therapist->role === 'clinic' && $therapist->clinic_type)
                        <span class="clinic-type-badge {{ $therapist->clinic_type }}">
                            {{ ucfirst($therapist->clinic_type) }}
                        </span>
                    @endif

                    <h3 class="therapist-name">{{ $therapist->name }}</h3>
                    <p class="therapist-role">{{ ucfirst($therapist->role_display) }}</p>

                    <p class="therapist-description">
                        {{ $therapist->description ?? 'A compassionate and dedicated therapist ready to assist you.' }}
                    </p>

                    <p class="therapist-location">
                        <i class="fa-solid fa-location-dot"></i>
                        @if($therapist->city && $therapist->province)
                            {{ $therapist->city }}, {{ $therapist->province }}
                        @else
                            {{ $therapist->address ?? 'Location not specified' }}
                        @endif
                    </p>

                    <div class="card-spacer"></div>
                    
                    <div class="therapist-card-footer">
                        @if($therapist->availability->where('is_active', true)->count() > 0)
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
                            <a href="{{ route('patient.therapists.profile', $therapist->id) }}" class="btn-profile">View Profile</a>
                        </div>
                    </div>

                </div>
            @endforeach

        </div>

        <div class="pagination">
            {{ $therapists->appends(request()->query())->links() }}
        </div>

    @else
        <p class="no-results">No therapists or clinics found matching your criteria.</p>
    @endif

</main>

<script>
function toggleAdvancedFilters() {
    const filters = document.getElementById('advancedFilters');
    if (filters.style.display === 'none' || filters.style.display === '') {
        filters.style.display = 'block';
    } else {
        filters.style.display = 'none';
    }
}
</script>

@endsection