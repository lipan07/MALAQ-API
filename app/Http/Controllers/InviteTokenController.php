<?php

namespace App\Http\Controllers;

use App\Models\InviteToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InviteTokenController extends Controller
{
    /**
     * Get user's invite tokens
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $tokens = InviteToken::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($token) {
                return [
                    'id' => $token->id,
                    'token' => $token->token,
                    'is_used' => $token->is_used,
                    'is_valid' => $token->isValid(),
                    'expires_at' => $token->expires_at->toIso8601String(),
                    'used_at' => $token->used_at?->toIso8601String(),
                    'used_by' => $token->usedBy ? [
                        'id' => $token->usedBy->id,
                        'name' => $token->usedBy->name,
                        'email' => $token->usedBy->email,
                    ] : null,
                    'created_at' => $token->created_at->toIso8601String(),
                ];
            });

        return response()->json([
            'tokens' => $tokens,
        ]);
    }

    /**
     * Validate an invite token (public endpoint for checking before registration)
     */
    public function validateToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string|size:7',
        ]);

        $token = InviteToken::where('token', $request->token)->first();

        if (!$token) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid invite token',
            ], 404);
        }

        if ($token->is_used) {
            return response()->json([
                'valid' => false,
                'message' => 'This invite token has already been used',
            ], 400);
        }

        if ($token->expires_at->isPast()) {
            return response()->json([
                'valid' => false,
                'message' => 'This invite token has expired',
            ], 400);
        }

        return response()->json([
            'valid' => true,
            'message' => 'Token is valid',
            'token' => $token->token,
            'expires_at' => $token->expires_at->toIso8601String(),
        ]);
    }

    /**
     * Get invite URL for a token
     */
    public function getInviteUrl(Request $request, $token)
    {
        $user = $request->user();
        
        $inviteToken = InviteToken::where('token', $token)
            ->where('user_id', $user->id)
            ->first();

        if (!$inviteToken) {
            return response()->json([
                'message' => 'Token not found',
            ], 404);
        }

        $baseUrl = config('app.url', 'https://big-brain.co.in');
        $inviteUrl = "{$baseUrl}/invite/{$token}";

        return response()->json([
            'url' => $inviteUrl,
            'token' => $token,
        ]);
    }
}
