@extends('layouts.app')

@section('title', 'Reset Password - HealConnect')

@section('content')
<link rel="stylesheet" href="{{ asset('css/Logandreg.css') }}">

<main>

    <div class="tab-container" style="max-width: 500px; margin-top: 120px;">
        <div class="tab-header">
            <button class="tab-btn active" style="cursor: default;">Reset Password</button>
        </div>

        <div class="tab-content active">
            <div class="login-container" style="margin: 20px auto; padding: 20px 20px 10px;">
                <h2 style="margin-bottom: 18px;">Create New Password</h2>

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ request()->email }}">

                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input 
                            id="password" 
                            type="password" 
                            name="password" 
                            required 
                            placeholder="Enter new password">
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirm New Password</label>
                        <input 
                            id="password_confirmation" 
                            type="password" 
                            name="password_confirmation" 
                            required 
                            placeholder="Re-type new password">
                    </div>

                    <button type="submit" style="margin-top: 10px;">Reset Password</button>

                    @if ($errors->any())
                        <div class="alert alert-danger" style="margin-top:10px;">
                            {{ $errors->first() }}
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

</main>
@endsection
