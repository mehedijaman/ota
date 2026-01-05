<?php

namespace Modules\Employee\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Company\Jobs\SyncDesignationWithAPI;
use Modules\Employee\Exports\DesignationExport;
use Modules\Employee\Http\Requests\DesignationValidate;
use Modules\Employee\Imports\DesignationImport;
use Modules\Employee\Models\Designation;
use Modules\Support\Http\Controllers\BackendController;

class DesignationController extends BackendController
{
    public function index(): Response
    {
        $designations = Designation::orderBy('id')
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

        return inertia('Employee/DesignationIndex', [
            'designations' => $designations,
        ]);
    }

    public function create(): Response
    {
        return inertia('Employee/DesignationForm');
    }

    public function store(DesignationValidate $request): RedirectResponse
    {
        Designation::create($request->validated());

        // SyncDesignationWithAPI::dispatch($designation, 'create');

        return redirect()->route('designation.index')
            ->with('success', 'Designation created.');
    }

    public function edit(Designation $designation): Response
    {
        // $designation = Designation::find($id);

        return inertia('Employee/DesignationForm', [
            'designation' => $designation,
        ]);
    }

    public function update(DesignationValidate $request, Designation $designation)
    {
        $designation->update($request->validated());

        // SyncDesignationWithAPI::dispatch($designation, 'update');

        return redirect()->route('designation.index')
            ->with('success', 'Designation updated.');
    }

    public function destroy(Designation $designation): RedirectResponse
    {
        // Check if the designation has employees
        if ($designation->employees()->count() > 0) {
            return redirect()->route('designation.index')
                ->with('error', 'Designation cannot be deleted because it has employees.');
        }

        $designation->delete();

        return redirect()->route('designation.index')
            ->with('success', 'Designation deleted.');
    }

    public function recycleBin(): Response
    {
        $designations = Designation::onlyTrashed()
            ->orderBy('id')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 20))
            ->withQueryString()
            ->through(fn ($designation) => [
                'id' => $designation->id,
                'name' => $designation->name,
                'code' => $designation->code,
                'active' => $designation->active,
            ]);

        return inertia('Employee/DesignationRecycleBin', [
            'designations' => $designations,
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        Designation::onlyTrashed()->findOrFail($id)->restore(); // Restore soft deleted record

        return redirect()->route('designation.recycleBin')
            ->with('success', 'Designation restored.');
    }

    public function destroyForce(int $id): RedirectResponse
    {
        Designation::onlyTrashed()->findOrFail($id)->forceDelete(); // Force delete soft deleted record

        return redirect()->route('designation.recycleBin')
            ->with('success', 'Designation deleted.');
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        Designation::onlyTrashed()->forceDelete();

        return redirect()->route('designation.recycleBin')
            ->with('success', 'Designation emptied.');
    }

    public function restoreRecycleBin(): RedirectResponse
    {
        Designation::onlyTrashed()->restore(); // Restore soft deleted records

        return redirect()->route('designation.recycleBin')
            ->with('success', 'Designation restored.');
    }

    public function import()
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

    /**
     * Handle the employee import request.
     *
     * @return RedirectResponse
     */
    public function postImport(Request $request)
    {
        // Validate file input
        $validatedData = $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',  // max 10MB
        ]);

        // Log the file upload attempt
        $this->logFileUpload($request);

        try {
            // Import employee data from the uploaded file
            Excel::import(new DesignationImport, $validatedData['file']);

            return redirect()->route('designation.index')->with('success', 'Designations imported successfully.');
        } catch (\Exception $e) {
            // Log the import error and return a response with error details
            Log::error('Error importing designations', [
                'file_name' => $validatedData['file']->getClientOriginalName(),
                'error_message' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'file' => 'Failed to import designations. Please check the file format.',
            ]);
        }
    }

    /**
     * Log the uploaded file information or error if no file was uploaded.
     */
    private function logFileUpload(Request $request): void
    {
        if ($request->hasFile('file')) {
            Log::info('File uploaded successfully', [
                'file_name' => $request->file('file')->getClientOriginalName(),
            ]);
        } else {
            Log::warning('No file uploaded');
        }
    }

    public function export()
    {
        return Excel::download(new DesignationExport, 'designations.xlsx');
    }
}
