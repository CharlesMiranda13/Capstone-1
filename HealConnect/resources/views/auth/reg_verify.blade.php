@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Email Verification</h2>
    <p>Please check your email and enter the 6-digit verification code.</p>

    @if(session('email'))
        <p><strong>Email:</strong> {{ session('email') }}</p>
    @endif

    @if ($errors->any())
        <div style="color: red;">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    @if(session('success'))
        <div style="color: green;">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('verification.confirm') }}">
        @csrf
        <input type="hidden" name="email" value="{{ session('email') }}">
        <div>
            <label>Verification Code:</label>
            <input type="text" name="verification_code" required>
        </div>
        <button type="submit">Verify</button>
    </form>

    <form method="POST" action="{{ route('verification.resend') }}">
        @csrf
        <input type="hidden" name="email" value="{{ session('email') }}">
        <button type="submit">Resend Code</button>
    </form>
</div>
@endsection
