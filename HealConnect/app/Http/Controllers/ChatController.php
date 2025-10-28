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
    public function index()
    {
        $user = Auth::user();

        $conversations = User::where('id', '!=', $user->id)
            ->where('role', '!=','admin')
            ->get();

        return view('shared.chat', compact('user', 'conversations'));
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
            ->get();

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
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json(['success' => true, 'message' => $message]);
    }
}
