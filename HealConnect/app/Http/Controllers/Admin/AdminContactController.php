<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail; 


class AdminContactController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::orderBy('created_at', 'desc')->get();
        return view('user.admin.user_concern', compact('messages'));
    }
    

    public function show($id)
    {
        $message = ContactMessage::findOrFail($id);

        // Mark as read
        if (!$message->is_read) {
            $message->is_read = true;
            $message->save();
        }

        return view('user.admin.concern_show', compact('message'));

    }

    public function reply(Request $request, $id)
    {
        $message = ContactMessage::findOrFail($id);

        $request->validate([
            'reply_message' => 'required|string|max:2000',
        ]);

        // Save reply in DB
        $message->reply = $request->reply_message;
        $message->is_replied = true;
        $message->save();

        // Send email to user
        Mail::to($message->email)->send(new \App\Mail\UserConcernReplyMail($message, $request->reply_message));

        return back()->with('success', 'Reply sent successfully.');
    }

}
