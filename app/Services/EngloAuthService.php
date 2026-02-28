<?php

namespace App\Services;

use App\Models\EngloUser;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Exception;

class EngloAuthService
{
    private const CACHE_PREFIX = 'englo_verification:';
    private const CACHE_TTL_MINUTES = 10;
    private const RATE_LIMIT_PREFIX = 'englo_verification_sent:';
    private const RATE_LIMIT_COOLDOWN_SECONDS = 60;
    private const MAX_SENDS_PER_HOUR = 5;
    private const HOURLY_COUNT_PREFIX = 'englo_verification_hourly:';

    /**
     * Generate a random 6-digit verification code
     */
    public function generateCode(): string
    {
        return str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Send verification code to email (works for new and existing users)
     */
    public function sendVerificationCode(string $email): array
    {
        $email = strtolower(trim($email));

        // Rate limit: cooldown between sends
        $rateLimitKey = self::RATE_LIMIT_PREFIX . $email;
        $lastSent = Cache::get($rateLimitKey);
        if ($lastSent) {
            $remaining = self::RATE_LIMIT_COOLDOWN_SECONDS - (time() - $lastSent);
            if ($remaining > 0) {
                return [
                    'success' => false,
                    'message' => "Please wait {$remaining} seconds before requesting a new code.",
                    'retry_after_seconds' => $remaining,
                ];
            }
        }

        // Rate limit: max sends per hour
        $hourlyKey = self::HOURLY_COUNT_PREFIX . $email . ':' . now()->format('YmdH');
        $hourlyCount = (int) Cache::get($hourlyKey, 0);
        if ($hourlyCount >= self::MAX_SENDS_PER_HOUR) {
            return [
                'success' => false,
                'message' => 'Too many verification requests. Please try again later.',
            ];
        }

        $code = $this->generateCode();
        $cacheKey = self::CACHE_PREFIX . $email;
        Cache::put($cacheKey, Hash::make($code), now()->addMinutes(self::CACHE_TTL_MINUTES));

        $emailSent = $this->sendVerificationEmail($email, $code);

        if (!$emailSent) {
            Cache::forget($cacheKey);
            Log::warning("Englo: Failed to send verification email to {$email}");

            if (config('app.debug')) {
                return [
                    'success' => true,
                    'message' => 'Code generated (email failed - check logs)',
                    'otp' => $code,
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to send verification email. Please try again.',
            ];
        }

        Cache::put($rateLimitKey, time(), now()->addSeconds(self::RATE_LIMIT_COOLDOWN_SECONDS));
        Cache::put($hourlyKey, $hourlyCount + 1, now()->addHour());

        return [
            'success' => true,
            'message' => 'Verification code sent to your email.',
            'otp' => config('app.debug') ? $code : null,
        ];
    }

    /**
     * Verify code and return user (create if new)
     */
    public function verifyAndLogin(string $email, string $code): ?array
    {
        $email = strtolower(trim($email));
        $cacheKey = self::CACHE_PREFIX . $email;
        $hashedCode = Cache::get($cacheKey);

        if (!$hashedCode || !Hash::check($code, $hashedCode)) {
            return null;
        }

        Cache::forget($cacheKey);

        $user = EngloUser::where('email', $email)->first();

        if (!$user) {
            $name = explode('@', $email)[0] ?? 'User';
            $user = EngloUser::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make(Str::random(32)),
            ]);
        } else {
            $user->update(['password' => Hash::make(Str::random(32))]);
        }

        $token = $user->createToken('Englo API Token')->plainTextToken;

        return [
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ];
    }

    private function sendVerificationEmail(string $email, string $code): bool
    {
        try {
            $subject = 'Your verification code - Englo';
            $fromAddress = config('mail.from.address');
            $fromName = config('mail.from.name', 'Englo');

            Mail::send('emails.englo-verification', ['otp' => $code], function ($message) use ($email, $subject, $fromAddress, $fromName) {
                $message->to($email)
                    ->subject($subject)
                    ->from($fromAddress, $fromName)
                    ->replyTo($fromAddress, $fromName);

                $headers = $message->getHeaders();
                $headers->addTextHeader('X-Mailer', 'Laravel');
                $headers->addTextHeader('X-Priority', '1');
            });

            Log::info("Englo verification email sent to {$email}");
            return true;
        } catch (Exception $e) {
            Log::error("Englo: Failed to send verification email to {$email}: " . $e->getMessage());
            return false;
        }
    }
}
