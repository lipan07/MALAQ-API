<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\FollowerController;
use App\Http\Controllers\MController;
use App\Http\Controllers\PostCarController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\SupportRequestController;
use App\Http\Controllers\UserController;
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
Route::post('login', [AuthController::class, 'login']);
//Sms
// Route::post('/send-sms', [SmsController::class, 'sendMessage']);

Route::middleware('auth:sanctum')->group(function () {
    //User
    Route::get('/user', [UserController::class, 'index']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::post('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    Route::get('/get-my-profile', [UserController::class, 'getProfile']);

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
    Route::get('/chats/{id}', [ChatController::class, 'show']);
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

    //Follower
    Route::post('/follow-user', [FollowerController::class, 'followUser']);
    Route::get('/user/followers', [FollowerController::class, 'userFollowers']);
    Route::get('/user/following', [FollowerController::class, 'userFollowing']);

    Route::post('/follow-post', [FollowerController::class, 'followPost']);
    Route::get('/post/followers/{post_id}', [FollowerController::class, 'postFollowerByPostID']);
    Route::get('/post/following', [FollowerController::class, 'postFollowing']);
    Route::get('/post/followers', [FollowerController::class, 'postFollowers']);

    //Settings
    Route::post('/settings/change-password', [SettingsController::class, 'changePassword']);
    Route::post('/settings/logout-all-devices', [SettingsController::class, 'logoutAllDevices']);
    Route::delete('/settings/delete-account', [SettingsController::class, 'deleteAccount']);

    //Support
    Route::post('/support-request', [SupportRequestController::class, 'store']);
});

Route::get('test', function () {
    return 'Hello World!!';
});
