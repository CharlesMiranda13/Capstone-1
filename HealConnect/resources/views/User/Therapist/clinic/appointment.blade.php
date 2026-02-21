@extends('layouts.clinic_layout')

@section('title', 'Clinic Appointments')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/therapist_appointment.css') }}">
<link rel="stylesheet" href="{{ asset('css/patient-profile.css') }}">
@endsection

@section('content')
<main class="appointments-main">
    <div class="container">
        <h2 class="page-title">Clinic Appointments</h2>

        {{-- Search & Filter --}}
        <div class="search-filter">
            <form method="GET" action="{{ route('clinic.appointments') }}" class="search-filter-form">
                <input type="text" name="search" placeholder="Search by patient name or type..."
                    value="{{ request('search') }}" class="search-input">

                <select name="status" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>

                <select name="type" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Types</option>
                    <option value="in-person" {{ request('type') == 'in-person' ? 'selected' : '' }}>In-Person</option>
                    <option value="online" {{ request('type') == 'online' ? 'selected' : '' }}>Online</option>
                </select>


                <button type="submit" class="btn-search">
                    <i class="fa fa-search"></i>
                </button>
            </form>
        </div>

        {{-- Alert --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Table --}}
        @if($appointments->isEmpty())
            <p class="empty-message">No appointments yet.</p>
        @else
            <div class="table-container">
                <table class="appointments-table">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Provider</th>
                            <th>Type</th>
                            <th>Schedule</th>
                            <th>Details</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($appointments as $appointment)
                            <tr>
                                <td class="patient-cell" data-label="Patient">
                                    <div class="patient-info-mini">
                                        <span class="patient-name">{{ $appointment->patient->name }}</span>
                                        <span class="patient-record-count"><i class="fa fa-folder-open"></i> {{ $appointment->record_count ?? 0 }} Records</span>
                                    </div>
                                </td>
                                <td data-label="Provider">{{ $appointment->provider->name ?? 'N/A' }}</td>
                                <td data-label="Type">
                                    <span class="type-tag {{ $appointment->appointment_type == 'online' ? 'type-online' : 'type-inperson' }}">
                                        <i class="fa {{ $appointment->appointment_type == 'online' ? 'fa-video' : 'fa-user-md' }}"></i>
                                        {{ ucfirst($appointment->appointment_type) }}
                                    </span>
                                </td>
                                <td class="schedule-cell" data-label="Schedule">
                                    <div class="schedule-info">
                                        <span class="date">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}</span>
                                        <span class="time">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</span>
                                    </div>
                                </td>
                                <td class="details-cell" data-label="Details">
                                    <div class="details-icons">
                                        @if($appointment->preferred_gender)
                                            <i class="fa fa-venus-mars" title="Preferred Gender: {{ ucfirst($appointment->preferred_gender) }}"></i>
                                        @endif
                                        @if($appointment->notes)
                                            <i class="fa fa-sticky-note" title="Notes: {{ $appointment->notes }}"></i>
                                        @endif
                                        @if($appointment->referral)
                                            <a href="{{ asset('storage/referrals/' . $appointment->referral) }}" target="_blank" title="View Referral">
                                                <i class="fa fa-file-medical"></i>
                                            </a>
                                        @endif
                                        <button class="openModalBtn icon-btn" data-link="{{ route('clinic.patients.profile', ['id' => $appointment->patient->id, 'embed' => 1]) }}" title="View Profile">
                                            <i class="fa fa-user-circle"></i>
                                        </button>
                                    </div>
                                </td>
                                <td data-label="Status">
                                    <span class="status-badge 
                                        @if($appointment->status == 'pending') status-pending
                                        @elseif($appointment->status == 'approved') status-approved
                                        @elseif($appointment->status == 'rejected') status-rejected
                                        @elseif($appointment->status == 'completed') status-completed
                                        @endif">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </td>
                                <td class="action-cell" data-label="Action">
                                    @if(auth()->user()->id == $appointment->provider_id || auth()->user()->role == 'admin')
                                        <form action="{{ route('clinic.appointments.updateStatus', $appointment->id) }}" method="POST" class="status-form">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="status-control" onchange="this.form.submit()">
                                                <option value="" disabled selected>Update</option>
                                                <option value="approved">Approve</option>
                                                <option value="rejected">Reject</option>
                                                <option value="completed">Complete</option>
                                            </select>
                                        </form>
                                    @else
                                        <span class="muted">No Action</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Move modal outside of main container logic -->
    <div id="patientModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="modal-body"></div>
        </div>
    </div>
</main>
@section('scripts')
<script>
    // Ensure modal transparency is fixed project-wide for this view
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('patientModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target.classList.contains('close')) {
                    modal.style.display = 'none';
                }
            });
        }
    });
</script>

<style>
    /* Absolute forcing of modal transparency fix */
    #patientModal, .modal {
        background-color: rgba(0, 0, 0, 0.7) !important;
    }
    
    #patientModal .modal-content {
        background-color: #ffffff !important;
        background: #ffffff !important;
        opacity: 1 !important;
        visibility: visible !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important;
    }

    #modal-body, .patient-profile {
        background-color: #ffffff !important;
        opacity: 1 !important;
    }

    /* Search Bar Alignment Fix */
    .search-filter-form {
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
    }

    .search-input, .filter-select, .btn-search {
        height: 45px !important;
        margin: 0 !important;
        box-sizing: border-box !important;
        display: flex !important;
        align-items: center !important;
    }

    .btn-search {
        justify-content: center !important;
        padding: 0 20px !important;
    }
</style>
@endsection
