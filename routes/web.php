<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\PrivacyPolicyController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\UserController;

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
    Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    // Route::get('/posts/{id}/approve', [PostController::class, 'approve'])->name('posts.approve');

    Route::post('/posts/{post}/status', [PostController::class, 'changeStatus'])->name('posts.changeStatus');

    // Route::get('/users', [UserController::class, 'index'])->name('users.index');

    Route::resource('users', UserController::class);
});


Route::get('/privacy-policy', [PrivacyPolicyController::class, 'index'])->name('privacy-policy');
// Route::get('/send-sms',[SmsController::class,'sendMessage']);
