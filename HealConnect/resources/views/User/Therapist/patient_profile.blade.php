@php
    use Illuminate\Support\Facades\Auth;
    
    $user = Auth::user();
    
    switch ($user->role) {
        case 'therapist':
            $layout = 'layouts.therapist';
            break;
        case 'clinic':
            $layout = 'layouts.clinic_layout';
            break;
        default:
            $layout = 'layouts.therapist';
            break;
    }
@endphp

@if (!request()->has('embed'))
    @extends($layout)

    @section('title', 'Patient Profile')

    @section('styles')
        <link rel="stylesheet" href="{{ asset('css/patient-profile.css') }}">
    @endsection

    @section('content')
@endif

<main class="patient-profile">
    <div class="container">
        {{-- Profile Header with Banner --}}
        <div class="profile-header-banner">
            <div class="header-overlay"></div>
            <div class="header-content">
                <div class="profile-main-info">
                    <div class="profile-pic">
                        <img src="{{ $patient->profile_picture ? asset('storage/' . $patient->profile_picture) : asset('images/logo1.png') }}" 
                            alt="{{ $patient->name }}">
                    </div>
                    <div class="profile-text">
                        <h2>{{ $patient->name }}</h2>
                        <span class="patient-id-badge">ID: #PT-{{ str_pad($patient->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('messages', ['receiver_id' => $patient->id]) }}" class="btn-message">
                        <i class="fa-solid fa-comments"></i> Send Message
                    </a>
                </div>
            </div>
        </div>

        {{-- Summary Stats Section --}}
        <div class="summary-grid">
            <div class="summary-card">
                <div class="card-icon blue"><i class="fa-solid fa-calendar-check"></i></div>
                <div class="card-info">
                    <span class="label">Total Appointments</span>
                    <span class="value">{{ isset($appointments) ? count($appointments) : 0 }}</span>
                </div>
            </div>
            <div class="summary-card">
                <div class="card-icon green"><i class="fa-solid fa-file-medical"></i></div>
                <div class="card-info">
                    <span class="label">Medical Records</span>
                    <span class="value">{{ $patient->record_count ?? 0 }}</span>
                </div>
            </div>
            <div class="summary-card">
                <div class="card-icon purple"><i class="fa-solid fa-cake-candles"></i></div>
                <div class="card-info">
                    <span class="label">Age / Gender</span>
                    <span class="value">
                        @if($patient->dob)
                            {{ \Carbon\Carbon::parse($patient->dob)->age }} yrs / 
                        @endif
                        {{ $patient->gender ? ucfirst($patient->gender) : '—' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="profile-content-grid">
            {{-- LEFT COLUMN: PERSONAL INFO & HEALTH --}}
            <div class="content-left">
                <div class="info-section">
                    <h3><i class="fa-solid fa-id-card"></i> Personal Information</h3>
                    <div class="info-list">
                        <div class="info-item">
                            <span class="info-label">Email Address</span>
                            <span class="info-value">{{ $patient->email }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Phone Number</span>
                            <span class="info-value">{{ $patient->phone ?? 'Not specified' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Address</span>
                            <span class="info-value">{{ $patient->address ?? 'Not specified' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Birth Date</span>
                            <span class="info-value">{{ $patient->formatted_dob }}</span>
                        </div>
                    </div>
                </div>

                <div class="info-section health-section">
                    <h3><i class="fa-solid fa-notes-medical"></i> Health Profile</h3>
                    @if($patient->ehr)
                        @php
                            $ehrData = [];
                            $lines = explode("\n", $patient->ehr);
                            foreach ($lines as $line) {
                                if (str_contains($line, ':')) {
                                    [$k, $v] = explode(':', $line, 2);
                                    $ehrData[trim($k)] = trim($v);
                                }
                            }
                        @endphp
                        <div class="health-grid">
                            <div class="health-card">
                                <span class="h-label">Diagnosis</span>
                                <span class="h-value">{{ $ehrData['Diagnosis'] ?? '—' }}</span>
                            </div>
                            <div class="health-card">
                                <span class="h-label">Allergies</span>
                                <span class="h-value {{ !isset($ehrData['Allergies']) || strtolower($ehrData['Allergies']) == 'none' ? '' : 'urgent-val' }}">
                                    {{ $ehrData['Allergies'] ?? 'None' }}
                                </span>
                            </div>
                            <div class="health-card wide">
                                <span class="h-label">Current Medications</span>
                                <span class="h-value">{{ $ehrData['Medications'] ?? '—' }}</span>
                            </div>
                            <div class="health-card wide">
                                <span class="h-label">Medical History</span>
                                <span class="h-value">{{ $ehrData['Medical History'] ?? 'No significant history.' }}</span>
                            </div>
                        </div>
                    @else
                        <div class="empty-state-mini">
                            <i class="fa-solid fa-folder-open"></i>
                            <p>No health info on file.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- RIGHT COLUMN: TIMELINE --}}
            <div class="content-right">
                <div class="info-section history-section">
                    <h3><i class="fa-solid fa-clock-rotate-left"></i> Appointment History</h3>
                    <div class="timeline">
                        @forelse($appointments ?? [] as $appt)
                            <div class="timeline-item">
                                <div class="timeline-marker {{ $appt->status }}"></div>
                                <div class="timeline-content">
                                    <div class="tl-header">
                                        <span class="tl-type">{{ ucfirst($appt->appointment_type) }}</span>
                                        <span class="tl-date">{{ \Carbon\Carbon::parse($appt->appointment_date)->format('M d, Y') }}</span>
                                    </div>
                                    <div class="tl-body">
                                        <span class="tl-status {{ $appt->status }}">{{ ucfirst($appt->status) }}</span>
                                        @if($appt->notes)
                                            <p class="tl-note">"{{ Str::limit($appt->notes, 60) }}"</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state-mini">
                                <p>No appointment records found.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@if (!request()->has('embed'))
    @endsection
@endif