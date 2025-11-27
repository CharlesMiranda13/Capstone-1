@extends('layouts.admin')

@section('title', 'Admin Settings')

@section('styles')
    <link rel="stylesheet" href="{{ asset('Css/admin_settings.css') }}">
@endsection

@section('content')
    {{-- Alert Messages --}}
    <div class="alerts-container">
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    {{-- Settings Container --}}
    <div class="settings-container">
        <h2>Admin Settings</h2>

        {{-- Tab Navigation --}}
        <div class="settings-tabs">
            <button class="tab-link active" data-tab="general">General</button>
            <button class="tab-link" data-tab="legal">Legal & Compliance</button>
            <button class="tab-link" data-tab="security">Security</button>
        </div>

        {{-- Settings Form --}}
        <form id="settingsForm" action="{{ route('admin.setting.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- General Tab --}}
            <div id="general" class="tab-content active">
                <div class="form-group">
                    <label for="system_name">System Name</label>
                    <input 
                        type="text" 
                        name="system_name" 
                        id="system_name" 
                        value="{{ old('system_name', $settings->system_name ?? 'HealConnect') }}"
                    >
                </div>

                <div class="form-group">
                    <label for="logo">System Logo</label>
                    <input type="file" name="logo" id="logo" accept="image/*">
                    @if(isset($settings->logo))
                        <small class="form-text text-muted">Current logo uploaded</small>
                    @endif
                </div>

                <div class="form-group">
                    <label for="contact_email">Contact Email</label>
                    <input 
                        type="email" 
                        name="contact_email" 
                        id="contact_email" 
                        value="{{ old('contact_email', $settings->contact_email ?? '') }}"
                    >
                </div>

                <div class="form-group">
                    <label for="description">Platform Description / Tagline</label>
                    <textarea 
                        name="description" 
                        id="description" 
                        rows="4"
                    >{{ old('description', $settings->description ?? '') }}</textarea>
                </div>
            </div>

            {{-- Legal & Compliance Tab --}}
            <div id="legal" class="tab-content">
                <div class="form-group">
                    <label for="terms">Terms & Conditions</label>
                    <textarea 
                        name="terms" 
                        id="terms" 
                        rows="8"
                    >{{ old('terms', $settings->terms ?? '') }}</textarea>
                </div>

                <div class="form-group">
                    <label for="privacy">Privacy Policy</label>
                    <textarea 
                        name="privacy" 
                        id="privacy" 
                        rows="8"
                    >{{ old('privacy', $settings->privacy ?? '') }}</textarea>
                </div>

                <div class="form-group">
                    <label for="telehealth_consent">Telehealth Consent Form</label>
                    <input 
                        type="file" 
                        name="telehealth_consent" 
                        id="telehealth_consent" 
                        accept=".pdf,.doc,.docx"
                    >
                    @if(isset($settings->telehealth_consent))
                        <small class="form-text text-muted">Current consent form uploaded</small>
                    @endif
                </div>

                <div class="form-group">
                    <label for="compliance_docs">PRC / DOH Compliance Documents</label>
                    <input 
                        type="file" 
                        name="compliance_docs[]" 
                        id="compliance_docs" 
                        multiple 
                        accept=".pdf,.doc,.docx"
                    >
                    @if(isset($settings->compliance_docs))
                        <small class="form-text text-muted">Current compliance documents uploaded</small>
                    @endif
                </div>
            </div>

            {{-- Security Tab --}}
            <div id="security" class="tab-content">
                <h3>Account Security</h3>

                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input 
                        type="password" 
                        name="current_password" 
                        id="current_password" 
                        autocomplete="current-password"
                    >
                </div>

                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input 
                        type="password" 
                        name="new_password" 
                        id="new_password" 
                        autocomplete="new-password"
                    >
                    <small class="form-text text-muted">Minimum 6 characters</small>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input 
                        type="password" 
                        name="new_password_confirmation" 
                        id="confirm_password" 
                        autocomplete="new-password"
                    >
                </div>
            </div>

            <button type="submit" class="save-btn">Save Changes</button>
        </form>
    </div>

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

    {{-- Password Update Confirmation Modal --}}
    <div id="passwordConfirmModal" class="modal">
        <div class="modal-content">
            <span class="close closePasswordModal">&times;</span>
            <h3>Update Password?</h3>
            <p>You are about to change your account password. Continue?</p>
            <div class="modal-actions">
                <button id="confirmPasswordUpdate" class="confirm-btn">Yes, Update</button>
                <button type="button" class="closePasswordModal cancel-btn">Cancel</button>
            </div>
        </div>
    </div>

    {{-- Hidden Modal Triggers --}}
    <button type="button" class="openTabSwitchModal" style="display:none;"></button>
    <button type="button" class="openPasswordModal" style="display:none;"></button>
@endsection

@section('scripts')
    <script src="{{ asset('js/modal.js') }}"></script>
@endsection