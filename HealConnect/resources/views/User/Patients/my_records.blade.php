@extends('layouts.patient_layout')

@section('title', 'My Medical Records')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/client.css') }}">
@endsection

@section('content')
<main class="therapist-patient-records">
    <div class="container">
        {{-- Header --}}
        <div class="header">
            <h2>My Medical Records</h2>
            <p class="subtitle">View your electronic health information below.</p>
        </div>

        {{-- Record Timestamp Banner --}}
        @if(isset($recordDateTime))
        <div class="record-timestamp-banner">
            <h3>📅 Record Snapshot</h3>
            <p>This record was created on <strong>{{ $recordDateTime->format('F d, Y \a\t h:i A') }}</strong></p>
            @if(isset($changedField))
            <p style="margin-top: 10px;">
                <strong>Updated:</strong> {{ ucfirst(str_replace('_', ' ', $changedField)) }}
            </p>
            @endif
            <span class="snapshot-badge">Historical Record</span>
        </div>
        @endif

        {{-- EHR Section --}}
        <section class="record-section">
            <div class="section-header">
                <h3>
                    🩺 Electronic Health Record (EHR)
                    @if(isset($changedField) && $changedField === 'ehr')
                        <span class="changed-indicator">✓ Changed in this record</span>
                    @endif
                </h3>
                <p class="text-muted">Your comprehensive health information — diagnosis, allergies, medications, and more.</p>
            </div>

            <div class="record-display card {{ isset($changedField) && $changedField === 'ehr' ? 'recently-changed' : '' }}">
                @if (!empty($ehr))
                    <div class="ehr-content">
                        {!! nl2br(e($ehr)) !!}
                    </div>
                @else
                    <p class="text-muted">No EHR record found at this point in time.</p>
                @endif
            </div>
        </section>

        <hr>

        {{-- === TREATMENT PLAN === --}}
        <section class="record-section">
            <div class="section-header">
                <h3>
                    💊 Treatment Plan
                    @if(isset($changedField) && $changedField === 'therapies')
                        <span class="changed-indicator">✓ Changed in this record</span>
                    @endif
                </h3>
                <p class="text-muted">Your ongoing treatment strategies and sessions.</p>
            </div>

            <div class="record-display card {{ isset($changedField) && $changedField === 'therapies' ? 'recently-changed' : '' }}">
                @if (!empty($therapies))
                    @php
                        // Split by double newlines to get individual entries
                        $therapyEntries = array_filter(explode("\n\n", $therapies));
                        $perPage = 5; // Show 5 entries per page
                        $currentPage = request()->get('therapy_page', 1);
                        $totalEntries = count($therapyEntries);
                        $totalPages = ceil($totalEntries / $perPage);
                        $currentPage = max(1, min($currentPage, $totalPages)); // Ensure valid page
                        
                        $offset = ($currentPage - 1) * $perPage;
                        $currentEntries = array_slice($therapyEntries, $offset, $perPage);
                    @endphp
                    
                    <div id="treatment-content">
                        @foreach ($currentEntries as $therapy)
                            <div class="treatment-entry">
                                {!! nl2br(e($therapy)) !!}
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination Controls --}}
                    @if ($totalPages > 1)
                        <div class="pagination-container">
                            <div class="pagination-info">
                                Showing {{ $offset + 1 }}-{{ min($offset + $perPage, $totalEntries) }} of {{ $totalEntries }} entries
                            </div>
                            <div class="pagination-controls">
                                <button 
                                    class="pagination-btn" 
                                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['therapy_page' => $currentPage - 1]) }}#treatment-plan'"
                                    {{ $currentPage <= 1 ? 'disabled' : '' }}>
                                    Previous
                                </button>
                                
                                @php
                                    $range = 2; // Show 2 pages on each side of current page
                                    $start = max(1, $currentPage - $range);
                                    $end = min($totalPages, $currentPage + $range);
                                @endphp
                                
                                @if($start > 1)
                                    <button class="pagination-btn" onclick="window.location.href='{{ request()->fullUrlWithQuery(['therapy_page' => 1]) }}#treatment-plan'">1</button>
                                    @if($start > 2)
                                        <span class="pagination-ellipsis">...</span>
                                    @endif
                                @endif
                                
                                @for($i = $start; $i <= $end; $i++)
                                    <button 
                                        class="pagination-btn {{ $i == $currentPage ? 'active' : '' }}" 
                                        onclick="window.location.href='{{ request()->fullUrlWithQuery(['therapy_page' => $i]) }}#treatment-plan'">
                                        {{ $i }}
                                    </button>
                                @endfor
                                
                                @if($end < $totalPages)
                                    @if($end < $totalPages - 1)
                                        <span class="pagination-ellipsis">...</span>
                                    @endif
                                    <button class="pagination-btn" onclick="window.location.href='{{ request()->fullUrlWithQuery(['therapy_page' => $totalPages]) }}#treatment-plan'">{{ $totalPages }}</button>
                                @endif
                                
                                <button 
                                    class="pagination-btn" 
                                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['therapy_page' => $currentPage + 1]) }}#treatment-plan'"
                                    {{ $currentPage >= $totalPages ? 'disabled' : '' }}>
                                    Next
                                </button>
                            </div>
                        </div>
                    @endif
                @else
                    <p class="text-muted">No treatment plan recorded at this point in time.</p>
                @endif
            </div>
        </section>

        <hr>

        {{-- === PROGRESS NOTES === --}}
        <section class="record-section" id="progress-notes">
            <div class="section-header">
                <h3>
                    📘 Progress Notes
                    @if(isset($changedField) && $changedField === 'exercises')
                        <span class="changed-indicator">✓ Changed in this record</span>
                    @endif
                </h3>
                <p class="text-muted">Your progress and follow-up evaluations documented by your therapist.</p>
            </div>

            <div class="record-display card {{ isset($changedField) && $changedField === 'exercises' ? 'recently-changed' : '' }}">
                @if (!empty($exercises))
                    @php
                        // Split by double newlines to get individual entries
                        $progressEntries = array_filter(explode("\n\n", $exercises));
                        $perPageProgress = 5; // Show 5 entries per page
                        $currentProgressPage = request()->get('progress_page', 1);
                        $totalProgressEntries = count($progressEntries);
                        $totalProgressPages = ceil($totalProgressEntries / $perPageProgress);
                        $currentProgressPage = max(1, min($currentProgressPage, $totalProgressPages)); // Ensure valid page
                        
                        $progressOffset = ($currentProgressPage - 1) * $perPageProgress;
                        $currentProgressEntries = array_slice($progressEntries, $progressOffset, $perPageProgress);
                    @endphp
                    
                    <div id="progress-content">
                        @foreach ($currentProgressEntries as $progress)
                            <div class="progress-entry">
                                {!! nl2br(e($progress)) !!}
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination Controls --}}
                    @if ($totalProgressPages > 1)
                        <div class="pagination-container">
                            <div class="pagination-info">
                                Showing {{ $progressOffset + 1 }}-{{ min($progressOffset + $perPageProgress, $totalProgressEntries) }} of {{ $totalProgressEntries }} entries
                            </div>
                            <div class="pagination-controls">
                                <button 
                                    class="pagination-btn" 
                                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['progress_page' => $currentProgressPage - 1]) }}#progress-notes'"
                                    {{ $currentProgressPage <= 1 ? 'disabled' : '' }}>
                                    Previous
                                </button>
                                
                                @php
                                    $progressRange = 2; // Show 2 pages on each side of current page
                                    $progressStart = max(1, $currentProgressPage - $progressRange);
                                    $progressEnd = min($totalProgressPages, $currentProgressPage + $progressRange);
                                @endphp
                                
                                @if($progressStart > 1)
                                    <button class="pagination-btn" onclick="window.location.href='{{ request()->fullUrlWithQuery(['progress_page' => 1]) }}#progress-notes'">1</button>
                                    @if($progressStart > 2)
                                        <span class="pagination-ellipsis">...</span>
                                    @endif
                                @endif
                                
                                @for($i = $progressStart; $i <= $progressEnd; $i++)
                                    <button 
                                        class="pagination-btn {{ $i == $currentProgressPage ? 'active' : '' }}" 
                                        onclick="window.location.href='{{ request()->fullUrlWithQuery(['progress_page' => $i]) }}#progress-notes'">
                                        {{ $i }}
                                    </button>
                                @endfor
                                
                                @if($progressEnd < $totalProgressPages)
                                    @if($progressEnd < $totalProgressPages - 1)
                                        <span class="pagination-ellipsis">...</span>
                                    @endif
                                    <button class="pagination-btn" onclick="window.location.href='{{ request()->fullUrlWithQuery(['progress_page' => $totalProgressPages]) }}#progress-notes'">{{ $totalProgressPages }}</button>
                                @endif
                                
                                <button 
                                    class="pagination-btn" 
                                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['progress_page' => $currentProgressPage + 1]) }}#progress-notes'"
                                    {{ $currentProgressPage >= $totalProgressPages ? 'disabled' : '' }}>
                                    Next
                                </button>
                            </div>
                        </div>
                    @endif
                @else
                    <p class="text-muted">No progress notes recorded at this point in time.</p>
                @endif
            </div>
        </section>

        {{-- Info Box --}}
        <div class="alert alert-info mt-4">
            <strong>ℹ️ Note:</strong> 
            This shows your complete medical record as of {{ $recordDateTime->format('M d, Y \a\t h:i A') }}. 
            @if(isset($changedField))
                The <strong>{{ ucfirst(str_replace('_', ' ', $changedField)) }}</strong> was updated in this record.
            @endif
            If you have questions, please contact your therapist.
        </div>

    </div>
</main>
@endsection