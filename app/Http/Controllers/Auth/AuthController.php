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

class AuthController extends Controller
{
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

        // if (!$user || ($request->otp != $user->otp)) {
        if (($request->otp != '1234')) {
            return response()->json(['message' => 'The provided credentials are incorrect.'], 401);
        }

        if (!$user) {
            $user = User::create([
                'name' => 'A',
                'phone_no' => $request->phoneNumber,
                'password' => Hash::make('1234'),
            ]);
        }
        $user->update(['password' => '']);

        // Save FCM token if present
        if ($request->has('fcmToken') && $request->has('platform')) {
            DeviceToken::updateOrCreate(
                ['user_id' => $user->id, 'token' => $request->fcmToken],
                ['platform' => $request->platform]
            );
        }


        // Use this updated code instead
        if ($request->has('fcmToken')) {
            $token = $request->fcmToken; // Get from request

            // Always use env() securely
            $serverKey = config('services.fcm.key'); // Better approach

            $response = Http::withHeaders([
                'Authorization' => 'key=' . $serverKey,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', [
                'to' => $token,
                'priority' => 'high', // Add priority for immediate delivery
                'notification' => [
                    'title' => 'Login Successful',
                    'body' => 'Welcome back!',
                    'sound' => 'default',
                ],
                'data' => [ // Add custom data payload
                    'type' => 'login',
                    'timestamp' => now()->toDateTimeString()
                ]
            ]);

            // Handle potential errors
            if ($response->failed()) {
                Log::error('FCM failed: ' . $response->body());
            }
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
