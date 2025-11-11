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
        <aside class="chat-sidebar">
            <div class="sidebar-header">
                <h3>Messages</h3>
            </div>

            <div class="sidebar-search">
                <input type="text" id="chat-search" placeholder="Search conversations..." autocomplete="off">
            </div>

            <div class="chat-list" id="chat-list">
                @forelse ($conversations as $conversation)
                    <div class="chat-item" data-user-id="{{ $conversation->id }}">
                        <img src="{{ $conversation->profile_picture ? asset('storage/' . $conversation->profile_picture) : asset('images/logo1.png') }}" class="avatar" alt="User">
                        <div class="chat-info">
                            <h4>{{ $conversation->name }}</h4>
                            <p>{{ Str::limit($conversation->latest_message ?? 'Start a conversation...', 35) }}</p>
                        </div>
                    </div>
                @empty
                    <p class="no-chats">No conversations yet.</p>
                @endforelse
            </div>
        </aside>

        {{-- RIGHT: CHAT AREA --}}
        <section class="chat-main">
            <header class="chat-header">
                <div class="chat-user">
                    <img src="{{ asset('images/logo1.png') }}" id="chat-user-avatar" class="avatar" alt="User">
                    <h2 id="chat-username">Select a conversation</h2>
                </div>
            </header>

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
<div id="imageModal" class="image-modal" style="display:none;">
    <span class="close">&times;</span>
    <img class="modal-content" id="modalImage">
</div>
@endsection

@section('scripts')
<script>
    window.userId = {{ auth()->user()->id }};
    window.pusherKey = "{{ config('broadcasting.connections.pusher.key') }}";
    window.pusherCluster = "{{ config('broadcasting.connections.pusher.options.cluster') }}";
</script>
<script src="{{ asset('js/chat.js') }}"></script>
@endsection
