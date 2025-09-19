@extends('layouts.auth')

@section('content')
<div class="container" style="max-width: 500px; margin: 50px auto;">
    <h2>Email Verification</h2>
    <p>Please check your email and enter the 6-digit verification code.</p>

    @if(session('info'))
        <div style="background-color: #fff3cd; padding: 10px; border-radius: 8px; margin-bottom: 15px;">
            {{ session('info') }}
        </div>
    @endif

    @if(session('success'))
        <div style="background-color: #d4edda; padding: 10px; border-radius: 8px; margin-bottom: 15px;">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div style="background-color: #f8d7da; padding: 10px; border-radius: 8px; margin-bottom: 15px;">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <p><strong>Email:</strong> {{ $email }}</p>

    <form method="POST" action="{{ route('verification.confirm') }}">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">
        <div style="margin-bottom: 10px;">
            <label>Verification Code:</label>
            <input type="text" name="verification_code" placeholder="Enter code" required autofocus>
        </div>
        <button type="submit" style="padding: 8px 15px; border-radius: 5px;">Verify</button>
    </form>

    <form method="POST" action="{{ route('verification.resend') }}" style="margin-top: 15px;">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">
        <button type="submit" style="padding: 8px 15px; border-radius: 5px;">Resend Code</button>
    </form>
</div>
@endsection
