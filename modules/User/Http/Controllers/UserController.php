<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Support\Http\Controllers\BackendController;
use Modules\User\Http\Requests\UserValidate;
use Modules\User\Models\User;
use Spatie\Permission\Models\Role;

class UserController extends BackendController
{
    public function index(): Response
    {
        $users = User::with('roles')
            ->orderBy('name')
            ->when(request('role'), fn ($query) => $query->role(request('role')))
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($user) => [
                'id' => $user->id,
                'uuid' => $user->uuid,
                'name' => $user->name,
                'email' => $user->email,
                'avatar_url' => $user->avatar_url,
                'profile_type' => $user->profile_type ? ucfirst($user->profile_type) : 'User',
                'roles' => $user->roles->pluck('name')->toArray(),
                'created_at' => $user->created_at->format('d/m/Y H:i').'h',
            ]);

        $roles = Role::orderBy('name')
            ->get()
            ->map(fn ($role) => [
                'value' => $role->name,
                'label' => $role->name,
            ])
            ->values()
            ->all();

        return inertia('User/UserIndex', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    public function create(): Response
    {
        // Get employees without an associated user (profile_type = 'employee')
        $employees = \Modules\Employee\Models\Employee::query()
            ->whereDoesntHave('user')
            ->where('is_active', true)
            ->whereNotNull('email')
            ->orderBy('name')
            ->get()
            ->map(fn ($employee) => [
                'value' => $employee->id,
                'label' => $employee->name,
                'email' => $employee->email,
            ])
            ->values()
            ->all();

        // Get roles for dropdown
        $roles = Role::orderBy('name')
            ->get()
            ->map(fn ($role) => [
                'value' => $role->id,
                'label' => $role->name,
            ])
            ->values()
            ->all();

        return inertia('User/UserForm', [
            'employees' => $employees,
            'roles' => $roles,
        ]);
    }

    public function store(UserValidate $request): RedirectResponse
    {
        $validated = $request->validated();
        
        // Extract role_ids before processing
        $roleIds = $validated['role_ids'] ?? [];
        unset($validated['role_ids']);
        
        // If employee_id is provided, set profile_type and profile_id
        if (!empty($validated['employee_id'])) {
            $validated['profile_type'] = 'employee';
            $validated['profile_id'] = $validated['employee_id'];
        }
        unset($validated['employee_id']);
        
        $user = User::create($validated);

        // Sync roles if provided
        if (!empty($roleIds)) {
            $user->syncRoles(Role::whereIn('id', $roleIds)->pluck('name'));
        }

        return redirect()->route('user.index')
            ->with('success', 'User created');
    }

    public function edit(string $uuid): Response
    {
        $user = User::where('uuid', $uuid)->firstOrFail();
        
        // Get user's current role IDs
        $userData = [
            'id' => $user->id,
            'uuid' => $user->uuid,
            'name' => $user->name,
            'email' => $user->email,
            'role_ids' => $user->roles->pluck('id')->toArray(),
        ];
        
        // Get roles for dropdown
        $roles = Role::orderBy('name')
            ->get()
            ->map(fn ($role) => [
                'value' => $role->id,
                'label' => $role->name,
            ])
            ->values()
            ->all();

        return inertia('User/UserForm', [
            'user' => $userData,
            'roles' => $roles,
        ]);
    }

    public function update(UserValidate $request, string $uuid): RedirectResponse
    {
        $user = User::where('uuid', $uuid)->firstOrFail();

        $params = $request->validated();

        // Extract role_ids before processing
        $roleIds = $params['role_ids'] ?? null;
        unset($params['role_ids']);

        if (empty($params['password'])) {
            unset($params['password']);
        }

        $user->update($params);

        // Sync roles if provided
        if ($roleIds !== null) {
            $user->syncRoles(Role::whereIn('id', $roleIds)->pluck('name'));
        }

        return redirect()->route('user.index')
            ->with('success', 'User updated');
    }

    public function destroy(string $uuid): RedirectResponse
    {
        User::where('uuid', $uuid)->firstOrFail()->delete();

        return redirect()->route('user.index')
            ->with('success', 'User deleted');
    }

    // Recycle Bin Methods
    public function recycleBin(): Response
    {
        $users = User::onlyTrashed()
            ->with('roles')
            ->orderBy('deleted_at', 'desc')
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($user) => [
                'id' => $user->id,
                'uuid' => $user->uuid,
                'name' => $user->name,
                'email' => $user->email,
                'profile_type' => $user->profile_type ? ucfirst($user->profile_type) : 'User',
                'roles' => $user->roles->pluck('name')->toArray(),
                'deleted_at' => $user->deleted_at->format('d/m/Y H:i'),
            ]);

        return inertia('User/UserRecycleBin', [
            'users' => $users,
        ]);
    }

    public function restore(string $uuid): RedirectResponse
    {
        User::onlyTrashed()->where('uuid', $uuid)->firstOrFail()->restore();

        return redirect()->route('user.recycleBin')
            ->with('success', 'User restored.');
    }

    public function destroyForce(string $uuid): RedirectResponse
    {
        User::onlyTrashed()->where('uuid', $uuid)->firstOrFail()->forceDelete();

        return redirect()->route('user.recycleBin')
            ->with('success', 'User deleted permanently.');
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        User::onlyTrashed()->forceDelete();

        return redirect()->route('user.recycleBin')
            ->with('success', 'Recycle bin emptied.');
    }

    public function restoreRecycleBin(): RedirectResponse
    {
        User::onlyTrashed()->restore();

        return redirect()->route('user.recycleBin')
            ->with('success', 'All users restored.');
    }

    // Impersonation
    public function impersonate(string $uuid): \Inertia\Response|\Symfony\Component\HttpFoundation\Response
    {
        $user = User::where('uuid', $uuid)->firstOrFail();
        
        auth()->user()->impersonate($user);

        // Use Inertia::location() to force a full page reload so shared props are refreshed
        return \Inertia\Inertia::location(route('dashboard.index'));
    }

    public function leaveImpersonation(): \Inertia\Response|\Symfony\Component\HttpFoundation\Response
    {
        auth()->user()->leaveImpersonation();

        // Use Inertia::location() to force a full page reload so shared props are refreshed
        return \Inertia\Inertia::location(route('user.index'));
    }
}
