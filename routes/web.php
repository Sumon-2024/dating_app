<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Auth\AdminController;
use App\Http\Controllers\Auth\SuperAdminController;
use App\Http\Controllers\Frontend\HomeController;

Route::middleware(['guest'])->group(function () {
    // Home Controller 
    Route::get('/', [HomeController::class, 'home'])->name('home');

    // AuthController 
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::get('/dashboard', [DashboardController::class, 'index']);
