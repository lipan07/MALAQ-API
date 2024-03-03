<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Twilio\Rest\Client;

class SmsController extends Controller
{
    public function sendMessage(Request $request)
    {
        try {
            \Log::info($request->all());
            $account_sid = getenv("TWILIO_SID");
            $auth_token = getenv("TWILIO_AUTH_TOKEN");
            $twilio_number = getenv("TWILIO_NUMBER");
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
