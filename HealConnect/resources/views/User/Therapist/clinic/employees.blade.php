@extends('layouts.clinic_layout')

@section('title', 'Clinic Employees')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/employee.css') }}">
@endsection

@section('content')
<main class="employee-main">
    <section class="employee-header">
        <div class="employee-header-left">
            <h2>Clinic Employees</h2>
            <p>Manage employees, update details, and monitor schedules</p>
        </div>

        <div class="employee-header-right">
            <!-- FILTER FORM -->
            <form method="GET" action="{{ route('clinic.employees') }}" class="filter-form">
                <input type="text" name="search"
                       placeholder="Search employees..."
                       value="{{ request('search') }}">

                <select name="position" onchange="this.form.submit()">
                    <option value="">All Positions</option>
                    <option value="Therapist">Therapist</option>
                    <option value="Assistant">Staff</option>
                </select>
            </form>
        </div>
    </section>

    <!-- TABLE SECTION -->
    <section class="employee-table-section">
        <div class="table-card">
            <div class="table-top">
                <button id="addEmployeeBtn" class="btn add-btn">
                    + Add Employee
                </button>
            </div>

            <table class="employee-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Profile</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Position</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $index => $employee)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <img 
                                    src="{{ $employee->profile_picture 
                                        ? asset('storage/' . $employee->profile_picture)
                                        : asset('images/logo1.png') }}"
                                    alt="Profile Picture"
                                    class="profile-pic">
                            </td>
                            <td>{{ $employee->name }}</td>
                            <td>{{ $employee->email }}</td>
                            <td>{{ $employee->position }}</td>
                            <td class="actions">
                                <button class="edit-btn" data-id="{{ $employee->id }}">Edit</button>
                                <button class="delete-btn" data-id="{{ $employee->id }}">Delete</button>
                                <button class="schedule-btn" data-id="{{ $employee->id }}">Schedule</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="no-data">No employees found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </section>

</main>

<!-- ADD EMPLOYEE MODAL -->
<div id="addEmployeeModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Add New Employee</h3>
        <form id="addEmployeeForm" method="POST" action="{{ route('clinic.employees.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" required>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Position</label>
                <input type="text" name="position" required>
            </div>

            <div class="form-group">
                <label>Profile Picture</label>
                <input type="file" name="profile_picture">
            </div>

            <button type="submit" class="submit-btn">Save Employee</button>
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
