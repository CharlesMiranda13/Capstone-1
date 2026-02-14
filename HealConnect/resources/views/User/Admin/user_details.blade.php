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

<div class="user-details-container">
    
    {{-- Back Button --}}
    <div class="back-btn-container">
        <a href="{{ route('admin.manage-users') }}" class="btn-back">
            <i class="fa fa-arrow-left"></i> Back to Users
        </a>
    </div>

    {{-- Profile Header Card --}}
    <div class="profile-header">
        <div class="header-main">
            <div class="profile-img-container">
                <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('images/logo1.png') }}" 
                     alt="Profile" class="profile-avatar">
            </div>
            <div class="profile-info">
                <h2 class="profile-name">{{ $user->name }}</h2>
                <p class="profile-role">
                    {{ ucfirst($user->role_display) }}
                    @if($user->role === 'clinic' && $user->clinic_type)
                        <span class="clinic-badge {{ $user->clinic_type }}">
                            {{ ucfirst($user->clinic_type) }}
                        </span>
                    @endif
                </p>
                <div class="profile-meta">
                    <span class="meta-item"><i class="fa fa-envelope"></i> {{ $user->email }}</span>
                    <span class="meta-item"><i class="fa fa-phone"></i> {{ $user->phone ?? 'N/A' }}</span>
                </div>
            </div>
            <div class="profile-status">
                <span class="status-badge {{ strtolower($user->status) }}">{{ $user->status }}</span>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="header-actions">
            <form action="{{ route('admin.users.verify', $user->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn-action btn-approve" onclick="return confirm('Approve this user?');">
                    <i class="fa fa-check"></i> Approve User
                </button>
            </form>
            <button type="button" class="btn-action btn-decline openDeclineBtn">
                <i class="fa fa-times"></i> Decline
            </button>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="profile-grid">
        
        {{-- Left Column: Basic & Professional --}}
        <div class="profile-col">
            
            {{-- Basic Info Card --}}
            <div class="info-card">
                <div class="card-header">
                    <h3><i class="fa fa-info-circle"></i> Basic Information</h3>
                </div>
                <div class="card-body">
                    <div class="detail-row">
                        <span class="label">User ID</span>
                        <span class="value">#{{ $user->id }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Joined</span>
                        <span class="value">{{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                    @if ($user->role !== 'clinic')
                    <div class="detail-row">
                        <span class="label">Gender</span>
                        <span class="value">{{ ucfirst($user->gender ?? 'N/A') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            @if (in_array($user->role, ['therapist','clinic']))
            <div class="info-card">
                <div class="card-header">
                    <h3><i class="fa fa-briefcase"></i> Professional Details</h3>
                </div>
                <div class="card-body">
                    <div class="detail-item">
                        <span class="label-block">Specialization</span>
                        @if ($user->specialization)
                            <div class="tags-container">
                                @foreach(explode(',', $user->specialization) as $spec)
                                    <span class="tag-badge">{{ trim($spec) }}</span>
                                @endforeach
                            </div>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </div>
                    
                    <div class="detail-grid">
                        <div class="detail-box">
                            <span class="label">Experience</span>
                            <span class="value-highlight">{{ round($user->experience_years ?? 0) }} Years</span>
                        </div>
                        <div class="detail-box">
                            <span class="label">Subscription</span>
                            <span class="value-highlight {{ strtolower($user->subscription_status ?? 'free') }}">
                                {{ ucfirst($user->subscription_status ?? 'None') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Middle Column: Location --}}
        <div class="profile-col">
            {{-- Address Card --}}
            <div class="info-card">
                <div class="card-header">
                    <h3><i class="fa fa-map-marker-alt"></i> Location Details</h3>
                </div>
                <div class="card-body">
                    @if($user->street || $user->city)
                        @if($user->street)
                        <div class="detail-row">
                            <span class="label">Street</span>
                            <span class="value">{{ $user->street }}</span>
                        </div>
                        @endif
                        @if($user->barangay)
                        <div class="detail-row">
                            <span class="label">Barangay</span>
                            <span class="value">{{ $user->barangay }}</span>
                        </div>
                        @endif
                        <div class="detail-row">
                            <span class="label">City</span>
                            <span class="value">{{ $user->city ?? '-' }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Province</span>
                            <span class="value">{{ $user->province ?? '-' }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Region</span>
                            <span class="value">{{ $user->region ?? '-' }}</span>
                        </div>
                    @elseif($user->address)
                        <div class="detail-row">
                            <span class="label">Address</span>
                            <span class="value">{{ $user->address }}</span>
                        </div>
                    @else
                        <p class="text-muted">No address provided.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Column: Documents & Staff --}}
        <div class="profile-col">
            
            {{-- Verification Documents --}}
            <div class="info-card">
                <div class="card-header">
                    <h3><i class="fa fa-id-card"></i> Verification Documents</h3>
                </div>
                <div class="card-body">
                    <div class="documents-grid">
                        {{-- Valid ID --}}
                        <div class="doc-item">
                            <span class="doc-label">Valid ID</span>
                            @if ($user->valid_id_path)
                                @php
                                    $validIds = json_decode($user->valid_id_path, true);
                                    if (!is_array($validIds)) {
                                        $validIds = ['front' => $user->valid_id_path];
                                    }
                                @endphp
                                <div class="doc-actions">
                                    @foreach($validIds as $key => $path)
                                        <button class="btn-view-doc" data-img="{{ asset('storage/' . $path) }}">
                                            <i class="fa fa-eye"></i> {{ ucfirst($key) }}
                                        </button>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-danger"><i class="fa fa-times-circle"></i> Missing</span>
                            @endif
                        </div>

                        {{-- License --}}
                        @if (in_array($user->role, ['therapist','clinic']))
                        <div class="doc-item">
                            <span class="doc-label">License</span>
                            @if ($user->license_path)
                                <button class="btn-view-doc" data-img="{{ asset('storage/' . $user->license_path) }}">
                                    <i class="fa fa-file-alt"></i> View License
                                </button>
                            @else
                                <span class="text-danger"><i class="fa fa-times-circle"></i> Missing</span>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Clinic Employees --}}
            @if($user->role === 'clinic')
            <div class="info-card">
                <div class="card-header">
                    <h3><i class="fa fa-users"></i> Clinic Staff</h3>
                </div>
                <div class="card-body">
                    @if(isset($employees) && $employees->count())
                        <div class="staff-list">
                            @foreach($employees as $emp)
                                <div class="staff-item">
                                    <div class="staff-avatar">
                                        <i class="fa fa-user-md"></i>
                                    </div>
                                    <div class="staff-info">
                                        <strong>{{ $emp->name }}</strong>
                                        <span>{{ $emp->email }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No staff registered.</p>
                    @endif
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

{{-- Standard Image Modal --}}
<div id="imageViewerModal" class="modal">
    <span class="close" id="closeImageViewer">&times;</span>
    <img class="modal-content" id="imageViewerContent">
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


@section('scripts')
{{-- Scripts are handled by global modal.js --}}
@endsection