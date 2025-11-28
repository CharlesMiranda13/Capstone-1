<h2>{{ $message->name }}</h2>

<p><strong>Email:</strong> {{ $message->email }}</p>
<p><strong>Phone:</strong> {{ $message->phone ?? 'N/A' }}</p>
<p><strong>Date:</strong> {{ $message->created_at->format('M d, Y H:i') }}</p>

<hr>

<p>{{ $message->message }}</p>

@if($message->is_replied)
    <h3>Admin Reply</h3>
    <p style="background: #f1f1f1; padding: 0.75rem; border-radius: 6px; font-size: 1rem; line-height: 1.5; color: #333;"">{{ $message->reply }}</p>
@else
    <h3>Reply to User</h3>
    <form method="POST" action="{{ route('admin.contact_messages.reply', $message->id) }}">
        @csrf
        <textarea name="reply_message" rows="5" placeholder="Type your reply..." required
            style="width: 100%; padding: 0.5rem; border-radius: 6px; border: 1px solid #ccc; font-size:1rem;"></textarea>
        <button type="submit"
            style="margin-top: 0.5rem; padding: 0.5rem 1rem; border-radius: 6px; background: #1a73e8; color: #fff; border: none; cursor: pointer;">
            Send Reply
        </button>
    </form>
@endif

@if(session('success'))
    <div style="margin-top: 1rem; padding: 0.75rem; background: #d4edda; color: #155724; border-radius: 6px;">
        {{ session('success') }}
    </div>
@endif
