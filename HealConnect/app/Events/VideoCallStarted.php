<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VideoCallStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('healconnect-chat.' . $this->data['receiver_id'])
        ];
    }

    public function broadcastAs(): string
    {
        return 'video.call.started';
    }

    public function broadcastWith(): array
    {
        return [
            'caller' => $this->data['caller'],
            'receiver_id' => $this->data['receiver_id'],
            'room' => $this->data['room'],
            'room_url' => $this->data['room_url'],
            'token' => $this->data['token'] ?? null
        ];
    }
}