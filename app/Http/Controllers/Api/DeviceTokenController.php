<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeviceToken;

class DeviceTokenController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'platform' => 'required|in:android,ios,web',
        ]);

        $user = $request->user();

        DeviceToken::updateOrCreate(
            ['user_id' => $user->id, 'token' => $request->token],
            ['platform' => $request->platform]
        );

        return response()->json(['message' => 'Device token saved successfully.']);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        DeviceToken::where('token', $request->token)->delete();

        return response()->json(['message' => 'Device token deleted.']);
    }
}
