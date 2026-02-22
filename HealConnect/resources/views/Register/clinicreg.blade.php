@extends('layouts.app')
@section('title', 'Register - Clinic')
@section('styles')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">


@section('content')
<div class="registration-hero-strip">
    <h1 class="register-title">Clinic Registration</h1>
    <p class="registration-hero-description">Partner with HealConnect to manage your clinic more efficiently and reach more patients in need of professional care.</p>
</div>

<main class="register-main">

    <div class="register-form-card">
        <!-- Stepper Indicator -->
        <div class="registration-stepper">
            <div class="step-item active">
                <div class="step-circle">1</div>
                <div class="step-label">Clinic</div>
            </div>
            <div class="step-item">
                <div class="step-circle">2</div>
                <div class="step-label">Location</div>
            </div>
            <div class="step-item">
                <div class="step-circle">3</div>
                <div class="step-label">Expertise</div>
            </div>
            <div class="step-item">
                <div class="step-circle">4</div>
                <div class="step-label">Credentials</div>
            </div>
        </div>

        {{-- Show selected plan notice --}}
        @if(session('selected_plan_for_registration'))
            <div class="registration-plan-notice">
                <i class="fas fa-info-circle"></i>
                <span>You've selected the <strong>{{ ucfirst(str_replace('_', ' ', session('selected_plan_for_registration'))) }}</strong> plan. Complete registration to continue to payment.</span>
            </div>
        @endif

        {{-- Display Validation Errors --}}
        @if ($errors->any())
        <div class="alert-error-container">
            <div class="alert-error-title">
                <i class="fas fa-exclamation-circle"></i>
                <strong>Please correct the following errors:</strong>
            </div>
            <ul class="error-list">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('register.store', ['type' => 'clinic']) }}" method="POST" enctype="multipart/form-data" novalidate id="multiStepForm">
            @csrf

            <!-- Step 1: Clinic Information -->
            <div class="form-step active" id="step0">
                <div class="form-section">
                    <h3 class="form-section-title"><i class="fas fa-hospital"></i> Clinic Information</h3>
                    <div class="form-grid">
                        <div class="form-group form-row-full">
                            <label for="clinic_name">Clinic Name</label>
                            <input type="text" id="clinic_name" name="clinic_name" class="form-control" value="{{ old('clinic_name') }}" placeholder="Enter full clinic name" required />
                            @error('clinic_name') <small class="field-error-msg">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label for="clinic_type">Clinic Type</label>
                            <select id="clinic_type" name="clinic_type" class="form-control" required>
                                <option value="">Select Clinic Type</option>
                                <option value="public" {{ old('clinic_type') == 'public' ? 'selected' : '' }}>Public</option>
                                <option value="private" {{ old('clinic_type') == 'private' ? 'selected' : '' }}>Private</option>
                            </select>
                            @error('clinic_type') <small class="field-error-msg">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label for="clinic_email">Clinic Email Address</label>
                            <input type="email" id="clinic_email" name="clinic_email" class="form-control" value="{{ old('clinic_email') }}" placeholder="clinic@email.com" required />
                            @error('clinic_email') <small class="field-error-msg">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label for="phone">Contact Number</label>
                            <input type="tel" id="phone" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="e.g. 09123456789" required />
                            @error('phone') <small class="field-error-msg">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label>Password</label>
                            <div class="password-field-wrapper">
                                <input type="password" name="password" id="password" class="form-control" placeholder="Create a password" required />
                                <button type="button" class="toggle-password-btn" onclick="togglePassword('password', this)" tabindex="-1">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password') <small class="field-error-msg">{{ $message }}</small> @enderror
                        </div>
                    </div>
                </div>
                <div class="wizard-navigation">
                    <button type="button" class="btn-next">Next Step <i class="fas fa-arrow-right"></i></button>
                </div>
            </div>

            <!-- Step 2: Location Details -->
            <div class="form-step" id="step1">
                <div class="form-section">
                    <h3 class="form-section-title"><i class="fas fa-map-marker-alt"></i> Location Details</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="region">Region</label>
                            <select id="region" name="region" class="form-control" required>
                                <option value="">Select Region</option>
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
                            @error('region') <small class="field-error-msg">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label for="province">Province</label>
                            <input type="text" id="province" name="province" class="form-control" value="{{ old('province') }}" placeholder="e.g. Metro Manila" required />
                            @error('province') <small class="field-error-msg">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label for="city">City/Municipality</label>
                            <input type="text" id="city" name="city" class="form-control" value="{{ old('city') }}" placeholder="e.g. Quezon City" required />
                            @error('city') <small class="field-error-msg">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label for="barangay">Barangay</label>
                            <input type="text" id="barangay" name="barangay" class="form-control" value="{{ old('barangay') }}" placeholder="Enter barangay" required />
                            @error('barangay') <small class="field-error-msg">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group form-row-full">
                            <label for="street">Clinic Street Address</label>
                            <input type="text" id="street" name="street" class="form-control" value="{{ old('street') }}" placeholder="House No., Street Name" required />
                            @error('street') <small class="field-error-msg">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label for="postal_code">Postal Code</label>
                            <input type="text" id="postal_code" name="postal_code" class="form-control" value="{{ old('postal_code') }}" maxlength="4" placeholder="e.g. 1100" />
                            @error('postal_code') <small class="field-error-msg">{{ $message }}</small> @enderror
                        </div>
                    </div>
                </div>
                <div class="wizard-navigation">
                    <button type="button" class="btn-prev"><i class="fas fa-arrow-left"></i> Previous</button>
                    <button type="button" class="btn-next">Next Step <i class="fas fa-arrow-right"></i></button>
                </div>
            </div>

            <!-- Step 3: Specialization & Practice -->
            <div class="form-step" id="step2">
                <div class="form-section">
                    <h3 class="form-section-title"><i class="fas fa-notes-medical"></i> Specialization & Practice</h3>
                    <div class="form-grid" style="margin-bottom: 20px;">
                        <div class="form-group">
                            <label for="start_year">Year Started Practicing</label>
                            <input type="number" id="start_year" name="start_year" class="form-control" min="1900" max="{{ date('Y') }}" required value="{{ old('start_year') }}" placeholder="YYYY">
                            @error('start_year') <small class="field-error-msg">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <label class="form-label" style="font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 8px; display: block;">Areas of Specialization</label>
                    <div class="checkbox-grid">
                        @foreach(['Orthopedic' => 'Orthopedic Rehabilitation', 'Neurological' => 'Neurological Rehabilitation', 'Pediatric' => 'Pediatric Therapy', 'Sports' => 'Sports Therapy', 'Geriatric' => 'Geriatric Therapy', 'Cardiopulmonary' => 'Cardiopulmonary Rehabilitation', 'WomenHealth' => "Women's Health", 'Other' => 'Other'] as $value => $label)
                            <label class="checkbox-item">
                                <input type="checkbox" name="specialization[]" value="{{ $value }}" {{ is_array(old('specialization')) && in_array($value, old('specialization')) ? 'checked' : '' }}>
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                    <p class="notice-small" style="text-align: left; margin-top: 10px;">Select all specializations offered by your clinic.</p>
                </div>
                <div class="wizard-navigation">
                    <button type="button" class="btn-prev"><i class="fas fa-arrow-left"></i> Previous</button>
                    <button type="button" class="btn-next">Next Step <i class="fas fa-arrow-right"></i></button>
                </div>
            </div>

            <!-- Step 4: Credentials -->
            <div class="form-step" id="step3">
                <div class="form-section">
                    <h3 class="form-section-title"><i class="fas fa-certificate"></i> Credentials & Document Upload</h3>
                    <div class="upload-grid">
                        <div class="upload-box">
                            <label for="license">Clinic License / DOH Accreditation</label>
                            <input type="file" id="license" name="license" accept=".jpg, .jpeg, .png, .pdf" required />
                            @error('license') <small class="field-error-msg">{{ $message }}</small> @enderror
                        </div>
                        <div class="upload-box">
                            <label for="ValidIDFront">Owner's Valid ID (Front)</label>
                            <input type="file" id="ValidIDFront" name="ValidIDFront" accept=".jpg, .jpeg, .png, .pdf" required />
                            @error('ValidIDFront') <small class="field-error-msg">{{ $message }}</small> @enderror
                        </div>
                        <div class="upload-box">
                            <label for="ValidIDBack">Owner's Valid ID (Back)</label>
                            <input type="file" id="ValidIDBack" name="ValidIDBack" accept=".jpg, .jpeg, .png, .pdf" required />
                            @error('ValidIDBack') <small class="field-error-msg">{{ $message }}</small> @enderror
                        </div>
                        <div class="upload-box">
                            <label for="business_permit">Business Permit (Optional)</label>
                            <input type="file" id="business_permit" name="business_permit" accept=".jpg, .jpeg, .png, .pdf" />
                            @error('business_permit') <small class="field-error-msg">{{ $message }}</small> @enderror
                        </div>
                    </div>
                </div>

                <div class="form-footer wizard-mode">
                    <p class="notice-small">
                        By uploading these documents, you agree that HealConnect will use them solely for verifying your clinic's legitimacy. 
                        Your information will be kept secure and will not be shared without your consent.
                    </p>

                    <div class="wizard-navigation">
                        <button type="button" class="btn-prev"><i class="fas fa-arrow-left"></i> Previous</button>
                        <button type="submit" class="btn-register-premium btn-next">Register Clinic Account</button>
                    </div>
                    
                    <p class="login-redirect">
                        Already have an account? <a href="{{ url('/logandsign') }}">Login here</a>
                    </p>
                </div>
            </div>
        </form>
    </div>
</main>

<script src="{{ asset('js/registration-wizard.js') }}"></script>
@endsection
