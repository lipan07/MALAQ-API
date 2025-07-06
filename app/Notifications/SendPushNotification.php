<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\CloudMessage;

class SendPushNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $body,
        public array $data = []
    ) {}

    public function via($notifiable)
    {
        return [\App\Broadcasting\FirebaseChannel::class];
    }

    public function toFirebase($notifiable)
    {
        return CloudMessage::new()
            ->withNotification([
                'title' => $this->title,
                'body' => $this->body
            ])
            ->withData($this->data);
    }
}
