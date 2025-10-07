@extends('layouts.patient_layout')

@section('title', 'List of Therapists & Clinics')
@section('styles')
<link rel="stylesheet" href="{{ asset('css/patient.css') }}">
@endsection

@section('content')
<div class="ptlist">
    <h2>Available Therapists & Clinics</h2>
    <ul>
        @forelse($therapists as $therapist)
            <li>
                <p><strong>Therapist/Clinic Name:</strong> {{ $therapist->name }}</p>
                <p><strong>Therapist/Clinic Email:</strong> {{ $therapist->email }}</p> 
                <p><strong>Therapist/Clinic Specialization:</strong> {{ $therapist->specialization }}</p>
                <span>{{ ucfirst($therapist->role) }}</span>
            </li>
        @empty
            <li style="text-align:center;">No therapists or clinics available.</li>
        @endforelse
    </ul>
</div>
@endsection
