<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;
use App\Events\MessageSent;
use App\Models\User;

class ChatController extends Controller
{
    // Show chat page
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get users who have exchanged messages with the current user
        $conversations = User::whereHas('sender', function ($query) use ($user) {
                $query->where('receiver_id', $user->id);
            })
            ->orWhereHas('receiver', function ($query) use ($user) {
                $query->where('sender_id', $user->id);
            })
            ->where('id', '!=', $user->id)
            ->where('role', '!=', 'admin')
            ->get()
            ->map(function ($otherUser) use ($user) {
                // Get latest message exchanged between the two users
                $latestMessage = Message::where(function ($query) use ($user, $otherUser) {
                        $query->where('sender_id', $user->id)
                            ->where('receiver_id', $otherUser->id);
                    })
                    ->orWhere(function ($query) use ($user, $otherUser) {
                        $query->where('sender_id', $otherUser->id)
                            ->where('receiver_id', $user->id);
                    })
                    ->latest('created_at')
                    ->first();

                // Attach latest message text and time to the user object
                $otherUser->latest_message = $latestMessage ? $latestMessage->message : null;
                $otherUser->latest_message_time = $latestMessage ? $latestMessage->created_at : now()->subYears(10);

                return $otherUser;
            })
            // Sort by latest message time descending 
            ->sortByDesc('latest_message_time')
            ->values();

        $receiverId = $request->query('receiver_id');
        $receiver = $receiverId ? User::find($receiverId) : null;

        return view('shared.chat', compact('user', 'conversations', 'receiver'));
    }


    // Fetch messages between current user and selected user
    public function fetch(Request $request)
    {
        $userId = Auth::id();
        $receiverId = $request->receiver_id;

        $messages = Message::where(function ($query) use ($userId, $receiverId) {
            $query->where('sender_id', $userId)
                ->where('receiver_id', $receiverId);
            })
            ->orWhere(function ($query) use ($userId, $receiverId) {
                $query->where('sender_id', $receiverId)
                  ->where('receiver_id', $userId);
        })
        ->orderBy('created_at', 'asc')
        ->get()
        ->map(function ($msg) {

            if ($msg->message_type === 'file') {
                $msg->type = 'file';
                $msg->file_url = asset('storage/' . $msg->message);
            } elseif ($msg->message_type === 'voice') {
                $msg->type = 'voice';
                $msg->message_url = asset('storage/' . $msg->message);
            } else {
                $msg->type = 'text';
            }
            return $msg;
        });

        return response()->json($messages);
    }

    // Send new message
    public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'message_type' => 'text',
        ]);

        $message->type = 'text';

        broadcast(new MessageSent($message))->toOthers();

        return response()->json(['success' => true, 'message' => $message]);
    }

    // Voice Message

    public function sendVoice(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'voice_message' => 'required|file|mimetypes:audio/webm,audio/mpeg,audio/ogg|max:10240', 
        ]);

        $storagePath = storage_path('app/public/voice_messages');
        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        // Store voice message in public storage
        $path = $request->file('voice_message')->store('voice_messages', 'public');

        $url = asset('storage/' . $path); 

        // Save to database
        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $path,
            'message_type' => 'voice',
        ]);

        $message->message_url = $url;
        $message->type = 'voice';

        broadcast(new MessageSent($message))->toOthers();

        return response()->json(['success' => true, 'message' => $message]);
    }

    // File Message

    public function sendFile(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi,pdf,doc,docx|max:20480', // 20MB
        ]);

        $storagePath = storage_path('app/public/chat_files');
        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        // Store file
        $path = $request->file('file')->store('chat_files', 'public');
        $url = asset('storage/' . $path);

        // Save message record
        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $path,
            'message_type' => 'file',
        ]);

        $message->file_url = $url;
        $message->type = 'file';

        broadcast(new MessageSent($message))->toOthers();

        return response()->json(['success' => true, 'message' => $message]);
    }

}
