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
        <a href="{{ route('admin.manage-users') }}" class="hc-btn hc-btn-outline">
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
                @php
                    $displayStatus = $user->status;
                    $statusClass = strtolower($user->status);
                    
                    if ($user->status === 'Active' && $user->isBusinessPermitExpired()) {
                        $displayStatus = 'Expired';
                        $statusClass = 'expired';
                    }
                @endphp
                <span class="status-badge {{ $statusClass }}">{{ $displayStatus }}</span>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="header-actions">
            @if(in_array(strtolower($user->status), ['pending', 're-verification pending']))
            <div class="hc-dropdown">
                <button class="hc-dropdown-toggle">Verification Actions</button>
                <div class="hc-dropdown-menu">
                    <button class="hc-dropdown-item" id="btnApproveUser">
                        <i class="fa fa-check-circle"></i> Approve User
                    </button>
                    <button class="hc-dropdown-item hc-dropdown-item-danger" id="btnDeclineUser">
                        <i class="fa fa-times-circle"></i> Decline User
                    </button>
                </div>
            </div>

            {{-- Hidden Form for Approval --}}
            <form id="approveUserForm" action="{{ route('admin.users.verify', $user->id) }}" method="POST" style="display:none;">
                @csrf
                @method('PATCH')
            </form>
            @else
                <span class="muted">No actions available</span>
            @endif
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
                            <div class="doc-content">
                                @if ($user->valid_id_path)
                                    @php
                                        $validIds = json_decode($user->valid_id_path, true);
                                        if (!is_array($validIds)) {
                                            $validIds = ['front' => $user->valid_id_path];
                                        }
                                    @endphp
                                    <div class="doc-actions">
                                        @foreach($validIds as $key => $path)
                                            <button class="hc-btn hc-btn-outline hc-btn-sm btn-view-doc" data-img="{{ route('secure.file', ['path' => $path]) }}">
                                                <i class="fa fa-eye"></i> {{ ucfirst($key) }}
                                            </button>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-danger"><i class="fa fa-times-circle"></i> Missing</span>
                                @endif
                            </div>
                        </div>

                        {{-- License --}}
                        @if (in_array($user->role, ['therapist','clinic']))
                        <div class="doc-item">
                            <span class="doc-label">License</span>
                            <div class="doc-content">
                                @if ($user->license_path)
                                    <button class="hc-btn hc-btn-outline hc-btn-sm btn-view-doc" data-img="{{ route('secure.file', ['path' => $user->license_path]) }}">
                                        <i class="fa fa-certificate"></i> View License
                                    </button>
                                @else
                                    <span class="text-danger"><i class="fa fa-times-circle"></i> Missing</span>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if ($user->role === 'clinic')
                        <div class="doc-item">
                            <span class="doc-label">Business Permit</span>
                            <div class="doc-content">
                                <div class="doc-info">
                                    @if ($user->business_permit_path)
                                        <button class="hc-btn hc-btn-outline hc-btn-sm btn-view-doc" data-img="{{ route('secure.file', ['path' => $user->business_permit_path]) }}">
                                            <i class="fa fa-file-contract"></i> View Permit
                                        </button>
                                    @else
                                        <span class="text-danger"><i class="fa fa-times-circle"></i> Missing</span>
                                    @endif
                                </div>
                                
                                @if($user->business_permit_expiry)
                                    <div class="expiry-info {{ $user->isBusinessPermitExpired() ? 'text-danger' : ($user->isBusinessPermitExpiringSoon() ? 'text-warning' : 'text-success') }}">
                                        <div class="expiry-date">
                                            <i class="fa fa-calendar-alt"></i> 
                                            Expires: <strong>{{ $user->business_permit_expiry->format('M d, Y') }}</strong>
                                        </div>

                                        @if($user->isBusinessPermitExpired())
                                            <span class="badge badge-danger">EXPIRED</span>
                                        @elseif($user->isBusinessPermitExpiringSoon())
                                            <span class="badge badge-warning">EXPIRING SOON</span>
                                        @endif
                                        
                                        {{-- Admin Correction Button --}}
                                        <button type="button" class="hc-btn-icon btn-edit-expiry" title="Correct Date" style="border: none; background: transparent; cursor: pointer; color: #64748b; padding: 0 4px;">
                                            <i class="fa fa-pencil-alt"></i>
                                        </button>
                                    </div>

                                    {{-- Admin Correction Form (Hidden) --}}
                                    <div class="expiry-edit-form" @if($errors->has('business_permit_expiry')) style="display: block;" @endif>
                                        <form action="{{ route('admin.users.update_business_expiry', $user->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <div>
                                                <input type="date" name="business_permit_expiry" class="form-control @error('business_permit_expiry') is-invalid @enderror" 
                                                       value="{{ old('business_permit_expiry', $user->business_permit_expiry ? $user->business_permit_expiry->format('Y-m-d') : '') }}" 
                                                       required>
                                                <button type="submit" class="btn-save">Save</button>
                                                <button type="button" class="btn-cancel-expiry"><i class="fa fa-times"></i></button>
                                            </div>
                                            @error('business_permit_expiry')
                                                <div class="text-danger" style="font-size: 0.75rem; margin-top: 4px;">{{ $message }}</div>
                                            @enderror
                                        </form>
                                    </div>
                                @endif
                            </div>
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

