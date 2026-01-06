<?php

use Illuminate\Support\Facades\Route;
use Modules\Faq\Http\Controllers\FaqCategoryController;
use Modules\Faq\Http\Controllers\FaqController;

// FAQ Category Routes
Route::get('faq-categories', [FaqCategoryController::class, 'index'])
    ->name('faqCategory.index')
    ->can('faq-category-list');

Route::get('faq-categories/create', [FaqCategoryController::class, 'create'])
    ->name('faqCategory.create')
    ->can('faq-category-create');

Route::post('faq-categories', [FaqCategoryController::class, 'store'])
    ->name('faqCategory.store')
    ->can('faq-category-create');

Route::get('faq-categories/{faqCategory:uuid}/edit', [FaqCategoryController::class, 'edit'])
    ->name('faqCategory.edit')
    ->can('faq-category-edit');

Route::put('faq-categories/{faqCategory:uuid}', [FaqCategoryController::class, 'update'])
    ->name('faqCategory.update')
    ->can('faq-category-edit');

Route::delete('faq-categories/{faqCategory:uuid}', [FaqCategoryController::class, 'destroy'])
    ->name('faqCategory.destroy')
    ->can('faq-category-delete');

// FAQ Category Recycle Bin Routes
Route::get('faq-categories/recycle-bin', [FaqCategoryController::class, 'recycleBin'])
    ->name('faqCategory.recycleBin')
    ->can('faq-category-list');

Route::put('faq-categories/{uuid}/restore', [FaqCategoryController::class, 'restore'])
    ->name('faqCategory.restore')
    ->can('faq-category-delete');

Route::delete('faq-categories/{uuid}/force-delete', [FaqCategoryController::class, 'destroyForce'])
    ->name('faqCategory.destroyForce')
    ->can('faq-category-delete');

Route::delete('faq-categories/recycle-bin/empty', [FaqCategoryController::class, 'emptyRecycleBin'])
    ->name('faqCategory.emptyRecycleBin')
    ->can('faq-category-delete');

Route::put('faq-categories/recycle-bin/restore-all', [FaqCategoryController::class, 'restoreRecycleBin'])
    ->name('faqCategory.restoreRecycleBin')
    ->can('faq-category-delete');

// FAQ Routes
Route::get('faqs', [FaqController::class, 'index'])
    ->name('faq.index')
    ->can('faq-list');

Route::get('faqs/create', [FaqController::class, 'create'])
    ->name('faq.create')
    ->can('faq-create');

// FAQ Recycle Bin Routes (must be defined before {faq:uuid} routes)
Route::get('faqs/recycle-bin', [FaqController::class, 'recycleBin'])
    ->name('faq.recycleBin')
    ->can('faq-list');

Route::delete('faqs/recycle-bin/empty', [FaqController::class, 'emptyRecycleBin'])
    ->name('faq.emptyRecycleBin')
    ->can('faq-delete');

Route::put('faqs/recycle-bin/restore-all', [FaqController::class, 'restoreRecycleBin'])
    ->name('faq.restoreRecycleBin')
    ->can('faq-delete');

Route::post('faqs', [FaqController::class, 'store'])
    ->name('faq.store')
    ->can('faq-create');

Route::get('faqs/{faq:uuid}', [FaqController::class, 'show'])
    ->name('faq.show')
    ->can('faq-view');

Route::get('faqs/{faq:uuid}/edit', [FaqController::class, 'edit'])
    ->name('faq.edit')
    ->can('faq-edit');

Route::put('faqs/{faq:uuid}', [FaqController::class, 'update'])
    ->name('faq.update')
    ->can('faq-edit');

Route::delete('faqs/{faq:uuid}', [FaqController::class, 'destroy'])
    ->name('faq.destroy')
    ->can('faq-delete');

Route::put('faqs/{uuid}/restore', [FaqController::class, 'restore'])
    ->name('faq.restore')
    ->can('faq-delete');

Route::delete('faqs/{uuid}/force-delete', [FaqController::class, 'destroyForce'])
    ->name('faq.destroyForce')
    ->can('faq-delete');
