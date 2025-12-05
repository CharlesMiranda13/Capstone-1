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

        <div class="form-row">
            <div class="form-col">
                <label for="street">Street Address:</label>
                <input type="text" id="street" name="street" required 
                    placeholder="House No., Street Name" 
                    value="{{ old('street') }}" />
                @error('street')
                    <small style="color:red;">{{ $message }}</small>
                @enderror
            </div>
            <div class="form-col">
                <label for="barangay">Barangay:</label>
                <input type="text" id="barangay" name="barangay" required 
                    value="{{ old('barangay') }}" />
                @error('barangay')
                    <small style="color:red;">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-col">
                <label for="city">City/Municipality:</label>
                <input type="text" id="city" name="city" required 
                    value="{{ old('city') }}" />
                @error('city')
                    <small style="color:red;">{{ $message }}</small>
                @enderror
            </div>
            <div class="form-col">
                <label for="province">Province:</label>
                <input type="text" id="province" name="province" required 
                    value="{{ old('province') }}" />
                @error('province')
                    <small style="color:red;">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-col">
                <label for="postal_code">Postal/ZIP Code:</label>
                <input type="text" id="postal_code" name="postal_code" 
                    pattern="^\d{4}$" 
                    maxlength="4" 
                    placeholder="1234"
                    value="{{ old('postal_code') }}" />
                @error('postal_code')
                    <small style="color:red;">{{ $message }}</small>
                @enderror
            </div>
            <div class="form-col">
                <label for="region">Region:</label>
                <select id="region" name="region" required>
                    <option value="">--Select Region--</option>
                    <option value="NCR" {{ old('region') == 'NCR' ? 'selected' : '' }}>NCR - National Capital Region</option>
                    <option value="CAR" {{ old('region') == 'CAR' ? 'selected' : '' }}>CAR - Cordillera Administrative Region</option>
                    <option value="Region I" {{ old('region') == 'Region I' ? 'selected' : '' }}>Region I - Ilocos Region</option>
                    <option value="Region II" {{ old('region') == 'Region II' ? 'selected' : '' }}>Region II - Cagayan Valley</option>
                    <option value="Region III" {{ old('region') == 'Region III' ? 'selected' : '' }}>Region III - Central Luzon</option>
                    <option value="Region IV-A" {{ old('region') == 'Region IV-A' ? 'selected' : '' }}>Region IV-A - CALABARZON</option>
                    <option value="Region IV-B" {{ old('region') == 'Region IV-B' ? 'selected' : '' }}>Region IV-B - MIMAROPA</option>
                    <option value="Region V" {{ old('region') == 'Region V' ? 'selected' : '' }}>Region V - Bicol Region</option>
                    <option value="Region VI" {{ old('region') == 'Region VI' ? 'selected' : '' }}>Region VI - Western Visayas</option>
                    <option value="Region VII" {{ old('region') == 'Region VII' ? 'selected' : '' }}>Region VII - Central Visayas</option>
                    <option value="Region VIII" {{ old('region') == 'Region VIII' ? 'selected' : '' }}>Region VIII - Eastern Visayas</option>
                    <option value="Region IX" {{ old('region') == 'Region IX' ? 'selected' : '' }}>Region IX - Zamboanga Peninsula</option>
                    <option value="Region X" {{ old('region') == 'Region X' ? 'selected' : '' }}>Region X - Northern Mindanao</option>
                    <option value="Region XI" {{ old('region') == 'Region XI' ? 'selected' : '' }}>Region XI - Davao Region</option>
                    <option value="Region XII" {{ old('region') == 'Region XII' ? 'selected' : '' }}>Region XII - SOCCSKSARGEN</option>
                    <option value="Region XIII" {{ old('region') == 'Region XIII' ? 'selected' : '' }}>Region XIII - Caraga</option>
                    <option value="BARMM" {{ old('region') == 'BARMM' ? 'selected' : '' }}>BARMM - Bangsamoro Autonomous Region</option>
                </select>
                @error('region')
                    <small style="color:red;">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <small style="font-size: 12px; color: gray; display: block; margin-bottom: 15px;">
            Please provide your complete and accurate address for verification purposes.
        </small>

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
            <label for="start_year" class="form-label">Year Started Practicing</label>
            <input type="number" id="start_year" name="start_year" min="1900" max="{{ date('Y') }}" required>
        </div>

        <div class="file-section">
            <div class="file-group">
                <div>
                    <label for="ValidIDFront">  Valid ID (Front Side):</label>
                    <input type="file" id="ValidIDFront" name="ValidIDFront" accept=".jpg, .jpeg, .png, .pdf" required />
                    @error('ValidIDFront')
                        <small style="color:red;">{{ $message }}</small>    
                    @enderror
                </div>
                <div>
                    <label for="ValidIDBack">Valid ID (Back Side):</label>
                    <input type="file" id="ValidIDBack" name="ValidIDBack" accept=".jpg, .jpeg, .png, .pdf" required />
                    @error('ValidIDBack')
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
