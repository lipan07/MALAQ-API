<?php

namespace App\Http\Controllers;

use App\Models\InviteToken;
use Illuminate\Http\Request;

class InviteLandingController extends Controller
{
    public function show(Request $request, string $token)
    {
        $invite = InviteToken::with('owner')
            ->where('token', $token)
            ->first();

        $status = 'invalid'; // invalid | active | expired | used
        $expiresAt = null;
        $inviterName = null;

        if ($invite) {
            $expiresAt = $invite->expires_at;
            $inviterName = $invite->owner?->name;

            if ($invite->is_used) {
                $status = 'used';
            } elseif ($invite->expires_at && $invite->expires_at->isPast()) {
                $status = 'expired';
            } else {
                $status = 'active';
            }
        }

        $webBaseUrl = config('app.url', 'https://nearx.co');
        $deepLink = "nearx://invite/{$token}";
        $registerUrl = "{$webBaseUrl}/register?invite_token={$token}";
        $installUrl = config('app.play_store_url', 'https://play.google.com/store/apps/details?id=com.malaq.notify');

        return view('invite.landing', [
            'token' => $token,
            'status' => $status,
            'expiresAt' => $expiresAt,
            'inviterName' => $inviterName,
            'deepLink' => $deepLink,
            'installUrl' => $installUrl,
            'registerUrl' => $registerUrl,
            'webBaseUrl' => $webBaseUrl,
        ]);
    }
}

