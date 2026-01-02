<?php

use Illuminate\Support\Facades\Route;
use Modules\Bill\Http\Controllers\SiteBillController;

Route::get('bill', [SiteBillController::class,'index'])->name('site.bill.index');
