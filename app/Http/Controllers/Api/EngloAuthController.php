<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\EngloAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EngloAuthController extends Controller
{
    public function __construct(
        private EngloAuthService $authService
    ) {
    }

    /**
     * Send 6-digit verification code to email.
     * Works for both new and existing users.
     */
    public function sendVerificationCode(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $result = $this->authService->sendVerificationCode($request->email);

        if ($result['success']) {
            $response = [
                'message' => $result['message'],
            ];
            if (isset($result['otp']) && $result['otp']) {
                $response['otp'] = $result['otp'];
            }
            return response()->json($response);
        }

        $statusCode = isset($result['retry_after_seconds']) ? 429 : 400;
        $errorResponse = ['message' => $result['message']];
        if (isset($result['retry_after_seconds'])) {
            $errorResponse['retry_after_seconds'] = $result['retry_after_seconds'];
        }

        return response()->json($errorResponse, $statusCode);
    }

    /**
     * Verify code and log in (creates user if new).
     */
    public function verifyAndLogin(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|regex:/^[0-9]{6}$/',
        ], [
            'code.regex' => 'The verification code must be exactly 6 digits.',
        ]);

        $result = $this->authService->verifyAndLogin($request->email, $request->code);

        if (!$result) {
            return response()->json([
                'message' => 'Invalid or expired verification code. Please try again.',
            ], 401);
        }

        return response()->json($result);
    }
}
