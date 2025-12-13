<?php

use App\Events\UserStatusChanged;
use App\Http\Controllers\Api\DeviceTokenController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\FollowerController;
use App\Http\Controllers\MController;
use App\Http\Controllers\PostCarController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostInteractionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ShareController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\SupportRequestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserStatusController;
use App\Http\Controllers\YouTubeController;
use App\Http\Controllers\BackblazeController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('register', [AuthController::class, 'register']);
Route::post('signup', [AuthController::class, 'signup']);
Route::post('login', [AuthController::class, 'login']);
Route::post('send-otp', [AuthController::class, 'sendOtp']);
Route::post('resend-otp', [AuthController::class, 'resendOtp']);
Route::post('otp-resend-status', [AuthController::class, 'getResendStatus']);
Route::post('test-sms', [AuthController::class, 'testSms']); // Debug only
//Sms
// Route::post('/send-sms', [SmsController::class, 'sendMessage']);

// Product sharing (public route - no auth required)
Route::post('product/{id}/track-share', [ShareController::class, 'trackShare'])->name('product.track-share');

Route::middleware('auth:sanctum')->group(function () {
    //User
    Route::get('/user', [UserController::class, 'index']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::post('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    Route::get('/get-my-profile', [UserController::class, 'getProfile']);

    Route::get('/seller-info/{user}', [UserController::class, 'sellerInfo']);
    Route::get('/sellers-post/{user}', [PostController::class, 'sellersPost']);

    //Post
    Route::get('posts', [PostController::class, 'index'])->name('posts.index');
    Route::get('posts/{post}', [PostController::class, 'show'])->name('posts.show');
    Route::post('posts', [PostController::class, 'store'])->name('posts.store');
    Route::post('posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    Route::get('/my-post', [PostController::class, 'myPost']);
    //Category
    Route::get('/category', [CategoryController::class, 'index']);
    //Chat
    Route::get('/chats', [ChatController::class, 'index']);
    Route::post('/chats', [ChatController::class, 'store']);
    Route::get('/chats/{chat}', [ChatController::class, 'show']);
    Route::delete('/chats/{chat}', [ChatController::class, 'destroy']);
    //Report
    Route::post('/reports', [ReportController::class, 'store']);
    //Vehicle brand
    Route::get('/motorcycle/brand', [PostCarController::class, 'motorcycleBrand']);
    Route::get('/scooter/brand', [PostCarController::class, 'scooterBrand']);
    Route::get('/bycycle/brand', [PostCarController::class, 'bycycleBrand']);
    //Logout
    Route::post('logout', [AuthController::class, 'logout']);

    Route::post('/open-chat', [ChatController::class, 'openChat']);
    Route::post('/send-message', [ChatController::class, 'sendMessage']);
    Route::post('/messages/{message}/seen', [ChatController::class, 'markMessagesAsSeen']);

    // YouTube Video Upload
    Route::post('/youtube/upload', [YouTubeController::class, 'uploadVideo']);

    // Backblaze B2 Direct Upload
    Route::get('/backblaze/credentials', [BackblazeController::class, 'getCredentials']);

    //Follower
    Route::post('/follow-user', [FollowerController::class, 'followUser']);
    Route::get('/user/followers', [FollowerController::class, 'userFollowers']);
    Route::get('/user/following', [FollowerController::class, 'userFollowing']);

    Route::post('/follow-post', [FollowerController::class, 'followPost']);
    Route::get('/post/likes/{post_id}', [FollowerController::class, 'postLikesByPostID']);
    Route::get('/user/liked-posts', [FollowerController::class, 'userLikedPosts']);
    Route::get('/post/likes', [FollowerController::class, 'postLikes']);

    // Post interactions (views and likes)
    Route::post('/post/track-view', [PostInteractionController::class, 'trackView']);
    Route::post('/post/toggle-like', [PostInteractionController::class, 'toggleLike']);
    Route::get('/post/{post_id}/stats', [PostInteractionController::class, 'getPostStats']);

    //Settings
    Route::post('/settings/change-password', [SettingsController::class, 'changePassword']);
    Route::post('/settings/logout-all-devices', [SettingsController::class, 'logoutAllDevices']);
    Route::delete('/settings/delete-account', [SettingsController::class, 'deleteAccount']);

    //Support
    Route::post('/support-request', [SupportRequestController::class, 'store']);

    Route::post('/user/online', [UserStatusController::class, 'storeOnlineStatus']);
    Route::post('/user/offline', [UserStatusController::class, 'storeOfflineStatus']);

    Route::get('/user/{id}/status', function ($id) {
        $user = User::findOrFail($id);

        return response()->json([
            'status' => $user->status,
            'last_activity' => $user->last_activity
        ]);
    });

    Route::post('/store-device-token', [DeviceTokenController::class, 'store']);
    Route::post('/delete-device-token', [DeviceTokenController::class, 'destroy']);

    Route::post('/feedback', [FeedbackController::class, 'store']);
});

Route::get('test', function () {
    return 'Hello World!!';
});
