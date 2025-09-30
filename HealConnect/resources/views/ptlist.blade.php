@extends('layouts.app')

@section('title', 'HealConnect - Therapists & Clinics')

@section('content')
<main class="Therapist-main">
    <h1 style="text-align: center;">List of Verified Therapists & Clinics</h1>

    @if($therapists->count() > 0)
        <div class="therapist-list">
            @foreach($therapists as $therapist)
                <div class="therapist-card">
                    <h3>{{ $therapist->name }}</h3>
                    <p><strong>Email:</strong> {{ $therapist->email }}</p>
                    <p><strong>Role:</strong> {{ ucfirst($therapist->role) }}</p>
                </div>
            @endforeach
        </div>
    @else
        <p style="text-align: center;">No verified therapists or clinics available at the moment.</p>
    @endif
</main>
@endsection
