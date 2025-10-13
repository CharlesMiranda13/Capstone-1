@extends('layouts.therapist')
@section('title', 'Therapist Availability')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/appointment.css') }}">
@endsection

@section('content')
<div class="availability-container">
    <h2>My Availability</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Add New Availability --}}
    <form action="{{ route('therapist.availability.store') }}" method="POST" class="availability-form">
        @csrf
        <label>Day of Week:</label>
        <select name="day_of_week" required>
            <option value="">Select Day</option>
            <option>Monday</option>
            <option>Tuesday</option>
            <option>Wednesday</option>
            <option>Thursday</option>
            <option>Friday</option>
            <option>Saturday</option>
            <option>Sunday</option>
        </select>

        <label>Start Time:</label>
        <input type="time" name="start_time" required>

        <label>End Time:</label>
        <input type="time" name="end_time" required>

        <button type="submit" class="btn btn-primary">Add Availability</button>
    </form>

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
                <tr>
                    <td>{{ $availability->day_of_week }}</td>
                    <td>{{ date('h:i A', strtotime($availability->start_time)) }}</td>
                    <td>{{ date('h:i A', strtotime($availability->end_time)) }}</td>
                    <td>
                        @if($availability->is_active)
                            <span class="status-active">Active</span>
                        @else
                            <span class="status-inactive">Cancelled</span>
                        @endif
                    </td>
                    <td>
                        {{-- Cancel or Reactivate --}}
                        <form action="{{ route('therapist.availability.toggle', $availability->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-warning">
                                {{ $availability->is_active ? 'Cancel' : 'Reactivate' }}
                            </button>
                        </form>

                        {{-- Delete --}}
                        <form action="{{ route('therapist.availability.destroy', $availability->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">No availability set.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
