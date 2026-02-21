@extends('layouts.clinic_layout')

@section('title', 'Clinic Employees')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/employee.css') }}">
@endsection

@section('content')
<main class="employee-main">
    <div class="w-100">
        <section class="employee-header page-header-row">
            <div class="header-flex-content">
                <div class="employee-header-left">
                    <h2 class="page-title-new">Clinic Employees</h2>
                    <p class="page-subtitle">Manage employees, update details, and monitor schedules</p>
                </div>

                <div class="header-actions">
                    <button id="addEmployeeBtn" class="hc-btn hc-btn-primary">
                        <i class="fa fa-plus"></i> Add Employee
                    </button>
                </div>
            </div>

            <div class="search-filter-new">
                <form method="GET" action="{{ route('clinic.employees') }}" class="search-filter-form-new">
                    <div class="search-input-wrapper">
                        <i class="fa fa-search search-icon-inside"></i>
                        <input type="text" name="search"
                               placeholder="Search employees..."
                               value="{{ request('search') }}" class="search-input-new">
                    </div>

                    <select name="position" class="filter-select-new" onchange="this.form.submit()">
                        <option value="">All Positions</option>
                        <option value="Therapist">Therapist</option>
                        <option value="Assistant">Staff</option>
                    </select>

                    <button type="submit" class="hc-btn hc-btn-primary hc-btn-search">
                        <i class="fa fa-search"></i> Search
                    </button>
                </form>
            </div>
        </section>

        <!-- TABLE SECTION -->
        <section class="employee-table-section">
            <div class="hc-table-container hc-table-responsive">
                <table class="hc-table">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th>Profile</th>
                            <th>Full Name</th>
                            <th class="text-center">Gender</th>
                            <th>Email Address</th>
                            <th>Position</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $index => $employee)
                            <tr>
                                <td><span class="muted-text">{{ $index + 1 }}</span></td>
                                <td>
                                    <img 
                                        src="{{ $employee->profile_picture 
                                            ? asset('storage/' . $employee->profile_picture)
                                            : asset('images/logo1.png') }}"
                                        alt="Profile Picture"
                                        class="patient-avatar">
                                </td>
                                <td><span class="patient-name-main">{{ $employee->name }}</span></td>
                                <td class="text-center">
                                    <span class="patient-gender-tag">{{ ucfirst($employee->gender ?? 'N/A') }}</span>
                                </td>
                                <td><span class="email-text">{{ $employee->email }}</span></td>
                                <td><span class="phone-text">{{ $employee->position }}</span></td>
                                <td class="text-center actions-cell">
                                    <button class="hc-icon-btn hc-btn-outline edit-btn" data-id="{{ $employee->id }}" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="hc-icon-btn hc-btn-danger delete-btn" data-id="{{ $employee->id }}" title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="no-data">No employees found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</main>

<!-- ADD EMPLOYEE MODAL -->
<div id="addEmployeeModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Add New Employee</h3>
        <form id="addEmployeeForm" method="POST" action="{{ route('clinic.employees.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="hc-form-row">
                <div class="hc-form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" class="hc-input" required>
                </div>
                <div class="hc-form-group">
                    <label>Gender</label>
                    <select name="gender" class="hc-select" required>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
            </div>

            <div class="hc-form-row">
                <div class="hc-form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="hc-input" required>
                </div>
                <div class="hc-form-group">
                    <label>Position</label>
                    <input type="text" name="position" class="hc-input" required>
                </div>
            </div>

            <div class="hc-form-group">
                <label>Profile Picture</label>
                <input type="file" name="profile_picture" class="hc-file-input">
            </div>

            <div class="modal-footer-new">
                <button type="submit" class="hc-btn hc-btn-primary hc-btn-block">Save Employee</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT MODAL -->
<div id="employeeModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div id="employeeModalBody"></div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Modal closing logic
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target.classList.contains('close') || e.target === modal) {
                    modal.style.display = 'none';
                }
            });
        });
    });
</script>
@endsection
