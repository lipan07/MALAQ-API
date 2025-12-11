<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\SignupUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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

    /**
     * Signup new user with name, email, and phone number
     */
    public function signup(SignupUserRequest $request)
    {
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('1234'), // Default password, not used for OTP login
        ];

        // Add phone number if provided
        if ($request->has('phoneNumber') && $request->phoneNumber) {
            $userData['phone_no'] = $request->phoneNumber;
        }

        $user = User::create($userData);

        return response()->json([
            'message' => 'Account created successfully. Please login with your email.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone_no' => $user->phone_no,
            ],
        ], 201);
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
        $user = User::where('email', $request->email)->first();

        // Verify OTP using the service
        if (!$this->otpService->verifyOtp($request->email, $request->otp)) {
            return response()->json(['message' => 'Invalid OTP. Please try again.'], 401);
        }

        if (!$user) {
            $userData = [
                'name' => 'User',
                'email' => $request->email,
                'password' => Hash::make('1234'),
            ];

            // Add phone number if provided
            if ($request->has('phoneNumber') && $request->phoneNumber) {
                $userData['phone_no'] = $request->phoneNumber;
            }

            $user = User::create($userData);
        } else {
            // Update phone number if provided and not set
            if ($request->has('phoneNumber') && $request->phoneNumber && !$user->phone_no) {
                $user->update(['phone_no' => $request->phoneNumber]);
            }
        }

        // After successful login, generate and store a strong random password
        // This ensures the OTP cannot be reused
        if ($user->id != '019a1261-375e-7287-b547-185e3099ee6e') {
            $strongPassword = Str::random(32); // Generate 32 character random password
            $user->update(['password' => Hash::make($strongPassword)]);
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
                'email' => $user->email,
                'phone_no' => $user->phone_no,
                'images' => $user->images, // Include the images data
            ],
        ]);
    }

    /**
     * Send OTP to user's email
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'phoneNumber' => 'nullable|string',
            'countryCode' => 'nullable|string|max:5',
        ]);

        $email = $request->email;
        $phoneNumber = $request->phoneNumber ?? null;
        $countryCode = $request->countryCode ?? null;

        $result = $this->otpService->sendOtp($email, $phoneNumber, $countryCode);

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
     * Resend OTP to user's email
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'phoneNumber' => 'nullable|string',
            'countryCode' => 'nullable|string|max:5',
        ]);

        $email = $request->email;
        $phoneNumber = $request->phoneNumber ?? null;
        $countryCode = $request->countryCode ?? null;

        $result = $this->otpService->sendOtp($email, $phoneNumber, $countryCode);

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
     * Test OTP sending (for development/testing)
     */
    public function testSms(Request $request)
    {
        if (!config('app.debug')) {
            return response()->json(['message' => 'This endpoint is only available in debug mode'], 403);
        }

        $request->validate([
            'email' => 'required|email',
            'phoneNumber' => 'nullable|string',
            'countryCode' => 'nullable|string|max:5',
        ]);

        $email = $request->email;
        $phoneNumber = $request->phoneNumber ?? null;
        $countryCode = $request->countryCode ?? null;

        $result = $this->otpService->sendOtp($email, $phoneNumber, $countryCode);

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
