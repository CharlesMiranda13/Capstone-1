@extends('layouts.app')

@section('title', $therapist->name . ' - Profile')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/publicprofile.css') }}">


@section('content')
<main class="public-therapist-profile-main">
    <div class="public-profile-section">
        <h2>{{ $therapist->name }}'s Profile</h2>

        <div class="public-profile-card">
            <div class="public-profile-picture">
                @if($therapist->profile_picture)
                    <img src="{{ asset('storage/' . $therapist->profile_picture) }}" alt="Profile Picture">
                @else
                    <img src="{{ asset('images/default-therapist.png') }}" alt="Default Profile Picture">
                @endif
            </div>

            <div class="public-profile-details">
                <h3>{{ $therapist->name }}</h3>
                <p class="public-role">{{ ucfirst($therapist->role) }}</p>
                <p class="public-description">{{ $therapist->description ?? 'A compassionate and dedicated therapist ready to assist you.' }}</p>
                <p class="public-location"><i class="fa-solid fa-location-dot"></i> {{ $therapist->location ?? 'Location not specified' }}</p>
                <p class="public-contact-info"><i class="fa-solid fa-envelope"></i> {{ $therapist->email }}</p>
                <p class="public-contact-info"><i class="fa-solid fa-phone"></i> {{ $therapist->phone ?? 'Phone not specified' }}</p>

                <div class="public-specializations">
                    <strong>Specializations:</strong>
                    @if($therapist->specialization)
                        <ul>
                            @foreach(explode(',', $therapist->specialization) as $spec)
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