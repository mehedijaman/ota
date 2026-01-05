<?php

namespace Modules\Employee\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Modules\Employee\Models\Designation;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DesignationExport implements FromCollection, WithHeadings, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Designation::select('name', 'code')->get();
    }

    public function headings(): array
    {
        return [
            'name',
            'code',
        ];
    }

    /**
     * Apply styles to the worksheet
     *
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Set background color and other styles for the first row (header)
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['argb' => 'FFFF00'], // Yellow background color (ARGB format)
                ],
            ],
        ];
    }
}
