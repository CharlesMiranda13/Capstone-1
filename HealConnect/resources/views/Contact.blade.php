@extends('layouts.app')

@section('title', 'Contact Us - HealConnect')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/contact.css') }}">
@endsection

@section('content')
<main class="contact-main">
    <div class="contact-container">
        
        <header class="contact-header">
            <h1>Get in <span class="brand-highlight">Touch</span></h1>
            <p>{{ $settings->contact_message ?? 'Have questions? We are here to help and would love to hear from you.' }}</p>
        </header>

        {{-- Only Contact Form --}}
        <div class="contact-form-wrapper">
            <form action="{{ route('contact.submit') }}" method="POST" class="contact-form">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" name="name" id="name" placeholder="John Doe" required value="{{ old('name') }}">
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" name="email" id="email" placeholder="john@example.com" required value="{{ old('email') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number (Optional)</label>
                    <input type="text" name="phone" id="phone" placeholder="+63 9..." value="{{ old('phone') }}">
                </div>

                <div class="form-group">
                    <label for="message">Your Message</label>
                    <textarea name="message" id="message" rows="4" placeholder="How can we help you?" required>{{ old('message') }}</textarea>
                </div>

                <button type="submit" class="btn-submit">
                    <span>Send Message</span>
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </form>
        </div>

    </div>
</main>
@section('scripts')
<script src="{{ asset('js/contact-form.js') }}"></script>
@endsection
@endsection
