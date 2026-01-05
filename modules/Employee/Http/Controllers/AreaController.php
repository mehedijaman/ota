<?php

namespace Modules\Employee\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Employee\Http\Requests\AreaValidate;
use Modules\Employee\Models\Area;
use Modules\Employee\Services\AreaService;
use Modules\Support\Http\Controllers\BackendController;

class AreaController extends BackendController
{
    public function __construct(
        protected AreaService $areaService
    ) {}

    public function index(): Response
    {
        $areas = $this->areaService->getAll();

        return inertia('Employee/AreaIndex', [
            'areas' => $areas,
        ]);
    }

    public function create(): Response
    {
        return inertia('Employee/AreaForm');
    }

    public function store(AreaValidate $request): RedirectResponse
    {
        $this->areaService->create($request->validated());

        return redirect()->route('area.index')
            ->with('success', 'Area created.');
    }

    public function edit(Area $area): Response
    {
        return inertia('Employee/AreaForm', [
            'area' => $area,
        ]);
    }

    public function update(AreaValidate $request, Area $area): RedirectResponse
    {
        $this->areaService->update($area, $request->validated());

        return redirect()->route('area.index')
            ->with('success', 'Area updated.');
    }

    public function destroy(Area $area): RedirectResponse
    {
        $result = $this->areaService->delete($area);

        if (! $result['success']) {
            return redirect()->route('area.index')
                ->with('error', $result['message']);
        }

        return redirect()->route('area.index')
            ->with('success', 'Area deleted.');
    }

    public function recycleBin(): Response
    {
        $areas = $this->areaService->getRecycleBin();

        return inertia('Employee/AreaRecycleBin', [
            'areas' => $areas,
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        $this->areaService->restore($id);

        return redirect()->route('area.recycleBin')
            ->with('success', 'Area restored.');
    }

    public function destroyForce(int $id): RedirectResponse
    {
        $this->areaService->forceDelete($id);

        return redirect()->route('area.recycleBin')
            ->with('success', 'Area deleted permanently.');
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        $this->areaService->emptyRecycleBin();

        return redirect()->route('area.recycleBin')
            ->with('success', 'Recycle bin emptied.');
    }

    public function restoreRecycleBin(): RedirectResponse
    {
        $this->areaService->restoreAll();

        return redirect()->route('area.recycleBin')
            ->with('success', 'All areas restored.');
    }
}
