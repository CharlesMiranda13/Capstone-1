@extends('layouts.app')

@section('title', 'HealConnect - Services')

@section('content')
<main class="services-main">
    <h1>Our Services</h1>
    <p class="intro">
        At HealConnect, we offer a range of services designed to connect patients with qualified 
        physical therapists for remote therapy sessions. Here’s what you can expect:
    </p>

    <div class="services-container">

        <div class="service-card">
            <img src="{{ asset('images/Onlineconsult.jpg') }}" alt="Virtual Consultation">
            <h2>Virtual Consultations</h2>
            <p>Schedule and attend therapy sessions from the comfort of your home with expert therapists.</p>
            <h3>What to expect</h3>
            <ul>
                <li>Initial assessment and diagnosis</li>
                <li>Personalized treatment plans</li>
                <li>Follow-up sessions to track progress</li>
            </ul>
        </div>

        <div class="service-card">
            <img src="{{ asset('images/treatmentplan.jpg') }}" alt="Personalized Treatment">
            <h2>Personalized Treatment Plans</h2>
            <p>Receive customized therapy plans tailored to your specific health needs and goals.</p>
            <h3>Includes</h3>
            <ul>
                <li>Exercise routines</li>
                <li>Tailored exercise programs</li>
                <li>Lifestyle recommendations</li>
            </ul>
        </div>

        <div class="service-card">
            <img src="{{ asset('images/progress.jpg') }}" alt="Progress Tracking">
            <h2>Progress Tracking</h2>
            <p>Monitor your recovery with easy-to-use tracking tools and progress reports.</p>
            <h3>Features</h3>
            <ul>
                <li>Regular progress updates</li>
                <li>Adjustments to treatment plans as needed</li>
                <li>Access to your therapy history</li>
            </ul>
        </div>

        <div class="service-card">
            <img src="{{ asset('images/secure.jpg') }}" alt="Secure Communication">
            <h2>Secure Communication</h2>
            <p>Stay connected with your therapist through a private, secure messaging system.</p>
            <h3>Benefits</h3>
            <ul>
                <li>Confidential messaging</li>
                <li>Easy appointment scheduling</li>
            </ul>
        </div>

    </div>
</main>
@endsection
