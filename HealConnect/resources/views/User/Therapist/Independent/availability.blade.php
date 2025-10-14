@extends('layouts.therapist')
@section('title', 'Therapist Availability')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/appointment.css') }}">
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />
@endsection

@section('content')
<h2 style="text-align:center;">My Services & Availability</h2>

<div class="availability-container">
    {{-- Success message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Form + Calendar Side by Side --}}
    <div class="availability-form-container">
        {{-- Add New Availability --}}
        <form action="{{ route('therapist.availability.store') }}" method="POST" class="availability-form">
            @csrf
            <label>Date:</label>
            <input type="date" name="date" required>

            <label>Start Time:</label>
            <input type="time" name="start_time" required>

            <label>End Time:</label>
            <input type="time" name="end_time" required>

            <button type="submit" class="btn btn-primary">Add Availability</button>
        </form>

        {{-- Calendar --}}
        <div id="calendar"></div>
    </div>

    {{-- Existing Schedule --}}
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
                    <td>{{ $availability->day_of_week }}, {{ \Carbon\Carbon::parse($availability->date)->format('F j, Y') }}</td>
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


@section('scripts')
    {{-- FullCalendar JS --}}
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

    {{-- Pass data to JS --}}
    <script>
        window.availabilities = @json($availabilities);
    </script>


    <script src="{{ asset('js/availability.js') }}"></script>
@endsection
