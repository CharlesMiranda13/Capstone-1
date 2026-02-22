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

    <div class="carousel-container">
        <div class="carousel-slide-wrapper">
            <!-- Slide 1: Assessment and Diagnosis -->
            <div class="carousel-slide active" id="assessment">
                <div class="slide-content">
                    <div class="card-icon">🏥</div>
                    <h2>1. Assessment and Diagnosis</h2>
                    <p>
                        Physiotherapists evaluate neurological, musculoskeletal, and orthopaedic cases using 
                        specialized tests and measurements to identify functional limitations or movement disorders.
                    </p>
                    <button class="speak-btn" data-target="assessment"></button>
                </div>
            </div>

            <!-- Slide 2: Therapeutic Interventions -->
            <div class="carousel-slide" id="interventions">
                <div class="slide-content">
                    <div class="card-icon">🎯</div>
                    <h2>2. Therapeutic Interventions</h2>
                    <div class="sub-grid">
                        <div class="sub-card">
                            <h3>A. Active Exercises</h3>
                            <p>Strengthening, stretching, and coordination drills to restore mobility and muscle function.</p>
                        </div>
                        <div class="sub-card">
                            <h3>B. Manual Therapy</h3>
                            <p>Joint mobilization and soft tissue manipulation to restore stiffness and improve range of motion.</p>
                        </div>
                        <div class="sub-card">
                            <h3>C. Functional Training</h3>
                            <p>Activities such as gait training and balance exercises that enhance daily living skills.</p>
                        </div>
                        <div class="sub-card">
                            <h3>D. Modalities & Machines</h3>
                            <p>Techniques like heat, cold therapy, therapeutic ultrasound, and TENS help manage pain and inflammation effectively.</p>
                        </div>
                    </div>
                    <button class="speak-btn" data-target="interventions"></button>
                </div>
            </div>

            <!-- Slide 3: Prevention and Education -->
            <div class="carousel-slide" id="prevention">
                <div class="slide-content">
                    <div class="card-icon">🌍</div>
                    <h2>3. Prevention and Education</h2>
                    <p>
                        Focus on injury prevention, posture correction, and self-management strategies such as home exercise programs
                        to promote long-term health and well-being.
                    </p>
                    <button class="speak-btn" data-target="prevention"></button>
                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <button class="carousel-prev" aria-label="Previous slide">❮</button>
        <button class="carousel-next" aria-label="Next slide">❯</button>

        <!-- Indicators -->
        <div class="carousel-indicators">
            <button class="active"></button>
            <button></button>
            <button></button>
        </div>
    </div>
</main>
@endsection

@section('scripts')
<script src="{{ asset('js/carousel.js') }}"></script>
@endsection
