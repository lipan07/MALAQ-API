<?php

namespace App\Traits;

use App\Models\DeviceToken;

trait HandlesDeviceTokens
{
    protected function updateDeviceTokens($user, $fcmToken, $platform)
    {
        // Update or create for current user
        DeviceToken::updateOrCreate(
            ['token' => $fcmToken],
            [
                'user_id' => $user->id,
                'platform' => $platform
            ]
        );
    }
}
