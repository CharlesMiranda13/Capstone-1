<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class VideoCallStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
        
        // Log the exact channel being used
        Log::info('VideoCallStarted Event Created', [
            'receiver_id' => $data['receiver_id'],
            'receiver_id_type' => gettype($data['receiver_id']),
            'channel_will_be' => 'private-healconnect-chat.' . $data['receiver_id']
        ]);
    }

    public function broadcastOn(): array
    {
        $channel = new PrivateChannel('healconnect-chat.' . $this->data['receiver_id']);
        
        Log::info('Broadcasting on channel:', [
            'channel_name' => 'private-healconnect-chat.' . $this->data['receiver_id'],
            'receiver_id' => $this->data['receiver_id']
        ]);
        
        return [$channel];
    }

    public function broadcastAs(): string
    {
        return 'video.call.started';
    }

    public function broadcastWith(): array
    {
        return [
            'caller' => $this->data['caller'],
            'receiver_id' => (int) $this->data['receiver_id'], 
            'room' => $this->data['room'],
            'room_url' => $this->data['room_url'],
            'token' => $this->data['token'] ?? null
        ];
    }
}