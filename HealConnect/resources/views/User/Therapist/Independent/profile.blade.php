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
        </div>

        {{-- PROFILE CARD --}}
        <section class="profile-card">

            {{-- LEFT: PROFILE INFO --}}
            <div class="profile-left">
                <div class="profile-pic">
                    <img 
                        src="{{ Auth::user()->profile_picture 
                            ? asset('storage/' . Auth::user()->profile_picture) 
                            : asset('images/logo1.png') }}" 
                        alt="{{ Auth::user()->name }}">
                </div>

                <h3>{{ Auth::user()->name }}</h3>

                <div class="role-display">
                    <p class="role">{{ ucfirst(Auth::user()->role_display ?? Auth::user()->role) }}</p>
                </div>

                <div class="bio-section">
                    <p class="bio">
                        {{ Auth::user()->description ?? 'A compassionate and dedicated therapist ready to assist you.' }}
                    </p>
                </div>

                <div class="contact-info">
                    <p><i class="fa-solid fa-location-dot"></i> {{ Auth::user()->address ?? 'Location not specified' }}</p>
                    <p><i class="fa-solid fa-envelope"></i> {{ Auth::user()->email }}</p>
                    <p><i class="fa-solid fa-phone"></i> {{ Auth::user()->phone ?? 'Phone not specified' }}</p>
                    <p><i class="fa-solid fa-briefcase"></i>
                        {{ Auth::user()->experience_years ? round(Auth::user()->experience_years) . ' years experience' : 'Experience not specified' }}
                    </p>
                    <p class="therapist-gender">
                        <i class="fa-solid fa-venus-mars"></i> {{ ucfirst(Auth::user()->gender) ?? 'Not specified' }}
                    </p>
                </div>
            </div>

            {{-- RIGHT SIDE --}}
            <div class="profile-right two-col">

                {{-- SPECIALIZATIONS --}}
                <div class="card-section col-1 row-1">
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
                <div class="card-section col-2 row-1">
                    <h4><i class="fa-solid fa-calendar-days"></i> Availability</h4>
                    @if(isset($availability) && count($availability) > 0)
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
                    @else
                        <p>No availability set.</p>
                    @endif
                </div>

                {{-- SERVICES --}}
                <div class="card-section col-1 row-2">
                    <h4><i class="fa-solid fa-list"></i> Services Offered</h4>
                    @if(!empty($servicesList) && count($servicesList) > 0)
                        <ul class="services-list">
                            @foreach($servicesList as $service)
                                <li>{{ ucfirst(trim($service)) }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p>No services added yet.</p>
                    @endif
                </div>

                {{-- FEES --}}
                <div class="card-section col-2 row-2">
                    <h4><i class="fa-solid fa-money-bill"></i> Fees</h4>
                    @if(!empty($price))
                        <ul class="fees-list">
                            <li>{{ $price }}</li>
                        </ul>
                    @else
                        <p>Fees not specified yet.</p>
                    @endif
                </div>

            </div>
        </section>
    </div>
</main>
@endsection