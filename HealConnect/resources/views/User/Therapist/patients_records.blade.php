@php
    use Illuminate\Support\Facades\Auth;

    $user = Auth::user();
    switch ($user->role ?? 'therapist') {
        case 'therapist':
            $layout = 'layouts.therapist';
            break;
        case 'clinic':
            $layout = 'layouts.clinic_layout';
            break;
        case 'patient':
            $layout = 'layouts.patient_layout';
            break;
        default:
            $layout = 'layouts.therapist';
            break;
    }
@endphp

@extends($layout)

@section('title', $patient->name . ' - Medical Records')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/client.css') }}">
@endsection

@section('content')
<main class="therapist-patient-records">
    <div class="container">
        {{-- Success Message --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Header --}}
        <div class="header">
            <h2>{{ $patient->name }}'s Medical Records</h2>
            <p class="subtitle">Review and manage electronic health information below.</p>
        </div>

        {{-- EHR Section --}}
        <section class="record-section">
            <div class="section-header">
                <h3>🩺 Electronic Health Record (EHR)</h3>
                <p class="text-muted">Comprehensive patient information — diagnosis, allergies, medications, and more.</p>
            </div>

            {{-- EHR Display --}}
            <div class="record-display card">
                @if (!empty($ehr))
                    @php
                        // Parse the EHR text into individual fields
                        $ehrLines = explode("\n", $ehr);
                        $ehrData = [];
                        foreach ($ehrLines as $line) {
                            $parts = explode(": ", $line, 2);
                            if (count($parts) == 2) {
                                $ehrData[trim($parts[0])] = trim($parts[1]);
                            }
                        }
                    @endphp
                    <div class="ehr-grid">
                        <div><strong>Diagnosis:</strong> {{ $ehrData['Diagnosis'] ?? '—' }}</div>
                        <div><strong>Allergies:</strong> {{ $ehrData['Allergies'] ?? '—' }}</div>
                        <div><strong>Medications:</strong> {{ $ehrData['Medications'] ?? '—' }}</div>
                        <div><strong>Medical History:</strong> {{ $ehrData['Medical History'] ?? '—' }}</div>
                        <div><strong>Notes:</strong> {{ $ehrData['Notes'] ?? '—' }}</div>
                    </div>
                @else
                    <p class="text-muted">No EHR record found. Your therapist will add information during your sessions.</p>
                @endif
            </div>

            {{-- Update Form --}}
            @if($user->role === 'therapist' || $user->role === 'clinic')
            <form action="{{ route('therapist.ehr.update', $patient->id) }}" method="POST" class="update-form mt-4">
                @csrf
                @method('PUT')
                <h4>Update EHR</h4>

                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="form-grid">
                    <label>Diagnosis</label>
                    <input type="text" name="diagnosis" value="" class="form-control">

                    <label>Allergies</label>
                    <input type="text" name="allergies" value="" class="form-control">

                    <label>Medications</label>
                    <textarea name="medications" rows="2" class="form-control"></textarea>

                    <label>Medical History</label>
                    <textarea name="medical_history" rows="2" class="form-control"></textarea>

                    <label>Additional Notes</label>
                    <textarea name="notes" rows="3" class="form-control"></textarea>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Save / Update EHR</button>
            </form>
            @endif
        </section>

        <hr>

        {{-- === TREATMENT PLAN === --}}
        <section class="record-section">
            <div class="section-header">
                <h3>💊 Treatment Plan</h3>
                <p class="text-muted">Track and update ongoing treatment strategies.</p>
            </div>

            {{-- Display with Pagination --}}
            <div class="record-display card">
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
                                {{ $therapy }}
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
                                    onclick="window.location.href='?therapy_page={{ $currentPage - 1 }}&progress_page={{ request()->get('progress_page', 1) }}#treatment-plan'"
                                    {{ $currentPage <= 1 ? 'disabled' : '' }}>
                                    Previous
                                </button>
                                
                                @php
                                    $range = 2; // Show 2 pages on each side of current page
                                    $start = max(1, $currentPage - $range);
                                    $end = min($totalPages, $currentPage + $range);
                                @endphp
                                
                                @if($start > 1)
                                    <button class="pagination-btn" onclick="window.location.href='?therapy_page=1&progress_page={{ request()->get('progress_page', 1) }}#treatment-plan'">1</button>
                                    @if($start > 2)
                                        <span class="pagination-ellipsis">...</span>
                                    @endif
                                @endif
                                
                                @for($i = $start; $i <= $end; $i++)
                                    <button 
                                        class="pagination-btn {{ $i == $currentPage ? 'active' : '' }}" 
                                        onclick="window.location.href='?therapy_page={{ $i }}&progress_page={{ request()->get('progress_page', 1) }}#treatment-plan'">
                                        {{ $i }}
                                    </button>
                                @endfor
                                
                                @if($end < $totalPages)
                                    @if($end < $totalPages - 1)
                                        <span class="pagination-ellipsis">...</span>
                                    @endif
                                    <button class="pagination-btn" onclick="window.location.href='?therapy_page={{ $totalPages }}&progress_page={{ request()->get('progress_page', 1) }}#treatment-plan'">{{ $totalPages }}</button>
                                @endif
                                
                                <button 
                                    class="pagination-btn" 
                                    onclick="window.location.href='?therapy_page={{ $currentPage + 1 }}&progress_page={{ request()->get('progress_page', 1) }}#treatment-plan'"
                                    {{ $currentPage >= $totalPages ? 'disabled' : '' }}>
                                    Next
                                </button>
                            </div>
                        </div>
                    @endif
                @else
                    <p class="text-muted">No treatment plan recorded yet. Your therapist will add this information.</p>
                @endif
            </div>

            {{-- Update Form --}}
            @if($user->role === 'therapist' || $user->role === 'clinic')
            <form action="{{ route('therapist.treatment.update', $patient->id) }}" method="POST" class="update-form mt-4" id="treatment-plan">
                @csrf
                @method('PUT')
                <h4>Add / Update Treatment Plan</h4>
                <div class="form-grid">
                    <label>Date</label>
                    <input type="date" name="session_date" class="form-control" required>

                    <label>Description</label>
                    <textarea name="description" rows="3" class="form-control" placeholder="Describe the treatment plan..." required></textarea>
                </div>
                <button type="submit" class="btn btn-success mt-3">Save Treatment Plan</button>
            </form>
            @endif
        </section>

        <hr>

        {{-- === PROGRESS NOTES === --}}
        <section class="record-section">
            <div class="section-header">
                <h3>📘 Progress Notes</h3>
                <p class="text-muted">Document patient's progress and follow-up evaluations.</p>
            </div>

            {{-- Display with Pagination --}}
            <div class="record-display card">
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
                                {{ $progress }}
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
                                    onclick="window.location.href='?therapy_page={{ request()->get('therapy_page', 1) }}&progress_page={{ $currentProgressPage - 1 }}#progress-notes'"
                                    {{ $currentProgressPage <= 1 ? 'disabled' : '' }}>
                                    Previous
                                </button>
                                
                                @php
                                    $progressRange = 2; // Show 2 pages on each side of current page
                                    $progressStart = max(1, $currentProgressPage - $progressRange);
                                    $progressEnd = min($totalProgressPages, $currentProgressPage + $progressRange);
                                @endphp
                                
                                @if($progressStart > 1)
                                    <button class="pagination-btn" onclick="window.location.href='?therapy_page={{ request()->get('therapy_page', 1) }}&progress_page=1#progress-notes'">1</button>
                                    @if($progressStart > 2)
                                        <span class="pagination-ellipsis">...</span>
                                    @endif
                                @endif
                                
                                @for($i = $progressStart; $i <= $progressEnd; $i++)
                                    <button 
                                        class="pagination-btn {{ $i == $currentProgressPage ? 'active' : '' }}" 
                                        onclick="window.location.href='?therapy_page={{ request()->get('therapy_page', 1) }}&progress_page={{ $i }}#progress-notes'">
                                        {{ $i }}
                                    </button>
                                @endfor
                                
                                @if($progressEnd < $totalProgressPages)
                                    @if($progressEnd < $totalProgressPages - 1)
                                        <span class="pagination-ellipsis">...</span>
                                    @endif
                                    <button class="pagination-btn" onclick="window.location.href='?therapy_page={{ request()->get('therapy_page', 1) }}&progress_page={{ $totalProgressPages }}#progress-notes'">{{ $totalProgressPages }}</button>
                                @endif
                                
                                <button 
                                    class="pagination-btn" 
                                    onclick="window.location.href='?therapy_page={{ request()->get('therapy_page', 1) }}&progress_page={{ $currentProgressPage + 1 }}#progress-notes'"
                                    {{ $currentProgressPage >= $totalProgressPages ? 'disabled' : '' }}>
                                    Next
                                </button>
                            </div>
                        </div>
                    @endif
                @else
                    <p class="text-muted">No progress notes recorded yet. Your therapist will document your progress during sessions.</p>
                @endif
            </div>

            {{-- Update Form --}}
            @if($user->role === 'therapist' || $user->role === 'clinic')
            <form action="{{ route('therapist.progress.update', $patient->id) }}" method="POST" class="update-form mt-4" id="progress-notes">
                @csrf
                @method('PUT')
                <h4>Add Progress Note</h4>
                <textarea name="notes" rows="3" class="form-control" placeholder="Enter progress observation..." required></textarea>
                <button type="submit" class="btn btn-info mt-3">Save Progress Note</button>
            </form>
            @endif
        </section>

    </div>
</main>
@endsection