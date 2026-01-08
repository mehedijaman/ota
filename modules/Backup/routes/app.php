<?php

use Illuminate\Support\Facades\Route;
use Modules\Backup\Http\Controllers\BackupController;

Route::controller(BackupController::class)->prefix('backup')->name('backup.')->group(function () {
    Route::get('/', 'index')->name('index')->can('backup-list');
    Route::get('create/full', 'createFull')->name('create.full')->can('backup-create');
    Route::get('create/db', 'createDB')->name('create.database')->can('backup-create');
    Route::get('create/file', 'createFile')->name('create.file')->can('backup-create');
    Route::get('download/{fileName}', 'download')->name('download')->can('backup-download');
    Route::get('clean', 'clean')->name('clean')->can('backup-delete');
    Route::delete('{fileName}', 'destroy')->name('destroy')->can('backup-delete');
});
