@extends('layouts.pendingpage')

@section('content')
    <div class="pending-message">
        <div class = "pending-card">
            <h1>Account Status:</h1> 
            <h2>Pending</h2>
            <p>Thank you for registering! Our admin team will review your account shortly.</p>
            <p>You will receive an email notification once your account has been approved.</p>

        @if(Auth::check())
            <p>Logged in as: <strong>{{ Auth::user()->name }}</strong></p>
        @endif
    </div>
@endsection
