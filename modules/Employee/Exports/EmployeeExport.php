<?php

namespace Modules\Employee\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Modules\Employee\Models\Employee;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeeExport implements FromCollection, WithHeadings, WithStyles
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Fetch and transform the collection of employees for export.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Employee::query()
            ->with(['designation', 'department', 'area'])
            ->when($this->filters['designation'] ?? null, fn ($q, $v) => $q->where('designation_id', $v))
            ->when($this->filters['department'] ?? null, fn ($q, $v) => $q->where('department_id', $v))
            ->when($this->filters['area'] ?? null, fn ($q, $v) => $q->where('area_id', $v))
            ->when(isset($this->filters['active']), fn ($q) => $q->where('is_active', $this->filters['active']))
            ->orderBy('name')
            ->get()
            ->map(fn ($employee) => $this->transformEmployee($employee));
    }

    /**
     * Transform the employee model into the exportable format.
     */
    private function transformEmployee(Employee $employee): array
    {
        return [
            'code' => $employee->code,
            'name' => $employee->name,
            'designation' => $employee->designation?->name,
            'department' => $employee->department?->name,
            'area' => $employee->area?->name,
            'joining_date' => $employee->joining_date,
            'type' => $employee->type,
            'service_length' => $employee->service_length,
            'birthday' => $employee->birthday,
            'marital_status' => $employee->marital_status,
            'religion' => $employee->religion,
            'blood_group' => $employee->blood_group,
            'gender' => $employee->gender,
            'father_name' => $employee->father_name,
            'mother_name' => $employee->mother_name,
            'spouse_name' => $employee->spouse_name,
            'present_address' => $employee->present_address,
            'permanent_address' => $employee->permanent_address,
            'nid_no' => $employee->nid_no,
            'email' => $employee->email,
            'phone' => $employee->phone,
            'emergency_contact' => $employee->emergency_contact,
            'status' => $employee->is_active ? 'Active' : 'Inactive',
            'resign_date' => $employee->resign_date,
        ];
    }

    /**
     * Define the headings for the exported sheet.
     */
    public function headings(): array
    {
        return [
            'Code',
            'Name',
            'Designation',
            'Department',
            'Area',
            'Joining Date',
            'Type',
            'Service Length',
            'Date of Birth',
            'Marital Status',
            'Religion',
            'Blood Group',
            'Gender',
            'Father Name',
            'Mother Name',
            'Spouse Name',
            'Present Address',
            'Permanent Address',
            'NID No',
            'Email',
            'Phone',
            'Emergency Contact',
            'Status',
            'Resign Date',
        ];
    }

    /**
     * Apply styles to the worksheet.
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['argb' => 'FFFF00'],
                ],
            ],
        ];
    }
}
