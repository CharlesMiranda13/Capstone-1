@php
    $role = Auth::user()->role;

    switch ($role) {
        case 'patient':
            $layouts = 'layouts.patient_layout';
            break;
        case 'therapist':
            $layouts = 'layouts.therapist';
            break;
        case 'clinic':
            $layouts = 'layouts.clinic_layout';
            break;
        default:
            $layouts = 'layouts.app';
            break;
    }
@endphp

@extends($layouts)

@section('title', 'Settings')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/settings.css') }}">
@endsection

@section('content')
<main class="settings-main">
    <div class="settings-content">
        <h2 class="settings-title">Account Settings</h2>

        {{-- SUCCESS & ERROR MESSAGES --}}
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul style="margin: 0; padding-left: 18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- TAB NAVIGATION --}}
        <div class="hc-tabs-wrapper">
            <button type="button" class="hc-tab-item tab-link active" data-tab="profile">Profile</button>
            <button type="button" class="hc-tab-item tab-link" data-tab="personal">Personal Information</button>
            <button type="button" class="hc-tab-item tab-link" data-tab="security">Security</button>
        </div>

        {{-- PROFILE TAB --}}
        <div id="profile" class="tab-content active">
            <div class="scard">
                <h4 class="section-title">Profile Picture</h4>

                {{-- FORM 1: PROFILE PICTURE --}}
                <form action="{{ route($role . '.update.profile') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="profile-upload">
                        <img src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : asset('images/default-avatar.png') }}"
                            alt="Profile Picture" class="profile-image" style="width: 100px; height: 100px;">

                        <div class="upload-controls">
                            <input type="file" name="profile_picture" accept="image/*">
                            @error('profile_picture')
                                <small class="error-message">{{ $message }}</small>
                            @enderror
                            <button type="submit" class="hc-btn hc-btn-primary">Upload</button>
                        </div>
                    </div>
                </form>

                {{-- FORM 2: BIO / DESCRIPTION --}}
                @if($role === 'therapist' || $role === 'clinic')
                <form action="{{ route($role . '.update.info') }}" method="POST" style="margin-top: 20px;">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="description">Professional Bio / Description</label>
                        <textarea name="description" id="description" rows="10">{{ old('description', Auth::user()->description) }}</textarea>
                    </div>

                    <button type="submit" class="hc-btn hc-btn-success">Save Bio</button>
                </form>
                @endif

            </div>
        </div>

        {{-- PERSONAL INFORMATION TAB --}}
        <div id="personal" class="tab-content">
            <div class="scard">
                <h4 class="section-title">Personal Information</h4>
                <form action="{{ route($role . '.update.info') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-grid">
                        {{-- NAME --}}
                        <div class="form-group">
                            <label for="name">{{ $role === 'clinic' ? 'Clinic Name' : 'Full Name' }}</label>
                            <input type="text" name="name" id="name" value="{{ old('name', Auth::user()->name) }}" required>
                        </div>

                        {{-- PHONE --}}
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" name="phone" id="phone" value="{{ old('phone', Auth::user()->phone) }}" 
                                   pattern="^(09\d{9}|\+639\d{9})$" placeholder="09XXXXXXXXX" required>
                        </div>

                        {{-- ADDRESS (Full Width) --}}
                        <div class="form-group form-group-full">
                            <label for="address">Full Address</label>
                            <input type="text" name="address" id="address" value="{{ old('address', Auth::user()->address) }}" required>
                        </div>

                        {{-- PATIENT SPECIFIC FIELDS --}}
                        @if($role === 'patient')
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" value="{{ old('email', Auth::user()->email) }}" required>
                            </div>

                            <div class="form-group">
                                <label for="gender">Sex</label>
                                <input type="text" id="gender" value="{{ Auth::user()->gender }}" disabled>
                            </div>
                        @endif
                    </div>

                    {{-- THERAPIST SPECIFIC FIELDS --}}
                    @if($role === 'therapist' || $role === 'clinic')
                        <div class="section-divider">
                            <i class="fa fa-stethoscope"></i> Professional Expertise
                        </div>

                        <div class="form-group">
                            <label>Specializations</label>
                            <p class="text-muted" style="font-size: 0.85rem; margin-bottom: 0.75rem;">
                                Add your areas of expertise (e.g., Physical Therapy, Occupational Therapy).
                            </p>
                            
                            <div id="spec-tag-container" class="spec-tag-container">
                                @php
                                    $specializations = Auth::user()->specialization
                                        ? explode(',', Auth::user()->specialization)
                                        : [];
                                @endphp

                                @foreach($specializations as $spec)
                                    @if(trim($spec))
                                    <div class="spec-tag">
                                        <span>{{ trim($spec) }}</span>
                                        <input type="hidden" name="specialization[]" value="{{ trim($spec) }}">
                                        <i class="fa fa-times remove-tag"></i>
                                    </div>
                                    @endif
                                @endforeach
                            </div>

                            <div class="spec-input-group">
                                <input type="text" id="new-spec-input" placeholder="Type a specialization..." class="form-control">
                                <button type="button" id="add-spec-btn" class="hc-btn hc-btn-primary">
                                    <i class="fa fa-plus"></i> Add
                                </button>
                            </div>
                        </div>

                        <div class="section-divider">
                            <i class="fa fa-file-shield"></i> Verification Documents
                        </div>

                        <div class="form-grid">
                            <div class="doc-upload-card">
                                <label for="license">Clinic License / Accreditation</label>
                                <input type="file" name="license" id="license" accept=".jpg, .jpeg, .png, .pdf">
                                @if(Auth::user()->license_path)
                                    <small class="text-success" style="display:block; margin-top:8px;">
                                        <i class="fa fa-check-circle"></i> <a href="{{ route('secure.file', ['path' => Auth::user()->license_path]) }}" target="_blank">View Current</a>
                                    </small>
                                @endif
                            </div>

                            @if($role === 'clinic')
                                <div class="doc-upload-card">
                                    <label for="Business">Business Permit</label>
                                    <input type="file" name="Business" id="Business" accept=".jpg, .jpeg, .png, .pdf">
                                    @if(Auth::user()->business_permit_path)
                                    <small class="text-success" style="display:block; margin-top:8px;">
                                        <i class="fa fa-check-circle"></i> <a href="{{ route('secure.file', ['path' => Auth::user()->business_permit_path]) }}" target="_blank">View Current</a>
                                    </small>
                                @endif
                                </div>

                                <div class="form-group form-group-full">
                                    <label for="business_permit_expiry">Business Permit Expiration Date</label>
                                    <input type="date" name="business_permit_expiry" id="business_permit_expiry" 
                                           value="{{ old('business_permit_expiry', Auth::user()->business_permit_expiry ? Auth::user()->business_permit_expiry->format('Y-m-d') : '') }}">
                                </div>
                            @endif
                        </div>
                    @endif

                    <div style="margin-top: 2rem; text-align: right;">
                        <button type="submit" class="hc-btn hc-btn-success hc-btn-lg">
                            <i class="fa fa-save"></i> Save All Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- SECURITY TAB --}}
        <div id="security" class="tab-content">
            <div class="scard">
                <h4 class="section-title">Change Password</h4>
                <form action="{{ route($role . '.update.password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" name="current_password" id="current_password" required>
                    </div>

                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" name="new_password" id="new_password" required>
                    </div>

                    <div class="form-group">
                        <label for="new_password_confirmation">Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" id="new_password_confirmation" required>
                    </div>

                    <button type="submit" class="hc-btn hc-btn-primary">Update Password</button>
                </form>
            </div>
        </div>

    </div>
</main>

{{-- Tab Switch Confirmation Modal --}}
<div id="tabSwitchModal" class="modal">
    <div class="modal-content">
        <span class="close closeTabModal">&times;</span>
        <h3>Switch Tab?</h3>
        <p>You have unsaved changes. Are you sure you want to switch tabs?</p>
        <div class="modal-actions">
            <button id="confirmTabSwitch" class="confirm-btn">Continue</button>
            <button type="button" class="closeTabSwitch cancel-btn">Cancel</button>
        </div>
    </div>
</div>

{{-- Hidden Modal Trigger --}}
<button type="button" class="openTabSwitchModal" style="display:none;"></button>
@endsection

@section('scripts')
    <script src="{{ asset('js/modal.js') }}"></script>
    <script src="{{ asset('js/specialization.js') }}"></script>
@endsection