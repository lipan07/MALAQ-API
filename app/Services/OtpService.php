<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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
     * Send OTP via Email
     */
    private function sendEmailOtp(string $email, string $otp): bool
    {
        try {
            $subject = 'Your Verification Code - nearX';

            Mail::send('emails.otp', ['otp' => $otp], function ($mail) use ($email, $subject) {
                $mail->to($email)
                    ->subject($subject);
            });

            Log::info("OTP email sent successfully to {$email}");
            return true;
        } catch (Exception $e) {
            Log::error("Failed to send OTP email to {$email}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send OTP to user via email
     */
    public function sendOtp(string $email, ?string $phoneNumber = null, ?string $countryCode = null): array
    {
        $user = User::where('email', $email)->first();

        // Generate dynamic OTP
        $otp = $this->generateOtp();

        if (!$user) {
            // Create new user with email
            $userData = [
                'name' => 'User',
                'email' => $email,
                'password' => Hash::make($otp), // Store OTP encrypted in password field
                'otp_resend_count' => 0,
                'otp_sent_at' => now(),
                'last_otp_resend_at' => now(),
            ];

            // Add phone number if provided
            if ($phoneNumber) {
                $userData['phone_no'] = $phoneNumber;
            }

            $user = User::create($userData);
        } else {
            // Update phone number if provided and not set
            if ($phoneNumber && !$user->phone_no) {
                $user->update(['phone_no' => $phoneNumber]);
            }

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

            // Update user with new OTP stored encrypted in password field
            $user->update([
                'password' => Hash::make($otp), // Store OTP encrypted in password field
                'otp_resend_count' => $user->otp_resend_count + 1,
                'otp_sent_at' => now(),
                'last_otp_resend_at' => now(),
            ]);
        }

        // Send OTP via Email
        $emailSent = $this->sendEmailOtp($email, $otp);

        if (!$emailSent) {
            Log::warning("Email sending failed for {$email}. OTP: {$otp}");

            // In development, we can still return success with the OTP
            if (config('app.debug')) {
                return [
                    'success' => true,
                    'message' => 'OTP generated (Email failed - check logs)',
                    'otp' => $otp, // Only in debug mode
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

        Log::info("OTP email sent successfully to {$email}");

        return [
            'success' => true,
            'message' => 'OTP sent successfully to your email',
            'otp' => config('app.debug') ? $otp : null, // Only return OTP in debug mode
            'resend_count' => $user->otp_resend_count,
            'next_resend_in_minutes' => $this->getNextResendInterval($user->otp_resend_count),
        ];
    }

    /**
     * Verify OTP against encrypted password field
     */
    public function verifyOtp(string $email, string $otp): bool
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return false;
        }

        // Verify OTP against encrypted password field
        if (!Hash::check($otp, $user->password)) {
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
        $nextResendAt = $user->last_otp_resend_at->copy()->addMinutes($resendInterval);

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
    public function getResendStatus(string $email): array
    {
        $user = User::where('email', $email)->first();

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
