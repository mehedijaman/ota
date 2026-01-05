<?php

use Illuminate\Support\Facades\Route;
use Modules\Employee\Http\Controllers\AreaController;
use Modules\Employee\Http\Controllers\DepartmentController;
use Modules\Employee\Http\Controllers\DesignationController;
use Modules\Employee\Http\Controllers\EmployeeController;
use Modules\Employee\Http\Controllers\EmployeeReportController;

Route::middleware(['auth', 'verified'])->group(function () {
    // Employee Routes
    Route::prefix('employees')->controller(EmployeeController::class)->name('employee.')->group(function () {

        // Report
        Route::prefix('report')->name('report.')->controller(EmployeeReportController::class)->group(function () {
            Route::get('/', 'index')->name('index')->can('employee-list');
            Route::post('/generate', 'generate')->name('generate')->can('employee-list');
            Route::post('/export', 'export')->name('export')->can('employee-list');
        });

        // Import & Export
        Route::get('export', 'export')->name('export')->can('employee-list');
        Route::get('import/download-template', 'downloadTemplate')->name('downloadTemplate')->can('employee-list');
        Route::get('import', 'import')->name('import')->can('employee-create');
        Route::post('import', 'postImport')->name('postImport')->can('employee-create');

        // Recycle Bin
        Route::get('/recycle-bin', 'recycleBin')->name('recycleBin')->can('employee-delete');
        Route::post('/recycle-bin/{uuid}/restore', 'restore')->name('restore')->can('employee-delete');
        Route::delete('/recycle-bin/{uuid}/force', 'destroyForce')->name('destroyForce')->can('employee-delete');
        Route::delete('/recycle-bin/empty', 'emptyRecycleBin')->name('emptyRecycleBin')->can('employee-delete');
        Route::post('/recycle-bin/restore-all', 'restoreRecycleBin')->name('restoreRecycleBin')->can('employee-delete');

        // Profile
        Route::get('profile/{employeeCode?}', 'profile')->name('profile')->can('employee-view');

        // Standard CRUD
        Route::get('/', 'index')->name('index')->can('employee-list');
        Route::get('create', 'create')->name('create')->can('employee-create');
        Route::get('{employee:uuid}', 'show')->name('show')->can('employee-view');
        Route::post('/', 'store')->name('store')->can('employee-create');
        Route::get('{employee:uuid}/edit', 'edit')->name('edit')->can('employee-edit');
        Route::put('{employee:uuid}', 'update')->name('update')->can('employee-edit');
        Route::delete('{employee:uuid}', 'destroy')->name('destroy')->can('employee-delete');
    });

    // Designation Routes
    Route::prefix('employee-designations')->controller(DesignationController::class)->name('designation.')->group(function () {

        // Import & Export
        Route::get('export', 'export')->name('export')->can('employee-designation-list');
        Route::get('import/download-template', 'downloadTemplate')->name('downloadTemplate')->can('employee-designation-list');
        Route::get('import', 'import')->name('import')->can('employee-designation-create');
        Route::post('import', 'postImport')->name('postImport')->can('employee-designation-create');

        // Recycle Bin
        Route::get('/recycle-bin', 'recycleBin')->name('recycleBin')->can('employee-designation-delete');
        Route::post('/recycle-bin/{uuid}/restore', 'restore')->name('restore')->can('employee-designation-delete');
        Route::delete('/recycle-bin/{uuid}/force', 'forceDestroy')->name('forceDestroy')->can('employee-designation-delete');
        Route::delete('/recycle-bin/empty', 'emptyRecycleBin')->name('emptyRecycleBin')->can('employee-designation-delete');
        Route::post('/recycle-bin/restore-all', 'restoreAll')->name('restoreAll')->can('employee-designation-delete');

        // Standard CRUD
        Route::get('/', 'index')->name('index')->can('employee-designation-list');
        Route::get('create', 'create')->name('create')->can('employee-designation-create');
        Route::get('{designation:uuid}', 'show')->name('show')->can('employee-designation-view');
        Route::post('/', 'store')->name('store')->can('employee-designation-create');
        Route::get('{designation:uuid}/edit', 'edit')->name('edit')->can('employee-designation-edit');
        Route::put('{designation:uuid}', 'update')->name('update')->can('employee-designation-edit');
        Route::delete('{designation:uuid}', 'destroy')->name('destroy')->can('employee-designation-delete');
    });

    // Designation Routes
    Route::prefix('employee-departments')->controller(DepartmentController::class)->name('department.')->group(function () {

        // Import & Export
        Route::get('export', 'export')->name('export')->can('employee-department-list');
        Route::get('import/download-template', 'downloadTemplate')->name('downloadTemplate')->can('employee-department-list');
        Route::get('import', 'import')->name('import')->can('employee-department-create');
        Route::post('import', 'postImport')->name('postImport')->can('employee-department-create');

        // Recycle Bin
        Route::get('/recycle-bin', 'recycleBin')->name('recycleBin')->can('employee-department-delete');
        Route::post('/recycle-bin/{uuid}/restore', 'restore')->name('restore')->can('employee-department-delete');
        Route::delete('/recycle-bin/{uuid}/force', 'forceDestroy')->name('forceDestroy')->can('employee-department-delete');
        Route::delete('/recycle-bin/empty', 'emptyRecycleBin')->name('emptyRecycleBin')->can('employee-department-delete');
        Route::post('/recycle-bin/restore-all', 'restoreAll')->name('restoreAll')->can('employee-department-delete');

        // Standard CRUD
        Route::get('/', 'index')->name('index')->can('employee-department-list');
        Route::get('create', 'create')->name('create')->can('employee-department-create');
        Route::get('{department:uuid}', 'show')->name('show')->can('employee-department-view');
        Route::post('/', 'store')->name('store')->can('employee-department-create');
        Route::get('{department:uuid}/edit', 'edit')->name('edit')->can('employee-department-edit');
        Route::put('{department:uuid}', 'update')->name('update')->can('employee-department-edit');
        Route::delete('{department:uuid}', 'destroy')->name('destroy')->can('employee-department-delete');
    });

    // Designation Routes
    Route::prefix('employee-areas')->controller(AreaController::class)->name('area.')->group(function () {

        // Import & Export
        Route::get('export', 'export')->name('export')->can('employee-area-list');
        Route::get('import/download-template', 'downloadTemplate')->name('downloadTemplate')->can('employee-area-list');
        Route::get('import', 'import')->name('import')->can('employee-area-create');
        Route::post('import', 'postImport')->name('postImport')->can('employee-area-create');

        // Recycle Bin
        Route::get('/recycle-bin', 'recycleBin')->name('recycleBin')->can('employee-area-delete');
        Route::post('/recycle-bin/{uuid}/restore', 'restore')->name('restore')->can('employee-area-delete');
        Route::delete('/recycle-bin/{uuid}/force', 'forceDestroy')->name('forceDestroy')->can('employee-area-delete');
        Route::delete('/recycle-bin/empty', 'emptyRecycleBin')->name('emptyRecycleBin')->can('employee-area-delete');
        Route::post('/recycle-bin/restore-all', 'restoreAll')->name('restoreAll')->can('employee-area-delete');

        // Standard CRUD
        Route::get('/', 'index')->name('index')->can('employee-area-list');
        Route::get('create', 'create')->name('create')->can('employee-area-create');
        Route::get('{area:uuid}', 'show')->name('show')->can('employee-area-view');
        Route::post('/', 'store')->name('store')->can('employee-area-create');
        Route::get('{area:uuid}/edit', 'edit')->name('edit')->can('employee-area-edit');
        Route::put('{area:uuid}', 'update')->name('update')->can('employee-area-edit');
        Route::delete('{area:uuid}', 'destroy')->name('destroy')->can('employee-area-delete');
    });
});
