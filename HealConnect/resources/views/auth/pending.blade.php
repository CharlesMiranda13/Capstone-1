@extends('layouts.pendingpage')

@section('content')
    <div class="pending-message">
        <div class = "pending-card">
            <h2>Account Status: Pending</h2>
            <p>Thank you for registering! Our admin team will review your account shortly.</p>

        @if(Auth::check())
            <p>Logged in as: <strong>{{ Auth::user()->name }}</strong></p>
        @endif
    </div>
@endsection
