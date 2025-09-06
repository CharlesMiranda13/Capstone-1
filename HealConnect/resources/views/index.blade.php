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
    <div class="main-image">
      <img src="{{ asset('images/pictherapy.jpg') }}" alt="Physical Therapy" />
    </div>
  </section>

  <section class="features-section">
    <h2>Why Choose HealConnect?</h2>
    <p>Comprehensive remote physical therapy solutions designed for your recovery journey</p>

    <div class="features">
      <div class="feature">
        <i class="fas fa-user-md feature-icon"></i>
        <h3>Expert Therapists</h3>
        <p>Connect with certified physical therapists for personalized care.</p>
      </div>
      <div class="feature">
        <i class="fas fa-video feature-icon"></i>
        <h3>Virtual Consultations</h3>
        <p>Receive therapy sessions from the comfort of your home.</p>
      </div>
      <div class="feature">
        <i class="fas fa-chart-line feature-icon"></i>
        <h3>Progress Tracking</h3>
        <p>Monitor your recovery with easy-to-use tools and reports.</p>
      </div>
      <div class="feature">
        <i class="fas fa-calendar-check feature-icon"></i>
        <h3>Appointment Scheduling</h3>
        <p>Book sessions at your convenience with our flexible scheduling system.</p>
      </div>
    </div>
  </section>
@endsection
