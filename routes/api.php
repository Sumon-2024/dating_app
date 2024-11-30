<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\ApiAuthController;


/**
 * @method
 * API Routes for Authentication
 * 
 * @description Defines routes for user login, profile update, logout, and account deletion.
 * 
 * @route POST /login {ApiAuthController@login} - User Login
 * @route POST /user/logout {ApiAuthController@logout} - User Logout
 * @route GET /user {ApiAuthController@user} - Get Logged-in User Info
 * @route PUT /user/update {ApiAuthController@updateProfile} - Update User Profile
 * @route DELETE /user/delete {ApiAuthController@deleteAccount} - Delete User Account
 */

Route::post('/login', [ApiAuthController::class, 'login'])->name('api.login');

Route::middleware(['auth:sanctum'])->prefix('user')->group(function () {
    Route::post('/logout', [ApiAuthController::class, 'logout'])->name('api.logout');
    Route::get('/', [ApiAuthController::class, 'user'])->name('api.user');
    Route::put('/update', [ApiAuthController::class, 'updateProfile'])->name('api.updateProfile');
    Route::post('/edit', [ApiAuthController::class, 'editProfile'])->name('api.editProfile');
    Route::delete('/delete', [ApiAuthController::class, 'deleteAccount'])->name('api.deleteAccount');
});
