<?php

use Illuminate\Support\Facades\Route;
use Modules\Medical\Http\Controllers\SiteMedicalController;

Route::get('medical', [SiteMedicalController::class, 'index'])->name('site.medical.index');
