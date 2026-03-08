@extends('layouts.pendingpage')

@section('content')
    <div class="pending-message">
        <div class = "pending-card">
            <h1>Account Status:</h1> 
            @if(Auth::check() && (Auth::user()->status === 'Expired' || (Auth::user()->role !== 'admin' && !Auth::user()->canAccessSystem())))
                <h2>Account Restriction: Expired</h2>
                <p>Your business permit has expired. To maintain a safe and verified environment for patients, your account features have been temporarily restricted.</p>
                @php
                    $role = Auth::user()->role;
                    $settingsUrl = match($role) {
                        'clinic'     => route('clinic.settings'),
                        'therapist'  => route('therapist.settings'),
                        'patient'    => route('patient.settings'),
                        default      => url('/'),
                    };
                @endphp
                <p>Please upload your updated business permit in the <a href="{{ $settingsUrl }}" style="color: #4f46e5; font-weight: 600;">Settings</a> to restore full access.</p>
            @elseif(Auth::check() && Auth::user()->status === 'Re-verification Pending')
                <h2>Verification in Progress</h2>
                <p>Thank you for updating your documents! Our admin team is currently reviewing your new business permit/license.</p>
                <p>You will be able to access your full dashboard once the verification is complete.</p>
            @else
                <h2>Pending Approval</h2>
                <p>Thank you for registering! Our admin team will review your account shortly.</p>
                <p>You will receive an email notification once your account has been approved.</p>
            @endif

        @if(Auth::check())
            <p>Logged in as: <strong>{{ Auth::user()->name }}</strong></p>
        @endif
    </div>
    
    <div class="pending-home-btn">
        <a href="{{ url('/') }}" class="home-btn">Return to Home</a>
    </div>
    
    
@endsection
