<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\User;

class NewUserRegistered implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    // Broadcast to a private admin channel
    public function broadcastOn()
    {
        return new Channel('admin-notifications');
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->user->id,
            'name' => $this->user->name,
            'role' => $this->user->role,
            'message' => 'New user registered awaiting approval'
        ];
    }
}
