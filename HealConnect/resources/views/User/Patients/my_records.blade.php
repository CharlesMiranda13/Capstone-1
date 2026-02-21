@extends('layouts.patient_layout')

@section('title', 'My Health Profile - Record Details')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/client.css') }}">
<!-- Font Awesome for EMR Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endsection

@section('content')
<main class="therapist-patient-records patient-portal-view">
    <div class="container">
        {{-- Health Snapshot Banner --}}
        <div class="patient-banner">
            <div class="patient-banner-avatar" style="background: linear-gradient(135deg, #0ea5e9, #2563eb);">
                <i class="fas fa-file-medical text-white"></i>
            </div>
            <div class="patient-banner-info">
                <div class="d-flex align-items-center mb-1">
                    <h2 class="mb-0">Health Record Snapshot</h2>
                    <span class="status-badge status-active ms-3">Historical Record</span>
                </div>
                <div class="patient-banner-meta">
                    <span><i class="far fa-calendar-alt me-2"></i>Date: {{ $recordDateTime->format('F d, Y') }}</span>
                    <span><i class="far fa-clock me-2"></i>Time: {{ $recordDateTime->format('h:i A') }}</span>
                    @if(isset($changedField))
                        <span><i class="fas fa-sync-alt me-2"></i>Updated: {{ ucfirst(str_replace('_', ' ', $changedField)) }}</span>
                    @endif
                </div>
            </div>

            {{-- Therapist Profile --}}
            @if($record->therapist)
            <div class="record-therapist-pill">
                <div class="record-therapist-avatar">
                    @if($record->therapist->profile_picture)
                        <img src="{{ asset('storage/' . $record->therapist->profile_picture) }}" alt="{{ $record->therapist->name }}">
                    @else
                        <span>{{ strtoupper(substr($record->therapist->name, 0, 1)) }}</span>
                    @endif
                </div>
                <div class="record-therapist-info">
                    <span class="record-therapist-label">Recorded by</span>
                    <span class="record-therapist-name">{{ $record->therapist->name }}</span>
                    <span class="record-therapist-role">{{ ucfirst($record->therapist->role_display ?? $record->therapist->role) }}</span>
                </div>
            </div>
            @endif
        </div>

        {{-- === EHR SECTION === --}}
        <section class="emr-section">
            <div class="emr-section-header">
                <h3>
                    <i class="fas fa-notes-medical text-primary"></i> Electronic Health Record
                    @if(isset($changedField) && $changedField === 'ehr')
                        <span class="status-badge status-active ms-2" style="font-size: 0.7rem; padding: 0.2rem 0.6rem; background: #e0f2fe; color: #0369a1;">Updated in this version</span>
                    @endif
                </h3>
            </div>

            <div class="emr-card">
                @if (!empty($ehr))
                    @php
                        $ehrLines = explode("\n", $ehr);
                        $ehrData = [];
                        foreach ($ehrLines as $line) {
                            $parts = explode(": ", $line, 2);
                            if (count($parts) == 2) {
                                $ehrData[trim($parts[0])] = trim($parts[1]);
                            }
                        }
                    @endphp
                    <div class="emr-records-grid">
                        <div class="record-panel {{ isset($changedField) && $changedField === 'ehr' ? 'border-primary' : '' }}">
                            <label>Primary Diagnosis</label>
                            <div class="content">{{ $ehrData['Diagnosis'] ?? 'No diagnosis recorded' }}</div>
                        </div>
                        <div class="record-panel">
                            <label>Known Allergies</label>
                            <div class="content">{{ $ehrData['Allergies'] ?? 'No allergies reported' }}</div>
                        </div>
                        <div class="record-panel">
                            <label>Prescribed Medications</label>
                            <div class="content">{{ $ehrData['Medications'] ?? 'None recorded' }}</div>
                        </div>
                        <div class="record-panel">
                            <label>Medical History</label>
                            <div class="content text-truncate-multiline" style="-webkit-line-clamp: 3; display: -webkit-box; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $ehrData['Medical History'] ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <div class="empty-icon-wrap" style="background: #eff6ff;">
                            <i class="fas fa-folder-open" style="color: #2563eb; font-size: 2rem;"></i>
                        </div>
                        <p class="text-muted mt-2">No health record data available for this snapshot.</p>
                    </div>
                @endif
            </div>
        </section>

        {{-- === TREATMENT PLAN === --}}
        <section class="emr-section">
            <div class="emr-section-header">
                <h3>
                    <i class="fas fa-clipboard-list text-success"></i> Comprehensive Treatment Plan
                    @if(isset($changedField) && $changedField === 'therapies')
                        <span class="status-badge status-active ms-2" style="font-size: 0.7rem; padding: 0.2rem 0.6rem; background: #dcfce7; color: #15803d;">Updated in this version</span>
                    @endif
                </h3>
            </div>

            <div class="emr-card">
                @if (!empty($therapies))
                    @php
                        $therapyEntries = array_filter(explode("\n\n", $therapies));
                        $perPage = 5;
                        $currentPage = request()->get('therapy_page', 1);
                        $totalEntries = count($therapyEntries);
                        $totalPages = ceil($totalEntries / $perPage);
                        $currentPage = max(1, min($currentPage, $totalPages));
                        $offset = ($currentPage - 1) * $perPage;
                        $currentEntries = array_slice($therapyEntries, $offset, $perPage);
                    @endphp
                    
                    <div class="treatment-list">
                        @foreach ($currentEntries as $therapy)
                            <div class="treatment-entry p-3 bg-light border-start border-success border-4 rounded mb-3">
                                {!! nl2br(e($therapy)) !!}
                            </div>
                        @endforeach
                    </div>

                    @if ($totalPages > 1)
                        <div class="pagination-container border-0 bg-light p-2 rounded mt-3">
                            <div class="pagination-info">Showing {{ $offset + 1 }}-{{ min($offset + $perPage, $totalEntries) }} of {{ $totalEntries }}</div>
                            <div class="pagination-controls">
                                <button class="pagination-btn {{ $currentPage <= 1 ? 'disabled' : '' }}" onclick="window.location.href='{{ request()->fullUrlWithQuery(['therapy_page' => $currentPage - 1]) }}#treatment-plan'">Prev</button>
                                <button class="pagination-btn active">{{ $currentPage }}</button>
                                <button class="pagination-btn {{ $currentPage >= $totalPages ? 'disabled' : '' }}" onclick="window.location.href='{{ request()->fullUrlWithQuery(['therapy_page' => $currentPage + 1]) }}#treatment-plan'">Next</button>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="text-center py-4">
                        <div class="empty-icon-wrap" style="background: #f0fdf4;">
                            <i class="fas fa-hand-holding-medical" style="color: #10b981; font-size: 2rem;"></i>
                        </div>
                        <p class="text-muted mt-2">No treatment strategies recorded in this snapshot.</p>
                    </div>
                @endif
            </div>
        </section>

        {{-- === PROGRESS NOTES === --}}
        <section class="emr-section">
            <div class="emr-section-header">
                <h3>
                    <i class="fas fa-history text-info"></i> Clinical Progress Notes
                    @if(isset($changedField) && $changedField === 'exercises')
                        <span class="status-badge status-active ms-2" style="font-size: 0.7rem; padding: 0.2rem 0.6rem; background: #e0f2fe; color: #0369a1;">Updated in this version</span>
                    @endif
                </h3>
            </div>

            <div class="emr-card">
                @if (!empty($exercises))
                    @php
                        $progressEntries = array_filter(explode("\n\n", $exercises));
                        $perPageProgress = 5;
                        $currentProgressPage = request()->get('progress_page', 1);
                        $totalProgressEntries = count($progressEntries);
                        $totalProgressPages = ceil($totalProgressEntries / $perPageProgress);
                        $currentProgressPage = max(1, min($currentProgressPage, $totalProgressPages));
                        $progressOffset = ($currentProgressPage - 1) * $perPageProgress;
                        $currentProgressEntries = array_slice($progressEntries, $progressOffset, $perPageProgress);
                    @endphp
                    
                    <div class="timeline ps-4 mt-2">
                        @foreach ($currentProgressEntries as $progress)
                            <div class="timeline-item">
                                <div class="timeline-dot" style="background: #0ea5e9;"></div>
                                <div class="timeline-content">
                                    <span class="timeline-date"><i class="far fa-clock me-1"></i> Recorded Entry</span>
                                    <div class="content">{!! nl2br(e($progress)) !!}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if ($totalProgressPages > 1)
                        <div class="pagination-container border-0 bg-light p-2 rounded mt-3">
                            <div class="pagination-info">Page {{ $currentProgressPage }} of {{ $totalProgressPages }}</div>
                            <div class="pagination-controls">
                                <button class="pagination-btn {{ $currentProgressPage <= 1 ? 'disabled' : '' }}" onclick="window.location.href='{{ request()->fullUrlWithQuery(['progress_page' => $currentProgressPage - 1]) }}#progress-notes'">Prev</button>
                                <button class="pagination-btn active">{{ $currentProgressPage }}</button>
                                <button class="pagination-btn {{ $currentProgressPage >= $totalProgressPages ? 'disabled' : '' }}" onclick="window.location.href='{{ request()->fullUrlWithQuery(['progress_page' => $currentProgressPage + 1]) }}#progress-notes'">Next</button>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="text-center py-4">
                        <div class="empty-icon-wrap" style="background: #f0f9ff;">
                            <i class="fas fa-book-medical" style="color: #0ea5e9; font-size: 2rem;"></i>
                        </div>
                        <p class="text-muted mt-2">No progress notes archived for this period.</p>
                    </div>
                @endif
            </div>
        </section>

        {{-- Help Alert --}}
        <div class="alert alert-info mt-4 d-flex align-items-center bg-white border-info-subtle shadow-sm rounded-3">
            <i class="fas fa-info-circle fa-2x text-info me-3"></i>
            <div>
                <strong>Medical Record Verification:</strong> 
                This snapshot represents your health profile as of {{ $recordDateTime->format('M d, Y') }}. 
                If you find any discrepancy, please discuss it with your therapist.
            </div>
        </div>

    </div>
</main>
@endsection