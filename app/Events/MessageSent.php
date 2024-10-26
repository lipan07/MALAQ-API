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
            'chat_id' => $this->message->chat_id,
            'created_at' => $this->message->created_at->toDateTimeString(),
            'id' => $this->message->id,
            'message' => $this->message->message,
            'updated_at' => $this->message->updated_at->toDateTimeString(),
            'user_id' => $this->message->user_id
        ];
    }
}
