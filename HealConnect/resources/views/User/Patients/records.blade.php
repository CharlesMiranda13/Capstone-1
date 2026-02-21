@extends('layouts.patient_layout')

@section('title', 'Medical Records')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/client.css') }}">
<!-- Font Awesome for EMR Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endsection

@section('content')
<main class="therapist-patients patient-portal-view">
    <div class="bg-white rounded-4 shadow-sm p-4 w-100">
        {{-- Header Section --}}
        <div class="page-header-row mb-4">
            <h2 class="page-title-new">Medical Records Archive</h2>
            <p class="page-subtitle">Your complete session history and health documentation.</p>
        </div>

        @if($records->count() > 0)
            <div class="hc-table-container hc-table-responsive">
                <table class="hc-table">
                    <thead>
                        <tr>
                            <th><i class="far fa-calendar-alt me-2"></i>Date & Time</th>
                            <th><i class="fas fa-info-circle me-2"></i>Activity / Update</th>
                            <th><i class="fas fa-user-md me-2"></i>Therapist</th>
                            <th class="text-end">Record Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($records as $record)
                            <tr>
                                <td>
                                    <div class="patient-meta">
                                        <span class="patient-name-main">{{ $record->created_at->format('M d, Y') }}</span>
                                        <span class="email-text">{{ $record->created_at->format('h:i A') }}</span>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $icon = 'fa-file-medical';
                                        $color = '#64748b';
                                        if (str_contains(strtolower($record->description), 'ehr')) { $icon = 'fa-notes-medical'; $color = '#3b82f6'; }
                                        elseif (str_contains(strtolower($record->description), 'treatment')) { $icon = 'fa-clipboard-list'; $color = '#10b981'; }
                                        elseif (str_contains(strtolower($record->description), 'progress')) { $icon = 'fa-history'; $color = '#0ea5e9'; }
                                    @endphp
                                    <div class="d-flex align-items-center">
                                        <i class="fas {{ $icon }} me-3" style="color: {{ $color }}; font-size: 1.1rem;"></i>
                                        <span class="fw-500 color-slate-700">{{ $record->description }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="patient-avatar-name">
                                        <div class="patient-avatar-init" style="width: 35px; height: 35px; font-size: 0.9rem;">
                                            {{ strtoupper(substr($record->therapist->name, 0, 1)) }}
                                        </div>
                                        <span class="patient-name-main" style="font-size: 0.95rem;">{{ $record->therapist->name }}</span>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('patient.my_records', [
                                        'record_id' => $record->id,
                                        'timestamp' => $record->created_at->timestamp
                                    ]) }}" 
                                       class="btn-emr-primary py-2 px-3" style="font-size: 0.85rem; text-decoration: none;">
                                        View Full Report
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="pagination-container mt-4 pt-3 border-top">
                <div class="pagination-info">
                    Showing {{ $records->firstItem() }} to {{ $records->lastItem() }} of {{ $records->total() }} recorded snapshots
                </div>
                <div class="pagination-wrapper">
                    {{ $records->links('pagination.custom') }}
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-folder-open fa-4x text-muted opacity-50"></i>
                </div>
                <h3 class="text-slate-800 fw-bold">No medical records archived</h3>
                <p class="text-slate-500">Your health records will appear here as they are generated by your therapist.</p>
            </div>
        @endif
    </div>
</main>
@endsection