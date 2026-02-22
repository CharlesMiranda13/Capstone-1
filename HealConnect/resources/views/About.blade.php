@extends('layouts.app')

@section('title', 'HealConnect - About Us')

@section('content')
<main class="about-main" id="about">

    <!-- HERO (compact) -->
    <div class="about-hero">
        <h1>About <span class="brand-highlight">HealConnect</span></h1>
        <p class="about-tagline">Bridging Distance, Delivering Care</p>
    </div>

    <!-- TOP ROW: Who We Are | Our Mission -->
    <div class="about-grid-2">
        <div class="about-card" id="about-intro">
            <div class="card-icon">🏥</div>
            <h2>Who We Are</h2>
            <p>
                HealConnect is a web-based platform that bridges the gap between patients and physical therapists.
                We believe that quality rehabilitation care should be accessible to everyone, regardless of location
                or mobility constraints. Our platform combines the convenience of telehealth with the personalized
                approach of traditional physical therapy.
            </p>
            <button class="speak-btn" data-target="about-intro"></button>
        </div>
        <div class="about-card" id="about-mission">
            <div class="card-icon">🎯</div>
            <h2>Our Mission</h2>
            <p>
                We're on a mission to make physical therapy more accessible, efficient, and effective. By leveraging
                technology, we empower patients to take control of their recovery journey while enabling therapists
                to expand their reach and impact. HealConnect transforms the traditional clinic experience into a
                flexible, patient-centered approach that fits modern lifestyles.
            </p>
            <button class="speak-btn" data-target="about-mission"></button>
        </div>
    </div>

    <!-- BOTTOM ROW: For Patients | For Therapists -->
    <div class="about-grid-2">
        <div class="about-card accent-blue" id="about-patients">
            <div class="card-icon">🤝</div>
            <h2>For Patients</h2>
            <p>
                We understand that recovering from injury or managing chronic conditions can be challenging.
                HealConnect puts the power of recovery in your hands with secure virtual consultations that
                bring expert care to your home.
                Whether you're recovering from surgery, managing chronic pain, or working to improve mobility,
                we're here to support every step of your journey.
            </p>
            <button class="speak-btn" data-target="about-patients"></button>
        </div>
        <div class="about-card accent-blue" id="about-therapists">
            <div class="card-icon">💼</div>
            <h2>For Therapists</h2>
            <p>
                HealConnect empowers physical therapists to expand their practice beyond traditional clinic walls.
                Our digital platform serves as your virtual clinic, allowing you to reach patients who might
                otherwise struggle to access care. Manage consultations efficiently with integrated scheduling
                and video conferencing tools. Maintain detailed records, communicate securely with patients,
                and deliver high-quality care on your schedule.
                Join a growing community of therapists who are redefining healthcare delivery.
            </p>
            <button class="speak-btn" data-target="about-therapists"></button>
        </div>
    </div>

    <!-- VALUES ROW (4 columns) -->
    <div class="about-card full-width" id="about-values">
        <h2 class="values-heading">Our Values</h2>
        <div class="values-grid">
            <div class="value-item">
                <span class="value-icon">🌍</span>
                <h3>Accessibility</h3>
                <p>Healthcare should reach everyone, everywhere. We break down barriers of distance and mobility.</p>
            </div>
            <div class="value-item">
                <span class="value-icon">⭐</span>
                <h3>Quality Care</h3>
                <p>We never compromise on the standard of care. Our platform supports evidence-based practice and personalized treatment.</p>
            </div>
            <div class="value-item">
                <span class="value-icon">🔒</span>
                <h3>Privacy & Security</h3>
                <p>Your health information is sacred. We employ industry-leading security measures to protect your data.</p>
            </div>
            <div class="value-item">
                <span class="value-icon">💡</span>
                <h3>Innovation</h3>
                <p>We continuously evolve our platform based on user feedback and emerging healthcare technologies.</p>
            </div>
        </div>
        <button class="speak-btn" data-target="about-values"></button>
    </div>

    <!-- CTA STRIP -->
    <section class="about-cta">
        <div class="cta-inner">
            <div>
                <h2>Ready to Get Started?</h2>
                <p>Whether you're a patient seeking care or a therapist looking to expand your practice, HealConnect is here for you.</p>
            </div>
            <a href="{{ url('/logandsign') }}" class="btn btn-primary">Get Started →</a>
        </div>
    </section>

</main>
@endsection
