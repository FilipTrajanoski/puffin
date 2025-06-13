<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TripUpdatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $tripId;

    public function __construct($tripId)
    {
        $this->tripId = $tripId;
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('trip-updates.' . $this->tripId);
    }

    public function broadcastAs(): string
    {
        return 'TripUpdatedEvent';
    }
}
