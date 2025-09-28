@extends('layouts.app')
@section('title', 'Register - Patient Register')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
@section('endsection')

@section('content')
<main class="register-main">
    <h1 style="text-align: center; font-size: 30px;">Patient Registration</h1>

    <form action="{{ route('register.store', ['type' => 'patient']) }}" method="POST" class="register-form" enctype="multipart/form-data">
        @csrf

        <div class="name-group">
            <div>
                <label for="Fname">First Name:</label>
                <input type="text" id="Fname" name="Fname" required />
            </div>
            <div>
                <label for="Mname">Middle Name:</label>
                <input type="text" id="Mname" name="Mname"/>
            </div>
        </div>

        <label for="Lname">Last Name:</label>
        <input type="text" id="Lname" name="Lname" required />

        <label for="address">Current Address:</label>
        <input type="text" id="address" name="address" required />

        <label for="phone">Phone Number:</label>
        <input type="tel" id="phone" name="phone" required />

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required />
            @error('email')
                <small style="color:red;">{{ $message }}</small>
            @enderror

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required />

        <label for="password_confirmation">Confirm Password:</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required />
            @error('password')
                <small style="color:red;">{{ $message }}</small>
            @enderror

        <label for="dob">Date of Birth:</label>
        <input type="date" id="dob" name="dob" required />

        <label for="Gender">Sex:</label>
        <select id="Gender-select" name="Gender" required>
            <option value="">--Please choose an option--</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
        </select>

        <label for="ValidID">Valid ID:</label>
        <input type="file" id="ValidID" name="ValidID" accept=".jpg, .jpeg, .png, .pdf" required />
            @error('ValidID')
                <small style="color:red;">{{ $message }}</small>
            @enderror

        <small style="font-size: 12px; color: gray;">
            By uploading your license, you agree that HealConnect will use this document solely for verifying your credentials. 
            Your information will be kept secure and will not be shared without your consent.
        </small>

        <button type="submit" class="register-button">Register</button>

        <p style="text-align: center; font-size: 15px;">
            Do you have an account?
            <a href="{{ url('/logandsign') }}" class="login-link">Login here</a>
        </p>
    </form>
</main>
@endsection
