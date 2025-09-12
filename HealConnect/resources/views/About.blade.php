@extends('layouts.app')

@section('content')
<main class="about-main" id="about">
    <h1>About Us</h1>  
    <p>
        HealConnect is a web-based platform that bridges the gap between patients and physical therapists. 
        For patients, we provide secure virtual consultations, personalized rehabilitation programs, 
        and easy progress tracking. For therapists, we offer a convenient digital clinic to reach more 
        patients, manage sessions, and deliver care anytime, anywhere.
    </p>
    <button class= "speak-btn" data-target="about"></button>
</main>
<script src="{{ asset('js/tts.js') }}"></script>
@endsection
