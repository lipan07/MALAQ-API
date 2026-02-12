<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class AccountDeletionController extends Controller
{
    /**
     * Google Play–compliant page: account and data deletion info.
     * Use for both "Delete account URL" and "Delete data URL".
     */
    public function page(Request $request, ?string $type = null)
    {
        $type = in_array($type, ['account', 'data', 'all'], true) ? $type : 'all';
        return response()->view('account-deletion', [
            'highlightSection' => $type,
            'appName' => config('app.name', 'NearX'),
        ])->header('Content-Type', 'text/html; charset=UTF-8');
    }

    /**
     * Public form submission: request account or data deletion (for users without app access).
     * Sends notification to support; does not delete immediately.
     */
    public function requestDeletion(Request $request)
    {
        $request->validate([
            'type' => 'required|in:account,data',
            'email' => 'nullable|email',
            'phone_no' => 'nullable|string|max:20',
        ], [
            'type.required' => 'Please specify whether you want to delete your account or only some data.',
            'type.in' => 'Type must be "account" or "data".',
        ]);

        if (empty($request->email) && empty($request->phone_no)) {
            return response()->json([
                'message' => 'Please provide either your registered email or phone number so we can identify your account.',
            ], 422);
        }

        $type = $request->type === 'account' ? 'Account deletion' : 'Data deletion';
        $email = $request->email ?? '(not provided)';
        $phone = $request->phone_no ?? '(not provided)';

        try {
            $supportEmail = config('mail.support_email', config('mail.from.address', 'support@nearx.co'));
            Mail::raw(
                "A user has requested {$type} via the web form.\n\n"
                . "Email: {$email}\n"
                . "Phone: {$phone}\n"
                . "Request type: {$type}\n"
                . "Submitted at: " . now()->toIso8601String() . "\n\n"
                . "Please process this request according to your data deletion policy.",
                function ($message) use ($supportEmail, $type) {
                    $message->to($supportEmail)
                        ->subject("[NearX] {$type} request from website");
                }
            );
        } catch (\Throwable $e) {
            Log::warning('AccountDeletionController::requestDeletion mail failed', [
                'error' => $e->getMessage(),
                'email' => $request->email,
                'phone' => $request->phone_no,
            ]);
            return response()->json([
                'message' => 'Your request could not be sent. Please contact us directly at support@nearx.co.',
            ], 500);
        }

        return response()->json([
            'message' => 'Your request has been received. We will process it and contact you at the email or phone you provided.',
        ]);
    }
}
