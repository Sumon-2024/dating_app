<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Frontend\HomeController;
use Spatie\Permission\Middlewares\RoleMiddleware;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Spatie\Permission\Middlewares\PermissionMiddleware;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;



Route::middleware('guest')->group(function () {
    // Login routes 
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    // Register routes 
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register/store', [RegisteredUserController::class, 'store']);
    //Forgot password routes
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    //Reset password routes
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});


Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])->middleware('throttle:6,1')->name('verification.send');
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});


// Protect routes that require authentication
Route::middleware(['auth', 'verified'])->group(function () {
    // HomeController
    Route::get('/home', [HomeController::class, 'home'])->name('home');

    // DashboardController - restrict to specific roles
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')
        ->middleware('role:super-admin|admin|moderator');
});


// Fallback route for unauthorized access
Route::fallback(function () {
    return response()->view('errors.403', [], 403);
});
