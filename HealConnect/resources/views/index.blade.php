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

<section class="what-is-pt">
  <div class="container">
    <div class="pt-content">
      <div class="pt-text">
        <h2>What is Physical Therapy?</h2>
        <p>
          Physical Therapy, also called Physiotherapy, is a healthcare profession aimed at developing, maintaining, and restoring 
          a person's maximum movement and functional ability throughout their lifespan. Through evidence-based interventions, 
          it seeks to improve quality of life and address conditions where movement and function 
          are limited or threatened due to injury, illness, disease, or aging.
        </p>
        <h2> Goals and Benefits of Physical Therapy </h2>
        <ul>
          <li> Restores physical function and mobility</li>
          <li> Reduces pain through guided therapy</li>
          <li> Prevents future injuries</li>
          <li> Improves overall quality of life</li>
        </ul>
        <a href="{{ url('/More') }}" class="learn-more-btn">Learn More</a>
      </div>
      <div class="pt-image">
        <img src="{{ asset('images/physicaltherapy.jpg') }}" alt="Physical Therapy session">
      </div>
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
