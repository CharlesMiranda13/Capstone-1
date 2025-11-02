<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function broadcastOn(): array
    {
        \Log::info('Broadcasting message', [
            'sender' => $this->message->sender_id,
            'receiver' => $this->message->receiver_id
        ]);
        return [
            new PrivateChannel('healconnect-chat.'. $this->message->sender_id),
            new PrivateChannel('healconnect-chat.'. $this->message->receiver_id),
        ];

    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}
