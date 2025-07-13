<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class SendPushNotification extends Notification
{
    use Queueable;

    protected $title;
    protected $body;
    protected $deviceToken;

    public function __construct($title, $body, $deviceToken)
    {
        $this->title = $title;
        $this->body = $body;
        $this->deviceToken = $deviceToken;
    }

    public function via($notifiable)
    {
        return ['fcm'];
    }

    public function toFcm($notifiable = null)
    {
        $serverKey = config('services.fcm.key'); // store in config/services.php

        $response = Http::withToken($serverKey)
            ->post('https://fcm.googleapis.com/fcm/send', [
                'to' => $this->deviceToken,
                'notification' => [
                    'title' => $this->title,
                    'body' => $this->body,
                    'sound' => 'default',
                ],
                'data' => [
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    'status' => 'done'
                ],
                'priority' => 'high',
            ]);

        return $response->json(); // For logging or debugging
    }
}
