@extends('layouts.patient_layout')

@section('title', 'List of Therapists & Clinics')

@section('content')
    <h2 style ="text-align: center">Available Therapists & Clinics</h2>

    <ul>
    @forelse($therapists as $therapist)
        <li>
            {{ $therapist->name }}
            <span style="color: gray;">({{ ucfirst($therapist->role) }})</span>
        </li>
    @empty
        <li style ="text-align:center"> No therapists or clinics available.</li>
    @endforelse
    </ul>
@endsection
