@extends('layouts.app')
@section('title', 'Register - Independent Therapist')
@section('styles') 
<link rel="stylesheet" href="{{ asset('css/register.css') }}">

@section('content')
<div class="registration-hero-strip">
    <h1 class="register-title">Independent Therapist Registration</h1>
    <p class="registration-hero-description">Showcase your expertise and connect with patients who need your specialized care. Join our network of verified professionals.</p>
</div>

<main class="register-main">

    <div class="register-form-card">
        <!-- Stepper Indicator -->
        <div class="registration-stepper">
            <div class="step-item active">
                <div class="step-circle">1</div>
                <div class="step-label">Personal</div>
            </div>
            <div class="step-item">
                <div class="step-circle">2</div>
                <div class="step-label">Account</div>
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

        <form action="{{ route('register.store', ['type' => 'therapist']) }}" method="POST" enctype="multipart/form-data" novalidate id="multiStepForm">
            @csrf

            <!-- Step 1: Personal Information -->
            <div class="form-step active" id="step0">
                <div class="form-section">
                    <h3 class="form-section-title"><i class="fas fa-user-md"></i> Personal Information</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="Fname">First Name</label>
                            <input type="text" id="Fname" name="Fname" class="form-control" value="{{ old('Fname') }}" placeholder="Enter first name" required />
                            @error('Fname') <small class="field-error-msg">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label for="Mname">Middle Name (Optional)</label>
                            <input type="text" id="Mname" name="Mname" class="form-control" value="{{ old('Mname') }}" placeholder="Enter middle name" />
                        </div>

                        <div class="form-group form-row-full">
                            <label for="Lname">Last Name</label>
                            <input type="text" id="Lname" name="Lname" class="form-control" value="{{ old('Lname') }}" placeholder="Enter last name" required />
                            @error('Lname') <small class="field-error-msg">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label for="dob">Date of Birth</label>
                            <input type="date" id="dob" name="dob" class="form-control" value="{{ old('dob') }}" required 
                                   max="{{ date('Y-m-d')}}" min="{{ date('Y-m-d', strtotime('-120 years'))}}" />
                            @error('dob') <small class="field-error-msg">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label for="Gender">Gender</label>
                            <select id="Gender" name="Gender" class="form-control" required>
                                <option value="">Choose Gender</option>
                                <option value="Male" {{ old('Gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('Gender') == 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                            @error('Gender') <small class="field-error-msg">{{ $message }}</small> @enderror
                        </div>
                    </div>
                </div>
                <div class="wizard-navigation">
                    <button type="button" class="btn-next">Next Step <i class="fas fa-arrow-right"></i></button>
                </div>
            </div>

            <!-- Step 2: Contact & Account -->
            <div class="form-step" id="step1">
                <div class="form-section">
                    <h3 class="form-section-title"><i class="fas fa-lock"></i> Contact & Account Information</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="e.g. 09123456789" required />
                            @error('phone') <small class="field-error-msg">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="example@email.com" required />
                            @error('email') <small class="field-error-msg">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <div class="password-field-wrapper">
                                <input type="password" name="password" id="password" class="form-control" placeholder="Create a password" required />
                                <button type="button" class="toggle-password-btn" onclick="togglePassword('password', this)" tabindex="-1">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password') <small class="field-error-msg">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Confirm Password</label>
                            <div class="password-field-wrapper">
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Confirm password" required />
                                <button type="button" class="toggle-password-btn" onclick="togglePassword('password_confirmation', this)" tabindex="-1">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Step 2b: Location (Merged into Account for simplicity or separate if requested) -->
                <div class="form-section" style="margin-top: 30px; border-top: 1px solid #f1f5f9; padding-top: 30px;">
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
                            <label for="street">Practice Street Address</label>
                            <input type="text" id="street" name="street" class="form-control" value="{{ old('street') }}" placeholder="House No., Street Name" required />
                            @error('street') <small class="field-error-msg">{{ $message }}</small> @enderror
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
                            <label for="ValidIDFront">Valid ID (Front Side)</label>
                            <input type="file" id="ValidIDFront" name="ValidIDFront" accept=".jpg, .jpeg, .png, .pdf" required />
                            @error('ValidIDFront') <small class="field-error-msg">{{ $message }}</small> @enderror
                        </div>
                        <div class="upload-box">
                            <label for="ValidIDBack">Valid ID (Back Side)</label>
                            <input type="file" id="ValidIDBack" name="ValidIDBack" accept=".jpg, .jpeg, .png, .pdf" required />
                            @error('ValidIDBack') <small class="field-error-msg">{{ $message }}</small> @enderror
                        </div>
                        <div class="upload-box form-row-full">
                            <label for="license">Professional License (PRC)</label>
                            <input type="file" id="license" name="license" accept=".jpg, .jpeg, .png, .pdf" required />
                            @error('license') <small class="field-error-msg">{{ $message }}</small> @enderror
                        </div>
                    </div>
                </div>

                <div class="form-footer wizard-mode">
                    <p class="notice-small">
                        By uploading your license and documents, you agree that HealConnect will use them solely for verifying your credentials. 
                        Your information will be kept secure and will not be shared without your consent.
                    </p>

                    <div class="wizard-navigation">
                        <button type="button" class="btn-prev"><i class="fas fa-arrow-left"></i> Previous</button>
                        <button type="submit" class="btn-register-premium btn-next">Register Therapist Account</button>
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
