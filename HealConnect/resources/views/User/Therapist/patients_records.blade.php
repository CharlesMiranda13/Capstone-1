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
<!-- Font Awesome for EMR Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endsection

@section('content')
<main class="therapist-patient-records {{ $user->role === 'patient' ? 'patient-portal-view' : '' }}">
    <div class="container">
        {{-- Success Message --}}
        @if(session('success'))
            <div class="alert alert-success mt-3 mb-4">{{ session('success') }}</div>
        @endif

        {{-- Patient Banner - Professional EMR Style --}}
        <div class="patient-banner">
            <div class="patient-banner-avatar">
                {{ strtoupper(substr($patient->name, 0, 1)) }}
            </div>
            <div class="patient-banner-info">
                <div class="d-flex align-items-center mb-1">
                    <h2 class="mb-0">{{ $user->role === 'patient' ? 'My Health Profile' : $patient->name }}</h2>
                    <span class="hc-badge hc-badge-success ms-3">{{ $user->role === 'patient' ? 'Verified Account' : 'Active Patient' }}</span>
                </div>
                <div class="patient-banner-meta">
                    <span><i class="far fa-user me-2"></i>{{ ucfirst($patient->gender ?? 'N/A') }}</span>
                    <span><i class="far fa-calendar-alt me-2"></i>Age: {{ $patient->age ?? 'N/A' }}</span>
                    <span><i class="far fa-envelope me-2"></i>{{ $patient->email }}</span>
                    <span><i class="fas fa-id-card me-2"></i>ID: #{{ $patient->id }}</span>
                </div>
            </div>
        </div>

        {{-- === EHR SECTION === --}}
        <section class="emr-section">
            <div class="emr-section-header">
                <h3><i class="fas fa-notes-medical text-primary"></i> Electronic Health Record</h3>
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
                        <div class="record-panel">
                            <label>Primary Diagnosis</label>
                            <div class="content">{{ $ehrData['Diagnosis'] ?? 'No diagnosis recorded' }}</div>
                        </div>
                        <div class="record-panel">
                            <label>Allergies & Sensitivities</label>
                            <div class="content">{{ $ehrData['Allergies'] ?? 'None documented' }}</div>
                        </div>
                        <div class="record-panel">
                            <label>Active Medications</label>
                            <div class="content">{{ $ehrData['Medications'] ?? 'No medications listed' }}</div>
                        </div>
                        <div class="record-panel">
                            <label>Medical History</label>
                            <div class="content">{{ $ehrData['Medical History'] ?? 'No history provided' }}</div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Missing patient health record data.</p>
                    </div>
                @endif

                @if($user->role === 'therapist' || $user->role === 'clinic')
                    <div class="mt-4 pt-4 border-top">
                        <h4 class="mb-3">Update EHR Data</h4>
                        <form action="{{ route('therapist.ehr.update', $patient->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6 emr-form-group">
                                    <label>Diagnosis</label>
                                    <input type="text" name="diagnosis" value="{{ $ehrData['Diagnosis'] ?? '' }}" class="emr-input" placeholder="e.g. Major Depressive Disorder">
                                </div>
                                <div class="col-md-6 emr-form-group">
                                    <label>Allergies</label>
                                    <input type="text" name="allergies" value="{{ $ehrData['Allergies'] ?? '' }}" class="emr-input" placeholder="e.g. Penicillin">
                                </div>
                                <div class="col-12 emr-form-group">
                                    <label>Medications</label>
                                    <textarea name="medications" rows="2" class="emr-input" placeholder="List active medications...">{{ $ehrData['Medications'] ?? '' }}</textarea>
                                </div>
                                <div class="col-12 emr-form-group">
                                    <label>Additional Notes</label>
                                    <textarea name="notes" rows="2" class="emr-input" placeholder="Confidential medical notes...">{{ $ehrData['Notes'] ?? '' }}</textarea>
                                </div>
                            </div>
                            <button type="submit" class="hc-btn hc-btn-primary mt-2">Update Electronic Record</button>
                        </form>
                    </div>
                @endif
            </div>
        </section>

        {{-- === TREATMENT PLAN === --}}
        <section class="emr-section">
            <div class="emr-section-header">
                <h3><i class="fas fa-clipboard-list text-success"></i> Comprehensive Treatment Plan</h3>
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
                    
                    <div class="treatment-list mb-4">
                        @foreach ($currentEntries as $therapy)
                            <div class="treatment-entry p-3 bg-light border-start border-success border-4 rounded mb-3">
                                {{ $therapy }}
                            </div>
                        @endforeach
                    </div>

                    @if ($totalPages > 1)
                        <div class="pagination-container border-0 bg-light p-2 rounded">
                            <div class="pagination-info">Showing {{ $offset + 1 }}-{{ min($offset + $perPage, $totalEntries) }} of {{ $totalEntries }}</div>
                            <div class="pagination-controls">
                                <button class="pagination-btn {{ $currentPage <= 1 ? 'disabled' : '' }}" onclick="window.location.href='?therapy_page={{ $currentPage - 1 }}#treatment-plan'">Prev</button>
                                <button class="pagination-btn active">{{ $currentPage }}</button>
                                <button class="pagination-btn {{ $currentPage >= $totalPages ? 'disabled' : '' }}" onclick="window.location.href='?therapy_page={{ $currentPage + 1 }}#treatment-plan'">Next</button>
                            </div>
                        </div>
                    @endif
                @else
                    <p class="text-muted text-center py-3">No active treatment strategies defined.</p>
                @endif

                @if($user->role === 'therapist' || $user->role === 'clinic')
                    <div class="mt-4 pt-4 border-top" id="treatment-plan">
                        <h4 class="mb-3">Append Treatment Strategy</h4>
                        <form action="{{ route('therapist.treatment.update', $patient->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-4 emr-form-group">
                                    <label>Session Date</label>
                                    <input type="date" name="session_date" class="emr-input" required>
                                </div>
                                <div class="col-md-8 emr-form-group">
                                    <label>Strategy / Goal Description</label>
                                    <input type="text" name="description" class="emr-input" placeholder="e.g. Cognitive Behavioral Therapy - Phase 1" required>
                                </div>
                            </div>
                            <button type="submit" class="hc-btn hc-btn-primary" style="background-color: #059669;">Log Treatment Strategy</button>
                        </form>
                    </div>
                @endif
            </div>
        </section>

        {{-- === PROGRESS NOTES === --}}
        <section class="emr-section">
            <div class="emr-section-header">
                <h3><i class="fas fa-history text-info"></i> Clinical Progress Notes</h3>
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
                                <div class="timeline-dot"></div>
                                <div class="timeline-content">
                                    <span class="timeline-date"><i class="far fa-clock me-1"></i> Recorded Entry</span>
                                    <div class="content">{{ $progress }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if ($totalProgressPages > 1)
                        <div class="pagination-container border-0 bg-light p-2 rounded mt-3">
                            <div class="pagination-info">Page {{ $currentProgressPage }} of {{ $totalProgressPages }}</div>
                            <div class="pagination-controls">
                                <button class="pagination-btn {{ $currentProgressPage <= 1 ? 'disabled' : '' }}" onclick="window.location.href='?progress_page={{ $currentProgressPage - 1 }}#progress-notes'">Prev</button>
                                <button class="pagination-btn {{ $currentProgressPage >= $totalProgressPages ? 'disabled' : '' }}" onclick="window.location.href='?progress_page={{ $currentProgressPage + 1 }}#progress-notes'">Next</button>
                            </div>
                        </div>
                    @endif
                @else
                    <p class="text-muted text-center py-3">No progress notes archived for this patient.</p>
                @endif

                @if($user->role === 'therapist' || $user->role === 'clinic')
                    <div class="mt-4 pt-4 border-top" id="progress-notes">
                        <h4 class="mb-3">New Progress Observation</h4>
                        <form action="{{ route('therapist.progress.update', $patient->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="emr-form-group">
                                <textarea name="notes" rows="3" class="emr-input" placeholder="Document clinical observations, mood status, and progress towards goals..." required></textarea>
                            </div>
                            <button type="submit" class="hc-btn hc-btn-primary" style="background-color: #0ea5e9;">Archive Progress Note</button>
                        </form>
                    </div>
                @endif
            </div>
        </section>

    </div>
</main>
@endsection