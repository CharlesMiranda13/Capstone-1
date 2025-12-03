@extends('layouts.app')
@section('title', 'HealConnect - Pricing')

@section('content')
<main class="pricing-main">
    <!-- Grow with HealConnect Section -->
    <section class="why-join">
        <div class="why-join-content">
            <div class="why-join-text">
                <h2>Be a part of HealConnect.com</h2>
                <ul class="why-simple">
                    <li><i class="fa fa-bullhorn"></i> Expand your reach and attract new patients</li>
                    <li><i class="fa fa-calendar-check"></i> Be Discovered Online</li>
                    <li><i class="fa fa-hand-holding-heart"></i> Take your practice to the next level</li>
                    <li><i class="fa fa-hand-holding-heart"></i> Offer your expertise to patients who need physical care</li>
                </ul>
            </div>

            <div class="why-join-image">
                <img src="{{ asset('images/jv.png') }}" alt="Join HealConnect">
            </div>
        </div>
    </section>

    <!-- Eligibility Section -->
    <section class="eligibility-section">
        <h2>Who is Eligible to Join HealConnect?</h2>
        <p>
            HealConnect welcomes licensed and certified rehabilitation professionals 
            who aim to reach more patients and provide quality physical therapy services. 
            Eligible members include:
        </p>
        <ul class="eligibility-list">
            <li><i class="fa fa-check-circle"></i> Licensed Physical Therapists (RPT)</li>
            <li><i class="fa fa-check-circle"></i> Licensed Occupational Therapists (OTR)</li>
            <li><i class="fa fa-check-circle"></i> Licensed Sports or Rehabilitation Specialists</li>
            <li><i class="fa fa-check-circle"></i> Accredited Clinics and Rehabilitation Centers</li>
            <li><i class="fa fa-check-circle"></i> Certified Manual, Exercise, or Neurological Therapists</li>
        </ul>
        <p class="eligibility-note">
            <em>Applicants must provide a valid PRC license or certification during registration 
            to ensure service quality and patient safety on HealConnect.</em>
        </p>
    </section>

    <!-- Pricing Section  -->
    <section class="pricing-section">
        <div class="pricing-container">
            <section class="pricing-hero">
                <h1>Choose Your Plan</h1>
                <p>
                    Join HealConnect and grow your physical therapy practice with ease. 
                    Whether you're working independently or running a clinic, 
                    our plans help you connect with patients, manage sessions, and build your reputation online.
                </p>
            </section>

            @foreach ($plans as $key => $plan)
                <div class="pricing-card {{ $key }}">
                    <h2>{{ $plan['name'] }}</h2>
                    <p class="price">{{ $plan['price'] }}</p>
                    <h4>{{ $plan['description'] }}</h4>
                    <ul>
                        @foreach ($plan['features'] as $feature)
                            <li>{{ $feature }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ url('/logandsign') }}?plan={{ $key }}" class="btn btn-primary">Get Started</a>
                </div>
            @endforeach
        </div>
        <p class="pricing-note">
            <em>Note: Each therapist on HealConnect sets their own session rates. 
            Fees may vary depending on experience, specialization, and treatment duration.</em>
        </p>
    </section>


</main>
@endsection
