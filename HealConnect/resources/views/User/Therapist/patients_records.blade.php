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
                    <div class="ehr-grid">
                        <div><strong>Diagnosis:</strong> {{ $ehr->diagnosis ?? '—' }}</div>
                        <div><strong>Allergies:</strong> {{ $ehr->allergies ?? '—' }}</div>
                        <div><strong>Medications:</strong> {{ $ehr->medications ?? '—' }}</div>
                        <div><strong>Medical History:</strong> {{ $ehr->medical_history ?? '—' }}</div>
                        <div><strong>Notes:</strong> {{ $ehr->notes ?? '—' }}</div>
                    </div>
                @else
                    <p class="text-muted">No EHR record found for this patient.</p>
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
                    <input type="text" name="diagnosis" value="{{ old('diagnosis', $ehr->diagnosis ?? '') }}" class="form-control">

                    <label>Allergies</label>
                    <input type="text" name="allergies" value="{{ old('allergies', $ehr->allergies ?? '') }}" class="form-control">

                    <label>Medications</label>
                    <textarea name="medications" rows="2" class="form-control">{{ old('medications', $ehr->medications ?? '') }}</textarea>

                    <label>Medical History</label>
                    <textarea name="medical_history" rows="2" class="form-control">{{ old('medical_history', $ehr->medical_history ?? '') }}</textarea>

                    <label>Additional Notes</label>
                    <textarea name="notes" rows="3" class="form-control">{{ old('notes', $ehr->notes ?? '') }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Save / Update EHR</button>
            </form>
            @endif
        </section>

        <hr>

        {{-- === TREATMENT PLAN === --}}
        <section class="record-section">
            <div class="section-header">
                <h3>Treatment Plan</h3>
                <p class="text-muted">Track and update ongoing treatment strategies.</p>
            </div>

            {{-- Display --}}
            <div class="record-display card">
                @if (!empty($therapies))
                    <ul class="treatment-list">
                        @foreach ($therapies as $therapy)
                            <li><strong>{{ $therapy->session_date }}:</strong> {{ $therapy->description }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">No treatment plan recorded for this patient yet.</p>
                @endif
            </div>

            {{-- Update Form --}}
            @if($user->role === 'therapist' || $user->role === 'clinic')
            <form action="{{ route('therapist.treatment.update', $patient->id) }}" method="POST" class="update-form mt-4">
                @csrf
                @method('PUT')
                <h4>Add / Update Treatment Plan</h4>
                <div class="form-grid">
                    <label>Date</label>
                    <input type="date" name="session_date" class="form-control">

                    <label>Description</label>
                    <textarea name="description" rows="3" class="form-control" placeholder="Describe the treatment plan..."></textarea>
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

            {{-- Display --}}
            <div class="record-display card">
                @if (!empty($exercises))
                    <ul class="progress-list">
                        @foreach ($exercises as $progress)
                            <li><strong>{{ $progress->created_at->format('M d, Y') }}:</strong> {{ $progress->notes }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">No progress notes recorded yet.</p>
                @endif
            </div>

            {{-- Update Form --}}
            @if($user->role === 'therapist' || $user->role === 'clinic')
            <form action="{{ route('therapist.progress.update', $patient->id) }}" method="POST" class="update-form mt-4">
                @csrf
                @method('PUT')
                <h4>Add Progress Note</h4>
                <textarea name="notes" rows="3" class="form-control" placeholder="Enter progress observation..."></textarea>
                <button type="submit" class="btn btn-info mt-3">Save Progress Note</button>
            </form>
            @endif
        </section>

    </div>
</main>
@endsection
