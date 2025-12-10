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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('content')
<div class="chat-page">
    <div class="chat-container">

        {{-- LEFT: CHAT LIST --}}
        <aside class="chat-sidebar" id="chat-sidebar">   {{-- ← ADDED ID --}}

            <div class="sidebar-header">
                <h3>Messages</h3>
            </div>

            <div class="sidebar-search">
                <input type="text" id="chat-search" placeholder="Search conversations..." autocomplete="off">
            </div>
            
            <div class="chat-list" id="chat-list">
                @forelse ($conversations as $conversation)
                    <div class="chat-item {{ $conversation->unread_count > 0 ? 'has-unread' : '' }}" 
                        data-user-id="{{ $conversation->id }}"
                        data-unread="{{ $conversation->unread_count }}">
                        <img src="{{ $conversation->profile_picture ? asset('storage/' . $conversation->profile_picture) : asset('images/logo1.png') }}" 
                            class="avatar" alt="User">
                        <div class="chat-info">
                            <div class="chat-info-header">
                                <h4>{{ $conversation->name }}</h4>
                                @if($conversation->unread_count > 0)
                                    <span class="unread-badge">{{ $conversation->unread_count > 99 ? '99+' : $conversation->unread_count }}</span>
                                @endif
                            </div>
                            <p class="{{ $conversation->unread_count > 0 ? 'unread-message' : '' }}">
                                {{ Str::limit($conversation->latest_message ?? 'Start a conversation...', 35) }}
                            </p>
                        </div>
                        @if($conversation->unread_count > 0)
                            <span class="unread-dot"></span>
                        @endif
                    </div>
                @empty
                    <p class="no-chats">No conversations yet.</p>
                @endforelse
            </div>
        </aside>

        {{-- RIGHT: CHAT AREA --}}
        <section class="chat-main" id="chat-main">   {{-- ← ADDED ID --}}

            {{-- ⭐ ADDED BACK BUTTON FOR MOBILE --}}
            <button class="back-btn" id="back-btn">
                <i class="fa-solid fa-arrow-left"></i>
            </button>

            <header class="chat-header">
                <div class="chat-user">
                    <img src="{{ asset('images/logo1.png') }}" id="chat-user-avatar" class="avatar" alt="User">
                    <h2 id="chat-username">Select a conversation</h2>
                </div>

                @if($user->role == 'therapist' || $user->role == 'clinic')
                    <button id="start-video-call" class="video-call-btn" title="Start Video Call">
                        <i class="fas fa-video"></i> 
                        <span>Start Call</span>
                    </button>
                @endif
            </header>

            {{-- Chat Messages --}}
            <div class="chat-messages" id="chat-messages">
                <div class="no-chat-selected">
                    <p>Select a chat to start messaging.</p>
                </div>
            </div>

            {{-- INPUT --}}
            <footer class="chat-input">
                <form id="chat-form" autocomplete="off" enctype="multipart/form-data">
                    @csrf
                    <input type="text" id="message-input" name="message" placeholder="Type your message..." required>

                    {{-- File Upload --}}
                    <input type="file" id="file-input" name="file" accept="image/*,video/*,application/pdf" style="display:none;">
                    <button type="button" id="file-btn" title="Send file">
                        <i class="fas fa-paperclip"></i>
                    </button>

                    {{-- Voice Message --}}
                    <button type="button" id="record-btn" class="mic-btn" title="Voice message">
                        <i class="fas fa-microphone"></i>
                    </button>

                    <button type="submit" class="send-btn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </footer>
        </section>
    </div>
</div>

{{-- Image Modal --}}
<div id="imageModal" class="image-modal" style="display:none;">
    <span class="close">&times;</span>
    <img class="modal-content" id="modalImage">
</div>

{{-- Incoming Call Notification --}}
<div id="incoming-call" class="incoming-call" style="display:none;">
    <div class="call-box">
        <h3 id="caller-name"></h3>
        <p>is calling you...</p>
        <button id="join-call-btn" class="join-btn">Join Call</button>
        <button id="decline-call-btn" class="decline-btn">Decline</button>
    </div>
</div>
@endsection

@section('scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Pusher Library --}}
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

{{-- Pass PHP variables to JavaScript --}}
<script>
    window.authUserRole = "{{ auth()->user()->role }}";
    window.userId = {{ auth()->user()->id }};
    window.authUserId = {{ auth()->id() }};
    window.authUserName = "{{ auth()->user()->name }}";
    
    window.pusherKey = "{{ config('broadcasting.connections.pusher.key') }}";
    window.pusherCluster = "{{ config('broadcasting.connections.pusher.options.cluster') }}";
</script>

<script src="https://unpkg.com/@daily-co/daily-js"></script>
<script src="{{ asset('js/chat.js') }}"></script>

@endsection
