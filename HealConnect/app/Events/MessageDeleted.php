<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class MessageDeleted implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public $messageId;
    public $receiverId;

    public function __construct($messageId, $receiverId)
    {
        $this->messageId = $messageId;
        $this->receiverId = $receiverId;
    }

    public function broadcastOn()
    {
        return new PresenceChannel('chat.' . $this->receiverId);
    }

    public function broadcastWith()
    {
        return ['id' => $this->messageId];
    }
}
