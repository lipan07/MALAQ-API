<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use SerializesModels;

    public $chatId;
    public $message;

    public function __construct($chatId, $message)
    {
        $this->chatId = $chatId;
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new Channel("chat.{$this->chatId}");
    }

    public function broadcastWith()
    {
        return [
            'chat_id' => $this->chatId,
            'message' => $this->message,
        ];
    }
}
