@extends('layouts.app')
@section('title', 'Register - Clinic Therapist')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
@section('endsection')

@section('content')
<main class="register-main clinic">
    <h1 class="register-title">Clinic Registration</h1>

    <form action="{{ route('register.store', ['type' => 'clinic']) }}" method="POST" class="register-form" enctype="multipart/form-data">
        @csrf

        <label for="ClinicName">Clinic Name:</label>
        <input type="text" id="ClinicName" name="ClinicName" required />

        <label for="address">Clinic Address:</label>
        <input type="text" id="address" name="address" required />

        <label for="phone">Phone Number:</label>
        <input type="tel" id="phone" name="phone" required />

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required />

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required />

        <label for="confirm-password">Confirm Password:</label>
        <input type="password" id="confirm-password" name="confirm-password" required />

        <label for="operating_hours">Operating Hours:</label>
        <input type="text" id="operating_hours" name="operating_hours" placeholder="e.g., Mon-Fri 9AM - 6PM" required />

        <label for="specialization">Specializations / Services:</label>
        <textarea id="specialization" name="specialization" rows="3" placeholder="e.g., Sports Rehab, Pediatric PT"></textarea>


        <!-- File Upload Section -->
        <div class="file-section">
            <div class="file-group">
                <div>
                    <label for="ValidID">Valid ID (Owner/Representative):</label>
                    <input type="file" id="ValidID" name="ValidID" accept=".jpg, .jpeg, .png, .pdf" required />
                </div>
                <div>
                    <label for="License">Clinic License/DOH Accreditation:</label>
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
