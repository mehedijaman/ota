<?php

namespace Modules\Employee\Services;

use Modules\Employee\Models\Department;

class DepartmentService
{
    public function getAll()
    {
        return Department::query()
            ->orderBy('id')
            ->withCount('employees')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 20))
            ->withQueryString()
            ->through(fn ($department) => [
                'uuid' => $department->uuid,
                'name' => $department->name,
                'code' => $department->code,
                'employee_count' => $department->employees_count,
                'is_active' => $department->is_active,
            ]);
    }

    public function getRecycleBin()
    {
        return Department::onlyTrashed()
            ->orderBy('id')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 20))
            ->withQueryString()
            ->through(fn ($department) => [
                'id' => $department->id,
                'uuid' => $department->uuid,
                'name' => $department->name,
                'code' => $department->code,
                'is_active' => $department->is_active,
            ]);
    }

    public function create(array $data): Department
    {
        return Department::create($data);
    }

    public function update(Department $department, array $data): bool
    {
        return $department->update($data);
    }

    public function delete(Department $department): array
    {
        if ($department->employees()->count() > 0) {
            return [
                'success' => false,
                'message' => 'Department cannot be deleted because it has employees.',
            ];
        }

        $department->delete();

        return ['success' => true];
    }

    public function restore(int $id): void
    {
        Department::onlyTrashed()->findOrFail($id)->restore();
    }

    public function forceDelete(int $id): void
    {
        Department::onlyTrashed()->findOrFail($id)->forceDelete();
    }

    public function emptyRecycleBin(): void
    {
        Department::onlyTrashed()->forceDelete();
    }

    public function restoreAll(): void
    {
        Department::onlyTrashed()->restore();
    }
}
