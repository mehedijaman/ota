<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>SMS Send History Report</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10pt; line-height: 1.4; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .report-title { font-size: 18pt; font-weight: bold; color: #2563eb; margin-bottom: 5px; }
        .report-info { font-size: 9pt; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #f8fafc; color: #475569; text-align: left; padding: 10px; font-size: 8pt; text-transform: uppercase; border: 1px solid #e2e8f0; }
        td { padding: 8px 10px; border: 1px solid #e2e8f0; font-size: 9pt; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer { margin-top: 30px; text-align: center; font-size: 8pt; color: #94a3b8; }
        .total-row { background-color: #f1f5f9; font-weight: bold; }
        .status-sent { color: #16a34a; font-weight: bold; }
        .status-failed { color: #dc2626; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <div class="report-title">SMS Send History Report</div>
        <div class="report-info">Generated on {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Sent At</th>
                <th>To Phone</th>
                <th>Message</th>
                <th class="text-center">Parts</th>
                <th class="text-center">Credits</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $row)
                <tr>
                    <td>{{ $row['created_at'] }}</td>
                    <td>{{ $row['to_phone'] }}</td>
                    <td style="max-width: 250px;">{{ $row['message'] }}</td>
                    <td class="text-center">{{ $row['parts'] }}</td>
                    <td class="text-center">{{ $row['cost_credits'] }}</td>
                    <td class="{{ strtolower($row['status']) === 'sent' ? 'status-sent' : (strtolower($row['status']) === 'failed' ? 'status-failed' : '') }}">
                        {{ $row['status'] }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="text-right">Total Messages:</td>
                <td class="text-center">{{ $totals['total_messages'] }}</td>
                <td></td>
            </tr>
            <tr class="total-row">
                <td colspan="4" class="text-right">Total Credits Used:</td>
                <td class="text-center">{{ $totals['total_credits'] }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        © {{ date('Y') }} SlimPOS. All rights reserved.
    </div>
</body>
</html>
