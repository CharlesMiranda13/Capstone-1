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
            <div class="view-toggle">
                <button id="tableViewBtn" class="active">Table View</button>
                <button id="cardViewBtn">Card View</button>
            </div>
        </div>

        @if($patients->count() > 0)
            <table class="patient-table" id="tableView">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Email</th>
                        <th>Phone</th>
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

                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="patient-cards-container hidden" id="cardView">
                @foreach($patients as $patient)
                    <div class="patient-card">
                        <div class="patient-pic">
                            @if($patient->profile_picture)
                                <img src="{{ asset('storage/' . $patient->profile_picture) }}" alt="{{ $patient->name }}">
                            @else
                                <img src="{{ asset('images/default-patient.png') }}" alt="Default Patient">
                            @endif
                        </div>

                        <h3>{{ $patient->name }}</h3>
                        <p class="email"><i class="fa-solid fa-envelope"></i> {{ $patient->email }}</p>
                        <p class="phone"><i class="fa-solid fa-phone"></i> {{ $patient->phone ?? 'Phone not specified' }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <p>You currently have no patients.</p>
        @endif
    </div>
</main>
@endsection
