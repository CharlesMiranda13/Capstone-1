@extends('layouts.app')

@section('title', 'HealConnect - Services')

@section('content')
<main class="services-main">
    <!-- HERO SECTION -->
    <div class="services-hero">
        <h1>Our <span class="brand-highlight">Services</span></h1>
        <p class="services-tagline">
            At HealConnect, we provide expert care tailored to your recovery journey.
        </p>
    </div>

    <p class="intro">
        Explore our range of professional physical therapy services designed for accessibility and effectiveness.
    </p>
    <button class="speak-btn" data-target="intro"></button>

    <div class="services-container">

        <div class="service-card" id="service1">
            <img src="{{ asset('images/Onlineconsult.jpg') }}" alt="Virtual Consultation">
            <h2>Virtual Consultations</h2>
            <p>Schedule and attend therapy sessions from the comfort of your home with expert therapists.</p>
            <h3>What to expect</h3>
            <ul class="service-list">
                <li><i class="fas fa-check-circle"></i> Initial assessment and diagnosis</li>
                <li><i class="fas fa-check-circle"></i> Personalized treatment plans</li>
                <li><i class="fas fa-check-circle"></i> Follow-up sessions to track progress</li>
            </ul>
             <button class="speak-btn" data-target="service1"></button>
        </div>

        <div class="service-card" id="service2">
            <img src="{{ asset('images/treatmentplan.jpg') }}" alt="Personalized Treatment">
            <h2>Personalized Treatment Plans</h2>
            <p>Receive customized therapy plans tailored to your specific health needs and goals.</p>
            <h3>Includes</h3>
            <ul class="service-list">
                <li><i class="fas fa-check-circle"></i> Exercise routines</li>
                <li><i class="fas fa-check-circle"></i> Tailored exercise programs</li>
                <li><i class="fas fa-check-circle"></i> Lifestyle recommendations</li>
            </ul>
            <button class="speak-btn" data-target="service2"></button>
        </div>

        <div class="service-card" id="service3">
            <img src="{{ asset('images/progress.jpg') }}" alt="Progress Tracking">
            <h2>Progress Tracking</h2>
            <p>Monitor your recovery with easy-to-use tracking tools and progress reports.</p>
            <h3>Features</h3>
            <ul class="service-list">
                <li><i class="fas fa-check-circle"></i> Regular progress updates</li>
                <li><i class="fas fa-check-circle"></i> Adjustments to treatment plans as needed</li>
                <li><i class="fas fa-check-circle"></i> Access to your therapy history</li>
            </ul>
            <button class="speak-btn" data-target="service3"></button>
        </div>

        <div class="service-card" id="service4">
            <img src="{{ asset('images/secure.jpg') }}" alt="Secure Communication">
            <h2>Secure Communication</h2>
            <p>Stay connected with your therapist through a private, secure messaging system.</p>
            <h3>Benefits</h3>
            <ul class="service-list">
                <li><i class="fas fa-check-circle"></i> Confidential messaging</li>
                <li><i class="fas fa-check-circle"></i> Easy appointment scheduling</li>
            </ul>
            <button class="speak-btn" data-target="service4"></button>
        </div>

    </div>
</main>
@endsection
