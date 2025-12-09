@extends('layouts.app')

@section('content')
<main class="contact-main">

    <h1>{{ $settings->contact_title ?? 'Contact Us' }}</h1>

    <p>{{ $settings->contact_message ?? 'If you have any questions, please reach out to us.' }}</p>

    <form action="{{ route('contact.submit') }}" method="POST" class="contact-form">
        @csrf

        <div class="form-group">
            <label for="name">Your Name</label>
            <input type="text" name="name" id="name" required value="{{ old('name') }}">
        </div>

        <div class="form-group">
            <label for="email">Your Email</label>
            <input type="email" name="email" id="email" required value="{{ old('email') }}">
        </div>

        <div class="form-group">
            <label for="phone">Phone Number (optional)</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone') }}">
        </div>

        <div class="form-group">
            <label for="message">Message</label>
            <textarea name="message" id="message" rows="5" required>{{ old('message') }}</textarea>
        </div>

        <button type="submit" class="btn-submit">Send Message</button>
    </form>

</main>
@endsection
