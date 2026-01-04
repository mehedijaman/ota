<?php

use Illuminate\Support\Facades\Route;
use Modules\Tour\Http\Controllers\SiteTourController;

Route::get('tour', [SiteTourController::class, 'index'])->name('site.tour.index');
Route::get('tour/search', [SiteTourController::class, 'search'])->name('site.tour.search');
Route::get('tour/{id}', [SiteTourController::class, 'show'])->name('site.tour.show');
