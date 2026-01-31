<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\PrivacyPolicyController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\InviteTokenController;
use App\Http\Controllers\InviteLandingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');


// Route::middleware('auth')->group(function () {
//     Route::get('/dashboard', function () {
//         return view('welcome');
//     })->name('dashboard');
// });


Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Posts routes
    Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
    Route::post('/posts/{post}/status', [PostController::class, 'changeStatus'])->name('posts.changeStatus');
    Route::post('/posts/{post}/report', [PostController::class, 'report'])->name('posts.report');

    // Users routes
    Route::resource('users', UserController::class);
    Route::post('/users/{user}/block', [UserController::class, 'block'])->name('users.block');
    Route::post('/users/{user}/unblock', [UserController::class, 'unblock'])->name('users.unblock');
    Route::get('/users/{user}/referral-tree', [UserController::class, 'referralTree'])->name('users.referral-tree');

    // Categories routes
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);

    // Invite Token routes (admin only)
    Route::post('/invite-tokens/{tokenId}/regenerate', [InviteTokenController::class, 'regenerateToken'])->name('invite-tokens.regenerate');

    // Payments (admin: list, view screenshot, confirm/reject)
    Route::get('/payments', [\App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}', [\App\Http\Controllers\Admin\PaymentController::class, 'show'])->name('payments.show');
    Route::post('/payments/{payment}/confirm', [\App\Http\Controllers\Admin\PaymentController::class, 'confirm'])->name('payments.confirm');
    Route::post('/payments/{payment}/reject', [\App\Http\Controllers\Admin\PaymentController::class, 'reject'])->name('payments.reject');
});


Route::get('/privacy-policy', [PrivacyPolicyController::class, 'index'])->name('privacy-policy');
// Route::get('/send-sms',[SmsController::class,'sendMessage']);

// Product sharing routes
Route::get('/product/{id}', [\App\Http\Controllers\ShareController::class, 'redirectToProduct'])->name('product.share');

// Invite token routes
Route::get('/invite/{token}', [InviteLandingController::class, 'show'])->name('invite.share');

// Digital Asset Links for Android App Links verification
// Must be accessible at: https://nearx.co/.well-known/assetlinks.json
// This route ensures proper Content-Type header (application/json)
Route::get('/.well-known/assetlinks.json', [\App\Http\Controllers\AssetLinksController::class, 'index'])->middleware('web');
