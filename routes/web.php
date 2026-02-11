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


Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // No permission required
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/permission-denied', [\App\Http\Controllers\Admin\DashboardController::class, 'permissionDenied'])->name('permission-denied');

    // Posts (permission: posts)
    Route::middleware('permission:posts')->group(function () {
        Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
        Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
        Route::post('/posts/{post}/status', [PostController::class, 'changeStatus'])->name('posts.changeStatus');
        Route::post('/posts/{post}/report', [PostController::class, 'report'])->name('posts.report');
    });

    // App Users (permission: users)
    Route::middleware('permission:users')->group(function () {
        Route::resource('users', UserController::class);
        Route::post('/users/{user}/block', [UserController::class, 'block'])->name('users.block');
        Route::post('/users/{user}/unblock', [UserController::class, 'unblock'])->name('users.unblock');
        Route::get('/users/{user}/referral-tree', [UserController::class, 'referralTree'])->name('users.referral-tree');
    });

    // Categories (permission: categories)
    Route::middleware('permission:categories')->group(function () {
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
    });

    // Invite Tokens (permission: users - same as users section)
    Route::middleware('permission:users')->group(function () {
        Route::post('/invite-tokens/{tokenId}/regenerate', [InviteTokenController::class, 'regenerateToken'])->name('invite-tokens.regenerate');
    });

    // Payments (permission: payments)
    Route::middleware('permission:payments')->group(function () {
        Route::get('/payments', [\App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/{payment}', [\App\Http\Controllers\Admin\PaymentController::class, 'show'])->name('payments.show');
        Route::post('/payments/{payment}/confirm', [\App\Http\Controllers\Admin\PaymentController::class, 'confirm'])->name('payments.confirm');
        Route::post('/payments/{payment}/reject', [\App\Http\Controllers\Admin\PaymentController::class, 'reject'])->name('payments.reject');
    });

    // All Invite Tokens (permission: all_invite_tokens)
    Route::middleware('permission:all_invite_tokens')->group(function () {
        Route::get('/invite-tokens', [\App\Http\Controllers\Admin\InviteTokenController::class, 'index'])->name('invite-tokens.index');
    });

    // Admin Users (permission: admin_users)
    Route::middleware('permission:admin_users')->group(function () {
        Route::resource('admin-users', \App\Http\Controllers\Admin\AdminUserController::class)->parameters(['admin-users' => 'admin_user']);
    });

    // Roles - predefined list (permission: roles_permissions)
    Route::middleware('permission:roles_permissions')->group(function () {
        Route::get('/roles', [\App\Http\Controllers\Admin\RoleController::class, 'index'])->name('roles.index');
    });

    // Reports (permission: reports)
    Route::middleware('permission:reports')->group(function () {
        Route::get('/reports', [\App\Http\Controllers\Admin\ReportsController::class, 'index'])->name('reports.index');
        Route::get('/reports/{report}', [\App\Http\Controllers\Admin\ReportsController::class, 'show'])->name('reports.show');
    });

    // Analytics (permission: analytics)
    Route::middleware('permission:analytics')->group(function () {
        Route::get('/analytics', [\App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics.index');
    });

    // Support Tickets (permission: support_tickets)
    Route::middleware('permission:support_tickets')->group(function () {
        Route::get('/support-tickets', [\App\Http\Controllers\Admin\SupportTicketsController::class, 'index'])->name('support-tickets.index');
        Route::get('/support-tickets/{support_request}', [\App\Http\Controllers\Admin\SupportTicketsController::class, 'show'])->name('support-tickets.show');
    });

    // Impersonate (super_admin, lead, admin only – search in navbar dropdown; no separate page)
    Route::get('/impersonate/search', [\App\Http\Controllers\Admin\ImpersonateController::class, 'search'])->name('impersonate.search');
    Route::post('/impersonate', [\App\Http\Controllers\Admin\ImpersonateController::class, 'store'])->name('impersonate.store');
    Route::post('/impersonate/stop', [\App\Http\Controllers\Admin\ImpersonateController::class, 'stop'])->name('impersonate.stop');
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
