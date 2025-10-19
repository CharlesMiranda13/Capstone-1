@extends('layouts.app')
@section('title', 'Register - Independent Therapist')
@section('styles') 
<link rel="stylesheet" href="{{ asset('css/register.css') }}">

@section('content')
<main class="register-main therapist">
    <h1 class="register-title">Independent Therapist Registration</h1>

    <form action="{{ route('register.store', ['type' => 'therapist']) }}" method="POST" class="register-form" enctype="multipart/form-data">
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

        <label for="Lname">Last Name:</label>
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

        <label for="address">Address:</label>
        <input type="text" id="address" name="address" required />

        <label for="phone">Phone Number:</label>
        <input type="tel" id="phone" name="phone" 
            pattern="^09\d{9}$" 
            maxlength="11"
            required 
            placeholder="09XXXXXXXXX" />

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required />

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required />

        <label for="password_confirmation">Confirm Password:</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required />
        @error('password')
            <small style="color:red;">{{ $message }}</small>
        @enderror

        <div class="form-group">
            <label class="form-label">Areas of Specialization</label>
            <div class="checkbox-group">
                <label><input type="checkbox" name="specialization[]" value="Orthopedic"> Orthopedic Rehabilitation</label>
                <label><input type="checkbox" name="specialization[]" value="Neurological"> Neurological Rehabilitation</label>
                <label><input type="checkbox" name="specialization[]" value="Pediatric"> Pediatric Therapy</label>
                <label><input type="checkbox" name="specialization[]" value="Sports Therapy"> Sports Therapy</label>
                <label><input type="checkbox" name="specialization[]" value="Geriatric"> Geriatric Therapy</label>
                <label><input type="checkbox" name="specialization[]" value="Cardiopulmonary"> Cardiopulmonary Rehabilitation</label>
                <label><input type="checkbox" name="specialization[]" value="WomenHealth"> Women's Health</label>
                <label><input type="checkbox" name="specialization[]" value="Other"> Other</label>
            </div>
            <small class="mess">You may select more than one specialization.</small>
        </div>

        <div class="form-group">
            <label for="experience_years" class="form-label">Years of Experience</label>
            <input type="number" id="experience_years" name="experience_years" min="0" max="50" placeholder="e.g., 5" required>
        </div>

        <div class="file-section">
            <div class="file-group">
                <div>
                    <label for="ValidID">Valid ID:</label>
                    <input type="file" id="ValidID" name="ValidID" accept=".jpg, .jpeg, .png, .pdf" required />
                    @error('ValidID')
                        <small style="color:red;">{{ $message }}</small>    
                    @enderror
                </div>
                <div>
                    <label for="license">License: </label>
                    <input type="file" id="license" name="license" accept=".jpg, .jpeg, .png, .pdf" required />
                    @error('License')
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
