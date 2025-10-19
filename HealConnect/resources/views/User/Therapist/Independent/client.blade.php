@extends('layouts.therapist')

@section('title', 'Clients')


@section('content')
<main class="therapist-clients">
    <div class="container">
        <h2>My Clients</h2>

        @if($clients->count() > 0)
            <div class="client-cards-container">
                @foreach($clients as $client)
                    <div class="client-card">
                        <div class="client-pic">
                            @if($client->profile_picture)
                                <img src="{{ asset('storage/' . $client->profile_picture) }}" alt="{{ $client->name }}">
                            @else
                                <img src="{{ asset('images/default-client.png') }}" alt="Default Client">
                            @endif
                        </div>

                        <h3>{{ $client->name }}</h3>
                        <p class="email"><i class="fa-solid fa-envelope"></i> {{ $client->email }}</p>
                        <p class="phone"><i class="fa-solid fa-phone"></i> {{ $client->phone ?? 'Phone not specified' }}</p>

                        <a href="{{ route('therapist.clients.show', $client->id) }}" class="btn btn-primary">
                            View Profile
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p>You currently have no clients.</p>
        @endif
    </div>