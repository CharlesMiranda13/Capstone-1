@extends('layouts.app')

@section('title', 'HealConnect - Therapists & Clinics')

@section('content')
<main class="Therapist-main">
    <h1>List of Verified Therapists & Clinics</h1>

    <form method="GET" action="{{ route('patient.therapists') }}" class="search-filter-bar">
        <input type="text" name="search" value="{{ request('search') }}" 
        placeholder="Search by name, ID, specialization, or role..."class="search-input">
        
        <button type="submit" class="search-btn">Search</button>
    </form>

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
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $therapists->links() }}
        </div>
    @else
        <p style="text-align: center;">No verified therapists or clinics registered at the moment.</p>
    @endif
</main>
@endsection
