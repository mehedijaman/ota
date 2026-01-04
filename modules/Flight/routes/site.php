<?php

use Illuminate\Support\Facades\Route;
use Modules\Flight\Http\Controllers\SiteFlightController;

Route::get('flight', [SiteFlightController::class, 'index'])->name('site.flight.index');
Route::get('flight/search', [SiteFlightController::class, 'search'])->name('site.flight.search');
