<?php

namespace Modules\Employee\Http\Controllers;

use App\Options\StatusOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Employee\Http\Requests\EmployeeValidate;
use Modules\Employee\Imports\EmployeeImport;
use Modules\Employee\Models\Employee;
use Modules\Employee\Options\AreaOptions;
use Modules\Employee\Options\BloodGroupOptions;
use Modules\Employee\Options\DepartmentOptions;
use Modules\Employee\Options\DesignationOptions;
use Modules\Employee\Options\GenderOptions;
use Modules\Employee\Options\MaritalStatusOptions;
use Modules\Employee\Options\ReligionOptions;
use Modules\Employee\Services\EmployeeService;
use Modules\Support\Http\Controllers\BackendController;

class EmployeeController extends BackendController
{
    public function __construct(
        protected EmployeeService $employeeService
    ) {}

    public function index(): Response
    {
        return inertia('Employee/EmployeeIndex', [
            'employees' => $this->employeeService->getAll(),
            'designations' => DesignationOptions::get(),
            'departments' => DepartmentOptions::get(),
            'areas' => AreaOptions::get(),
            'statuses' => StatusOptions::get(),
        ]);
    }

    public function create(): Response
    {
        return inertia('Employee/EmployeeForm', [
            'designations' => DesignationOptions::get(),
            'departments' => DepartmentOptions::get(),
            'areas' => AreaOptions::get(),
            'genders' => GenderOptions::get(),
            'religions' => ReligionOptions::get(),
            'blood_groups' => BloodGroupOptions::get(),
            'marital_statuses' => MaritalStatusOptions::get(),
        ]);
    }

    public function store(EmployeeValidate $request): RedirectResponse
    {
        try {
            $this->employeeService->create($request->validated());

            return redirect()->route('employee.index')
                ->with('success', 'Employee created.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function show(Employee $employee): Response
    {
        $employee->load(['designation', 'department', 'area', 'bankAccount']);
        $employee->append('documents');

        return inertia('Employee/EmployeeShow', [
            'employee' => $employee,
        ]);
    }

    public function profile(?string $employeeCode = null): Response|RedirectResponse
    {
        if (! $employeeCode) {
            return inertia('Employee/EmployeeProfile');
        }

        $employee = Employee::with(['designation', 'department', 'area', 'bankAccount'])
            ->where('code', $employeeCode)
            ->first();

        if (! $employee) {
            return redirect()->route('employee.profile')
                ->with('error', 'Employee not found.');
        }

        $employee->append('service_length');

        return inertia('Employee/EmployeeProfile', [
            'employee' => $employee,
        ]);
    }

    public function edit(Employee $employee): Response
    {
        $employee->load('bankAccount');
        $employee->append('documents');

        return inertia('Employee/EmployeeForm', [
            'employee' => $employee,
            'designations' => DesignationOptions::get(),
            'departments' => DepartmentOptions::get(),
            'areas' => AreaOptions::get(),
            'genders' => GenderOptions::get(),
            'religions' => ReligionOptions::get(),
            'blood_groups' => BloodGroupOptions::get(),
            'marital_statuses' => MaritalStatusOptions::get(),
        ]);
    }

    public function update(EmployeeValidate $request, Employee $employee): RedirectResponse
    {
        try {
            $this->employeeService->update($employee, $request->validated());

            return redirect()->route('employee.index')
                ->with('success', 'Employee updated.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        try {
            $this->employeeService->delete($employee);

            return redirect()->route('employee.index')
                ->with('success', 'Employee deleted.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function recycleBin(): Response
    {
        return inertia('Employee/EmployeeRecycleBin', [
            'employees' => $this->employeeService->getRecycleBin(),
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        $this->employeeService->restore($id);

        return redirect()->route('employee.recycleBin')
            ->with('success', 'Employee restored.');
    }

    public function destroyForce(int $id): RedirectResponse
    {
        $this->employeeService->forceDelete($id);

        return redirect()->route('employee.recycleBin')
            ->with('success', 'Employee deleted permanently.');
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        $this->employeeService->emptyRecycleBin();

        return redirect()->route('employee.recycleBin')
            ->with('success', 'Recycle bin emptied.');
    }

    public function restoreRecycleBin(): RedirectResponse
    {
        $this->employeeService->restoreAll();

        return redirect()->route('employee.recycleBin')
            ->with('success', 'All employees restored.');
    }

    public function import(): Response
    {
        return inertia('Employee/EmployeeImport');
    }

    public function downloadTemplate()
    {
        $filePath = public_path('templates/employees.xlsx');

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
            Excel::import(new EmployeeImport, $validatedData['file']);

            return redirect()->route('employee.index')
                ->with('success', 'Employees imported successfully.');
        } catch (\Exception $e) {
            Log::error('Error importing employees', [
                'file_name' => $validatedData['file']->getClientOriginalName(),
                'error_message' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'file' => 'Failed to import employees. Please check the file format.',
            ]);
        }
    }

    public function export()
    {
        return Excel::download(new \Modules\Employee\Exports\EmployeeExport([]), 'employees.xlsx');
    }

    public function createUser(Request $request, Employee $employee): RedirectResponse
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Check if user already exists
        $existingUser = \Modules\User\Models\User::where('profile_type', 'employee')
            ->where('profile_id', $employee->id)
            ->first();

        if ($existingUser) {
            return back()->with('error', 'User already exists for this employee.');
        }

        \Modules\User\Models\User::create([
            'name' => $employee->name,
            'email' => $employee->email,
            'password' => bcrypt($request->password),
            'profile_type' => 'employee',
            'profile_id' => $employee->id,
        ]);

        return back()->with('success', 'User account created successfully.');
    }
}
