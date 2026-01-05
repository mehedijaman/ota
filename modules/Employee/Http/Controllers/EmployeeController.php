<?php

namespace Modules\Employee\Http\Controllers;

use App\Filters\StatusFilter;
use App\Models\User;
use App\Options\StatusOptions;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Employee\Filters\DesignationFilter;
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
use Modules\Support\Http\Controllers\BackendController;

class EmployeeController extends BackendController
{
    public function index()
    {
        $employeesQuery = Employee::query()->with([
            'designation',
        ]);

        $employees = app(Pipeline::class)
            ->send($employeesQuery)
            ->through([
                DesignationFilter::class,
                StatusFilter::class,
            ])
            ->thenReturn();

        // Apply search and ordering
        $employees = $employees
            ->search(request('searchContext'), request('searchTerm'))
            ->orderBy('id', 'asc')
            ->paginate(request('rowsPerPage', 20))
            ->withQueryString();

        // Transform the employee data
        $employees->getCollection()->transform(function ($employee) {
            return [
                'id' => $employee->id,
                'uuid' => $employee->uuid,
                'code' => $employee->code,
                'name' => $employee->name,
                'photo_url' => $employee->photo_url,
                'designation' => $employee->designation,
                'type' => $employee->type,
                'joining_date' => Carbon::parse($employee->joining_date)->format('d M Y'),
                'service_length' => $employee->service_length,
                'email' => $employee->email,
                'phone' => $employee->phone,
                'is_active' => $employee->is_active,
                'updated_at' => $employee->updated_at->format('d/m/Y H:i').'h',
                'created_at' => $employee->created_at->format('d/m/Y H:i').'h',
            ];
        });

        return inertia('Employee/EmployeeIndex', [
            'employees' => $employees,
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

    public function store(EmployeeValidate $request)
    {
        $validatedData = $request->validated();

        DB::beginTransaction();
        try {
            $employee = Employee::create($validatedData);

            $employee->bankAccount()->updateOrCreate(
                ['employee_id' => $employee->id], // match condition
                [
                    'bank_name' => $validatedData['bank_name'] ?? null,
                    'branch_name' => $validatedData['branch_name'] ?? null,
                    'account_no' => $validatedData['account_no'] ?? null,
                    'account_name' => $validatedData['account_name'] ?? null,
                    'routing_no' => $validatedData['routing_no'] ?? null,
                    'account_type' => $validatedData['account_type'] ?? null,
                ]
            );

            // Handle photo upload
            if (isset($validatedData['photo']) && $validatedData['photo']) {
                $employee->addMedia($validatedData['photo'])->toMediaCollection('photo');
            }

            // Handle document uploads
            $documentTypes = ['nid_card', 'passport', 'driving_licence', 'tin_certificate'];
            foreach ($documentTypes as $docType) {
                if (isset($validatedData[$docType]) && $validatedData[$docType]) {
                    $employee->addMedia($validatedData[$docType])->toMediaCollection($docType);
                }
            }

            DB::commit();

            return redirect()->route('employee.index')->with('success', 'Employee created.');
        } catch (\Throwable $th) {
            DB::rollBack();

            return back()->with('error', $th->getMessage());
        }
    }

    public function show(Employee $employee)
    {
        $employee->load([
            'designation',
            'department',
            'area',
            'bankAccount',
        ]);

        // Append documents
        $employee->append('documents');

        return inertia('Employee/EmployeeShow', [
            'employee' => $employee,
        ]);
    }

    public function profile(?string $employeeCode = null)
    {
        if (! $employeeCode) {
            return inertia('Employee/EmployeeProfile');
        }

        $employee = Employee::with([
            'designation',
            'department',
            'area',
        ])
            ->where('code', $employeeCode)
            ->with('bankAccount')
            ->first();

        if (! $employee) {
            return redirect()->route('employee.profile')->with('error', 'Employee not found.');
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
        $validatedData = $request->validated();

        DB::beginTransaction();
        try {
            $employee->update($validatedData);
            // Update or create bank account
            $employee->bankAccount()->updateOrCreate(
                ['employee_id' => $employee->id], // match condition
                [
                    'bank_name' => $validatedData['bank_name'] ?? null,
                    'branch_name' => $validatedData['branch_name'] ?? null,
                    'account_no' => $validatedData['account_no'] ?? null,
                    'account_name' => $validatedData['account_name'] ?? null,
                    'routing_no' => $validatedData['routing_no'] ?? null,
                    'account_type' => $validatedData['account_type'] ?? null,
                ]
            );

            // Handle photo upload or removal
            if ($request->hasFile('photo')) {
                $employee->clearMediaCollection('photo');
                $employee->addMedia($request->file('photo'))->toMediaCollection('photo');
            } elseif ($request->boolean('remove_photo')) {
                $employee->clearMediaCollection('photo');
            }

            // Handle document uploads or removal
            $documentTypes = ['nid_card', 'passport', 'driving_licence', 'tin_certificate'];
            foreach ($documentTypes as $docType) {
                if ($request->hasFile($docType)) {
                    $employee->clearMediaCollection($docType);
                    $employee->addMedia($request->file($docType))->toMediaCollection($docType);
                } elseif ($request->boolean("remove_{$docType}")) {
                    $employee->clearMediaCollection($docType);
                }
            }

            $userQuery = User::query()->where('type', 'employee');

            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'employee_id')) {
                $userQuery->where('employee_id', $employee->id);
            } else {
                // Fallback: match by email. (Keeps behavior working without schema changes.)
                $userQuery->where('email', $employee->email);
            }

            $user = $userQuery->first();

            if ($user) {
                $user->name = $employee->name;
                $user->email = $employee->email;

                $user->save();
            }

            DB::commit();

            return redirect()->route('employee.index')
                ->with('success', 'Employee updated.');
        } catch (\Throwable $th) {
            DB::rollBack();

            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy(Employee $employee): RedirectResponse
    {

        DB::beginTransaction();
        try {
            $employee->delete();

            $userQuery = User::query()->where('profile_type', 'employee');

            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'employee_id')) {
                $userQuery->where('employee_id', $employee->id);
            } else {
                $userQuery->where('email', $employee->email);
            }

            $user = $userQuery->first();

            if ($user) {
                $user->delete();
            }
            DB::commit();

            return redirect()->route('employee.index')
                ->with('success', 'Employee deleted.');
        } catch (\Throwable $th) {
            DB::rollBack();

            return back()->with('error', $th->getMessage());
        }
    }

    public function recycleBin(): Response
    {
        $employees = Employee::onlyTrashed()
            ->with([
                'designation',
                'department',
                'area',
            ])
            ->orderBy('id')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($employee) => [
                'id' => $employee->id,
                'code' => $employee->code,
                'name' => $employee->name,
                'designation' => $employee->designation,
                'department' => $employee->department,
                'area' => $employee->area,
                'joining_date' => $employee->joining_date,
                'is_active' => $employee->is_active,
            ]);

        return inertia('Employee/EmployeeRecycleBin', [
            'employees' => $employees,
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        Employee::onlyTrashed()->findOrFail($id)->restore(); // Restore soft deleted record

        return redirect()->route('employee.recycleBin')
            ->with('success', 'Employee restored.');
    }

    public function destroyForce(int $id)
    {
        $employee = Employee::onlyTrashed()->findOrFail($id);

        $employee->forceDelete();

        return redirect()->route('employee.recycleBin')->with('success', 'Employee deleted.');
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        $employees = Employee::onlyTrashed()->get();

        foreach ($employees as $employee) {

            $employee->forceDelete();
        }

        return redirect()->route('employee.recycleBin')
            ->with('success', 'Recycle bin emptied.');
    }

    public function restoreRecycleBin(): RedirectResponse
    {
        Employee::onlyTrashed()->restore(); // Restore soft deleted records

        return redirect()->route('employee.recycleBin')
            ->with('success', 'Employee restored.');
    }

    public function import()
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
            Excel::import(new EmployeeImport, $validatedData['file']);

            return redirect()->route('employee.index')->with('success', 'Employees imported successfully.');
        } catch (\Exception $e) {
            // Log the import error and return a response with error details
            Log::error('Error importing employees', [
                'file_name' => $validatedData['file']->getClientOriginalName(),
                'error_message' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'file' => 'Failed to import employees. Please check the file format.',
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
}
