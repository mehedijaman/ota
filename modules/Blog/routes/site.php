<?php

use Illuminate\Support\Facades\Route;
use Modules\Blog\Http\Controllers\SiteArchiveController;
use Modules\Blog\Http\Controllers\SitePostController;
use Modules\Blog\Http\Controllers\SitePostSearchController;
use Modules\Blog\Http\Controllers\SiteTagController;

Route::get('/blog', [SitePostController::class, 'index'])->name('site.blog.index');
Route::get('/blog/{slug}', [SitePostController::class, 'show'])->name('site.blog.show');

Route::get('/blog/archive/{archiveDate}', [SiteArchiveController::class, 'index'])->name('site.blog.archive');
Route::get('/blog/tag/{tagSlug}', [SiteTagController::class, 'index'])->name('site.blog.tag');
Route::get('/blog/search/{searchTerm}', [SitePostSearchController::class, 'index'])->name('site.blog.search');
