<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\ApiAuthController;
use App\Http\Controllers\Api\Auth\ForgotPasswordController;


// Public routes
Route::post('/login', [ApiAuthController::class, 'login'])->name('api.login');
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset', [ForgotPasswordController::class, 'resetForm'])->name('password.reset');
Route::post('/password/reset/post', [ForgotPasswordController::class, 'reset'])->name('password.reset.post');


Route::middleware(['auth:sanctum'])->prefix('user')->group(function () {
    Route::post('/logout', [ApiAuthController::class, 'logout'])->name('api.logout');
    Route::get('/', [ApiAuthController::class, 'user'])->name('api.user');
    Route::post('/profile/edit', [ApiAuthController::class, 'editProfile'])->name('api.editProfile');
    Route::put('/profile/update', [ApiAuthController::class, 'updateProfile'])->name('api.updateProfile');
    Route::delete('/profile/delete', [ApiAuthController::class, 'deleteAccount'])->name('api.deleteAccount');


    // Change password 
    Route::post('/change-password', [ApiAuthController::class, 'changePassword']);
});


