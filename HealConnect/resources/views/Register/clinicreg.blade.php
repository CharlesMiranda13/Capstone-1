@extends('layouts.app')
@section('title', 'Register - Clinic')
@section('styles')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">


@section('content')
<main class="register-main clinic">
    <h1 class="register-title">Clinic Registration</h1>

    {{-- Display Validation Errors --}}
    @if ($errors->any())
    <div style="background-color: #f8d7da; padding: 10px; border-radius: 8px; margin-bottom: 15px;">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('register.store', ['type' => 'clinic']) }}" method="POST" class="register-form" enctype="multipart/form-data">
        @csrf

        <label for="Fname">Clinic Name:</label>
        <input type="text" id="Fname" name="Fname" required value="{{ old('Fname') }}"/>

        <label for="address">Clinic Address:</label>
        <input type="text" id="address" name="address" required value="{{ old('address') }}"/>

        <label for="phone">Phone Number:</label>
        <input type="tel" id="phone" name="phone" required value="{{ old('phone') }}"/>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required value="{{ old('email') }}"/>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required />

        <label for="password_confirmation">Confirm Password:</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required />

        <label for="operating_hours">Operating Hours:</label>
        <input type="text" id="operating_hours" name="operating_hours" placeholder="Mon-Fri 9AM - 6PM" value="{{ old('operating_hours') }}" />

        <label for="specialization">Specializations / Services:</label>
        <textarea id="specialization" name="specialization" rows="3">{{ old('specialization') }}</textarea>

        {{-- File Upload Section --}}
        <div class="file-section">
            <div class="file-group">
                <div>
                    <label for="ValidID">Valid ID (Owner/Representative):</label>
                    <input type="file" id="ValidID" name="ValidID" accept=".jpg, .jpeg, .png, .pdf" required />
                </div>
                <div>
                    <label for="License">Clinic License / DOH Accreditation:</label>
                    <input type="file" id="License" name="License" accept=".jpg, .jpeg, .png, .pdf" required />
                </div>
            </div>
        </div>

        <small style="font-size: 12px; color: gray;">
            By uploading your license, you agree that HealConnect will use this document solely for verifying your credentials. 
            Your information will be kept secure and will not be shared without your consent.
        </small>

        <button type="submit" class="register-button">Register</button>

        <p>
            Already have an account? <a href="{{ url('/logandsign') }}">Login here</a>
        </p>
    </form>
</main>
@endsection
