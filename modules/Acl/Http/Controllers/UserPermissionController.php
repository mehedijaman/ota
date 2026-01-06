<?php

namespace Modules\Acl\Http\Controllers;

use Modules\Acl\Services\GetUserPermissions;
use Modules\Support\Http\Controllers\BackendController;
use Modules\User\Models\User;
use Spatie\Permission\Models\Permission;

class UserPermissionController extends BackendController
{
    public function edit(string $uuid)
    {
        $user = User::where('uuid', $uuid)->firstOrFail();
        $userPermissions = (new GetUserPermissions)->run($user->id);

        $permissions = Permission::orderBy('name')->get(['id', 'name']);

        return inertia('AclUserPermission/UserPermissionForm', [
            'user' => $user,
            'userPermissions' => $userPermissions,
            'permissions' => $permissions,
        ]);
    }

    public function update(string $uuid)
    {
        $user = User::where('uuid', $uuid)->firstOrFail();

        $user->syncPermissions(request('userPermissions'));

        return redirect()->route('user.index')
            ->with('success', 'User permissions updated');
    }
}
