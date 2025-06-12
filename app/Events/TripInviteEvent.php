<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TripInviteEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $count;

    public function __construct($user_id, $count)
    {
        $this->userId = $user_id;
        $this->count = $count;
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('trip-invites.' . $this->userId);
    }

    public function broadcastAs(): string
    {
        return 'TripInviteEvent';
    }

    public function broadcastWith(): array
    {
        return [
            'count' => $this->count
        ];
    }
}
