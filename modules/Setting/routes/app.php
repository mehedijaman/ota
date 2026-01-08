<?php

use Illuminate\Support\Facades\Route;
use Modules\Setting\Http\Controllers\SettingController;

Route::get('setting', [
    SettingController::class,
    'index',
])->name('setting.index');

Route::put('setting', [
    SettingController::class,
    'update',
])->name('setting.update');


Route::get('maintenance', [
    SettingController::class,
    'maintenance',
])->name('appMaintendance.index')->can('setting-maintenance');
