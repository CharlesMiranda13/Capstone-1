@php
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

@extends($layout)

@section('title', 'Messages')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/chat.css') }}">
@endsection

@section('content')
<div class="chat-page">
    <div class="chat-container">

        {{--  CHAT LIST --}}
        <div class="chat-sidebar">
            <div class="sidebar-header">
                <h3>Chats</h3>
            </div>
            <div class="sidebar-search">
                <input type="text" id="chat-search" placeholder="Search..." autocomplete="off">
            </div>

            <div class="chat-list" id="chat-list">
                @forelse ($conversations as $conversation)
                    <div class="chat-item" data-user-id="{{ $conversation->id }}">
                        <img src="{{ $conversation->profile_picture ? asset('storage/' . $conversation->profile_picture) : asset('images/default-profile.png') }}" class="avatar" alt="User">
                        <div class="chat-info">
                            <h4>{{ $conversation->name }}</h4>
                            <p>{{ $conversation->latest_message ?? 'Start a conversation...' }}</p>
                        </div>
                    </div>
                @empty
                    <p class="no-chats">No conversations yet.</p>
                @endforelse
            </div>
        </div>

        {{-- RIGHT SIDE: CHAT AREA --}}
        <div class="chat-main">
            <div class="chat-header">
                <div class="chat-user">
                    <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('images/default-profile.png') }}" class="avatar" alt="User">
                    <h2 id="chat-username">Select a conversation</h2>
                </div>
                <div class="chat-search">
                    <input type="text" id="message-search" placeholder="Search messages...">
                </div>
            </div>

            {{-- MESSAGES DISPLAY --}}
            <div class="chat-messages" id="chat-messages">
                <div class="no-chat-selected">
                    <p>Please select a chat to start messaging.</p>
                </div>
            </div>

            {{-- MESSAGE INPUT --}}
            <div class="chat-input">
                <form id="chat-form">
                    @csrf
                    <input type="text" id="message-input" placeholder="Type your message..." autocomplete="off" required>
                    <button type="submit">Send</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    window.userId = {{ auth()->user()->id }};
    window.pusherKey = "{{ config('broadcasting.connections.pusher.key') }}";
    window.pusherCluster = "{{ config('broadcasting.connections.pusher.options.cluster') }}"
</script>
<script src="{{ asset('js/chat.js') }}"></script>
@endsection
