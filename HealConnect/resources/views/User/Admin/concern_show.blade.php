<div class="concern-details">
    <div class="concern-meta">
        <div class="user-info">
            <div class="user-avatar">
                {{ substr($message->name, 0, 1) }}
            </div>
            <div>
                <h2 class="concern-user-name">{{ $message->name }}</h2>
                <p class="concern-user-email">{{ $message->email }}</p>
            </div>
        </div>
        <div class="concern-date">
            <span class="date-badge">
                <i class="far fa-calendar-alt"></i> {{ $message->created_at->format('M d, Y') }}
            </span>
            <span class="time-badge">
                <i class="far fa-clock"></i> {{ $message->created_at->format('H:i') }}
            </span>
        </div>
    </div>

    <div class="concern-body">
        <div class="message-label">User Message:</div>
        <div class="message-content">
            {{ $message->message }}
        </div>
        @if($message->phone)
            <div class="phone-info">
                <i class="fas fa-phone-alt"></i> <strong>Phone:</strong> {{ $message->phone }}
            </div>
        @endif
    </div>

    <hr class="concern-divider">

    <div class="reply-section">
        @if($message->is_replied)
            <div class="reply-header">
                <i class="fas fa-reply-all"></i>
                <h3>Admin Response</h3>
                <span class="status-replied">Sent</span>
            </div>
            <div class="reply-box">
                {{ $message->reply }}
            </div>
            <p class="concern-info-text">
                <i class="fas fa-info-circle"></i> This response has been sent to the user's email.
            </p>
        @else
            <div class="reply-header">
                <i class="fas fa-paper-plane"></i>
                <h3>Reply to User</h3>
            </div>
            <form method="POST" action="{{ route('admin.contact_messages.reply', $message->id) }}" class="admin-reply-form">
                @csrf
                <div class="textarea-wrapper">
                    <textarea name="reply_message" rows="5" placeholder="Type your professional response here..." required></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-send-reply">
                        <span>Send Email Reply</span>
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
