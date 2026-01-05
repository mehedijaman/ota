<?php

namespace Modules\Employee\Imports;

use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\Company\Models\Area;
use Modules\Company\Models\Company;
use Modules\Company\Models\Department;
use Modules\Company\Models\Section;
use Modules\Company\Models\Shift;
use Modules\Employee\Models\Designation;
use Modules\Employee\Models\Employee;

class EmployeeImport implements ToModel, WithHeadingRow
{
    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        try {
            // Helper function to safely access array values
            $getValue = function ($key) use ($row) {
                return array_key_exists($key, $row) ? $row[$key] : null;
            };

            // Look up the ID for mandatory fields
            $company_id = Company::where('name', $row['company'])->first()?->id;
            $area_id = Area::where('name', $row['area'])->first()?->id;
            $department_id = Department::where('name', $row['department'])->first()?->id;
            $code = $getValue('code');
            $name = $getValue('name');

            Log::info('Processing row', compact('company_id', 'area_id', 'department_id', 'code', 'name'));

            // Check if mandatory fields are present and not blank
            $required = compact('company_id', 'area_id', 'department_id', 'code', 'name');

            if (count(array_filter($required)) !== count($required)) {
                Log::error('Skipping row due to missing mandatory fields', $required);

                return null;
            }

            // Check if the code already exists in the Employee table
            $existingEmployee = Employee::where('code', $code)->first();
            if ($existingEmployee) {
                Log::info('Skipping row because employee code already exists', ['code' => $code]);

                return null;
            }

            // Look up the ID for optional fields
            $designation_id = Designation::where('name', $getValue('designation'))->first()?->id;
            $shift_id = Shift::where('name', $getValue('shift'))->first()?->id;
            $section_id = Section::where('name', $getValue('section'))->first()?->id;

            // Prepare employee data array
            $employeeData = [
                'company_id' => $company_id,
                'department_id' => $department_id,
                'area_id' => $area_id,
                'code' => $code,
                'name' => $name,
                'designation_id' => $designation_id,
                'shift_id' => $shift_id,
                'section_id' => $section_id,
                'type' => $getValue('type'),
                'card_type' => $getValue('card_type'),
                'joining_date' => $getValue('joining_date'),
                'father_name' => $getValue('father_name'),
                'mother_name' => $getValue('mother_name'),
                'birthday' => $getValue('birthday'),
                'marital_status' => $getValue('marital_status'),
                'nid_no' => $getValue('nid_no'),
                'gender' => $getValue('gender'),
                'religion' => $getValue('religion'),
                'blood_group' => $getValue('blood_group'),
                'email' => $getValue('email'),
                'phone' => $getValue('phone'),
                'emergency_contact' => $getValue('emergency_contact'),
                'present_address' => $getValue('present_address'),
                'permanent_address' => $getValue('permanent_address'),
                'basic_salary' => $getValue('basic_salary'),
            ];

            // Attempt to create the new Employee
            return new Employee($employeeData);
        } catch (\Exception $e) {
            // Log error with row data and exception message
            Log::error('Error creating employee', [
                'row' => $row,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
