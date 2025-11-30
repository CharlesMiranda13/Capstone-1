<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AppointmentUpdateEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $appointmentCount;

    public function __construct($userId, $appointmentCount)
    {
        $this->userId = $userId;
        $this->appointmentCount = $appointmentCount;
    }

    public function broadcastOn()
    {
        return new Channel('user.' . $this->userId);
    }

    public function broadcastAs()
    {
        return 'appointment-update';
    }

    public function broadcastWith()
    {
        return [
            'appointment_count' => $this->appointmentCount
        ];
    }
}