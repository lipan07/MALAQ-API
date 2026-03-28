<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserStatusChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithBroadcasting, InteractsWithSockets, SerializesModels;

    public $userId;
    public $status;

    public function __construct($userId, $status = 'offline')
    {
        $this->userId = $userId;
        $this->status = $status;
        $this->broadcastVia('reverb');
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
        ];
    }

    public function broadcastAs(): string
    {
        return 'UserStatusChanged';
    }
}
