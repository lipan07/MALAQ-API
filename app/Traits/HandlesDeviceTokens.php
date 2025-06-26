<?php

namespace App\Traits;

use App\Models\DeviceToken;

trait HandlesDeviceTokens
{
    protected function updateDeviceTokens($user, $fcmToken, $platform)
    {
        // Remove token from any other user (if token exists elsewhere)
        DeviceToken::where('token', $fcmToken)
            ->where('user_id', '!=', $user->id)
            ->delete();

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
