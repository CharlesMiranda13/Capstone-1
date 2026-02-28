<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageReadEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $readerId; // The user who read the messages
    public $senderId; // The user whose messages were read

    /**
     * Create a new event instance.
     */
    public function __construct($readerId, $senderId)
    {
        $this->readerId = $readerId;
        $this->senderId = $senderId;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('healconnect-chat.' . $this->senderId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'messages.read';
    }
}
