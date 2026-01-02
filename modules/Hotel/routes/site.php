<?php

use Illuminate\Support\Facades\Route;
use Modules\Hotel\Http\Controllers\SiteHotelController;

Route::get('hotel', [SiteHotelController::class,'index'])->name('site.hotel.index');
