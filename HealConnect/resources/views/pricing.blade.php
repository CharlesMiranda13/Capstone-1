@extends('layouts.app')
@section('title', 'HealConnect - Pricing')
@section('content')
<main class ="pricing-main">
    <link rel="stylesheet" href="{{ asset('css/pricing.css') }}">
    <div class="pricing-container">
        <div class="pricing-card">
            <h2>Basic Plan</h2>
            <p class="price">/month</p>
            <h4>For Independent Therapist</h4>
            <ul>
                <li>Profile listing in HealConnect</li>
                <li>Access to all features</li>
                <li>Priority support</li>
                <li>Client management tools</li>

            </ul>
        </div>
        <div class="pricing-card">
            <h2>Premium Plan</h2>
            <p class="price">/month</p>
            <h4>For Clinic or Group Therapist</h4>
            <ul>
                <li>Profile listing in HealConnect</li>
                <li>Multiple therapist profiles</li>
                <li>Access to all features</li>
                <li>Priority support</li>
                <li>Team management tools</li>
                
            </ul>    

        </div>
    </div>
    <p class="pricing-note">
    <em>Note: Session fees are set individually by each therapist on HealConnect. 
    Prices may vary depending on expertise, specialization, and session duration.</em>
    </p>
</main>
@endsection