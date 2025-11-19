@extends('layouts.clinic_layout')

@section('title', 'Clinic Employees')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/employee.css') }}">
@endsection

@section('content')
<main class="employee-main">
    <section class="employee-header">
        <h2>Clinic Employees</h2>
        <button id="addEmployeeBtn">Add Employee</button>
    </section>

    <section class="employee-table-section">
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
                            @if($employee->profile_picture)
                                <img src="{{ asset('storage/' . $employee->profile_pictures) }}" alt="Profile Picture" class="profile-pic">
                            @else
                                <img src="{{ asset('images/logo1.png') }}" alt="Default Profile Picture" class="profile-pic">
                            @endif
                        <td>{{ $employee->name }}</td>
                        <td>{{ $employee->email }}</td>
                        <td>{{ $employee->role }}</td>
                        <td class="actions">
                            <button class="edit-btn" data-id="{{ $employee->id }}">Edit</button>
                            <button class="delete-btn" data-id="{{ $employee->id }}">Delete</button>
                            <button class="schedule-btn" data-id="{{ $employee->id }}">Manage Schedule</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="no-data">No employees found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <!-- Add Employee Modal -->
    <div id="addEmployeeModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Add New Employee</h3>

            <form id="addEmployeeForm" method="POST" action="{{ route('clinic.employees.store') }}">
                @csrf   
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>

                <label for="position">Position</label>
                <input type="text" id="position" name="position" required>

                <label for="profile_picture">Profile</label>
                <input type="file" id="profile_picture" name="profile_picture" required>

                <button type="submit" class="submit-btn">Save Employee</button>
            </form>
        </div>
    </div>

    <div id="employeeModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="employeeModalBody"></div>
        </div>
    </div>
</main>
@endsection
