@extends('layouts.admin')

@section('title', 'Subscription Details')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin-subscriptions.css') }}">
@endsection

@section('content')
<div class="subscriptions-container">
    <div class="detail-header">
        <h2>Subscription Details</h2>
        <a href="{{ route('admin.subscriptions.index') }}" class="btn-back">
            <i class="fa-solid fa-arrow-left"></i> Back
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- User and Subscription Info -->
    <div class="details-grid">
        <div class="detail-card">
            <div class="detail-card-header">
                <h5>User Information</h5>
            </div>
            <div class="detail-card-body">
                <p><strong>Name:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Role:</strong> <span class="badge badge-secondary">{{ ucfirst($user->role) }}</span></p>
                <p><strong>Account Status:</strong> 
                    @if($user->status == 'verified')
                        <span class="badge badge-success">Verified</span>
                    @else
                        <span class="badge badge-warning">{{ ucfirst($user->status) }}</span>
                    @endif
                </p>
            </div>
        </div>

        <!-- Subscription Info -->
        <div class="detail-card">
            <div class="detail-card-header">
                <h5>Subscription Information</h5>
            </div>
            <div class="detail-card-body">
                <p><strong>Plan:</strong> <span class="badge badge-primary">{{ ucfirst($user->plan ?? 'None') }}</span></p>
                <p><strong>Status:</strong> 
                    @if($user->subscription_status == 'active')
                        <span class="badge badge-success">Active</span>
                    @elseif($user->subscription_status == 'expired')
                        <span class="badge badge-danger">Expired</span>
                    @else
                        <span class="badge badge-warning">Inactive</span>
                    @endif
                </p>
                <p><strong>Started:</strong> 
                    @if($user->subscription_started_at)
                        {{ \Carbon\Carbon::parse($user->subscription_started_at)->format('M d, Y h:i A') }}
                    @else
                        N/A
                    @endif
                </p>
                <p><strong>Stripe ID:</strong> <code>{{ $user->stripe_subscription_id ?? 'N/A' }}</code></p>
            </div>
        </div>
    </div>

    <!-- Stripe Details-->
    @if($subscriptionDetails)
    <div class="detail-card stripe-info-card">
        <div class="detail-card-header">
            <h5>Stripe Subscription Details</h5>
        </div>
        <div class="detail-card-body">
            <p><strong>Subscription ID:</strong> <code>{{ $subscriptionDetails->id }}</code></p>
            <p><strong>Status:</strong> <span class="badge badge-info">{{ $subscriptionDetails->status }}</span></p>
            <p><strong>Current Period Start:</strong> {{ date('M d, Y', $subscriptionDetails->current_period_start) }}</p>
            <p><strong>Current Period End:</strong> {{ date('M d, Y', $subscriptionDetails->current_period_end) }}</p>
            <p><strong>Cancel at Period End:</strong> {{ $subscriptionDetails->cancel_at_period_end ? 'Yes' : 'No' }}</p>
        </div>
    </div>
    @endif

    <!-- Admin Actions -->
    <div class="admin-actions-card">
        <div class="admin-actions-header">
            <h5>Admin Actions</h5>
        </div>
        <div class="admin-actions-body">
            <div class="actions-grid">
                <div class="action-section">
                    <h6>Update Subscription Status</h6>
                    <form action="{{ route('admin.subscriptions.updateStatus', $user->id) }}" method="POST" class="action-form">
                        @csrf
                        @method('PATCH')
                        <select name="status" required>
                            <option value="active" {{ $user->subscription_status == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $user->subscription_status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="expired" {{ $user->subscription_status == 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                        <button type="submit" class="btn-primary">Update</button>
                    </form>
                </div>

                <!-- Manual Activation -->
                <div class="action-section">
                    <h6>Manually Activate Subscription</h6>
                    <form action="{{ route('admin.subscriptions.manualActivate', $user->id) }}" method="POST" class="action-form">
                        @csrf
                        <select name="plan" required>
                            <option value="pro solo">Pro Solo</option>
                            <option value="pro clinic">Pro Clinic</option>
                        </select>
                        <button type="submit" class="btn-success">Activate</button>
                    </form>
                </div>

                <!-- Cancel Subscription -->
                @if($user->subscription_status == 'active')
                <div class="action-section">
                    <h6>Cancel Subscription</h6>
                    <form action="{{ route('admin.subscriptions.cancel', $user->id) }}" method="POST" 
                          onsubmit="return confirm('Are you sure you want to cancel this subscription?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger">
                            <i class="fa-solid fa-xmark"></i> Cancel Subscription
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection