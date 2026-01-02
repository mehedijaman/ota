<?php

use Illuminate\Support\Facades\Route;
use Modules\Tour\Http\Controllers\SiteTourController;

Route::get('tour', [SiteTourController::class,'index'])->name('site.tour.index');
