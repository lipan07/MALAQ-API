<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Twilio\Rest\Client;

class SmsController extends Controller
{
    public function sendMessage(Request $request)
    {
        $user = User::updateOrCreate(['phone_no' => $request->phoneNumber], ['name' => 'A', 'password' => Hash::make(1234)]);

        return response()->json(['status' => 'success', 'data' => $user], 200);
        //
        try {
            $account_sid = Config::get("credentials.twilio.sid");
            $auth_token = Config::get("credentials.twilio.auth_token");
            $twilio_number = Config::get("credentials.twilio.number");
            $client = new Client($account_sid, $auth_token);
            $client->messages->create(
                '+91' . $request->phoneNumber,
                ['from' => $twilio_number, 'body' => 'Hi Lipan, your otp is - 9876']
            );
            return response()->json(['status' => 'success'], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed'], 404);
        }
    }
}
