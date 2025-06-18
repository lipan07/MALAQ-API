<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $status;
    public $lastActivity;

    public function __construct($userId, $status = 'offline', $lastActivity = null)
    {
        $this->userId = $userId;
        $this->status = $status;
        $this->lastActivity = $lastActivity;
    }

    public function broadcastOn()
    {
        return new Channel('userStatus.' . $this->userId);
    }

    public function broadcastWith()
    {
        return [
            'userId' => $this->userId,
            'status' => $this->status,
            'lastActivity' => $this->lastActivity,
        ];
    }

    public function broadcastAs(): string
    {
        return 'UserStatusChanged';
    }
}
