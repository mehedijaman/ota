<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>SMS Usage Summary Report</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10pt; line-height: 1.4; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .report-title { font-size: 18pt; font-weight: bold; color: #2563eb; margin-bottom: 5px; }
        .report-info { font-size: 9pt; color: #666; }
        .section-title { font-size: 12pt; font-weight: bold; margin-top: 25px; margin-bottom: 10px; color: #1e293b; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #f8fafc; color: #475569; text-align: left; padding: 10px; font-size: 8pt; text-transform: uppercase; border: 1px solid #e2e8f0; }
        td { padding: 8px 10px; border: 1px solid #e2e8f0; font-size: 9pt; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer { margin-top: 50px; text-align: center; font-size: 8pt; color: #94a3b8; }
        .summary-card { background-color: #eff6ff; padding: 15px; border: 1px solid #bfdbfe; border-radius: 8px; text-align: center; margin-top: 30px; }
        .card-label { font-size: 9pt; color: #1e40af; text-transform: uppercase; }
        .card-value { font-size: 20pt; font-weight: bold; color: #1e3a8a; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="report-title">SMS Usage Summary Report</div>
        <div class="report-info">Generated on {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <div class="section-title">Status Summary</div>
    <table>
        <thead>
            <tr>
                <th>Status</th>
                <th class="text-center">Count</th>
                <th class="text-center">Credits Used</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData['status_summary'] as $row)
                <tr>
                    <td style="text-transform: capitalize;">{{ $row['status'] }}</td>
                    <td class="text-center">{{ $row['count'] }}</td>
                    <td class="text-center">{{ $row['credits'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Type Summary</div>
    <table>
        <thead>
            <tr>
                <th>Type</th>
                <th class="text-center">Count</th>
                <th class="text-center">Credits Used</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData['type_summary'] as $row)
                <tr>
                    <td style="text-transform: capitalize;">{{ $row['type'] }}</td>
                    <td class="text-center">{{ $row['count'] }}</td>
                    <td class="text-center">{{ $row['credits'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table style="width: 100%; border: none; margin-top: 30px;">
        <tr>
            <td style="width: 50%; border: none; padding-right: 15px;">
                <div class="summary-card">
                    <div class="card-label">Total SMS Messages</div>
                    <div class="card-value">{{ $totals['total_messages'] }}</div>
                </div>
            </td>
            <td style="width: 50%; border: none; padding-left: 15px;">
                <div class="summary-card">
                    <div class="card-label">Total Credits Used</div>
                    <div class="card-value">{{ $totals['total_credits'] }}</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="footer">
        © {{ date('Y') }} SlimPOS. All rights reserved.
    </div>
</body>
</html>
