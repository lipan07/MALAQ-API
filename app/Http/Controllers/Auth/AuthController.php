<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\DeviceToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Traits\HandlesDeviceTokens;
use App\Notifications\SendPushNotification;
use App\Services\OtpService;

class AuthController extends Controller
{
    use HandlesDeviceTokens;

    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function register(StoreUserRequest $request)
    {
        $user = User::create([
            'name' => 'A',
            'phone_no' => $request->phone_no,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['user' => $user->makeHidden(['id'])->toArray()]);
    }

    // public function login(LoginUserRequest $request)
    // {
    //     $user = User::where(['phone_no' => $request->phoneNumber])->first();

    //     if (!$user || !Hash::check($request->password, $user->password)) {
    //         return response()->json(['message' => 'The provided credentials are incorrect.'], 401);
    //     }
    //     $user->update(['password' => '']);

    //     return response()->json(['token' => $user->createToken('API Token')->plainTextToken]);
    // }

    public function login(LoginUserRequest $request)
    {
        $user = User::where(['phone_no' => $request->phoneNumber])->first();

        // Verify OTP using the service
        if (!$this->otpService->verifyOtp($request->phoneNumber, $request->otp)) {
            return response()->json(['message' => 'The provided credentials are incorrect.'], 401);
        }

        if (!$user) {
            $user = User::create([
                'name' => 'User',
                'phone_no' => $request->phoneNumber,
                'password' => Hash::make('1234'),
            ]);
        }

        if ($user->id != 1) {
            $user->update(['password' => '']);
        }

        // Save FCM token if present
        if ($request->has('fcmToken') && $request->has('platform')) {
            DeviceToken::where('user_id', $user->id)
                ->delete();
            DeviceToken::updateOrCreate(
                ['token' => $request->fcmToken],
                ['user_id' => $user->id, 'platform' => $request->platform]
            );
        }

        // Load the images relationship
        $user->load('images');

        return response()->json([
            'token' => $user->createToken('API Token')->plainTextToken,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone_no' => $user->phone_no,
                'images' => $user->images, // Include the images data
            ],
        ]);
    }

    /**
     * Send OTP to user's phone number
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'phoneNumber' => 'required|regex:/^[0-9]{10}$/',
            'countryCode' => 'nullable|string|max:5',
        ]);

        $countryCode = $request->countryCode ?? '+91';
        $result = $this->otpService->sendOtp($request->phoneNumber, $countryCode);

        if ($result['success']) {
            return response()->json([
                'message' => $result['message'],
                'resend_count' => $result['resend_count'],
                'next_resend_in_minutes' => $result['next_resend_in_minutes'],
                // Remove 'otp' in production
                'otp' => $result['otp'] ?? null,
            ]);
        }

        return response()->json([
            'message' => $result['message'],
            'next_resend_at' => $result['next_resend_at'],
            'resend_count' => $result['resend_count'],
        ], 429); // Too Many Requests
    }

    /**
     * Resend OTP to user's phone number
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'phoneNumber' => 'required|regex:/^[0-9]{10}$/',
            'countryCode' => 'nullable|string|max:5',
        ]);

        $countryCode = $request->countryCode ?? '+91';
        $result = $this->otpService->sendOtp($request->phoneNumber, $countryCode);

        if ($result['success']) {
            return response()->json([
                'message' => $result['message'],
                'resend_count' => $result['resend_count'],
                'next_resend_in_minutes' => $result['next_resend_in_minutes'],
                // Remove 'otp' in production
                'otp' => $result['otp'] ?? null,
            ]);
        }

        return response()->json([
            'message' => $result['message'],
            'next_resend_at' => $result['next_resend_at'],
            'resend_count' => $result['resend_count'],
        ], 429); // Too Many Requests
    }

    /**
     * Test SMS sending (for development/testing)
     */
    public function testSms(Request $request)
    {
        if (!config('app.debug')) {
            return response()->json(['message' => 'This endpoint is only available in debug mode'], 403);
        }

        $request->validate([
            'phoneNumber' => 'required|regex:/^[0-9]{10}$/',
            'countryCode' => 'nullable|string|max:5',
        ]);

        $countryCode = $request->countryCode ?? '+91';
        $result = $this->otpService->sendOtp($request->phoneNumber, $countryCode);

        return response()->json($result);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        if ($request->has('fcmToken')) {
            DeviceToken::where('token', $request->fcmToken)->delete();
        }

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }
}
