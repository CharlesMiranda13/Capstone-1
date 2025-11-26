@extends('layouts.app')
@section('title', 'Register - Patient-Register')
@section('styles') 
<link rel="stylesheet" href="{{ asset('css/register.css') }}">

@section('content')
<main class="register-main patient">
    <h1 class="register-title">Patient Registration</h1>

    <form action="{{ route('register.store', ['type' => 'patient']) }}" method="POST" class="register-form" enctype="multipart/form-data">
        @csrf

        <div class="form-row">
            <div class="form-col">
                <label for="Fname">First Name:</label>
                <input type="text" id="Fname" name="Fname" required />
            </div>
            <div class="form-col">
                <label for="Mname">Middle Name:</label>
                <input type="text" id="Mname" name="Mname"/>
            </div>
        </div>

        <label for="Lname" style ="font-weight: 600;">Last Name:</label>
        <input type="text" id="Lname" name="Lname" required />

        <div class="form-row">
            <div class="form-col">
                <label for="dob">Date of Birth:</label>
                <input type="date" id="dob" name="dob" required max="{{ date('Y-m-d')}}" min= "{{ date('Y-m-d', strtotime('-120 years'))}}" />
            </div>
            <div class="form-col">
                <label for="Gender">Gender:</label>
                <select id="Gender" name="Gender" required>
                    <option value="">--Please choose an option--</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
        </div>

        <label for="address" style ="font-weight: 600;">Address:</label>
        <input type="text" id="address" name="address" required />

        <label for="phone" style ="font-weight: 600;">Phone Number:</label>
        <input type="tel" id="phone" name="phone" 
            pattern="^09\d{9}$" 
            maxlength="11"
            required 
            placeholder="09XXXXXXXXX" />

        <label for="email" style ="font-weight: 600;">Email:</label>
        <input type="email" id="email" name="email" required />
            @error('email')
                <small style="color:red;">{{ $message }}</small>
            @enderror

        <label for="password" style ="font-weight: 600;">Password:</label>
        <input type="password" id="password" name="password" required />

        <label for="password_confirmation" style ="font-weight: 600;">Confirm Password:</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required />
            @error('password')
                <small style="color:red;">{{ $message }}</small>
            @enderror

        <div class="file-section">
            <div class="file-group">
                <!-- Front ID Upload -->
                <div>
                    <label for="ValidID">Valid ID (Front Side):</label>
                    <input type="file" id="ValidIDFront" name="ValidIDFront" accept=".jpg, .jpeg, .png, .pdf" required />
                    @error('ValidIDFront')
                        <small style="color:red;">{{ $message }}</small>    
                    @enderror
                </div>
                <!-- Back ID Upload -->
                <div>
                    <label for="ValidIDBack">Valid ID (Back Side):</label>
                    <input type="file" id="ValidIDBack" name="ValidIDBack" accept=".jpg, .jpeg, .png, .pdf" required />
                    @error('ValidIDBack')
                        <small style="color:red;">{{ $message }}</small>    
                    @enderror
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
