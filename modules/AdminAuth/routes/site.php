<?php

use Illuminate\Support\Facades\Route;
use Modules\AdminAuth\Http\Controllers\AuthenticatedSessionController;
use Modules\AdminAuth\Http\Controllers\NewPasswordController;
use Modules\AdminAuth\Http\Controllers\PasswordResetLinkController;
use Modules\AdminAuth\Http\Controllers\RegisterController;

Route::get(config('modular.login-url'), [
    AuthenticatedSessionController::class, 'loginForm',
])->name('adminAuth.loginForm');

Route::post('/admin-auth/login', [
    AuthenticatedSessionController::class, 'login',
])->name('adminAuth.login');

// Customer Registration
Route::get('/register', [
    RegisterController::class, 'registerForm',
])->name('adminAuth.registerForm');

Route::post('/register', [
    RegisterController::class, 'register',
])->name('adminAuth.register');

Route::get('/admin-auth/logout', [
    AuthenticatedSessionController::class, 'logout',
])->name('adminAuth.logout');

// form to receive the email that contains the link to reset password
Route::get('/admin-auth/forgot-password', [
    PasswordResetLinkController::class, 'forgotPasswordForm',
])->name('adminAuth.forgotPassword');

Route::post('/admin-auth/send-reset-link-email', [
    PasswordResetLinkController::class, 'sendResetLinkEmail',
])->name('adminAuth.sendResetLinkEmail');

// password reset form
Route::get('/admin-auth/reset-password/{token}', [
    NewPasswordController::class, 'resetPasswordForm',
])->name('adminAuth.resetPasswordForm');

Route::post('/admin-auth/reset-password', [
    NewPasswordController::class, 'store',
])->name('adminAuth.resetPassword');
