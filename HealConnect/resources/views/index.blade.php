@extends('layouts.app')

@section('title', 'Home - HealConnect')
@section('body-class', 'homepage')

@section('loading')
  @include('loading')
@endsection

@section('content')
<section class="main-section">
  <div class="main-content">
    <h1>Connecting Patients and Therapists for Better Recovery</h1>
    <p>Your journey to better health starts here.</p>
    <button class="get-started-btn" onclick="window.location.href='{{ url('/about') }}'">About Us</button>
  </div>  
  <div class="flip-card">
    <div class="flip-card-inner">
 
      <div class="flip-card-front">
        <img src="{{ asset('images/pictherapy.jpg') }}" alt="Physical Therapy">
      </div>
    </div>
  </div>
</section>

<section class="steps-section">
  <div class="steps-content">
      <h1>Find Care, Book Fast, Recover Confidently</h1>

      <div class="steps-cards">
          <div class="step-card">
              <span class="step-number">1</span>
              <h3>Find a Therapist</h3>
              <p>Browse certified independent therapists and clinics that match your needs.</p>
          </div>

          <div class="step-card">
              <span class="step-number">2</span>
              <h3>Book Your Session</h3>
              <p>Choose your preferred schedule and secure your appointment instantly.</p>
          </div>

          <div class="step-card">
              <span class="step-number">3</span>
              <h3>Start Your Recovery</h3>
              <p>Join your virtual session and begin personalized physical therapy care.</p>
          </div>
      </div>
  </div>
</section>


<section class="what-is-pt">
  <div class="pt-wrapper">

    <div class="pt-left">
      <h2 class="section-title">What is Physical Therapy?</h2>

      <p class="section-desc">
        Physical Therapy, also called Physiotherapy, is a healthcare profession focused on restoring movement, reducing pain, and improving quality of life through guided therapeutic interventions.
      </p>

      <h3 class="section-subtitle">Goals & Benefits</h3>
      <ul class="pt-list">
        <li>Restore physical function and mobility</li>
        <li>Reduce pain through guided therapy</li>
        <li>Prevent future injuries</li>
        <li>Improve overall quality of life</li>
      </ul>

      <a href="{{ url('/More') }}" class="pt-btn">Learn More</a>
    </div>

    <div class="pt-right">
      <img src="{{ asset('images/physicaltherapy.jpg') }}" alt="Physical Therapy session">
    </div>
  </div>
</section>

<section class="features-section">
  <h2>Why Choose HealConnect?</h2>
  <p>Comprehensive remote physical therapy solutions designed for your recovery journey</p>

  <div class="features">
    <div class="feature" id ="feature1">
      <i class="fas fa-user-md feature-icon"></i>
      <h3>Expert Therapists</h3>
      <p>Connect with certified physical therapists for personalized care.</p>
      <button class="speak-btn" data-target="feature1"></button>
    </div>
    <div class="feature" id="feature2">
      <i class="fas fa-video feature-icon"></i>
      <h3>Virtual Consultations</h3>
      <p>Receive therapy sessions from the comfort of your home.</p>
      <button class="speak-btn" data-target="feature2"></button>
    </div>
    <div class="feature" id="feature3">
      <i class="fas fa-chart-line feature-icon"></i>
      <h3>Progress Tracking</h3>
      <p>Monitor your recovery with easy-to-use tools and reports.</p>
      <button class="speak-btn" data-target="feature3"></button>
    </div>
    <div class="feature" id="feature4">
      <i class="fas fa-calendar-check feature-icon"></i>
      <h3>Appointment Scheduling</h3>
      <p>Book sessions at your convenience with our flexible scheduling system.</p>
      <button class="speak-btn" data-target="feature4"></button>
    </div>
  </div>
</section>
@endsection
