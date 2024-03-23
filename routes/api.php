<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SmsController;
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

Route::middleware('auth:sanctum')->group(function () {
    //User
    Route::get('/user', [UserController::class, 'index']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    //Post
    Route::resource('posts', PostController::class);
    Route::get('/my-post', [PostController::class, 'myPost']);
    //Category
    Route::get('/category', [CategoryController::class, 'index']);
    //Chat
    Route::post('/chats', [ChatController::class, 'store']);
    Route::get('/chats/{post_id}', [ChatController::class, 'show']);
    //Sms
    Route::get('/send-sms', [SmsController::class, 'sendMessage']);
    //Report
    Route::post('/reports', [ReportController::class, 'store']);
    //Logout
    Route::post('logout', [AuthController::class, 'logout']);
});



Route::get('test', function () {
    return 'Hello World!!';
});
