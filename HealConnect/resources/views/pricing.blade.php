@php
    $layout = 'layouts.app';
    if (auth()->check()) {
        switch (auth()->user()->role) {
            case 'therapist':
                $layout = 'layouts.therapist';
                break;
            case 'clinic':
                $layout = 'layouts.clinic_layout';
                break;
            case 'patient':
                $layout = 'layouts.patient_layout';
                break;
        }
    }
@endphp

@extends($layout)
@section('title', 'HealConnect - Pricing')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/pricing.css') }}">
@endsection

@section('content')
<div class="page-header-row pricing-hero-strip">
    <h1 class="page-title-new">Choose Your Plan</h1>
    <p class="page-subtitle">Join HealConnect and grow your physical therapy practice with ease. Whether you're working independently or running a clinic, we have a plan for you.</p>
</div>

<div class="pricing-main">
    <section class="pricing-cards-section container">
        <div class="pricing-layout-wrapper">
            @guest
            <!-- Left Info Card -->
            <div class="info-side-card why-join-side">
                <div class="info-card-content">
                    <h3><i class="fa fa-rocket"></i> Be a part of HealConnect</h3>
                    <ul class="check-list">
                        <li><i class="fas fa-check"></i> Expand your reach & attract new patients</li>
                        <li><i class="fas fa-check"></i> Be Discovered Online</li>
                        <li><i class="fas fa-check"></i> Take your practice to the next level</li>
                        <li><i class="fas fa-check"></i> Offer expertise to patients in need</li>
                    </ul>
                </div>
            </div>
            @endguest

            <!-- Center Pricing Grid -->
            <div class="pricing-grid">
                @foreach ($plans as $key => $plan)
                    <div class="pricing-card {{ $key }}">
                        <div class="card-header">
                            <h2>{{ $plan['name'] }}</h2>
                            <div class="price-box">
                                <span class="currency">₱</span>
                                <span class="price-value">{{ filter_var($plan['price'], FILTER_SANITIZE_NUMBER_INT) }}</span>
                                <span class="period">/mo</span>
                            </div>
                            <p class="plan-desc">{{ $plan['description'] }}</p>
                        </div>

                        <div class="card-body">
                            <ul class="feature-list">
                                @foreach ($plan['features'] as $feature)
                                    <li><i class="fas fa-check-circle"></i> {{ $feature }}</li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="card-footer">
                            @auth
                                @if(auth()->user()->role === 'therapist' || auth()->user()->role === 'clinic')
                                    <a href="{{ route('subscribe.show', $key) }}" class="btn-get-started">Get Started</a>
                                @else
                                    <span class="btn-disabled">For Therapists & Clinics</span>
                                @endif
                            @else
                                @php
                                    $registrationType = ($key === 'pro clinic') ? 'clinic' : 'therapist';
                                @endphp
                                <a href="{{ route('register.form', $registrationType) }}?plan={{ $key }}" class="btn-get-started">Get Started</a>
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>

            @guest
            <!-- Right Info Card -->
            <div class="info-side-card eligibility-side">
                <div class="info-card-content">
                    <h3><i class="fa fa-user-shield"></i> Who can join?</h3>
                    <ul class="check-list">
                        <li><i class="fas fa-check"></i> Licensed PTs & OTs (PRC)</li>
                        <li><i class="fas fa-check"></i> Rehab & Sports Specialists</li>
                        <li><i class="fas fa-check"></i> Accredited Clinics & Centers</li>
                    </ul>
                    <p class="mini-note">Valid license is required during registration.</p>
                </div>
            </div>
            @endguest
        </div>

        <div class="pricing-disclaimer">
            <p><strong>Note:</strong> Each therapist sets their own session rates. Fees varies by experience and specialization.</p>
        </div>
    </section>
</div>
@endsection
