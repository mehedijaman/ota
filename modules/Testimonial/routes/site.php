<?php

use Illuminate\Support\Facades\Route;
use Modules\Testimonial\Http\Controllers\SiteTestimonialController;

Route::prefix('testimonials')->name('site.testimonial.')->group(function () {
    Route::get('/', [SiteTestimonialController::class, 'index'])->name('index');
});