{{-- Document Viewer Modal (image or PDF) --}}
<div id="imageViewerModal" class="modal">
    <span class="close" id="closeImageViewer">&times;</span>
    <img class="modal-content" id="imageViewerContent" style="display:none;">
    <iframe id="pdfViewerContent" class="modal-content"
            style="display:none; width:80vw; height:85vh; border:none; border-radius:8px;"
            src=""></iframe>
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

{{-- APPROVE CONFIRMATION MODAL --}}
<div id="approveModal" class="decline-modal-overlay">
    <div class="decline-modal-content" style="max-width: 420px;">
        <span class="decline-modal-close" onclick="closeApproveModal()">&times;</span>
        <div style="text-align: center; padding: 8px 0 16px;">
            <div style="width: 56px; height: 56px; background: #d1fae5; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                <i class="fa fa-check-circle" style="font-size: 1.6rem; color: #065f46;"></i>
            </div>
            <h2 class="decline-modal-title" style="color: #065f46;">Approve User?</h2>
            <p style="color: #6b7280; font-size: 0.93rem; margin: 0 0 24px;">This user will be verified and granted full access to the platform.</p>
        </div>
        <div class="decline-modal-actions">
            <button type="button" onclick="closeApproveModal()" class="btn-cancel">Cancel</button>
            <button type="button" onclick="document.getElementById('approveUserForm').submit()" class="hc-btn hc-btn-primary" style="background:#059669; border-color:#059669;">
                <i class="fa fa-check"></i> Approve
            </button>
        </div>
    </div>
</div>


@section('scripts')
<script>
    document.getElementById('btnApproveUser')?.addEventListener('click', function() {
        document.getElementById('approveModal').style.display = 'flex';
    });

    function closeApproveModal() {
        document.getElementById('approveModal').style.display = 'none';
    }

    // Close approve modal when clicking outside
    document.getElementById('approveModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeApproveModal();
    });

    document.getElementById('btnDeclineUser')?.addEventListener('click', function() {
        const overlay = document.getElementById('declineModal');
        if (overlay) {
            overlay.style.display = 'flex';
        }
    });

    function closeDeclineModal() {
        const overlay = document.getElementById('declineModal');
        if (overlay) {
            overlay.style.display = 'none';
        }
    }

    // Modal Close buttons
    document.querySelector('.closeDeclineModal')?.addEventListener('click', closeDeclineModal);
    // Expiry Correction Logic
    document.querySelector('.btn-edit-expiry')?.addEventListener('click', function() {
        const form = document.querySelector('.expiry-edit-form');
        const info = document.querySelector('.expiry-info');
        if (form && info) {
            form.style.display = 'block';
            info.style.display = 'none';
        }
    });

    document.querySelector('.btn-cancel-expiry')?.addEventListener('click', function() {
        const form = document.querySelector('.expiry-edit-form');
        const info = document.querySelector('.expiry-info');
        if (form && info) {
            form.style.display = 'none';
            info.style.display = 'flex';
        }
    });
</script>
@endsection