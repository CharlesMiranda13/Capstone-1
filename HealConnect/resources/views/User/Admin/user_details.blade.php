@extends('layouts.admin')

@section('title', 'User Details')
@section('styles')
<link rel="stylesheet" href="{{ asset('Css/user_details.css') }}">
@endsection

@section('content')

@if (session('success') || session('error'))
    <div class="flash-message-wrapper">
        <div class="flash-message 
                    {{ session('success') ? 'flash-success' : '' }} 
                    {{ session('error') ? 'flash-error' : '' }}">
            {{ session('success') ?? session('error') }}
        </div>
    </div>
@endif

<div class="user-details">
    <h2 class="user-details-title">User Details</h2>

    <div class="user-form">

        {{-- Profile Picture --}}
        <div class="profile-section">
            <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('images/logo1.png') }}" 
                 alt="Profile Picture" 
                 class="profile-picture">
            <h3 class="profile-name">{{ $user->name }}</h3>
            <p class="profile-role">{{ ucfirst($user->role_display) }}</p>
            
            {{-- Clinic Type Badge --}}
            @if($user->role === 'clinic' && $user->clinic_type)
                <span class="clinic-type-badge {{ $user->clinic_type }}">
                    {{ ucfirst($user->clinic_type) }} Clinic
                </span>
            @endif
        </div>

        {{-- Basic Details --}}
        <div class="form-group">
            <label><strong>ID</strong></label>
            <input type="text" class="form-control" value="{{ $user->id }}" readonly>
        </div>

        <div class="form-group">
            <label><strong>Email</strong></label>
            <input type="text" class="form-control" value="{{ $user->email }}" readonly>
        </div>

        <div class="form-group">
            <label><strong>Status</strong></label>
            <input type="text" class="form-control" value="{{ ucfirst($user->status) }}" readonly>
        </div>

        <div class="form-group">
            <label><strong>Phone</strong></label>
            <input type="text" class="form-control" value="{{ $user->phone ?? 'Not Specified' }}" readonly>
        </div>

        @if ($user->role !== 'clinic')
            <div class="form-group">
                <label><strong>Gender</strong></label>
                <input type="text" class="form-control" value="{{ $user->gender ?? 'Not specified' }}" readonly>
            </div>
        @endif

        {{-- Clinic Type as Field --}}
        @if($user->role === 'clinic')
            <div class="form-group">
                <label><strong>Clinic Type</strong></label>
                <input type="text" class="form-control" 
                       value="{{ $user->clinic_type ? ucfirst($user->clinic_type) : 'Not Specified' }}" 
                       readonly>
            </div>
        @endif

        {{-- Therapist / Clinic Details --}}
        @if (in_array($user->role, ['therapist','clinic']))
            <div class="form-group">
                <label><strong>Specialization</strong></label>
                @if ($user->specialization)
                    <div class="specialization-container">
                        <ul class="specialization-list">
                            @foreach(explode(',', $user->specialization) as $spec)
                                <li class="specialization-badge">
                                    {{ trim($spec) }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <p class="text-muted">N/A</p>
                @endif
            </div>

            <div class="form-group">
                <label><strong>Experience (Years)</strong></label>
                <input type="text" class="form-control" value="{{ round($user->experience_years ?? 0) }}" readonly>
            </div>
            <div class="form-group">
                <label><strong>Subscription Status</strong></label>
                <input type="text" class="form-control" 
                    value="{{ ucfirst($user->subscription_status ?? 'Not Subscribed') }}" readonly>
            </div>
        @endif

        {{-- Clinic Employees Section --}}
        @if($user->role === 'clinic' && isset($employees) && $employees->count())
            <div class="form-group employees-section">
                <label><strong>Clinic Employees</strong></label>
                <div class="employees-container">
                    <ul class="employees-list">
                        @foreach($employees as $emp)
                            <li class="employee-card">
                                <div class="employee-info">
                                    <div class="employee-name">{{ $emp->name }}</div>
                                    <div class="employee-email">
                                        <i class="fa fa-envelope"></i>
                                        {{ $emp->email }}
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @elseif($user->role === 'clinic')
            <div class="form-group employees-section">
                <label><strong>Clinic Employees</strong></label>
                <div class="employees-empty">
                    <p class="text-muted">No employees registered yet</p>
                </div>
            </div>
        @endif

        {{-- Created Date --}}
        <div class="form-group">
            <label><strong>Created At</strong></label>
            <input type="text" class="form-control" value="{{ $user->created_at->format('M d, Y') }}" readonly>
        </div>

        {{-- Uploaded Credentials --}}
        <div class="form-group">
            <label><strong>Submitted Credentials</strong></label><br>

            {{-- Valid ID --}}
            @if ($user->valid_id_path)
                @php
                    $validIds = json_decode($user->valid_id_path, true);
                    if (!is_array($validIds)) {
                        $validIds = ['front' => $user->valid_id_path, 'back' => $user->valid_id_path];
                    }
                @endphp
                <div class="form-group">
                    <label><strong>Valid ID</strong></label><br>
                    @if(isset($validIds['front']))
                        <a href="#" id="viewValidIdBtn" class="btn btn-primary" data-valid-id="{{ asset('storage/' . $validIds['front']) }}">
                            View Front
                        </a>
                    @endif
                    @if(isset($validIds['back']))
                        <a href="#" id="viewValidIdBackBtn" class="btn btn-primary" data-valid-id="{{ asset('storage/' . $validIds['back']) }}">
                            View Back
                        </a>
                    @endif

                    <div id="validIdModal" class="modal">
                        <span class="close" id="closeModalBtn">&times;</span>
                        <img class="modal-content" id="validIdImage" alt="Valid ID">
                    </div>
                </div>
            @else
                <p><em>No Valid ID submitted.</em></p>
            @endif

            {{-- License --}}
            @if (in_array($user->role, ['therapist','clinic']))
                @if ($user->license_path)
                    <div class="form-group">
                        <label><strong>License</strong></label><br>
                        <a href="#" id="viewLicenseBtn" class="btn btn-primary" data-license="{{ asset('storage/' . $user->license_path) }}">
                            View License
                        </a>

                        <div id="licenseModal" class="modal">
                            <span class="close" id="closeLicenseBtn">&times;</span>
                            <img class="modal-content" id="licenseImage" alt="License">
                        </div>
                    </div>
                @else
                    <p><em>No License submitted.</em></p>
                @endif
            @endif
        </div>
            
        {{-- Actions --}}
        <div class="form-actions">
            {{-- APPROVE --}}
            <form action="{{ route('admin.users.verify', $user->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn-success">Approve</button>
            </form>

            {{-- DECLINE BUTTON --}}
            <button type="button" class="btn-warning openDeclineBtn">Decline</button>
        </div>
    </div>
</div>

{{-- DECLINE MODAL --}}
<div id="declineModal" class="decline-modal-overlay">
    <div class="decline-modal-content">
        <span class="closeDeclineModal decline-modal-close">&times;</span>

        <h2 class="decline-modal-title">Decline User Verification</h2>
        
        <form action="{{ route('admin.users.decline', $user->id) }}" method="POST">
            @csrf
            @method('PATCH')

            <textarea name="reason" rows="4" class="form-control decline-textarea" 
                      placeholder="Enter reason..." required></textarea>

            <div class="decline-modal-actions">
                <button type="button" onclick="closeDeclineModal()" class="btn-cancel">
                    Cancel
                </button>
                <button type="submit" class="btn-decline">
                    Submit
                </button>
            </div>
        </form>
    </div>
</div>

@endsection