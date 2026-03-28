<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithBroadcasting, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct($message)
    {
        $this->message = $message;
        $this->broadcastVia('reverb');
    }

    public function broadcastOn()
    {
        Log::info("Channel for broadcasting message: chat.{$this->message->chat_id}");
        return new Channel("chat.{$this->message->chat_id}");
    }

    public function broadcastWith()
    {
        return [
            'chat_id' => $this->message->chat_id,
            'created_at' => $this->message->created_at->toDateTimeString(),
            'id' => $this->message->id,
            'message' => $this->message->message,
            'user_id' => $this->message->user_id,
            'is_seen' => (int) (bool) $this->message->is_seen,
            'updated_at' => $this->message->updated_at->toDateTimeString(),
        ];
    }

    public function broadcastAs(): string
    {
        return 'MessageSent';
    }
}
