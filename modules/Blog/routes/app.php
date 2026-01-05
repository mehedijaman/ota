<?php

use Illuminate\Support\Facades\Route;
use Modules\Blog\Http\Controllers\AuthorController;
use Modules\Blog\Http\Controllers\CategoryController;
use Modules\Blog\Http\Controllers\PostController;
use Modules\Blog\Http\Controllers\TagController;

// Posts
Route::post('blog-post/upload-editor-image', [
    PostController::class, 'uploadEditorImage',
])
    ->name('blogPost.uploadEditorImage')
    ->can('blog-post-create')
    ->can('blog-post-edit');

Route::get('blog-post', [
    PostController::class, 'index',
])
    ->name('blogPost.index')
    ->can('blog-post-list');

Route::get('blog-post/create', [
    PostController::class, 'create',
])
    ->name('blogPost.create')
    ->can('blog-post-create');

Route::post('blog-post', [
    PostController::class, 'store',
])
    ->name('blogPost.store')
    ->can('blog-post-create');

Route::get('blog-post/{id}/edit', [
    PostController::class, 'edit',
])
    ->name('blogPost.edit')
    ->can('blog-post-edit');

Route::put('blog-post/{id}', [
    PostController::class, 'update',
])
    ->name('blogPost.update')
    ->can('blog-post-edit');

Route::delete('blog-post/{id}', [
    PostController::class, 'destroy',
])
    ->name('blogPost.destroy')
    ->can('blog-post-delete');

// Categories
Route::post('blog-category/upload-editor-image', [
    CategoryController::class, 'uploadEditorImage',
])
    ->name('blogCategory.uploadEditorImage')
    ->can('blog-category-create')
    ->can('blog-category-edit');

Route::get('blog-category', [
    CategoryController::class, 'index',
])
    ->name('blogCategory.index')
    ->can('blog-category-list');

Route::get('blog-category/create', [
    CategoryController::class, 'create',
])
    ->name('blogCategory.create')
    ->can('blog-category-create');

Route::post('blog-category', [
    CategoryController::class, 'store',
])
    ->name('blogCategory.store')
    ->can('blog-category-create');

Route::get('blog-category/{id}/edit', [
    CategoryController::class, 'edit',
])
    ->name('blogCategory.edit')
    ->can('blog-category-edit');

Route::put('blog-category/{id}', [
    CategoryController::class, 'update',
])
    ->name('blogCategory.update')
    ->can('blog-category-edit');

Route::delete('blog-category/{id}', [
    CategoryController::class, 'destroy',
])
    ->name('blogCategory.destroy')
    ->can('blog-category-delete');

// Tags
Route::get('blog-tag', [
    TagController::class, 'index',
])
    ->name('blogTag.index')
    ->can('blog-tag-list');

Route::get('blog-tag/create', [
    TagController::class, 'create',
])
    ->name('blogTag.create')
    ->can('blog-tag-create');

Route::post('blog-tag', [
    TagController::class, 'store',
])
    ->name('blogTag.store')
    ->can('blog-tag-create');

Route::get('blog-tag/{id}/edit', [
    TagController::class, 'edit',
])
    ->name('blogTag.edit')
    ->can('blog-tag-edit');

Route::put('blog-tag/{id}', [
    TagController::class, 'update',
])
    ->name('blogTag.update')
    ->can('blog-tag-edit');

Route::delete('blog-tag/{id}', [
    TagController::class, 'destroy',
])
    ->name('blogTag.destroy')
    ->can('blog-tag-delete');

// Authors
Route::get('blog-author', [
    AuthorController::class, 'index',
])
    ->name('blogAuthor.index')
    ->can('blog-author-list');

Route::get('blog-author/create', [
    AuthorController::class, 'create',
])
    ->name('blogAuthor.create')
    ->can('blog-author-create');

Route::post('blog-author', [
    AuthorController::class, 'store',
])
    ->name('blogAuthor.store')
    ->can('blog-author-create');

Route::get('blog-author/{id}/edit', [
    AuthorController::class, 'edit',
])
    ->name('blogAuthor.edit')
    ->can('blog-author-edit');

Route::put('blog-author/{id}', [
    AuthorController::class, 'update',
])
    ->name('blogAuthor.update')
    ->can('blog-author-edit');

Route::delete('blog-author/{id}', [
    AuthorController::class, 'destroy',
])
    ->name('blogAuthor.destroy')
    ->can('blog-author-delete');
