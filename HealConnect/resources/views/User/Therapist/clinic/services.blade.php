@extends('layouts.clinic_layout')

@section('title', 'Clinic Services & Availability')

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


        <!-- ====================== SERVICES ====================== -->
        <div class="services-container">
            <h3>Services Offered</h3>

            <form action="{{ route('clinic.services.store') }}" method="POST" class="service-form">
                @csrf

                <label>Service Name</label>
                <input type="text" name="name" placeholder="Physical Therapy Session" required>

                <div class="checkbox-group" style="gap:100px;">
                    <div style="flex:1;">
                        <label>Duration (minutes)</label>
                        <input type="number" name="duration" required>
                    </div>

                    <div style="flex:1;">
                        <label>Fee</label>
                        <input type="number" step="0.01" name="price">
                    </div>
                </div>

                <button type="submit" class="btn btn-success">Add Service</button>
            </form>

            <h3 style="margin-top:25px;">Existing Services</h3>

            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Duration</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($services as $service)
                        <tr>
                            <td>
                                <strong>{{ $service->name }}</strong>
                                <br>
                                <span class="text-muted">{{ $service->description }}</span>
                            </td>
                            <td>{{ $service->duration }} mins</td>
                            <td>₱{{ number_format($service->price,2) }}</td>
                            <td>
                                <button class="btn btn-primary open-modal" data-target="editServiceModal{{ $service->id }}">Edit</button>

                                <form action="{{ route('clinic.services.destroy', $service->id) }}" 
                                      method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>

                        <!-- EDIT MODAL -->
                        <div id="editServiceModal{{ $service->id }}" class="modal">
                            <div class="modal-content">
                                <form action="{{ route('clinic.services.update', $service->id) }}" method="POST">
                                    @csrf @method('PUT')

                                    <h4>Edit Service</h4>
                                    <span class="close-modal">&times;</span>

                                    <label>Name</label>
                                    <input type="text" name="name" value="{{ $service->name }}" required>

                                    <label>Description</label>
                                    <textarea name="description" rows="3">{{ $service->description }}</textarea>

                                    <div class="checkbox-group">
                                        <div style="flex:1;">
                                            <label>Duration</label>
                                            <input type="number" name="duration" value="{{ $service->duration }}" required>
                                        </div>

                                        <div style="flex:1;">
                                            <label>Price</label>
                                            <input type="number" step="0.01" name="price" value="{{ $service->price }}" required>
                                        </div>
                                    </div>

                                    <button class="btn btn-primary">Save Changes</button>
                                </form>
                            </div>
                        </div>

                    @empty
                        <tr><td colspan="4">No services yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>


        <hr>


        <!-- ====================== AVAILABILITY ====================== -->
        <h3>Weekly Availability</h3>

        <div class="availability-form-container">
            <form action="{{ route('clinic.schedules.store') }}" method="POST" class="availability-form">
                @csrf

                <label>Day of the Week:</label>
                <select name="day_of_week" required>
                    @foreach(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $i => $day)
                        <option value="{{ $i }}">{{ $day }}</option>
                    @endforeach
                </select>

                <label>Start Time:</label>
                <input type="time" name="start_time" required>

                <label>End Time:</label>
                <input type="time" name="end_time" required>

                <label style="margin-top:10px;">
                    <input type="checkbox" name="is_active" value="1" checked> Active
                </label>

                <button class="btn btn-primary">Add Availability</button>
            </form>

            <div id="calendar"></div>
        </div>

        <h3>Existing Schedule</h3>

        <table>
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
                    <tr>
                        <td>{{ ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'][$schedule->day_of_week] }}</td>
                        <td>{{ date('h:i A', strtotime($schedule->start_time)) }} - {{ date('h:i A', strtotime($schedule->end_time)) }}</td>
                        <td>
                            @if($schedule->is_active)
                                <span class="status-active">Active</span>
                            @else
                                <span class="status-inactive">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('clinic.schedules.toggle', $schedule->id) }}" method="POST" style="display:inline;">
                                @csrf @method('PATCH')
                                <button class="btn btn-warning">{{ $schedule->is_active ? 'Disable' : 'Enable' }}</button>
                            </form>

                            <form action="{{ route('clinic.schedules.destroy', $schedule->id) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4">No schedule yet.</td></tr>
                @endforelse
            </tbody>
        </table>

    </div>
</main>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
    window.availabilities = @json($calendarSchedules ?? []);
</script>
<script src="{{ asset('js/availability.js') }}"></script>
@endsection
