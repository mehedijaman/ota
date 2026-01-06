<?php

use Illuminate\Support\Facades\Route;
use Modules\Acl\Http\Controllers\PermissionController;
use Modules\Acl\Http\Controllers\RoleController;
use Modules\Acl\Http\Controllers\RolePermissionController;
use Modules\Acl\Http\Controllers\UserController;
use Modules\Acl\Http\Controllers\UserPermissionController;
use Modules\Acl\Http\Controllers\UserRoleController;

// acl
Route::get('acl/get-user-roles-and-permissions', [
    UserController::class, 'getUserRolesAndPermissions',
]);

// acl - permission
Route::get('acl-permission', [
    PermissionController::class, 'index',
])->name('aclPermission.index')
    ->can('acl-permission-list');

Route::get('acl-permission/create', [
    PermissionController::class, 'create',
])->name('aclPermission.create')
    ->can('acl-permission-create');

Route::post('acl-permission', [
    PermissionController::class, 'store',
])->name('aclPermission.store')
    ->can('acl-permission-create');

Route::get('acl-permission/{id}/edit', [
    PermissionController::class, 'edit',
])->name('aclPermission.edit')
    ->can('acl-permission-edit');

Route::put('acl-permission/{id}', [
    PermissionController::class, 'update',
])->name('aclPermission.update')
    ->can('acl-permission-edit');

Route::delete('acl-permission/{id}', [
    PermissionController::class, 'destroy',
])->name('aclPermission.destroy')
    ->can('acl-permission-delete');

// acl - role
Route::get('acl-role', [
    RoleController::class, 'index',
])->name('aclRole.index')
    ->can('acl-role-list');

Route::get('acl-role/create', [
    RoleController::class, 'create',
])->name('aclRole.create')
    ->can('acl-role-create');

Route::post('acl-role', [
    RoleController::class, 'store',
])->name('aclRole.store')
    ->can('acl-role-create');

Route::get('acl-role/{id}/edit', [
    RoleController::class, 'edit',
])->name('aclRole.edit')
    ->can('acl-role-edit');

Route::put('acl-role/{id}', [
    RoleController::class, 'update',
])->name('aclRole.update')
    ->can('acl-role-edit');

Route::delete('acl-role/{id}', [
    RoleController::class, 'destroy',
])->name('aclRole.destroy')
    ->can('acl-role-delete');

// acl - role => permissions
Route::get('acl-role-permission/{id}/edit', [
    RolePermissionController::class, 'edit',
])->name('aclRolePermission.edit')
    ->can('acl-role-permission-edit');

Route::put('acl-role-permission/{id}', [
    RolePermissionController::class, 'update',
])->name('aclRolePermission.update')
    ->can('acl-role-permission-edit');

// acl - user role
Route::get('acl-user-role/{uuid}/edit', [
    UserRoleController::class, 'edit',
])->name('aclUserRole.edit')
    ->can('acl-user-role-edit');

Route::put('acl-user-role/{uuid}', [
    UserRoleController::class, 'update',
])->name('aclUserRole.update')
    ->can('acl-user-role-edit');

// acl - user => permissions
Route::get('acl-user-permission/{uuid}/edit', [
    UserPermissionController::class, 'edit',
])->name('aclUserPermission.edit')
    ->can('acl-user-permission-edit');

Route::put('acl-user-permission/{uuid}', [
    UserPermissionController::class, 'update',
])->name('aclUserPermission.update')
    ->can('acl-user-permission-edit');
