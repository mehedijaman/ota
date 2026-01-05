<?php

namespace Modules\Employee\Services;

use Modules\Employee\Models\Area;

class AreaService
{
    public function getAll()
    {
        return Area::query()
            ->orderBy('id')
            ->withCount('employees')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 20))
            ->withQueryString()
            ->through(fn ($area) => [
                'uuid' => $area->uuid,
                'name' => $area->name,
                'code' => $area->code,
                'employee_count' => $area->employees_count,
                'is_active' => $area->is_active,
            ]);
    }

    public function getRecycleBin()
    {
        return Area::onlyTrashed()
            ->orderBy('id')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 20))
            ->withQueryString()
            ->through(fn ($area) => [
                'id' => $area->id,
                'uuid' => $area->uuid,
                'name' => $area->name,
                'code' => $area->code,
                'is_active' => $area->is_active,
            ]);
    }

    public function create(array $data): Area
    {
        return Area::create($data);
    }

    public function update(Area $area, array $data): bool
    {
        return $area->update($data);
    }

    public function delete(Area $area): array
    {
        if ($area->employees()->count() > 0) {
            return [
                'success' => false,
                'message' => 'Area cannot be deleted because it has employees.',
            ];
        }

        $area->delete();

        return ['success' => true];
    }

    public function restore(int $id): void
    {
        Area::onlyTrashed()->findOrFail($id)->restore();
    }

    public function forceDelete(int $id): void
    {
        Area::onlyTrashed()->findOrFail($id)->forceDelete();
    }

    public function emptyRecycleBin(): void
    {
        Area::onlyTrashed()->forceDelete();
    }

    public function restoreAll(): void
    {
        Area::onlyTrashed()->restore();
    }
}
