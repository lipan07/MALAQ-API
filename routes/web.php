<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\PrivacyPolicyController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SmsController;
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

Route::get('/', function () {
    return view('welcome');
});
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::get('/privacy-policy', [PrivacyPolicyController::class, 'index'])->name('privacy-policy');
// Route::get('/send-sms',[SmsController::class,'sendMessage']);
