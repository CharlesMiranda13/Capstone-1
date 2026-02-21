@extends('layouts.clinic_layout')

@section('title', 'Clinic Services & Availability')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />
@endsection

@section('content')
<div class="bg-white rounded-4 shadow-sm p-4 w-100">
    {{-- Page Header --}}
    <div class="page-header-row">
        <h2 class="page-title-new">Services & Schedule</h2>
        <p class="page-subtitle">Manage your appointment types and weekly availability</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Two-column layout: Services + Calendar --}}
    <div class="services-schedule-grid">

        {{-- LEFT: Services Card --}}
        <div class="hc-card">
            <h3 class="hc-card-title"><i class="fa fa-stethoscope"></i> Appointment Types Offered</h3>

            <form action="{{ route('clinic.services.store') }}" method="POST" class="service-form">
                @csrf
                <div class="checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="appointment_types[]" value="Online"
                            {{ in_array('Online', $existingServices ?? []) ? 'checked' : '' }}>
                        <i class="fa fa-video"></i> Online
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="appointment_types[]" value="In-Clinic"
                            {{ in_array('In-Clinic', $existingServices ?? []) ? 'checked' : '' }}>
                        <i class="fa fa-hospital"></i> In-Clinic
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="appointment_types[]" value="In-home"
                            {{ in_array('In-home', $existingServices ?? []) ? 'checked' : '' }}>
                        <i class="fa fa-house-medical"></i> In-Home
                    </label>
                </div>

                <div class="service-price-row">
                    <label class="field-label">Price / Fee</label>
                    <input type="text" name="price" value="{{ $existingPrice ?? '' }}"
                        placeholder="Enter your price/fee" class="hc-input">
                </div>

                <button type="submit" class="hc-btn hc-btn-primary" style="width:100%;margin-top:1rem;">
                    <i class="fa fa-save"></i> Save Services
                </button>
            </form>

            <hr class="section-divider">

            <h3 class="hc-card-title"><i class="fa fa-calendar-plus"></i> Add Availability</h3>
            <form action="{{ route('clinic.schedules.store') }}" method="POST" class="availability-form">
                @csrf
                <div class="field-row">
                    <label class="field-label">Day of the Week</label>
                    <select name="day_of_week" required class="hc-input">
                        @foreach(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $i => $day)
                            <option value="{{ $i }}">{{ $day }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field-row-split">
                    <div class="field-row">
                        <label class="field-label">Start Time</label>
                        <input type="time" name="start_time" required class="hc-input">
                    </div>
                    <div class="field-row">
                        <label class="field-label">End Time</label>
                        <input type="time" name="end_time" required class="hc-input">
                    </div>
                </div>
                <button class="hc-btn hc-btn-primary" style="width:100%;margin-top:1rem;">
                    <i class="fa fa-plus"></i> Add Availability
                </button>
            </form>
        </div>

        {{-- RIGHT: Calendar --}}
        <div class="hc-card calendar-card">
            <h3 class="hc-card-title"><i class="fa fa-calendar"></i> Availability Calendar</h3>
            <div id="calendar"></div>
        </div>

    </div>

    {{-- Existing Schedule Table --}}
    <div class="hc-card" style="margin-top:1.5rem;">
        <h3 class="hc-card-title"><i class="fa fa-list-check"></i> Existing Schedule</h3>
        <div class="hc-table-container hc-table-responsive">
            <table class="hc-table">
                <thead>
                    <tr>
                        <th>Day</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $schedule)
                        @php
                            $days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
                            $dayName = is_numeric($schedule->day_of_week) ? $days[$schedule->day_of_week] : $schedule->day_of_week;
                        @endphp
                        <tr>
                            <td>{{ $dayName }}</td>
                            <td>{{ date('h:i A', strtotime($schedule->start_time)) }} – {{ date('h:i A', strtotime($schedule->end_time)) }}</td>
                            <td>
                                @if($schedule->is_active)
                                    <span class="hc-badge hc-badge-success">Active</span>
                                @else
                                    <span class="hc-badge hc-badge-danger">Inactive</span>
                                @endif
                            </td>
                            <td class="action-cell">
                                <form action="{{ route('clinic.schedules.toggle', $schedule->id) }}" method="POST" class="action-form">
                                    @csrf @method('PATCH')
                                    <button class="hc-btn hc-btn-outline hc-btn-sm">{{ $schedule->is_active ? 'Disable' : 'Enable' }}</button>
                                </form>
                                <form action="{{ route('clinic.schedules.destroy', $schedule->id) }}" method="POST" class="action-form">
                                    @csrf @method('DELETE')
                                    <button class="hc-btn hc-btn-danger hc-btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="empty-message">No schedule yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
    window.availabilities = @json($calendarSchedules ?? []);
    window.userRole = 'clinic';
</script>
<script src="{{ asset('js/availability.js') }}"></script>
@endsection
