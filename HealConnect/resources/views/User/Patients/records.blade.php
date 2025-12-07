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
            <table class="records-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Doctor</th>
                        <th>Record</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $record)
                        <tr>
                            <td>{{ $record->record_date->format('M d, Y') }}</td>
                            <td>{{ $record->description }}</td>
                            <td>{{ $record->therapist->name }}</td>
                            <td>
                                <a href="{{ route('patient.my_records') }}" class="btn-open" target="_blank">
                                    View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-records">
                <h3 style="text-align: center; margin-top: 40px; color: #6c757d;">
                    No medical records yet
                </h3>
                <p style="text-align: center; color: #adb5bd;">
                    Your therapist will create records during your sessions
                </p>
            </div>
        @endif
    </div>
</main>
@endsection

