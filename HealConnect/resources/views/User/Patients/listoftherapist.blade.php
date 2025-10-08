@extends('layouts.patient_layout')

@section('title', 'List of Therapists & Clinics')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/tts.css') }}">
@endsection

@section('content')
<div class="ptlist">
    <h2 class="therapist-title">Available Therapists & Clinics</h2>

    {{-- Search Bar --}}
    <form method="GET" action="{{ route('patient.therapists') }}" class="search-filter-bar">
        <input 
            type="text" 
            name="search" 
            value="{{ request('search') }}" 
            placeholder="Search by name, ID, specialization, or role..." 
            class="search-input">
        <button type="submit" class="search-btn">Search</button>
    </form>

    {{-- Therapist Table --}}
    @if($therapists->count() > 0)
        <div class="therapist-table-container">
            <table class="therapist-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Specialization</th>
                        <th>Years of Experience</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($therapists as $therapist)
                        <tr>
                            <td>{{ $therapist->name }}</td>
                            <td>{{ $therapist->email }}</td>
                            <td>{{ ucfirst($therapist->role) }}</td>
                            <td>{{ ucfirst($therapist->specialization) }}</td>
                            <td>{{ $therapist->experience_years }}</td>
                            <td>
                                @if($patientHasApprovedReferral)
                                    <a href="{{ route('patient.book', $therapist->id) }}" class="btn-book">Book Appointment</a>
                                @else
                                    <a href="{{ route('patient.referral.upload') }}" class="btn-upload">
                                        Upload Referral First
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $therapists->links() }}
        </div>
    @else
        <p class="no-results">No therapists or clinics available.</p>
    @endif
</div>
@endsection
