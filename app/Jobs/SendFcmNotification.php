<?php

namespace App\Jobs;

use App\Models\DeviceToken;
use App\Services\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendFcmNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $receiverId;
    public $title;
    public $body;
    public $data;

    public function __construct($receiverId, $title, $body, $data = [])
    {
        $this->receiverId = $receiverId;
        $this->title = $title;
        $this->body = $body;
        $this->data = $data;
    }

    public function handle(FcmService $fcmService)
    {
        $deviceToken = DeviceToken::where('user_id', $this->receiverId)->first();

        if ($deviceToken) {
            $fcmService->sendNotification(
                $deviceToken->token,
                $this->title,
                $this->body,
                $this->data
            );
        }
    }
}
