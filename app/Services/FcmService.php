<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FcmService
{
    public static function sendNotification($deviceToken, $title, $body, $data = [])
    {
        $serverKey = config('services.fcm.key');

        return Http::withToken($serverKey)
            ->post('https://fcm.googleapis.com/fcm/send', [
                'to' => $deviceToken,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                    'sound' => 'default',
                ],
                'data' => $data + [
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    'status' => 'done',
                ],
                'priority' => 'high',
            ])->json();
    }
}
