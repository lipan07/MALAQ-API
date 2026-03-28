<?php

namespace App\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Queue\SerializesModels;

class MessageSeen implements ShouldBroadcastNow
{
    use InteractsWithBroadcasting, SerializesModels;

    public $message;

    public function __construct($message)
    {
        $this->message = $message;
        $this->broadcastVia('reverb');
    }

    public function broadcastOn()
    {
        return new Channel("chat.{$this->message->chat_id}");
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->message->id,
            'chat_id' => $this->message->chat_id,
            'is_seen' => true,
        ];
    }

    public function broadcastAs(): string
    {
        return 'MessageSeen';
    }
}
