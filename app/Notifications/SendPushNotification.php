<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

class SendPushNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $title;
    protected $body;
    protected $data;

    public function __construct($title, $body, $data = [])
    {
        $this->title = $title;
        $this->body = $body;
        $this->data = $data;
    }

    // Change to use the channel class instead of string
    public function via($notifiable)
    {
        return ['firebase'];
    }

    public function toFirebase($notifiable)
    {
        return CloudMessage::new()
            ->withNotification(FirebaseNotification::create(
                $this->title,
                $this->body
            ))
            ->withData($this->data);
    }
}
