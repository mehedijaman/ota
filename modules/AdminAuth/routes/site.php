<?php

use Illuminate\Support\Facades\Route;
use Modules\AdminAuth\Http\Controllers\AuthenticatedSessionController;
use Modules\AdminAuth\Http\Controllers\EmailVerificationController;
use Modules\AdminAuth\Http\Controllers\NewPasswordController;
use Modules\AdminAuth\Http\Controllers\PasswordResetLinkController;
use Modules\AdminAuth\Http\Controllers\RegisterController;

// Guest routes - only accessible when not logged in
Route::middleware('guest:user')->group(function () {
    Route::get(config('modular.login-url'), [
        AuthenticatedSessionController::class, 'loginForm',
    ])->name('auth.loginForm');

    Route::post('/login', [
        AuthenticatedSessionController::class, 'login',
    ])->name('auth.login');

    // Customer Registration
    Route::get('/register', [
        RegisterController::class, 'registerForm',
    ])->name('auth.registerForm');

    Route::post('/register', [
        RegisterController::class, 'register',
    ])->name('auth.register');

    // form to receive the email that contains the link to reset password
    Route::get('/forgot-password', [
        PasswordResetLinkController::class, 'forgotPasswordForm',
    ])->name('auth.forgotPassword');

    Route::post('/send-reset-link-email', [
        PasswordResetLinkController::class, 'sendResetLinkEmail',
    ])->name('auth.sendResetLinkEmail');

    // password reset form
    Route::get('/reset-password/{token}', [
        NewPasswordController::class, 'resetPasswordForm',
    ])->name('auth.resetPasswordForm');

    Route::post('/reset-password', [
        NewPasswordController::class, 'store',
    ])->name('auth.resetPassword');
});

// Authenticated routes
Route::get('/logout', [
    AuthenticatedSessionController::class, 'logout',
])->name('auth.logout');

// Email Verification
Route::get('/email/verify', [
    EmailVerificationController::class, 'notice',
])->middleware('auth:user')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', [
    EmailVerificationController::class, 'verify',
])->middleware(['auth:user', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', [
    EmailVerificationController::class, 'send',
])->middleware(['auth:user', 'throttle:6,1'])->name('verification.send');
