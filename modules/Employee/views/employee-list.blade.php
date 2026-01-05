<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee List</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #fafafa;
        }
        .print-date {
            text-align: right;
            font-size: 10px;
            color: #999;
            margin-top: 20px;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Employee List</h1>
        @if(isset($report['header']['active']))
            <p>Status: {{ $report['header']['active'] }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>SL</th>
                <th>Code</th>
                <th>Name</th>
                <th>Designation</th>
                <th>Department</th>
                <th>Area</th>
                <th>Type</th>
                <th>Joining Date</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($report['data'] ?? [] as $index => $employee)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $employee['code'] ?? '-' }}</td>
                    <td>{{ $employee['name'] ?? '-' }}</td>
                    <td>{{ $employee['designation'] ?? '-' }}</td>
                    <td>{{ $employee['department'] ?? '-' }}</td>
                    <td>{{ $employee['area'] ?? '-' }}</td>
                    <td>{{ $employee['type'] ?? '-' }}</td>
                    <td>{{ $employee['joining_date'] ? date('d/m/Y', strtotime($employee['joining_date'])) : '-' }}</td>
                    <td>{{ $employee['phone'] ?? '-' }}</td>
                    <td>{{ $employee['email'] ?? '-' }}</td>
                    <td>{{ $employee['is_active'] ? 'Active' : 'Inactive' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" style="text-align: center; padding: 20px;">No employees found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p class="print-date">Printed on: {{ now()->format('d/m/Y H:i:s') }}</p>

    <script>
        // Auto-trigger print dialog on page load
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
