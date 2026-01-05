<?php

namespace Modules\Employee\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Employee\Exports\EmployeeExport;
use Modules\Employee\Models\Designation;
use Modules\Employee\Services\EmployeeReportService;
use Modules\Support\Http\Controllers\BackendController;

class EmployeeReportController extends BackendController
{
    public function print(Request $request)
    {
        // Validate request
        $validated = $this->validateReportRequest($request);

        $report = $this->prepareReportHeader($validated);

        /** Student List */
        if ($report['type'] === 'employee-list') {
            $report['data'] = EmployeeReportService::getList();

            return view('employee::employee-list', compact('report'));
            // $pdf = Pdf::setOption(['dpi' => 88, 'defaultFont' => 'sans-serif'])
            //     ->setPaper('a4', 'portrait')
            //     ->loadView('employee::employee-list', compact('report'));
            $html = view('employee::employee-list', compact('report'))->render();
            $pdf = Pdf::loadHTML($html);

            return $pdf->stream();
        }

        return 'No data found';
    }

    private function prepareReportHeader($validated)
    {
        return [
            'header' => [
                'active' => isset($validated['active']) ? ((bool) $validated['active'] ? 'Active' : 'Inactive') : null,
            ],
            'type' => request('reportType') ?? null,
        ];
    }

    public function export(Request $request)
    {
        $validated = $this->validateReportRequest($request);

        switch (request('exportType')) {
            case 'xlsx':
                return Excel::download(new EmployeeExport($validated), 'employees.xlsx', \Maatwebsite\Excel\Excel::XLSX);
                break;
            case 'xls':
                return Excel::download(new EmployeeExport($validated), 'employees.xls', \Maatwebsite\Excel\Excel::XLS);
                break;
            case 'csv':
                return Excel::download(new EmployeeExport($validated), 'employees.csv', \Maatwebsite\Excel\Excel::CSV);
                break;
            case 'ods':
                return Excel::download(new EmployeeExport($validated), 'employees.ods', \Maatwebsite\Excel\Excel::ODS);
                break;
            case 'tsv':
                return Excel::download(new EmployeeExport($validated), 'employees.tsv', \Maatwebsite\Excel\Excel::TSV);
                break;
            case 'html':
                return Excel::download(new EmployeeExport($validated), 'employees.html', \Maatwebsite\Excel\Excel::HTML);
                break;
            case 'pdf':
                return Excel::download(new EmployeeExport($validated), 'employees.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
                break;
            default:
                return Excel::download(new EmployeeExport($validated), 'employees.xlsx', \Maatwebsite\Excel\Excel::XLSX);
                break;
        }
    }

    private function hasFilters(Request $request)
    {
        $filters = [
            'company',
            'department',
            'designation',
            'area',
            'shift',
            'section',
            'type',
            'active',
        ];

        return collect($filters)->contains(fn ($filter) => $request->filled($filter));
    }

    private function validateReportRequest(Request $request)
    {
        return $request->validate([

            'designation' => [
                'nullable',
                'integer',
                Rule::exists(Designation::class, 'id'),
            ],
            'active' => [
                'nullable',
                'boolean',
            ],
        ]);
    }
}
