<?php

use Illuminate\Support\Facades\Route;
use Modules\Visa\Http\Controllers\SiteVisaController;

Route::get('visa', [SiteVisaController::class,'index'])->name('site.visa.index');
