<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\UserController;

// Recycle Bin routes (must come before resource routes)
Route::get('user/recycle-bin', [
    UserController::class, 'recycleBin',
])->name('user.recycleBin');

Route::post('user/recycle-bin/restore-all', [
    UserController::class, 'restoreRecycleBin',
])->name('user.restoreRecycleBin');

Route::delete('user/recycle-bin/empty', [
    UserController::class, 'emptyRecycleBin',
])->name('user.emptyRecycleBin');

Route::post('user/{uuid}/restore', [
    UserController::class, 'restore',
])->name('user.restore');

Route::delete('user/{uuid}/force-delete', [
    UserController::class, 'destroyForce',
])->name('user.destroyForce');

// Impersonation routes
Route::get('user/{uuid}/impersonate', [
    UserController::class, 'impersonate',
])->name('impersonate.start');

Route::get('user/leave-impersonation', [
    UserController::class, 'leaveImpersonation',
])->name('impersonate.leave');

// Standard CRUD routes with UUID
Route::get('user', [
    UserController::class, 'index',
])->name('user.index');

Route::get('user/create', [
    UserController::class, 'create',
])->name('user.create');

Route::get('user/{uuid}/edit', [
    UserController::class, 'edit',
])->name('user.edit');

Route::post('user', [
    UserController::class, 'store',
])->name('user.store');

Route::put('user/{uuid}', [
    UserController::class, 'update',
])->name('user.update');

Route::delete('user/{uuid}', [
    UserController::class, 'destroy',
])->name('user.destroy');
