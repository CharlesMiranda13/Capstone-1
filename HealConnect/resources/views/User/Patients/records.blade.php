@extends('layouts.patient_layout')

@section('title', 'Medical Records')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/records.css') }}">
@endsection

@section('content')
<main class="records-main">
    <div class="records-content">
        <h2>Medical Records</h2>
        <p class="subtitle">Click on any record to view full details</p>
        
        @if($records->count() > 0)
            <div class="records-table-wrapper">
                <table class="records-table">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Description</th>
                            <th>Doctor</th>
                            <th>Record</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($records as $record)
                            <tr>
                                <td data-label="Date & Time">
                                    {{ $record->created_at->format('M d, Y') }} 
                                    <small style="color: #6c757d;">{{ $record->created_at->format('h:i A') }}</small>
                                </td>
                                <td data-label="Description">{{ $record->description }}</td>
                                <td data-label="Doctor">{{ $record->therapist->name }}</td>
                                <td data-label="Record">
                                    <a href="{{ route('patient.my_records', [
                                        'record_id' => $record->id,
                                        'timestamp' => $record->created_at->timestamp
                                    ]) }}" 
                                       class="btn-view-records">
                                        View Records
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Move pagination inside records-content -->
            <div class="pagination-wrapper">
                {{ $records->links('pagination.custom') }}
            </div>
        @else
            <div class="no-records">
                <h3>No medical records yet</h3>
                <p>Your therapist will create records during your sessions</p>
            </div>
        @endif
    </div>
</main>
@endsection