<?php

namespace Modules\Employee\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Employee\Exports\DesignationExport;
use Modules\Employee\Http\Requests\DesignationValidate;
use Modules\Employee\Imports\DesignationImport;
use Modules\Employee\Models\Designation;
use Modules\Employee\Services\DesignationService;
use Modules\Support\Http\Controllers\BackendController;

class DesignationController extends BackendController
{
    public function __construct(
        protected DesignationService $designationService
    ) {}

    public function index(): Response
    {
        return inertia('Employee/DesignationIndex', [
            'designations' => $this->designationService->getAll(),
        ]);
    }

    public function create(): Response
    {
        return inertia('Employee/DesignationForm');
    }

    public function store(DesignationValidate $request): RedirectResponse
    {
        $this->designationService->create($request->validated());

        return redirect()->route('designation.index')
            ->with('success', 'Designation created.');
    }

    public function edit(Designation $designation): Response
    {
        return inertia('Employee/DesignationForm', [
            'designation' => $designation,
        ]);
    }

    public function update(DesignationValidate $request, Designation $designation): RedirectResponse
    {
        $this->designationService->update($designation, $request->validated());

        return redirect()->route('designation.index')
            ->with('success', 'Designation updated.');
    }

    public function destroy(Designation $designation): RedirectResponse
    {
        $result = $this->designationService->delete($designation);

        if (! $result['success']) {
            return redirect()->route('designation.index')
                ->with('error', $result['message']);
        }

        return redirect()->route('designation.index')
            ->with('success', 'Designation deleted.');
    }

    public function recycleBin(): Response
    {
        return inertia('Employee/DesignationRecycleBin', [
            'designations' => $this->designationService->getRecycleBin(),
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        $this->designationService->restore($id);

        return redirect()->route('designation.recycleBin')
            ->with('success', 'Designation restored.');
    }

    public function forceDestroy(int $id): RedirectResponse
    {
        $this->designationService->forceDelete($id);

        return redirect()->route('designation.recycleBin')
            ->with('success', 'Designation deleted permanently.');
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        $this->designationService->emptyRecycleBin();

        return redirect()->route('designation.recycleBin')
            ->with('success', 'Recycle bin emptied.');
    }

    public function restoreAll(): RedirectResponse
    {
        $this->designationService->restoreAll();

        return redirect()->route('designation.recycleBin')
            ->with('success', 'All designations restored.');
    }

    public function import(): Response
    {
        return inertia('Employee/DesignationImport');
    }

    public function downloadTemplate()
    {
        $filePath = public_path('templates/designations.xlsx');

        if (! file_exists($filePath)) {
            abort(404, 'Template file not found.');
        }

        return response()->download($filePath);
    }

    public function postImport(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            Excel::import(new DesignationImport, $validatedData['file']);

            return redirect()->route('designation.index')
                ->with('success', 'Designations imported successfully.');
        } catch (\Exception $e) {
            Log::error('Error importing designations', [
                'file_name' => $validatedData['file']->getClientOriginalName(),
                'error_message' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'file' => 'Failed to import designations. Please check the file format.',
            ]);
        }
    }

    public function export()
    {
        return Excel::download(new DesignationExport, 'designations.xlsx');
    }
}
