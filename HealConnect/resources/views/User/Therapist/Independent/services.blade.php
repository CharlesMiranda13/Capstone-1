@extends('layouts.therapist')

@section('title', 'My Services & Availability')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />
@endsection

@section('content')
<div class="bg-white rounded-4 shadow-sm p-4 w-100">
    {{-- Page Header --}}
    <div class="page-header-row">
        <h2 class="page-title-new">Services & Schedule</h2>
        <p class="page-subtitle">Manage your appointment types and availability</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Two-column layout: Services + Calendar --}}
    <div class="services-schedule-grid">

        {{-- LEFT: Services + Add Availability --}}
        <div class="hc-card">
            <h3 class="hc-card-title"><i class="fa fa-stethoscope"></i> Appointment Types I Offer</h3>

            <form action="{{ route('therapist.services.store') }}" method="POST" class="service-form">
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
            <form action="{{ route('therapist.availability.store') }}" method="POST" class="availability-form">
                @csrf
                <div class="field-row">
                    <label class="field-label">Date</label>
                    <input type="date" name="date" required min="{{ date('Y-m-d') }}" class="hc-input">
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
                <button type="submit" class="hc-btn hc-btn-primary" style="width:100%;margin-top:1rem;">
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
        <h3 class="hc-card-title"><i class="fa fa-list-check"></i> Existing Availability</h3>
        <div class="hc-table-container hc-table-responsive">
            <table class="hc-table">
                <thead>
                    <tr>
                        <th>Day / Date</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($availabilities as $availability)
                        <tr>
                            <td>{{ $availability->day_of_week }}, {{ \Carbon\Carbon::parse($availability->date)->format('F j, Y') }}</td>
                            <td>{{ date('h:i A', strtotime($availability->start_time)) }}</td>
                            <td>{{ date('h:i A', strtotime($availability->end_time)) }}</td>
                            <td>
                                @if($availability->is_active)
                                    <span class="hc-badge hc-badge-success">Active</span>
                                @else
                                    <span class="hc-badge hc-badge-danger">Inactive</span>
                                @endif
                            </td>
                            <td class="action-cell">
                                <div class="hc-dropdown">
                                    <button class="hc-dropdown-toggle">Actions</button>
                                    <div class="hc-dropdown-menu">
                                        <form action="{{ route('therapist.availability.toggle', $availability->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="hc-dropdown-item">
                                                <i class="fa {{ $availability->is_active ? 'fa-toggle-off' : 'fa-toggle-on' }}"></i>
                                                {{ $availability->is_active ? 'Disable' : 'Enable' }}
                                            </button>
                                        </form>
                                        <form action="{{ route('therapist.availability.destroy', $availability->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="hc-dropdown-item hc-dropdown-item-danger"
                                                onclick="return confirm('Are you sure you want to delete this availability?');">
                                                <i class="fa fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="empty-message">No availability set.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $availabilities->links('pagination.custom') }}
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
    window.availabilities = @json($calendarAvailabilities);
</script>
<script src="{{ asset('js/availability.js') }}"></script>
@endsection
