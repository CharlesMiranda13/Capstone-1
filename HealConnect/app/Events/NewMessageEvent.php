<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessageEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $unreadCount;

    public function __construct($userId, $unreadCount)
    {
        $this->userId = $userId;
        $this->unreadCount = $unreadCount;
        
        \Log::info('NewMessageEvent constructor called', [
            'user_id' => $userId,
            'unread_count' => $unreadCount
        ]);
    }

    public function broadcastOn()
    {
        $channel = new Channel('user.' . $this->userId);
        \Log::info('Broadcasting on channel', ['channel' => 'user.' . $this->userId]);
        return $channel;
    }

    public function broadcastAs()
    {
        return 'new-message';
    }

    public function broadcastWith()
    {
        return [
            'unread_count' => $this->unreadCount
        ];
    }
}