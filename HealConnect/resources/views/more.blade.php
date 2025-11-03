@extends('layouts.app')

@section('title', 'Core Components of Physical Therapy')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/more.css') }}">
@endsection

@section('content')
<main class="learn-more-page">
    <section class="intro-section">
        <div class="container">
            <h1>Core Components of Physical Therapy</h1>
            <p class="subtitle">
                Discover the essential elements that form the foundation of effective physical therapy — 
                from assessment to recovery and prevention.
            </p>
        </div>
    </section>

    <section class="content-section">
        <div class="container">
            <div class="component">
                <h2>1. Assessment and Diagnosis</h2>
                <p>
                    Physiotherapists evaluate neurological, musculoskeletal, and orthopaedic cases using 
                    specialized tests and measurements to identify functional limitations or movement disorders.
                </p>
            </div>

            <div class="component">
                <h2>2. Therapeutic Interventions</h2>

                <div class="sub-component">
                    <h3>A. Active Exercises</h3>
                    <p>Strengthening, stretching, and coordination drills to restore mobility and muscle function.</p>
                </div>

                <div class="sub-component">
                    <h3>B. Manual Therapy</h3>
                    <p>Joint mobilization and soft tissue manipulation to restore stiffness and improve range of motion.</p>
                </div>

                <div class="sub-component">
                    <h3>C. Functional Training</h3>
                    <p>Activities such as gait training and balance exercises that enhance daily living skills.</p>
                </div>

                <div class="sub-component">
                    <h3>D. Modalities & Machines</h3>
                    <p>
                        Techniques like heat, cold therapy, therapeutic ultrasound, and TENS (Transcutaneous Electrical Nerve Stimulation)
                        help manage pain and inflammation effectively.
                    </p>
                </div>
            </div>

            <div class="component">
                <h2>3. Prevention and Education</h2>
                <p>
                    Focus on injury prevention, posture correction, and self-management strategies such as home exercise programs
                    to promote long-term health and well-being.
                </p>
            </div>
        </div>
    </section>
</main>
@endsection
