<?php

use Illuminate\Support\Facades\Route;
use Modules\Visa\Http\Controllers\SiteVisaController;

// Public visa routes
Route::get('visa', [SiteVisaController::class, 'index'])->name('site.visa.index');
Route::get('visa/search', [SiteVisaController::class, 'search'])->name('site.visa.search');
Route::get('visa/country/{slug}', [SiteVisaController::class, 'country'])->name('site.visa.country');
Route::get('visa/requirements/{country}', [SiteVisaController::class, 'requirements'])->name('site.visa.requirements');
Route::get('visa/{slug}', [SiteVisaController::class, 'show'])->name('site.visa.show');

// Authenticated visa booking routes
Route::middleware('auth')->group(function () {
    Route::get('visa/{slug}/apply', [SiteVisaController::class, 'bookingPage'])->name('site.visa.apply');
    Route::post('visa/{slug}/apply', [SiteVisaController::class, 'submitBooking'])->name('site.visa.submit');
    Route::get('visa/application/{application}/payment', [SiteVisaController::class, 'paymentPage'])->name('site.visa.payment');
    Route::post('visa/application/{application}/payment', [SiteVisaController::class, 'processPayment'])->name('site.visa.processPayment');
    Route::get('visa/{slug}/confirmation/{application}', [SiteVisaController::class, 'confirmation'])->name('site.visa.confirmation');
});

// Payment gateway callbacks (no auth middleware - payment gateways redirect back)
Route::post('visa/payment/{application}/success', [SiteVisaController::class, 'paymentSuccess'])->name('site.visa.payment.success');
Route::post('visa/payment/{application}/fail', [SiteVisaController::class, 'paymentFail'])->name('site.visa.payment.fail');
Route::post('visa/payment/{application}/cancel', [SiteVisaController::class, 'paymentCancel'])->name('site.visa.payment.cancel');
Route::post('visa/payment/{application}/ipn', [SiteVisaController::class, 'paymentIPN'])->name('site.visa.payment.ipn');
Route::get('visa/payment/{application}/bkash/callback', [SiteVisaController::class, 'bkashCallback'])->name('site.visa.payment.bkash.callback');
