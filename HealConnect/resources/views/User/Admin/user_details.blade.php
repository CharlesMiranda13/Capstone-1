@extends('layouts.admin')

@section('title', 'User Details')

@section('content')
<div class="user-details">
    <h2 style="margin-bottom: 25px; text-align:center;">User Details</h2>

    <div class="user-form" style="text-align: center;">

        {{-- Profile Picture --}}
        <div style="margin-bottom: 20px;">
            <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('images/default-profile.png') }}" 
                 alt="Profile Picture" 
                 style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid #ccc;">
            <h3 style="margin-top: 10px;">{{ $user->name }}</h3>
            <p style="color: #666;">{{ ucfirst($user->role) }}</p>
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

        <div class="form-group">
            <label><strong>Gender</strong></label>
            <input type="text" class="form-control" value="{{ $user->gender ?? 'Not specified' }}" readonly>
        </div>

        {{-- Therapist / Clinic Details --}}
        @if (in_array($user->role, ['therapist','clinic']))
            <div class="form-group">
                <label><strong>Specialization</strong></label>
                @if ($user->specialization)
                    <ul style="list-style: none; padding: 0;">
                        @foreach(explode(',', $user->specialization) as $spec)
                            <li>• {{ trim($spec) }}</li>
                        @endforeach
                    </ul>
                @else
                    <p>N/A</p>
                @endif
            </div>

            <div class="form-group">
                <label><strong>Experience (Years)</strong></label>
                <input type="text" class="form-control" value="{{ round($user->experience_years ?? 0) }}" readonly>
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
                <a href="#" id="viewValidIdBtn" class="btn btn-primary" data-valid-id="{{ asset('storage/' . $user->valid_id_path) }}">
                    View Valid ID
                </a>

                <div id="validIdModal" class="modal">
                    <span class="close" id="closeModalBtn">&times;</span>
                    <img class="modal-content" id="validIdImage" alt="Valid ID">
                </div>
            @else
                <p><em>No Valid ID submitted.</em></p>
            @endif

            {{-- License --}}
            @if (in_array($user->role, ['therapist','clinic']))
                @if ($user->license_path)
                    <a href="#" id="viewLicenseBtn" class="btn btn-primary" data-license="{{ asset('storage/' . $user->license_path) }}">
                        View License
                    </a>

                    <div id="licenseModal" class="modal">
                        <span class="close" id="closeLicenseBtn">&times;</span>
                        <img class="modal-content" id="licenseImage" alt="License">
                    </div>
                @else
                    <p><em>No License submitted.</em></p>
                @endif
            @endif
        </div>

        {{-- Actions --}}
        <div class="form-actions" style="margin-top: 25px; display:flex; justify-content:center; gap:15px;">
            <form action="{{ route('admin.users.verify', $user->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn-success">Approve</button>
            </form>

            <form action="{{ route('admin.users.decline', $user->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn-warning">Decline</button>
            </form>
        </div>
    </div>
</div>
@endsection
