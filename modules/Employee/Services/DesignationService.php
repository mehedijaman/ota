<?php

namespace Modules\Employee\Services;

use Modules\Employee\Models\Designation;

class DesignationService
{
    public function getAll()
    {
        return Designation::query()
            ->orderBy('id')
            ->with('employees')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 20))
            ->withQueryString()
            ->through(fn ($designation) => [
                'uuid' => $designation->uuid,
                'name' => $designation->name,
                'code' => $designation->code,
                'employee_count' => $designation->employees->count(),
                'is_active' => $designation->is_active,
            ]);
    }

    public function getRecycleBin()
    {
        return Designation::onlyTrashed()
            ->orderBy('id')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 20))
            ->withQueryString()
            ->through(fn ($designation) => [
                'id' => $designation->id,
                'uuid' => $designation->uuid,
                'name' => $designation->name,
                'code' => $designation->code,
                'is_active' => $designation->is_active,
            ]);
    }

    public function create(array $data): Designation
    {
        return Designation::create($data);
    }

    public function update(Designation $designation, array $data): bool
    {
        return $designation->update($data);
    }

    public function delete(Designation $designation): array
    {
        if ($designation->employees()->count() > 0) {
            return [
                'success' => false,
                'message' => 'Designation cannot be deleted because it has employees.',
            ];
        }

        $designation->delete();

        return ['success' => true];
    }

    public function restore(int $id): void
    {
        Designation::onlyTrashed()->findOrFail($id)->restore();
    }

    public function forceDelete(int $id): void
    {
        Designation::onlyTrashed()->findOrFail($id)->forceDelete();
    }

    public function emptyRecycleBin(): void
    {
        Designation::onlyTrashed()->forceDelete();
    }

    public function restoreAll(): void
    {
        Designation::onlyTrashed()->restore();
    }
}
