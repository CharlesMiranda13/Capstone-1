@extends('layouts.therapist')

@section('title', 'Clients')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/client.css') }}">
@endsection

@section('content')
<main class="therapist-patients">
    <div class="container">
        <div class="patients-header">
            <h2>My Patients</h2>    
        </div>
        
        {{-- Search & Filter --}}
        <div class="search-filter">
            <form method="GET" action="{{ route('therapist.client') }}" class="search-filter-form">
                <input type="text" name="search" placeholder="Search by patient name ..."
                    value="{{ request('search') }}" class="search-input">

                <select name="gender" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Genders</option>
                    <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female</option>
                </select>

                <button type="submit" class="btn-search">
                    <i class="fa fa-search"></i>
                </button>
            </form>
        </div>

        @if($patients->count() > 0)
            <table class="patient-table" id="tableView">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Records</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($patients as $patient)
                        <tr>
                            <td class="name">
                                @if($patient->profile_picture)
                                    <img src="{{ asset('storage/' . $patient->profile_picture) }}" alt="{{ $patient->name }}">
                                @else
                                    <img src="{{ asset('images/default-patient.png') }}" alt="Default Patient">
                                @endif
                                {{ $patient->name }}
                            </td>
                            <td>{{ $patient->email }}</td>
                            <td>{{ $patient->phone ?? 'Not specified' }}</td>
                            <td>
                                <a href="{{ route('therapist.patients_records', ['patientId' => $patient->id]) }}" class="btn-view-records">
                                    Medical Records
                                </a>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>You currently have no patients.</p>
        @endif
    </div>
</main>
@endsection
