<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attendance Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .stats {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 5px;
        }
        .stats table {
            width: 100%;
            border-collapse: collapse;
        }
        .stats td {
            padding: 5px;
            text-align: center;
        }
        .stats .label {
            font-weight: bold;
            color: #555;
        }
        .stats .value {
            font-size: 16px;
            color: #333;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table.data th {
            background-color: #333;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        table.data td {
            padding: 6px;
            border: 1px solid #ddd;
        }
        table.data tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
        .status-present {
            color: green;
            font-weight: bold;
        }
        .status-absent {
            color: red;
            font-weight: bold;
        }
        .status-late {
            color: orange;
            font-weight: bold;
        }
        .filters {
            margin: 10px 0;
            padding: 10px;
            background-color: #f0f0f0;
            border-left: 4px solid #333;
        }
    </style>
</head>
<body>
    @php use Carbon\Carbon; @endphp
    <div class="header">
        <h1>Attendance Report</h1>
        <p>Generated on: {{ Carbon::now()->format('d-m-Y H:i:s') }}</p>
    </div>

    @if(!empty($filters) && count(array_filter($filters)) > 0)
    <div class="filters">
        <strong>Applied Filters:</strong><br>
        @foreach($filters as $key => $value)
            @if(!empty($value) && $key != 'page' && $key != '_token')
                <span style="margin-right: 10px;">
                    <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}
                </span>
            @endif
        @endforeach
    </div>
    @endif

    <div class="stats">
        <table>
            <tr>
                <td>
                    <div class="label">Total Records</div>
                    <div class="value">{{ $statistics['total'] }}</div>
                </td>
                <td>
                    <div class="label">Present</div>
                    <div class="value">{{ $statistics['present'] }}</div>
                </td>
                <td>
                    <div class="label">Absent</div>
                    <div class="value">{{ $statistics['absent'] }}</div>
                </td>
                <td>
                    <div class="label">Late</div>
                    <div class="value">{{ $statistics['late'] }}</div>
                </td>
                <td>
                    <div class="label">Present %</div>
                    <div class="value">{{ $statistics['present_percentage'] }}%</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Exam</th>
                <th>Hall</th>
                <th>Student</th>
                <th>Roll No</th>
                <th>Teacher</th>
                <th>Status</th>
                <th>Marked At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $index => $att)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ Carbon::parse($att->exam_date)->format('d-m-Y') }}</td>
                <td>{{ $att->exam->subject_name ?? 'N/A' }}</td>
                <td>{{ $att->hall->hall_name ?? 'N/A' }}</td>
                <td>{{ $att->student->name ?? 'N/A' }}</td>
                <td>{{ $att->student->roll_no ?? 'N/A' }}</td>
                <td>{{ $att->teacher->name ?? 'N/A' }}</td>
                <td class="status-{{ $att->status }}">{{ ucfirst($att->status) }}</td>
                <td>{{ $att->marked_at ? Carbon::parse($att->marked_at)->format('d-m-Y H:i') : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Total Records: {{ $attendances->count() }} | Generated by: {{ Auth::user()->name ?? 'System' }}</p>
    </div>
</body>
</html>