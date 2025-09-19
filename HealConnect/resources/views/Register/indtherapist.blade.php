@extends('layouts.app')
@section('title', 'Register - Independent Therapist')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">

@section('content')
<main class="register-main therapist">
    <h1 class="register-title">Independent Therapist Registration</h1>

    <form action="{{ route('register.store', ['type' => 'therapist']) }}" method="POST" class="register-form" enctype="multipart/form-data">
        @csrf

        <label for="Fname">First Name:</label>
        <input type="text" id="Fname" name="Fname" required />

        <label for="Mname">Middle Name (Optional):</label>
        <input type="text" id="Mname" name="Mname" />

        <label for="Lname">Last Name:</label>
        <input type="text" id="Lname" name="Lname" required />

        <label for="dob">Date of Birth:</label>
        <input type="date" id="dob" name="dob" required />

        <label for="Gender">Gender:</label>
        <select id="Gender" name="Gender" required>
            <option value="">Select Gender</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
        </select>

        <label for="address">Address:</label>
        <input type="text" id="address" name="address" required />

        <label for="phone">Phone Number:</label>
        <input type="tel" id="phone" name="phone" required />

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required />

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required />

        <label for="password_confirmation">Confirm Password:</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required />

        <label for="ValidID">Valid ID:</label>
        <input type="file" id="ValidID" name="ValidID" accept=".jpg,.jpeg,.png,.pdf" required />

        <button type="submit" class="register-button">Register</button>

        <p>
            Already have an account? <a href="{{ url('/logandsign') }}">Login here</a>
        </p>
    </form>
</main>
@endsection
