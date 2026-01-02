<?php

use Illuminate\Support\Facades\Route;
use Modules\Index\Http\Controllers\IndexController;

Route::get('/', [IndexController::class,'index'])->name('site.index');
Route::get('/about', [IndexController::class,'about'])->name('site.about');
Route::get('/why-us', [IndexController::class,'whyUs'])->name('site.why-us');
Route::get('/terms-and-conditions', [IndexController::class,'termsAndConditions'])->name('site.terms-and-conditions');
Route::get('/privacy-policy', [IndexController::class,'privacyPolicy'])->name('site.privacy-policy');
