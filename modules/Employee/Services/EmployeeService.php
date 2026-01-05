<?php

namespace Modules\Employee\Services;

use App\Filters\StatusFilter;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Employee\Filters\DesignationFilter;
use Modules\Employee\Models\Employee;

class EmployeeService
{
    public function getAll()
    {
        $employeesQuery = Employee::query()->with([
            'designation',
            'department',
            'area',
        ]);

        $employees = app(Pipeline::class)
            ->send($employeesQuery)
            ->through([
                DesignationFilter::class,
                StatusFilter::class,
            ])
            ->thenReturn();

        return $employees
            ->search(request('searchContext'), request('searchTerm'))
            ->orderBy('id', 'asc')
            ->paginate(request('rowsPerPage', 20))
            ->withQueryString()
            ->through(fn ($employee) => [
                'id' => $employee->id,
                'uuid' => $employee->uuid,
                'code' => $employee->code,
                'name' => $employee->name,
                'photo_url' => $employee->photo_url,
                'designation' => $employee->designation,
                'department' => $employee->department,
                'area' => $employee->area,
                'type' => $employee->type,
                'joining_date' => $employee->joining_date ? Carbon::parse($employee->joining_date)->format('d M Y') : null,
                'service_length' => $employee->service_length,
                'email' => $employee->email,
                'phone' => $employee->phone,
                'is_active' => $employee->is_active,
                'has_user' => \Modules\User\Models\User::where('profile_type', 'employee')
                    ->where('profile_id', $employee->id)
                    ->exists(),
                'updated_at' => $employee->updated_at->format('d/m/Y H:i').'h',
                'created_at' => $employee->created_at->format('d/m/Y H:i').'h',
            ]);
    }

    public function getRecycleBin()
    {
        return Employee::onlyTrashed()
            ->with(['designation', 'department', 'area'])
            ->orderBy('id')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($employee) => [
                'id' => $employee->id,
                'code' => $employee->code,
                'name' => $employee->name,
                'designation' => $employee->designation,
                'joining_date' => $employee->joining_date,
                'is_active' => $employee->is_active,
            ]);
    }

    public function create(array $data): Employee
    {
        return DB::transaction(function () use ($data) {
            $employee = Employee::create($data);

            $this->saveBankAccount($employee, $data);
            $this->handleMediaUploads($employee, $data);

            return $employee;
        });
    }

    public function update(Employee $employee, array $data): Employee
    {
        return DB::transaction(function () use ($employee, $data) {
            $employee->update($data);

            $this->saveBankAccount($employee, $data);
            $this->handleMediaUpdates($employee, $data);
            $this->syncUser($employee);

            return $employee;
        });
    }

    public function delete(Employee $employee): void
    {
        DB::transaction(function () use ($employee) {
            $employee->delete();
            $this->deleteRelatedUser($employee);
        });
    }

    public function restore(int $id): void
    {
        Employee::onlyTrashed()->findOrFail($id)->restore();
    }

    public function forceDelete(int $id): void
    {
        Employee::onlyTrashed()->findOrFail($id)->forceDelete();
    }

    public function emptyRecycleBin(): void
    {
        $employees = Employee::onlyTrashed()->get();
        foreach ($employees as $employee) {
            $employee->forceDelete();
        }
    }

    public function restoreAll(): void
    {
        Employee::onlyTrashed()->restore();
    }

    protected function saveBankAccount(Employee $employee, array $data): void
    {
        $employee->bankAccount()->updateOrCreate(
            ['employee_id' => $employee->id],
            [
                'bank_name' => $data['bank_name'] ?? null,
                'branch_name' => $data['branch_name'] ?? null,
                'account_no' => $data['account_no'] ?? null,
                'account_name' => $data['account_name'] ?? null,
                'routing_no' => $data['routing_no'] ?? null,
                'account_type' => $data['account_type'] ?? null,
            ]
        );
    }

    protected function handleMediaUploads(Employee $employee, array $data): void
    {
        if (isset($data['photo']) && $data['photo']) {
            $employee->addMedia($data['photo'])->toMediaCollection('photo');
        }

        $documentTypes = ['nid_card', 'passport', 'driving_licence', 'tin_certificate'];
        foreach ($documentTypes as $docType) {
            if (isset($data[$docType]) && $data[$docType]) {
                $employee->addMedia($data[$docType])->toMediaCollection($docType);
            }
        }
    }

    protected function handleMediaUpdates(Employee $employee, array $data): void
    {
        // Handle photo upload or removal
        if (isset($data['photo']) && $data['photo'] instanceof \Illuminate\Http\UploadedFile) {
            $employee->clearMediaCollection('photo');
            $employee->addMedia($data['photo'])->toMediaCollection('photo');
        } elseif (! empty($data['remove_photo'])) {
            $employee->clearMediaCollection('photo');
        }

        // Handle document uploads or removal
        $documentTypes = ['nid_card', 'passport', 'driving_licence', 'tin_certificate'];
        foreach ($documentTypes as $docType) {
            if (isset($data[$docType]) && $data[$docType] instanceof \Illuminate\Http\UploadedFile) {
                $employee->clearMediaCollection($docType);
                $employee->addMedia($data[$docType])->toMediaCollection($docType);
            } elseif (! empty($data["remove_{$docType}"])) {
                $employee->clearMediaCollection($docType);
            }
        }
    }

    protected function syncUser(Employee $employee): void
    {
        $userQuery = User::query()->where('type', 'employee');

        if (Schema::hasColumn('users', 'employee_id')) {
            $userQuery->where('employee_id', $employee->id);
        } else {
            $userQuery->where('email', $employee->email);
        }

        $user = $userQuery->first();

        if ($user) {
            $user->name = $employee->name;
            $user->email = $employee->email;
            $user->save();
        }
    }

    protected function deleteRelatedUser(Employee $employee): void
    {
        $userQuery = User::query()->where('type', 'employee');

        if (Schema::hasColumn('users', 'employee_id')) {
            $userQuery->where('employee_id', $employee->id);
        } else {
            $userQuery->where('email', $employee->email);
        }

        $user = $userQuery->first();
        $user?->delete();
    }
}
