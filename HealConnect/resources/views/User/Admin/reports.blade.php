@extends('layouts.admin')

@section('title', 'View Reports')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin-reports.css') }}">
@endsection

@section('content')
<div class="page-header-row">
    <h2 class="page-title-new">System Reports</h2>
    <p class="page-subtitle">Platform-wide subscription, user, and appointment statistics</p>
</div>
<div class="reports-container">

    <!-- Subscription Reports -->
    <div class="report-card">
        <div class="report-card-header hide-on-print">
            <h5><i class="fa-solid fa-credit-card"></i> Subscription Reports</h5>
            <a href="{{ route('admin.subscriptions.index') }}" class="btn-primary-small">View Details</a>
        </div>
        <div class="report-card-body">
            <div class="stats-grid hide-on-print">
                <div class="report-stat">
                    <h3>{{ $subscriptionStats['total'] }}</h3>
                    <p class="text-muted">Total Subscriptions</p>
                </div>
                <div class="report-stat">
                    <h3 class="text-success">{{ $subscriptionStats['active'] }}</h3>
                    <p class="text-muted">Active</p>
                </div>
                <div class="report-stat">
                    <h3 class="text-warning">{{ $subscriptionStats['inactive'] }}</h3>
                    <p class="text-muted">Inactive</p>
                </div>
                <div class="report-stat">
                    <h3 class="text-danger">{{ $subscriptionStats['expired'] }}</h3>
                    <p class="text-muted">Expired</p>
                </div>
                <div class="report-stat">
                    <h3 class="text-primary">{{ $subscriptionStats['pro_solo'] }}</h3>
                    <p class="text-muted">Pro Solo</p>
                </div>
                <div class="report-stat">
                    <h3 class="text-info">{{ $subscriptionStats['pro_clinic'] }}</h3>
                    <p class="text-muted">Pro Clinic</p>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 15px;">
                <!-- Monthly Revenue Box -->
                <div class="revenue-box hide-on-print" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                    <div>
                        <strong>Estimated Monthly Revenue:</strong> 
                        ₱{{ number_format(($subscriptionStats['pro_solo'] * 499) + ($subscriptionStats['pro_clinic'] * 999), 2) }}
                    </div>
                    <button type="button" class="hc-btn hc-btn-secondary" onclick="printReport('monthly')" style="white-space: nowrap;">
                        <i class="fa fa-print"></i> Print Monthly Report
                    </button>
                </div>

                <!-- Yearly Revenue Box -->
                <div class="revenue-box hide-on-print" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; border-left-color: #f59e0b;">
                    <div>
                        <strong>Estimated Yearly Revenue:</strong> 
                        ₱{{ number_format((($subscriptionStats['pro_solo'] * 499) + ($subscriptionStats['pro_clinic'] * 999)) * 12, 2) }}
                    </div>
                    <button type="button" class="hc-btn hc-btn-warning" onclick="printReport('yearly')" style="white-space: nowrap; color: white;">
                        <i class="fa fa-print"></i> Print Yearly Report
                    </button>
                </div>
            </div>

            <!-- Printable Revenue Table (Hidden outside of print) -->
            <div class="print-only" style="margin-top: 50px;">
                <div class="print-only-title" style="text-align: center; font-size: 26px; font-weight: bold; margin-bottom: 30px;">
                    HealConnect - <span id="printReportTitle">Estimated Monthly Revenue Details</span> <br>
                    <span style="font-size: 16px; font-weight: normal; color: #555;">{{ now()->format('F Y') }}</span>
                    <div style="font-size: 14px; font-weight: normal; margin-top: 10px; color: #666;">
                        Printed by: {{ auth()->user()->name ?? 'Admin' }}<br>
                        Date: {{ now()->format('F j, Y, g:i A') }}
                    </div>
                </div>
                <table class="hc-table" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                    <thead>
                        <tr>
                            <th style="border: 1px solid #ddd; padding: 12px 8px; text-align: left; font-size: 13px; text-transform: uppercase;">Subscriber Name</th>
                            <th style="border: 1px solid #ddd; padding: 12px 8px; text-align: left; font-size: 13px; text-transform: uppercase;">Role</th>
                            <th style="border: 1px solid #ddd; padding: 12px 8px; text-align: left; font-size: 13px; text-transform: uppercase;">Subscription Plan</th>
                            <th style="border: 1px solid #ddd; padding: 12px 8px; text-align: left; font-size: 13px; text-transform: uppercase;">Amount (₱)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subscribedUsers as $subscriber)
                            @php
                                $monthlyAmount = $subscriber->plan === 'pro solo' ? 499 : ($subscriber->plan === 'pro clinic' ? 999 : 0);
                            @endphp
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 12px 8px; color: #333;">{{ $subscriber->name }}</td>
                                <td style="border: 1px solid #ddd; padding: 12px 8px; color: #333;">{{ ucfirst($subscriber->role_display) }}</td>
                                <td style="border: 1px solid #ddd; padding: 12px 8px; color: #333;">{{ ucwords($subscriber->plan) }}</td>
                                <td style="border: 1px solid #ddd; padding: 12px 8px; color: #333;" class="plan-amount-cell" data-monthly="{{ $monthlyAmount }}">
                                    ₱{{ number_format($monthlyAmount, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="border: 1px solid #ddd; padding: 8px; text-align: center;">No active subscriptions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" id="printTotalLabel" style="border: 1px solid #ddd; padding: 12px 8px; text-align: left; font-size: 13px; text-transform: uppercase;">Total Estimated Monthly Revenue:</th>
                            <th id="printTotalAmount" style="border: 1px solid #ddd; padding: 12px 8px; font-weight: bold;" data-monthly="{{ ($subscriptionStats['pro_solo'] * 499) + ($subscriptionStats['pro_clinic'] * 999) }}">
                                ₱{{ number_format(($subscriptionStats['pro_solo'] * 499) + ($subscriptionStats['pro_clinic'] * 999), 2) }}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- User Statistics -->
    <div class="report-card hide-on-print">
        <div class="report-card-header">
            <h5><i class="fa-solid fa-users"></i> User Statistics</h5>
        </div>
        <div class="report-card-body">
            <div class="stats-grid">
                <div class="report-stat">
                    <h3>{{ $userStats['total_users'] }}</h3>
                    <p class="text-muted">Total Users</p>
                </div>
                <div class="report-stat">
                    <h3 class="text-primary">{{ $userStats['patients'] }}</h3>
                    <p class="text-muted">Patients</p>
                </div>
                <div class="report-stat">
                    <h3 class="text-success">{{ $userStats['therapists'] }}</h3>
                    <p class="text-muted">Independent Therapists</p>
                </div>
                <div class="report-stat">
                    <h3 class="text-info">{{ $userStats['clinics'] }}</h3>
                    <p class="text-muted">Clinics</p>
                </div>
                <div class="report-stat">
                    <h3 class="text-success">{{ $userStats['verified'] }}</h3>
                    <p class="text-muted">Verified Accounts</p>
                </div>
                <div class="report-stat">
                    <h3 class="text-warning">{{ $userStats['pending'] }}</h3>
                    <p class="text-muted">Pending Verification</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Appointment Statistics -->
    <div class="report-card hide-on-print">
        <div class="report-card-header">
            <h5><i class="fa-solid fa-calendar-check"></i> Appointment Statistics</h5>
        </div>
        <div class="report-card-body">
            <div class="stats-grid">
                <div class="report-stat">
                    <h3>{{ $appointmentStats['total'] }}</h3>
                    <p class="text-muted">Total Appointments</p>
                </div>
                <div class="report-stat">
                    <h3 class="text-warning">{{ $appointmentStats['pending'] }}</h3>
                    <p class="text-muted">Pending</p>
                </div>
                <div class="report-stat">
                    <h3 class="text-success">{{ $appointmentStats['confirmed'] }}</h3>
                    <p class="text-muted">Confirmed</p>
                </div>
                <div class="report-stat">
                    <h3 class="text-info">{{ $appointmentStats['completed'] }}</h3>
                    <p class="text-muted">Completed</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="report-card hide-on-print">
        <div class="report-card-header">
            <h5><i class="fa-solid fa-chart-line"></i> Registration Trend (Last 30 Days)</h5>
        </div>
        <div class="report-card-body">
            <div class="hc-table-container hc-table-responsive">
                <table class="hc-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>New Registrations</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentRegistrations as $reg)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($reg->date)->format('M d, Y') }}</td>
                            <td><span class="hc-badge hc-badge-info">{{ $reg->count }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="empty-state">No recent registrations</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function printReport(type) {
        const titleEl = document.getElementById('printReportTitle');
        const labelEl = document.getElementById('printTotalLabel');
        const amountCells = document.querySelectorAll('.plan-amount-cell');
        const totalAmountEl = document.getElementById('printTotalAmount');
        
        let multiplier = 1;
        
        if (type === 'yearly') {
            titleEl.textContent = 'Estimated Yearly Revenue Details';
            labelEl.textContent = 'Total Estimated Yearly Revenue:';
            multiplier = 12;
        } else {
            titleEl.textContent = 'Estimated Monthly Revenue Details';
            labelEl.textContent = 'Total Estimated Monthly Revenue:';
            multiplier = 1;
        }

        // Update each subscriber's row amount
        amountCells.forEach(cell => {
            const monthlyVal = parseFloat(cell.getAttribute('data-monthly'));
            const calculatedVal = monthlyVal * multiplier;
            cell.textContent = '₱' + calculatedVal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        });

        // Update total row amount
        const monthlyTotal = parseFloat(totalAmountEl.getAttribute('data-monthly'));
        const calculatedTotal = monthlyTotal * multiplier;
        totalAmountEl.textContent = '₱' + calculatedTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});

        // Call print window
        window.print();
    }
</script>
@endsection