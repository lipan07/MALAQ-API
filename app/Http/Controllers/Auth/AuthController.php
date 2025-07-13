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

class AuthController extends Controller
{
    use HandlesDeviceTokens;

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
            // Handle FCM token
            if ($request->fcmToken && $request->platform) {
                $this->updateDeviceTokens($user, $request->fcmToken, $request->platform);
            }

            // Send login notification to all devices
            $deviceToken = $request->fcmToken;

            $title = 'Hello 👋';
            $body = 'This is a test push notification from Laravel 🚀';

            $notification = new SendPushNotification($title, $body, $deviceToken);

            // You can also send to a notifiable model with `notify()`
            $notification->toFcm();
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
