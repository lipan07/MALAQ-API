<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Laravel\Firebase\Facades\FirebaseMessaging;

class FirebaseChannel
{
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toFirebase($notifiable);

        if (!$message) {
            return;
        }

        $tokens = (array) $notifiable->routeNotificationFor('firebase', $notification);

        try {
            if (count($tokens)) {
                FirebaseMessaging::sendMulticast($message, $tokens);
            }
        } catch (\Throwable $e) {
            \Log::error('FCM Error: ' . $e->getMessage());
        }
    }
}
