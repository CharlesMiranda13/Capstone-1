<h2>{{ $message->name }}</h2>

<p><strong>Email:</strong> {{ $message->email }}</p>
<p><strong>Phone:</strong> {{ $message->phone ?? 'N/A' }}</p>
<p><strong>Date:</strong> {{ $message->created_at->format('M d, Y H:i') }}</p>

<hr>

<p>{{ $message->message }}</p>

@if($message->is_replied)
    <h3>Admin Reply</h3>
    <p class="reply-box">{{ $message->reply }}</p>
@else
    <h3>Reply to User</h3>
    <form method="POST" action="{{ route('admin.contact_messages.reply', $message->id) }}">
        @csrf
        <textarea name="reply_message" rows="5" placeholder="Type your reply..." required></textarea>
        <button type="submit">Send Reply</button>
    </form>
@endif

@if(session('success'))
    <div class="page-alert">
        {{ session('success') }}
    </div>
@endif