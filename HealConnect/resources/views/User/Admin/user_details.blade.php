@extends('layouts.admin')

@section('title', 'User Details')

@section('content')
    <h2 style="margin-bottom: 20px;">User Details</h2>

    <div class="user-form">
        <div class="form-group">
            <label><strong>ID</strong></label>
            <input type="text" class="form-control" value="{{ $user->id }}" readonly>
        </div>

        <div class="form-group">
            <label><strong>Name</strong></label>
            <input type="text" class="form-control" value="{{ $user->name }}" readonly>
        </div>

        <div class="form-group">
            <label><strong>Email</strong></label>
            <input type="text" class="form-control" value="{{ $user->email }}" readonly>
        </div>

        <div class="form-group">
            <label><strong>Role</strong></label>
            <input type="text" class="form-control" value="{{ ucfirst($user->role) }}" readonly>
        </div>

        <div class="form-group">
            <label><strong>Status</strong></label>
            <input type="text" class="form-control" value="{{ ucfirst($user->status) }}" readonly>
        </div>

        <div class="form-group">
            <label><strong>Created At</strong></label>
            <input type="text" class="form-control" value="{{ $user->created_at->format('M d, Y') }}" readonly>
        </div>

        {{-- Uploaded Credentials --}}
        <div class="form-group">
            <label><strong>Submitted Credentials</strong></label><br>

            {{-- Show Valid ID (for all roles) --}}
            @if ($user->valid_id_path)
            <a href="{{ asset('storage/' . $user->valid_id_path) }}" target="_blank" class="btn btn-primary">
                View Valid ID
            </a>
            @else
                <p><em>No Valid ID submitted.</em></p>
            @endif

            {{-- Show License (only for therapist or clinic) --}}

            @if (in_array($user->role, ['therapist','clinic']))
                @if ($user->license_path)
                    <a href="{{ asset('storage/' . $user->license_path) }}" target="_blank" class="btn btn-primary" style="margin-left:10px;">
                        View License
                    </a>
                @else
                    <p><em>No License submitted.</em></p>
                @endif
            @endif
        </div>
        <div class="form-actions" style="margin-top: 20px;">
        {{-- Approve --}}
            <form action="{{ route('admin.users.verify', $user->id) }}"method="POST" style="display:inline;">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-success">Approve</button>
            </form>

        {{-- Decline --}}
            <form action="{{ route('admin.users.decline', $user->id) }}"method="POST" style="display:inline;">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-warning">Decline</button>
            </form>
        </div>
    </div>
@endsection
