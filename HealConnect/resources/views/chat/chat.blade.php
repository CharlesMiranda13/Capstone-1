@php
    $user = Auth::user();

    switch ($user->role) {
        case 'patient':
            $layout = 'layouts.patient_layout';
            break;
        case 'therapist':
            $layout = 'layouts.therapist';
            break;
        case 'clinic':
            $layout = 'layouts.clinic_layout';
            break;
        default:
            $layout = 'layouts.patient_layout'; 
            break;
    }
@endphp

@extends($layouts)

@section('title', 'Chat')

@section('content')
<div class="chat-container">
    <h2 class="chat-title">Chat</h2>

    <div id="chat-box" class="chat-box">
        <!-- Messages  -->
    </div>

    <form id="chat-form" class="chat-form">
        @csrf
        <input type="text" id="message-input" placeholder="Type a message..." autocomplete="off">
        <button type="submit">Send</button>
    </form>
</div>
@endsection
