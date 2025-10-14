@extends('layouts.therapist')

@section('title', 'Therapist Profile')
@section('styles')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection
@section('content')
<main class="therapist-profile-main">
    <div class="profile-section">
        <h2>Your Profile</h2>

        <div class="profile-card">
            <div class="profile-picture">
                @if(Auth::user()->profile_picture)
                    <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile Picture">
                @else
                    <img src="{{ asset('images/default-therapist.png') }}" alt="Default Profile Picture">
                @endif
            </div>

            <div class="profile-details">
                <h3>{{ Auth::user()->name }}</h3>
                <p class="role">{{ ucfirst(Auth::user()->role) }}</p>
                <p class="description">{{ Auth::user()->description ?? 'A compassionate and dedicated therapist ready to assist you.' }}</p>
                <p class="location"><i class="fa-solid fa-location-dot"></i> {{ Auth::user()->location ?? 'Location not specified' }}</p>
                <p class="contact-info"><i class="fa-solid fa-envelope"></i> {{ Auth::user()->email }}</p>
                <p class="contact-info"><i class="fa-solid fa-phone"></i> {{ Auth::user()->phone ?? 'Phone not specified' }}</p>

                <div class="specializations">
                    <strong>Specializations:</strong>
                    @if(Auth::user()->specialization)
                        <ul>
                            @foreach(explode(',', Auth::user()->specialization) as $spec)
                                <li>{{ trim($spec) }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p>N/A</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</main>
@endsection