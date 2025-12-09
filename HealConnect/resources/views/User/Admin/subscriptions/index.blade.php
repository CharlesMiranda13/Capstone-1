@extends('layouts.admin')

@section('title', 'Subscription Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin-subscriptions.css') }}">
@endsection

@section('content')
<div class="subscriptions-container">
    <div class="subscriptions-header">
        <h2>Subscription Management</h2>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Stats Cards -->
    <div class="stats-cards-grid">
        <div class="stats-card">
            <h3>{{ $stats['total'] }}</h3>
            <p>Total Subscriptions</p>
        </div>
        <div class="stats-card border-success">
            <h3 class="text-success">{{ $stats['active'] }}</h3>
            <p>Active</p>
        </div>
        <div class="stats-card border-warning">
            <h3 class="text-warning">{{ $stats['inactive'] }}</h3>
            <p>Inactive</p>
        </div>
        <div class="stats-card border-danger">
            <h3 class="text-danger">{{ $stats['expired'] }}</h3>
            <p>Expired</p>
        </div>
    </div>

    <!-- Plan Distribution -->
    <div class="plan-distribution">
        <div class="plan-card">
            <h5>Pro Solo: <span class="badge badge-primary">{{ $stats['pro_solo'] }}</span></h5>
        </div>
        <div class="plan-card">
            <h5>Pro Clinic: <span class="badge badge-info">{{ $stats['pro_clinic'] }}</span></h5>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-card">
        <form method="GET" action="{{ route('admin.subscriptions.index') }}" class="filter-form">
            <input type="text" name="search" placeholder="Search by name or email" value="{{ request('search') }}">
            
            <select name="status">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
            </select>
            
            <select name="plan">
                <option value="">All Plans</option>
                <option value="pro solo" {{ request('plan') == 'pro solo' ? 'selected' : '' }}>Pro Solo</option>
                <option value="pro clinic" {{ request('plan') == 'pro clinic' ? 'selected' : '' }}>Pro Clinic</option>
            </select>
            
            <div>
                <button type="submit" class="btn-primary">Filter</button>
                <a href="{{ route('admin.subscriptions.index') }}" class="btn-secondary">Clear</a>
            </div>
        </form>
    </div>

    <!-- Subscriptions Table -->
    <div class="table-card">
        <div class="table-card-body">
            <table class="subscription-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Plan</th>
                        <th>Status</th>
                        <th>Started</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscriptions as $subscription)
                    <tr>
                        <td>{{ $subscription->name }}</td>
                        <td>{{ $subscription->email }}</td>
                        <td><span class="badge badge-secondary">{{ ucfirst($subscription->role_display) }}</span></td>
                        <td><span class="badge badge-primary">{{ ucfirst($subscription->plan) }}</span></td>
                        <td>
                            @if($subscription->subscription_status == 'active')
                                <span class="badge badge-success">Active</span>
                            @elseif($subscription->subscription_status == 'expired')
                                <span class="badge badge-danger">Expired</span>
                            @else
                                <span class="badge badge-warning">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $subscription->subscription_started_at ? $subscription->subscription_started_at->format('M d, Y') : 'N/A' }}</td>
                        <td>
                            <a href="{{ route('admin.subscriptions.show', $subscription->id) }}" class="btn-info">
                                <i class="fa-solid fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="empty-state">No subscriptions found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            @if($subscriptions->hasPages())
            <div class="pagination">
                {{-- Previous Page Link --}}
                @if ($subscriptions->onFirstPage())
                    <span class="disabled">« Previous</span>
                @else
                    <a href="{{ $subscriptions->previousPageUrl() }}">« Previous</a>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($subscriptions->getUrlRange(1, $subscriptions->lastPage()) as $page => $url)
                    @if ($page == $subscriptions->currentPage())
                        <span class="active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($subscriptions->hasMorePages())
                    <a href="{{ $subscriptions->nextPageUrl() }}">Next »</a>
                @else
                    <span class="disabled">Next »</span>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
@endsection