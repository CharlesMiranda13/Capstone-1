<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\ContactMessage;

class NewConcernSubmitted implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $concern;

    public function __construct(ContactMessage $concern)
    {
        $this->concern = $concern;
    }

    public function broadcastOn()
    {
        return new Channel('admin-notifications');
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->concern->id,
            'name' => $this->concern->name,
            'message' => $this->concern->message,
        ];
    }
}
