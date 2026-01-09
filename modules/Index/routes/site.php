<?php

use Illuminate\Support\Facades\Route;
use Modules\Index\Http\Controllers\IndexController;

Route::get('/{slug?}', [IndexController::class, 'index'])->name('site.index');
