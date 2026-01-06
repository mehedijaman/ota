<?php

namespace Modules\Acl\Http\Controllers;

use Modules\Support\Http\Controllers\BackendController;
use Modules\User\Models\User;
use Spatie\Permission\Models\Role;

class UserRoleController extends BackendController
{
    public function edit(string $uuid)
    {
        $user = User::with(['roles' => function ($q) {
            $q->get(['id', 'name']);
        }])->where('uuid', $uuid)->firstOrFail();

        $user->roles->map(function ($role) {
            unset($role->pivot);

            return $role;
        });

        $roles = Role::orderBy('name')->get(['id', 'name']);

        return inertia('AclUserRole/UserRoleForm', [
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    public function update(string $uuid)
    {
        $user = User::where('uuid', $uuid)->firstOrFail();

        $user->syncRoles(request('userRoles'));

        return redirect()->route('user.index')
            ->with('success', 'User roles updated');
    }
}
