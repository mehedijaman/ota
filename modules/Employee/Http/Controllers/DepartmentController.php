<?php

namespace Modules\Employee\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Employee\Http\Requests\DepartmentValidate;
use Modules\Employee\Models\Department;
use Modules\Employee\Services\DepartmentService;
use Modules\Support\Http\Controllers\BackendController;

class DepartmentController extends BackendController
{
    public function __construct(
        protected DepartmentService $departmentService
    ) {}

    public function index(): Response
    {
        $departments = $this->departmentService->getAll();

        return inertia('Employee/DepartmentIndex', [
            'departments' => $departments,
        ]);
    }

    public function create(): Response
    {
        return inertia('Employee/DepartmentForm');
    }

    public function store(DepartmentValidate $request): RedirectResponse
    {
        $this->departmentService->create($request->validated());

        return redirect()->route('department.index')
            ->with('success', 'Department created.');
    }

    public function edit(Department $department): Response
    {
        return inertia('Employee/DepartmentForm', [
            'department' => $department,
        ]);
    }

    public function update(DepartmentValidate $request, Department $department): RedirectResponse
    {
        $this->departmentService->update($department, $request->validated());

        return redirect()->route('department.index')
            ->with('success', 'Department updated.');
    }

    public function destroy(Department $department): RedirectResponse
    {
        $result = $this->departmentService->delete($department);

        if (! $result['success']) {
            return redirect()->route('department.index')
                ->with('error', $result['message']);
        }

        return redirect()->route('department.index')
            ->with('success', 'Department deleted.');
    }

    public function recycleBin(): Response
    {
        $departments = $this->departmentService->getRecycleBin();

        return inertia('Employee/DepartmentRecycleBin', [
            'departments' => $departments,
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        $this->departmentService->restore($id);

        return redirect()->route('department.recycleBin')
            ->with('success', 'Department restored.');
    }

    public function destroyForce(int $id): RedirectResponse
    {
        $this->departmentService->forceDelete($id);

        return redirect()->route('department.recycleBin')
            ->with('success', 'Department deleted permanently.');
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        $this->departmentService->emptyRecycleBin();

        return redirect()->route('department.recycleBin')
            ->with('success', 'Recycle bin emptied.');
    }

    public function restoreRecycleBin(): RedirectResponse
    {
        $this->departmentService->restoreAll();

        return redirect()->route('department.recycleBin')
            ->with('success', 'All departments restored.');
    }
}
