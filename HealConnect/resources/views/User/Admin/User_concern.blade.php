@extends('layouts.admin')

@section('title', 'User Concerns')
@section('styles')
<link rel="stylesheet" href="{{ asset('Css/concern.css') }}">
@endsection

@section('content')
<main class="admin-main">

    <h1>User Concerns</h1>

    @if ($messages->isEmpty())
        <p>No user concerns yet.</p>
    @else
        <div class="concern-list">

            @foreach ($messages as $msg)
                <div class="concern-item openConcernModal"
                    data-link="{{ route('admin.contact_messages.show', $msg->id) }}"
                    data-id="{{ $msg->id }}">

                    <div class="left">
                        <h3>{{ $msg->name }}</h3>
                        <p>{{ Str::limit($msg->message, 40) }}</p>
                    </div>

                    <div class="right">
                        <span class="date">{{ $msg->created_at->format('M d, Y') }}</span>

                        @if(!$msg->is_read)
                            <span class="badge">NEW</span>
                        @endif
                    </div>

                </div>
            @endforeach

        </div>
    @endif
</main>

<div id="concernViewModal" class="modal-overlay">
    <div class="modal-box">
        <button class="close">&times;</button>

        <div id="concernModalBody">

        </div>
    </div>
</div>

@endsection
