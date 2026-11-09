<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\AdminPasswordController;
use App\Http\Controllers\User\LoginController;
use App\Http\Controllers\User\PasswordResetController;
use App\Http\Controllers\User\RegisterController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'create'])
        ->name('login');
    Route::post('/login', [AdminLoginController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('login.process');

    Route::get('/lupa-password', [AdminPasswordController::class, 'create'])
        ->name('password.request');
    Route::post('/lupa-password', [AdminPasswordController::class, 'sendResetLink'])
        ->middleware('throttle:5,1')
        ->name('password.email');
    Route::get('/reset-password/{token}', [AdminPasswordController::class, 'edit'])
        ->name('password.reset');
    Route::post('/reset-password', [AdminPasswordController::class, 'update'])
        ->middleware('throttle:5,1')
        ->name('password.update');

    Route::middleware('admin')->group(function () {
        Route::get('/dashboard', AdminDashboardController::class)
            ->name('dashboard');
        Route::post('/logout', [AdminLoginController::class, 'destroy'])
            ->name('logout');
    });
});

Route::get('/login', [LoginController::class, 'create'])
    ->name('login');
Route::post('/login', [LoginController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('login.store');
Route::get('/register', [RegisterController::class, 'create'])
    ->name('register');
Route::post('/register', [RegisterController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('register.store');

Route::get('/lupa-password', [PasswordResetController::class, 'create'])
    ->name('password.request');
Route::post('/lupa-password', [PasswordResetController::class, 'emailResetLink'])
    ->middleware('throttle:5,1')
    ->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'edit'])
    ->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'update'])
    ->middleware('throttle:5,1')
    ->name('password.update');
