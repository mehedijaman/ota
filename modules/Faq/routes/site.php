<?php

use Illuminate\Support\Facades\Route;
use Modules\Faq\Http\Controllers\SiteFaqController;

Route::get('faq', [SiteFaqController::class, 'index'])->name('site.faq.index');
