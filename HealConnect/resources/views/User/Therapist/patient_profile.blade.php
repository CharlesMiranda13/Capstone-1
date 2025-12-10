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
        <div class="header">
            <h2>{{ $patient->name }}'s Profile</h2>
        </div>

        <section class="profile-card">
            {{-- LEFT SIDE --}}
            <div class="profile-left">
                <div class="profile-pic">
                    <img src="{{ $patient->profile_picture ? asset('storage/' . $patient->profile_picture) : asset('images/logo1.png') }}" 
                        alt="{{ $patient->name }}">
                </div>

                <h3>{{ $patient->name }}</h3>
                <p class="role">Patient</p>

                <div class="contact-info">
                    <p><i class="fa-solid fa-envelope"></i> {{ $patient->email }}</p>
                    <p><i class="fa-solid fa-phone"></i> {{ $patient->phone ?? 'Not specified' }}</p>
                    <p><i class="fa-solid fa-location-dot"></i> {{ $patient->address ?? 'Not specified' }}</p>
                    <p><i class="fa-solid fa-calendar"></i> {{ $patient->formatted_dob }}</p>
                    <p><i class="fa-solid fa-venus-mars"></i> {{ $patient->gender ? ucfirst($patient->gender) : 'Not specified' }}</p>
                </div>

                <div class="app">
                    <a href="{{ route('messages', ['receiver_id' => $patient->id]) }}" class="btn-book">
                        <i class="fa-solid fa-comments"></i> Message
                    </a>
                </div>
            </div>

            {{-- RIGHT SIDE --}}
            <div class="profile-right">
                <div class="card-section">
                    <h4><i class="fa-solid fa-notes-medical"></i> Health Information</h4>
                    @if($patient->ehr)
                        @php
                            // Parse the EHR text into individual fields
                            $ehrLines = explode("\n", $patient->ehr);
                            $ehrData = [];
                            foreach ($ehrLines as $line) {
                                $parts = explode(": ", $line, 2);
                                if (count($parts) == 2) {
                                    $ehrData[trim($parts[0])] = trim($parts[1]);
                                }
                            }
                        @endphp
                        
                        <div class="health-info">
                            <p><strong>Diagnosis:</strong> {{ $ehrData['Diagnosis'] ?? '—' }}</p>
                            <p><strong>Allergies:</strong> {{ $ehrData['Allergies'] ?? '—' }}</p>
                            <p><strong>Medications:</strong> {{ $ehrData['Medications'] ?? '—' }}</p>
                            <p><strong>Medical History:</strong> {{ $ehrData['Medical History'] ?? '—' }}</p>
                            <p><strong>Notes:</strong> {{ $ehrData['Notes'] ?? '—' }}</p>
                        </div>
                    @else
                        <p>No health information provided.</p>
                    @endif
                </div>

                <div class="card-section">
                    <h4><i class="fa-solid fa-calendar-days"></i> Appointment History</h4>
                    @if(isset($appointments) && count($appointments) > 0)
                        <ul class="availability">
                            @foreach($appointments as $appt)
                                <li>
                                    <strong>{{ ucfirst($appt->appointment_type) }}</strong> - 
                                    {{ \Carbon\Carbon::parse($appt->appointment_date)->format('F j, Y') }} 
                                    ({{ ucfirst($appt->status) }})
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p>No past appointments found.</p>
                    @endif
                </div>
            </div>
        </section>
    </div>
</main>

@if (!request()->has('embed'))
    @endsection
@endif