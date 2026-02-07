<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\SignupUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\DeviceToken;
use Illuminate\Support\Facades\Http;
use App\Traits\HandlesDeviceTokens;
use App\Notifications\SendPushNotification;
use App\Services\OtpService;
use App\Models\InviteToken;

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
            'joined_via_invite' => false, // Default to false
        ];

        // Add phone number if provided
        if ($request->has('phoneNumber') && $request->phoneNumber) {
            $userData['phone_no'] = $request->phoneNumber;
        }

        // Validate invite token before creating user (must be valid and active)
        if ($request->has('invite_token') && $request->invite_token) {
            $this->validateInviteTokenForSignup($request->invite_token);
            $userData['joined_via_invite'] = true;
        }

        $user = User::create($userData);

        // Generate 2 invite tokens for the new user
        $this->generateInviteTokens($user);

        // Handle invite token if provided
        if ($request->has('invite_token') && $request->invite_token) {
            $this->processInviteToken($request->invite_token, $user);
        }

        // Send welcome email
        try {
            $subject = 'Welcome to nearX - Your Account is Ready!';
            $fromAddress = config('mail.from.address');
            $fromName = config('mail.from.name', 'nearX');

            Mail::send('emails.welcome', ['userName' => $user->name], function ($message) use ($user, $subject, $fromAddress, $fromName) {
                $message->to($user->email)
                    ->subject($subject)
                    ->from($fromAddress, $fromName)
                    ->replyTo($fromAddress, $fromName);

                // Add headers to improve deliverability
                $headers = $message->getHeaders();
                $headers->addTextHeader('X-Mailer', 'Laravel');
                $headers->addTextHeader('X-Priority', '1');
                $headers->addTextHeader('Precedence', 'bulk');
                $headers->addTextHeader('List-Unsubscribe', '<mailto:' . $fromAddress . '?subject=unsubscribe>');
                $headers->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
            });
            Log::info("Welcome email sent successfully to {$user->email}");
        } catch (\Exception $e) {
            // Log error but don't fail the signup process
            Log::error("Failed to send welcome email to {$user->email}: " . $e->getMessage());
        }

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

        // Verify one time verification code using the service
        if (!$this->otpService->verifyOtp($request->email, $request->otp)) {
            return response()->json(['message' => 'Invalid one time verification code. Please try again.'], 401);
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
                'joined_via_invite' => $user->joined_via_invite,
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

        // Return error response (could be 400 for user not found, or 429 for rate limit)
        $statusCode = isset($result['next_resend_at']) ? 429 : 400;
        $errorResponse = [
            'message' => $result['message'],
            'resend_count' => $result['resend_count'] ?? 0,
        ];

        if (isset($result['next_resend_at'])) {
            $errorResponse['next_resend_at'] = $result['next_resend_at'];
        }

        return response()->json($errorResponse, $statusCode);
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

        // Return error response (could be 400 for user not found, or 429 for rate limit)
        $statusCode = isset($result['next_resend_at']) ? 429 : 400;
        $errorResponse = [
            'message' => $result['message'],
            'resend_count' => $result['resend_count'] ?? 0,
        ];

        if (isset($result['next_resend_at'])) {
            $errorResponse['next_resend_at'] = $result['next_resend_at'];
        }

        return response()->json($errorResponse, $statusCode);
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

    /**
     * Generate 2 invite tokens for a user.
     * Tokens are inactive until admin confirms a payment (for users who joined via invite).
     */
    private function generateInviteTokens(User $user)
    {
        $isActive = !$user->joined_via_invite; // Active only if user did not join via invite

        for ($i = 0; $i < 2; $i++) {
            InviteToken::create([
                'user_id' => $user->id,
                'token' => InviteToken::generateUniqueToken(),
                'expires_at' => now()->addHours(24),
                'is_active' => $isActive,
            ]);
        }
    }

    /**
     * Validate invite token before signup (throws ValidationException if invalid or inactive)
     */
    private function validateInviteTokenForSignup(string $token): void
    {
        $inviteToken = InviteToken::where('token', $token)->first();

        if (!$inviteToken) {
            throw ValidationException::withMessages(['invite_token' => ['Invalid invite token.']]);
        }
        if ($inviteToken->is_used) {
            throw ValidationException::withMessages(['invite_token' => ['This invite token has already been used.']]);
        }
        if ($inviteToken->expires_at->isPast()) {
            throw ValidationException::withMessages(['invite_token' => ['This invite token has expired.']]);
        }
        if (!$inviteToken->is_active) {
            throw ValidationException::withMessages(['invite_token' => ['This invite token is inactive. The owner must complete a payment and have it confirmed by admin to activate it.']]);
        }
    }

    /**
     * Process invite token when a user registers with one
     */
    private function processInviteToken(string $token, User $newUser)
    {
        $inviteToken = InviteToken::where('token', $token)->first();

        if (!$inviteToken) {
            Log::warning("Invalid invite token used: {$token}");
            return;
        }

        if ($inviteToken->is_used) {
            Log::warning("Already used invite token attempted: {$token}");
            return;
        }

        if ($inviteToken->expires_at->isPast()) {
            Log::warning("Expired invite token used: {$token}");
            return;
        }

        if (!$inviteToken->is_active) {
            Log::warning("Inactive invite token used (owner must have payment confirmed): {$token}");
            return;
        }

        // Mark token as used
        $inviteToken->update([
            'is_used' => true,
            'used_by_user_id' => $newUser->id,
            'used_at' => now(),
        ]);

        Log::info("Invite token used: {$token} by user {$newUser->id} (invited by {$inviteToken->user_id})");
    }
}
