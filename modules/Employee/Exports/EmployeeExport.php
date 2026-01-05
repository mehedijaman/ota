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
    protected $validated;

    public function __construct($validated)
    {
        $this->validated = $validated;
    }

    /**
     * Fetch and transform the collection of employees for export.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Employee::filter($this->validated)
            ->get()
            ->map(fn ($employee) => $this->transformEmployee($employee));
    }

    /**
     * Transform the employee model into the exportable format.
     *
     * @param  Employee  $employee
     */
    private function transformEmployee($employee): array
    {
        return [
            'code' => $employee->code,
            'company' => optional($employee->company)->name,
            'name' => $employee->name,
            'designation' => optional($employee->designation)->name,
            'section' => optional($employee->section)->name,
            'department' => optional($employee->department)->name,
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
            'status' => $employee->active,
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
            'Company',
            'Name',
            'Designation',
            'Section',
            'Department',
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
                    'color' => ['argb' => 'FFFF00'], // Yellow background color for the header row
                ],
            ],
        ];
    }
}
