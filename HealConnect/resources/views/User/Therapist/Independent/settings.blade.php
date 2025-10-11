@extends('layouts.therapist')

@section('title', 'Settings')
@section('styles')
<link rel="stylesheet" href="{{ asset('css/settings.css') }}">
@endsection

@section('content')
<main class="settings-main">
    <div class="settings-content">
        <h2 class="settings-title">Account Settings</h2>

        <div class="scard">
            <h4 class="section-title">Profile Picture</h4>
            <form action="{{ route('therapist.update.profile') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="profile-upload">
                    <img src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : asset('images/default-avatar.png') }}" alt="Profile Picture"
                         class="profile-image" 
                         style="width: 100px; height: 100px;">

                    <div class="upload-controls">
                        <input type="file" name="profile_picture" accept="image/*" required>
                        @error('profile_picture')
                            <small class="error-message">{{ $message }}</small>
                        @enderror
                        <button type="submit" class="submit-button primary-button">Upload</button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Update Personal Information --}}
        <div class="scard">
            <h4 class="section-title">Personal Information</h4>
            <form action="{{ route('therapist.update.info') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', Auth::user()->name) }}" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" name="phone" id="phone" value="{{ old('phone', Auth::user()->phone) }}" 
                           pattern="^(09\d{9}|\+639\d{9})$" placeholder="09XXXXXXXXX or +639XXXXXXXXX" required>
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" name="address" id="address" value="{{ old('address', Auth::user()->address) }}" required>
                </div>

                <button type="submit" class="submit-button success-button">Save Changes</button>
            </form>
        </div>

        {{-- Change Password --}}
        <div class="scard">
            <h4 class="section-title">Change Password</h4>
            <form action="{{ route('therapist.update.password') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" name="current_password" id="current_password" required>
                </div>

                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" name="new_password" id="new_password" required>
                </div>

                <div class="form-group">
                    <label for="new_password_confirmation">Confirm New Password</label>
                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" required>
                </div>

                <button type="submit" class="submit-button warning-button">Update Password</button>
            </form>
        </div>
    </div>
</main>
@endsection