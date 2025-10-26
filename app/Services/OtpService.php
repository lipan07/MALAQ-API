<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Twilio\Rest\Client;
use Exception;

class OtpService
{
    /**
     * Progressive resend timer intervals in minutes
     */
    private const RESEND_INTERVALS = [2, 5, 10, 15, 20];

    /**
     * Maximum resend attempts
     */
    private const MAX_RESEND_ATTEMPTS = 5;

    /**
     * Generate a random OTP
     */
    public function generateOtp(): string
    {
        return str_pad(random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
    }

    /**
     * Send SMS via Twilio
     */
    private function sendSms(string $phoneNumber, string $otp, string $countryCode = '+91'): bool
    {
        try {
            $accountSid = Config::get("credentials.twilio.sid");
            $authToken = Config::get("credentials.twilio.auth_token");
            $twilioNumber = Config::get("credentials.twilio.number");

            // Validate Twilio configuration
            if (!$accountSid || !$authToken || !$twilioNumber) {
                Log::error('Twilio configuration missing');
                return false;
            }

            $client = new Client($accountSid, $authToken);

            // Format phone number with country code
            $formattedNumber = $countryCode . $phoneNumber;

            // Create SMS message
            $message = "Your Reuse app OTP is: {$otp}. This OTP is valid for 10 minutes. Do not share this code with anyone.";

            $client->messages->create(
                $formattedNumber,
                [
                    'from' => $twilioNumber,
                    'body' => $message
                ]
            );

            Log::info("SMS sent successfully to {$formattedNumber}");
            return true;
        } catch (Exception $e) {
            Log::error("Failed to send SMS to {$phoneNumber}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send OTP to user
     */
    public function sendOtp(string $phoneNumber, string $countryCode = '+91'): array
    {
        $user = User::where('phone_no', $phoneNumber)->first();

        if (!$user) {
            $user = User::create([
                'name' => 'User',
                'phone_no' => $phoneNumber,
                // 'password' => bcrypt('1234'),
                'otp' => $this->generateOtp(),
                'otp_resend_count' => 0,
                'otp_sent_at' => now(),
                'last_otp_resend_at' => now(),
            ]);
        } else {
            // Check if user can resend OTP
            $canResend = $this->canResendOtp($user);

            if (!$canResend['can_resend']) {
                return [
                    'success' => false,
                    'message' => $canResend['message'],
                    'next_resend_at' => $canResend['next_resend_at'],
                    'resend_count' => $user->otp_resend_count,
                ];
            }

            // Update user with new OTP
            $user->update([
                'otp' => $this->generateOtp(),
                'otp_resend_count' => $user->otp_resend_count + 1,
                'otp_sent_at' => now(),
                'last_otp_resend_at' => now(),
            ]);
        }

        // Send SMS via Twilio
        $smsSent = $this->sendSms($phoneNumber, $user->otp, $countryCode);

        if (!$smsSent) {
            // Fallback: Log the OTP for development/testing
            Log::warning("SMS sending failed for {$phoneNumber}. OTP: {$user->otp}");

            // In development, we can still return success with the OTP
            // In production, you might want to return failure
            if (config('app.debug')) {
                return [
                    'success' => true,
                    'message' => 'OTP generated (SMS failed - check logs)',
                    'otp' => $user->otp, // Only in debug mode
                    'resend_count' => $user->otp_resend_count,
                    'next_resend_in_minutes' => $this->getNextResendInterval($user->otp_resend_count),
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to send OTP. Please try again.',
                'resend_count' => $user->otp_resend_count,
            ];
        }

        return [
            'success' => true,
            'message' => 'OTP sent successfully',
            'otp' => $user->otp, // Remove this in production
            'resend_count' => $user->otp_resend_count,
            'next_resend_in_minutes' => $this->getNextResendInterval($user->otp_resend_count),
        ];
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(string $phoneNumber, string $otp): bool
    {
        $user = User::where('phone_no', $phoneNumber)->first();

        if (!$user || $user->otp !== $otp) {
            return false;
        }

        // Reset OTP resend count on successful verification
        $user->update([
            'otp_resend_count' => 0,
            'otp_sent_at' => null,
            'last_otp_resend_at' => null,
        ]);

        return true;
    }

    /**
     * Check if user can resend OTP
     */
    public function canResendOtp(User $user): array
    {
        if ($user->otp_resend_count >= self::MAX_RESEND_ATTEMPTS) {
            return [
                'can_resend' => false,
                'message' => 'Maximum resend attempts reached. Please try again later.',
                'next_resend_at' => null,
            ];
        }

        if (!$user->last_otp_resend_at) {
            return [
                'can_resend' => true,
                'message' => 'OTP can be resent',
                'next_resend_at' => null,
            ];
        }

        $resendInterval = $this->getNextResendInterval($user->otp_resend_count);
        $nextResendAt = $user->last_otp_resend_at->addMinutes($resendInterval);

        if (now()->lt($nextResendAt)) {
            $remainingMinutes = now()->diffInMinutes($nextResendAt, false);

            return [
                'can_resend' => false,
                'message' => "Please wait {$remainingMinutes} minutes before resending OTP",
                'next_resend_at' => $nextResendAt,
            ];
        }

        return [
            'can_resend' => true,
            'message' => 'OTP can be resent',
            'next_resend_at' => null,
        ];
    }

    /**
     * Get the next resend interval based on current resend count
     */
    private function getNextResendInterval(int $resendCount): int
    {
        $index = min($resendCount, count(self::RESEND_INTERVALS) - 1);
        return self::RESEND_INTERVALS[$index];
    }

    /**
     * Get resend status for frontend
     */
    public function getResendStatus(string $phoneNumber): array
    {
        $user = User::where('phone_no', $phoneNumber)->first();

        if (!$user) {
            return [
                'can_resend' => true,
                'resend_count' => 0,
                'next_resend_in_minutes' => 2,
                'message' => 'OTP can be sent',
            ];
        }

        $canResend = $this->canResendOtp($user);

        return [
            'can_resend' => $canResend['can_resend'],
            'resend_count' => $user->otp_resend_count,
            'next_resend_in_minutes' => $canResend['can_resend']
                ? $this->getNextResendInterval($user->otp_resend_count)
                : now()->diffInMinutes($canResend['next_resend_at'], false),
            'message' => $canResend['message'],
            'next_resend_at' => $canResend['next_resend_at']?->toISOString(),
        ];
    }
}
