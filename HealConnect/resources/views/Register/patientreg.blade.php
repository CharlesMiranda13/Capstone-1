@extends('layouts.app')
@section('title', 'Register - Patient-Register')
@section('styles') 
<link rel="stylesheet" href="{{ asset('css/register.css') }}">

@section('content')
<main class="register-main patient">
    <h1 class="register-title">Patient Registration</h1>

    {{-- Display ALL Validation Errors --}}
    @if ($errors->any())
    <div class="alert alert-danger">
        <strong>Please correct the following errors:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('register.store', ['type' => 'patient']) }}" method="POST" class="register-form" enctype="multipart/form-data" novalidate>
        @csrf

        <div class="form-row">
            <div class="form-col">
                <label for="Fname">First Name:</label>
                <input type="text" id="Fname" name="Fname" value="{{ old('Fname') }}" required />
                @error('Fname') <small class="field-error">{{ $message }}</small> @enderror
            </div>

            <div class="form-col">
                <label for="Mname">Middle Name:</label>
                <input type="text" id="Mname" name="Mname" value="{{ old('Mname') }}" />
            </div>
        </div>

        <label for="Lname">Last Name:</label>
        <input type="text" id="Lname" name="Lname" value="{{ old('Lname') }}" required />
        @error('Lname') <small class="field-error">{{ $message }}</small> @enderror

        <div class="form-row">
            <div class="form-col">
                <label for="dob">Date of Birth:</label>
                <input type="date" id="dob" name="dob" value="{{ old('dob') }}" required 
                       max="{{ date('Y-m-d')}}" min="{{ date('Y-m-d', strtotime('-120 years'))}}" />
                @error('dob') <small class="field-error">{{ $message }}</small> @enderror
            </div>

            <div class="form-col">
                <label for="Gender">Gender:</label>
                <select id="Gender" name="Gender" required>
                    <option value="">--Please choose an option--</option>
                    <option value="Male" {{ old('Gender')=='Male'?'selected':'' }}>Male</option>
                    <option value="Female" {{ old('Gender')=='Female'?'selected':'' }}>Female</option>
                </select>
                @error('Gender') <small class="field-error">{{ $message }}</small> @enderror
            </div>
        </div>

        {{-- Address --}}
        <div class="form-row">
            <div class="form-col">
                <label for="street">Street Address:</label>
                <input type="text" id="street" name="street" value="{{ old('street') }}" required />
                @error('street') <small class="field-error">{{ $message }}</small> @enderror
            </div>

            <div class="form-col">
                <label for="barangay">Barangay:</label>
                <input type="text" id="barangay" name="barangay" value="{{ old('barangay') }}" required />
                @error('barangay') <small class="field-error">{{ $message }}</small> @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-col">
                <label for="city">City:</label>
                <input type="text" id="city" name="city" value="{{ old('city') }}" required />
                @error('city') <small class="field-error">{{ $message }}</small> @enderror
            </div>

            <div class="form-col">
                <label for="province">Province:</label>
                <input type="text" id="province" name="province" value="{{ old('province') }}" required />
                @error('province') <small class="field-error">{{ $message }}</small> @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-col">
                <label for="postal_code">Postal Code:</label>
                <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code') }}" maxlength="4" />
                @error('postal_code') <small class="field-error">{{ $message }}</small> @enderror
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
                @error('region') <small class="field-error">{{ $message }}</small> @enderror
            </div>
        </div>

        <label for="phone">Phone Number:</label>
        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required />
        @error('phone') <small class="field-error">{{ $message }}</small> @enderror

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required />
        @error('email') <small class="field-error">{{ $message }}</small> @enderror

        {{-- Password --}}
        <label>Password:</label>
        <input type="password" name="password" required />
        @error('password') <small class="field-error">{{ $message }}</small> @enderror

        <label for="password_confirmation" style="font-weight: 600;">Confirm Password:</label>
        <div class="password-wrapper">
            <input type="password" id="password_confirmation" name="password_confirmation" required />
            <button type="button" class="toggle-password" onclick="togglePassword('password_confirmation', this)" tabindex="-1">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path class="eye-open" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle class="eye-open" cx="12" cy="12" r="3"></circle>
                    <line class="eye-closed" style="display:none;" x1="1" y1="1" x2="23" y2="23"></line>
                </svg>
            </button>
        </div>

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
