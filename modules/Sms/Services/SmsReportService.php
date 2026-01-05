<?php

namespace Modules\Sms\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Modules\Sms\Models\SmsMessage;

class SmsReportService
{
    /**
     * Generate report data.
     */
    public function generate(string $reportType, Carbon $dateFrom, Carbon $dateTo): array
    {
        return match ($reportType) {
            'usage' => $this->generateUsageReport($dateFrom, $dateTo),
            default => $this->generateHistoryReport($dateFrom, $dateTo),
        };
    }

    /**
     * Generate SMS send history report.
     */
    private function generateHistoryReport(Carbon $from, Carbon $to): array
    {
        $messages = SmsMessage::whereBetween('created_at', [$from, $to])
            ->orderByDesc('created_at')
            ->get();

        $reportData = $messages->map(fn ($msg) => [
            'created_at' => $msg->created_at->format('d/m/Y H:i'),
            'to_phone' => $msg->to_phone,
            'message' => $msg->message,
            'parts' => $msg->parts,
            'cost_credits' => $msg->cost_credits,
            'status' => $msg->status,
            'provider' => $msg->provider,
        ]);

        return [
            'reportType' => 'history',
            'reportData' => $reportData,
            'totals' => [
                'total_messages' => $messages->count(),
                'total_credits' => $messages->sum('cost_credits'),
            ],
        ];
    }

    /**
     * Generate usage summary report.
     */
    private function generateUsageReport(Carbon $from, Carbon $to): array
    {
        $messages = SmsMessage::whereBetween('created_at', [$from, $to])
            ->get();

        $groupedByStatus = $messages->groupBy('status');
        $groupedByType = $messages->groupBy('type');

        $statusData = $groupedByStatus->map(fn ($items, $status) => [
            'status' => $status,
            'count' => $items->count(),
            'credits' => $items->sum('cost_credits'),
        ])->values();

        $typeData = $groupedByType->map(fn ($items, $type) => [
            'type' => $type,
            'count' => $items->count(),
            'credits' => $items->sum('cost_credits'),
        ])->values();

        return [
            'reportType' => 'usage',
            'reportData' => [
                'status_summary' => $statusData,
                'type_summary' => $typeData,
            ],
            'totals' => [
                'total_messages' => $messages->count(),
                'total_credits' => $messages->sum('cost_credits'),
            ],
        ];
    }

    /**
     * Export report as PDF.
     */
    public function exportPdf(string $reportType, Carbon $dateFrom, Carbon $dateTo)
    {
        $data = $this->generate($reportType, $dateFrom, $dateTo);

        $view = $reportType === 'usage'
            ? 'sms::reports.usage-summary-pdf'
            : 'sms::reports.send-history-pdf';

        $fileName = strtoupper($reportType).'_SMS_REPORT_'.now()->format('Ymd_His').'.pdf';

        return Pdf::loadView($view, $data)
            ->setPaper('A4', 'portrait')
            ->download($fileName);
    }
}
