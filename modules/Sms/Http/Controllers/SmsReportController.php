<?php

namespace Modules\Sms\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Sms\Options\SmsReportTypes;
use Modules\Sms\Services\SmsReportService;
use Modules\Support\Http\Controllers\BackendController;

class SmsReportController extends BackendController
{
    protected SmsReportService $smsReportService;

    public function __construct(SmsReportService $smsReportService)
    {
        $this->smsReportService = $smsReportService;
    }

    /**
     * Show report index page.
     */
    public function index(): Response
    {
        return Inertia::render('Sms/Report/SmsReportIndex', [
            'reportTypes' => SmsReportTypes::get(),
        ]);
    }

    /**
     * Generate report for display.
     */
    public function generate(Request $request)
    {
        $validated = $this->validateReportRequest($request);
        [$reportType, $dateFrom, $dateTo] = $this->parseDateFilters($validated);

        $data = $this->smsReportService->generate($reportType, $dateFrom, $dateTo);

        return response()->json($data);
    }

    /**
     * Export report (PDF).
     */
    public function export(Request $request)
    {
        $validated = $this->validateReportRequest($request, true);
        [$reportType, $dateFrom, $dateTo] = $this->parseDateFilters($validated);

        return match ($validated['export']) {
            'pdf' => $this->smsReportService->exportPdf($reportType, $dateFrom, $dateTo),
            default => abort(400, 'Invalid export type. Only PDF is currently supported for SMS reports.'),
        };
    }

    /**
     * Shared validation for both actions.
     */
    protected function validateReportRequest(Request $request, bool $requireExport = false): array
    {
        $rules = [
            'report_type' => [
                'required',
                'string',
                Rule::in(SmsReportTypes::getValues()),
            ],
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
        ];

        if ($requireExport) {
            $rules['export'] = 'required|string|in:pdf';
        }

        return $request->validate($rules);
    }

    /**
     * Normalize and parse date filters.
     */
    protected function parseDateFilters(array $data): array
    {
        $reportType = $data['report_type'];
        $dateFrom = isset($data['date_from'])
            ? Carbon::parse($data['date_from'])->startOfDay()
            : Carbon::now()->startOfMonth();

        $dateTo = isset($data['date_to'])
            ? Carbon::parse($data['date_to'])->endOfDay()
            : Carbon::now()->endOfDay();

        return [$reportType, $dateFrom, $dateTo];
    }
}
