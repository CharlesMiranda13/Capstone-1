@extends('layouts.patient_layout')

@section('title', 'List of Therapists & Clinics')

@section('content')
    <h2>Available Therapists & Clinics</h2>

    <ul>
    @forelse($therapists as $therapist)
        <li>
            {{ $therapist->name }}
            <span style="color: gray;">({{ ucfirst($therapist->role) }})</span>
        </li>
    @empty
        <li>No therapists or clinics available.</li>
    @endforelse
    </ul>
@endsection
