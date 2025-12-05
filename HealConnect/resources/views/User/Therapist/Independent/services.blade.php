@extends('layouts.therapist')
@section('title', 'Therapist Services & Availability')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/therapist_appointment.css') }}">
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />
@endsection

@section('content')
<main class="availability-page">
    <h2>My Services & Schedule</h2>

    <div class="availability-container">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- SERVICES SECTION -->
        <div class="services-container">
            <h3>Appointment Types I Offer</h3>

            <form action="{{ route('therapist.services.store') }}" method="POST" class="service-form">
                @csrf
                <div class="checkbox-group">
                    <label>
                        <input type="checkbox" name="appointment_types[]" value="Online" {{ in_array('Online', $existingServices ?? []) ? 'checked' : '' }}> Online
                    </label>

                    <label>
                        <input type="checkbox" name="appointment_types[]" value="In-Clinic" {{ in_array('In-Clinic', $existingServices ?? []) ? 'checked' : '' }}> In-Clinic
                    </label>

                    <label>
                        <input type="checkbox" name="appointment_types[]" value="In-home" {{ in_array('In-home', $existingServices ?? []) ? 'checked' : '' }}> In-home
                    </label>
                </div>

                <!-- Price Input  -->
                
                <div class="service-price">
                    <label>Price / Fee</label>
                    <input type="text" name="price" value="{{ $existingPrice ?? '' }}" placeholder="Enter your price/fee" style=margin-top:10px;>
                </div>

                    <button type="submit" class="btn btn-success">Save Services</button>
            </form>


        </div>

        <hr>

        <!-- AVAILABILITY SECTION -->
        <h3>Set Your Availability</h3>
        <div class="availability-form-container">
            <form action="{{ route('therapist.availability.store') }}" method="POST" class="availability-form">
                @csrf
                <label>Date:</label>
                <input type="date" name="date" required min="{{ date('Y-m-d') }}">
                <label>Start Time:</label>
                <input type="time" name="start_time" required>
                <label>End Time:</label>
                <input type="time" name="end_time" required>
                <button type="submit" class="btn btn-primary">Add Availability</button>
            </form>

            <div id="calendar"></div>
        </div>

        <h3>Existing Schedule</h3>
        <table>
            <thead>
                <tr>
                    <th>Day</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($availabilities as $availability)
                    @php
                        $bookedCount = $availability->appointments->count();
                    @endphp
                    <tr>
                        <td>{{ $availability->day_of_week }}, {{ \Carbon\Carbon::parse($availability->date)->format('F j, Y') }}</td>
                        <td>{{ date('h:i A', strtotime($availability->start_time)) }}</td>
                        <td>{{ date('h:i A', strtotime($availability->end_time)) }}</td>
                        <td>
                            @if($availability->is_active)
                                <span class="status-active">Active</span>
                            @else
                                <span class="status-inactive">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <!-- Toggle Active/Inactive -->
                            <form action="{{ route('therapist.availability.toggle', $availability->id) }}" method="POST" class="action-form">
                                @csrf @method('PATCH')
                                <button class="btn btn-warning">{{ $availability->is_active ? 'Disable' : 'Enable' }}</button>
                            </form>

                            <!-- Delete -->
                            <form action="{{ route('therapist.availability.destroy', $availability->id) }}" method="POST" class="action-form">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">No availability set.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-container">
            {{ $availabilities->links() }}
        </div>
    </div>
</main>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
    window.availabilities = @json($calendarAvailabilities);
</script>
<script src="{{ asset('js/availability.js') }}"></script>
@endsection
