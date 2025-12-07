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

        {{-- EHR Section --}}
        <section class="record-section">
            <div class="section-header">
                <h3>🩺 Electronic Health Record (EHR)</h3>
                <p class="text-muted">Your comprehensive health information — diagnosis, allergies, medications, and more.</p>
            </div>

            {{-- EHR Display (Read-only for patient) --}}
            <div class="record-display card">
                @if (!empty($ehr))
                    <div class="ehr-content">
                        {!! nl2br(e($ehr)) !!}
                    </div>
                @else
                    <p class="text-muted">No EHR record found. Your therapist will add information during your sessions.</p>
                @endif
            </div>
        </section>

        <hr>

        {{-- === TREATMENT PLAN === --}}
        <section class="record-section">
            <div class="section-header">
                <h3>💊 Treatment Plan</h3>
                <p class="text-muted">Your ongoing treatment strategies and sessions.</p>
            </div>

            {{-- Display (Read-only for patient) --}}
            <div class="record-display card">
                @if (!empty($therapies))
                    <div class="treatment-content">
                        {!! nl2br(e($therapies)) !!}
                    </div>
                @else
                    <p class="text-muted">No treatment plan recorded yet. Your therapist will add this information.</p>
                @endif
            </div>
        </section>

        <hr>

        {{-- === PROGRESS NOTES === --}}
        <section class="record-section">
            <div class="section-header">
                <h3>📘 Progress Notes</h3>
                <p class="text-muted">Your progress and follow-up evaluations documented by your therapist.</p>
            </div>

            {{-- Display (Read-only for patient) --}}
            <div class="record-display card">
                @if (!empty($exercises))
                    <div class="progress-content">
                        {!! nl2br(e($exercises)) !!}
                    </div>
                @else
                    <p class="text-muted">No progress notes recorded yet. Your therapist will document your progress during sessions.</p>
                @endif
            </div>
        </section>

        {{-- Info Box --}}
        <div class="alert alert-info mt-4">
            <strong>ℹ️ Note:</strong> These records are maintained by your healthcare provider. 
            If you have questions or notice any discrepancies, please contact your therapist.
        </div>

    </div>
</main>
@endsection