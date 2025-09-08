@extends('layouts.app')
@section('title', 'HealConnect - Pricing')
@section('content')
<main class="pricing-main">
    <!-- Hero Section -->
    <section class="pricing-hero">
        <h1>Choose Your Plan</h1>
        <p>Select a plan that suits your therapy needs and schedule. Each therapist sets their own session fees.</p>
    </section>

    <!-- Pricing Cards -->
    <div class="pricing-container">
        <!-- Basic Plan -->
        <div class="pricing-card basic">
            <h2>Basic Plan</h2>
            <p class="price">/month</p>
            <h4>For Independent Therapist</h4>
            <ul>
                <li>Profile listing in HealConnect</li>
                <li>Access to all features</li>
                <li>Priority support</li>
                <li>Individual client management tools</li>
            </ul>
            <a href="{{ route('register.therapist') }}" class="btn btn-primary">Get Started</a>
        </div>

        <!-- Premium Plan -->
        <div class="pricing-card premium">
            <h2>Premium Plan</h2>
            <p class="price"> /month</p>
            <h4>For Clinic or Group Therapist</h4>
            <ul>
                <li>Profile listing in HealConnect</li>
                <li>Multiple therapist profiles</li>
                <li>Access to all features</li>
                <li>Priority support</li>
                <li>Team management & scheduling tools</li>
            </ul>
            <a href="{{ route('register.therapist') }}" class="btn btn-primary">Get Started</a>
        </div>
    </div>

    <!-- Pricing Note -->
    <p class="pricing-note">
        <em>Note: Session fees are set individually by each therapist on HealConnect. Prices may vary depending on expertise, specialization, and session duration.</em>
    </p>
</main>
@endsection
