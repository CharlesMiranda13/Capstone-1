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

        <div class="form-group">
            <label for="clinic_type" class="form-label">Clinic Type:</label>
            <select id="clinic_type" name="clinic_type" required>
                <option value="">Select Clinic Type</option>
                <option value="public" {{ old('clinic_type') == 'public' ? 'selected' : '' }}>Public</option>
                <option value="private" {{ old('clinic_type') == 'private' ? 'selected' : '' }}>Private</option>
            </select>
            @error('clinic_type')
                <small style="color:red;">{{ $message }}</small>
            @enderror
        </div>

        <label for="address">Clinic Address:</label>
        <input type="text" id="address" name="address" required value="{{ old('address') }}"/>

        <label for="phone">Phone/Tel Number:</label>
        <input type="tel" id="phone" name="phone" 
            pattern="^(09\d{9}|0\d{1,3}-?\d{6,7})$"
            maxlength="13"
            required 
            placeholder="e.g. 09123456789 or 02-1234567" />

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required value="{{ old('email') }}"/>

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
                <label><input type="checkbox" name="specialization[]" value="Sports"> Sports Therapy</label>
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

        {{-- File Upload Section --}}
        <div class="file-section">
            <div class="file-group">
                <div>
                    <label for="ValidIDFront">Valid ID (Front Side) (Owner/Representative):</label>
                    <input type="file" id="ValidIDFront" name="ValidIDFront" accept=".jpg, .jpeg, .png, .pdf" required />
                @error('ValidIDFront')
                    <small style="color:red;">{{ $message }}</small>
                @enderror
                </div>
                <div>
                    <label for="ValidIDBack">Valid ID (Back Side) (Owner/Representative):</label>
                    <input type="file" id="ValidIDBack" name="ValidIDBack" accept=".jpg, .jpeg, .png, .pdf" required />
                @error('ValidIDFront')
                    <small style="color:red;">{{ $message }}</small>
                @enderror
                </div>

                <div>
                    <label for="license">Clinic License / DOH Accreditation:</label>
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
