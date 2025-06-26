<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Laravel\Firebase\Facades\FirebaseMessaging;

class FirebaseChannel
{
    public function send($notifiable, Notification $notification)
    {
        // Check if the notification has the required method
        if (!method_exists($notification, 'toFirebase')) {
            throw new \Exception('Notification is missing toFirebase method');
        }

        $message = $notification->toFirebase($notifiable);

        // Validate we have a CloudMessage instance
        if (!$message instanceof CloudMessage) {
            return;
        }

        // Get tokens from notifiable
        $tokens = (array) $notifiable->routeNotificationFor('firebase', $notification);

        if (empty($tokens)) {
            return;
        }

        try {
            // Send multicast notification
            $report = FirebaseMessaging::sendMulticast($message, $tokens);

            // Handle invalid tokens
            if ($report->hasFailures()) {
                $invalidTokens = [];
                foreach ($report->invalidTokens() as $invalidToken) {
                    $invalidTokens[] = $invalidToken->value();
                }

                if (!empty($invalidTokens)) {
                    // Delete invalid tokens from database
                    \App\Models\DeviceToken::whereIn('token', $invalidTokens)->delete();
                }
            }
        } catch (\Throwable $e) {
            \Log::error('FCM Error: ' . $e->getMessage());
        }
    }
}
