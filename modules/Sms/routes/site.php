<?php

use Illuminate\Support\Facades\Route;
use Modules\Sms\Http\Controllers\RegistrationOtpController;

Route::post('register/otp/send', [RegistrationOtpController::class, 'send'])
    ->middleware(['throttle:10,1'])
    ->name('sms.register.otp.send');

Route::post('register/otp/verify', [RegistrationOtpController::class, 'verify'])
    ->middleware(['throttle:10,1'])
    ->name('sms.register.otp.verify');
