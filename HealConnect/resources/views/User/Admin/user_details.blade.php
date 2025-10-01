@extends('layouts.admin')

@section('title', 'User Details')

@section('content')
    <h2>User Details</h2>

    <div class="user-card">
        <p><strong>ID:</strong> {{ $user->id }}</p>
        <p><strong>Name:</strong> {{ $user->name }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Role:</strong> {{ ucfirst($user->role) }}</p>
        <p><strong>Status:</strong> {{ $user->status ?? 'Pending' }}</p>
        <p><strong>Created At:</strong> {{ $user->created_at->format('M d, Y') }}</p>

     
        @if ($user->profile_details)
            <p><strong>Profile Details:</strong> {{ $user->profile_details }}</p>
        @endif

        {{--  Uploaded Credentials --}}
        @if ($user->valid_id_path)
            <p><strong>Submitted Credentials:</strong></p>
            <a href="{{ asset('storage/' . $user->valid_id_path) }}" target="_blank" class="btn btn-primary">
                View Valid ID
            </a>
        @else
            <p><em>No credentials submitted.</em></p>
        @endif
    </div>
    
@endsection
