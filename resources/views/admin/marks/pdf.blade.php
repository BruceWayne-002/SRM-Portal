<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $exam->subject_name }} - Marksheet</title>

    <style>
        body {
            font-family: "Times New Roman", serif;
            margin: 30px;
            color: #333;
        }

        .marksheet {
            width: 100%;
            position: relative;
        }

        /* Watermark */
        .watermark {
            position: fixed;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.08;
            z-index: -1;
        }

        .watermark img {
            width: 420px;
            max-width: 100%;
        }

        /* Header */
        .header {
            text-align: center;
            border-bottom: 2px solid #1e3c72;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .logo {
            width: 100px;
            height: auto;
            margin-bottom: 6px;
        }

        h1 {
            font-size: 22px;
            margin: 2px 0;
            color: #1e3c72;
        }

        h3 {
            font-size: 16px;
            margin: 2px 0;
        }

        .exam-type {
            font-size: 12px;
            color: #555;
        }

        /* College Info */
        .college-info {
            text-align: center;
            font-size: 13px;
            background: #f3f6fa;
            padding: 8px;
            margin-bottom: 15px;
            border-left: 4px solid #1e3c72;
            border-right: 4px solid #1e3c72;
        }

        /* Exam Info Table */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .info-table td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: center;
        }

        .info-label {
            font-size: 10px;
            color: #555;
            text-transform: uppercase;
        }

        .info-value {
            font-size: 13px;
            font-weight: bold;
            color: #1e3c72;
        }

        /* Marks Distribution */
        .marks-dist {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .dist-card {
            flex: 1;
            margin: 0 5px;
            padding: 10px;
            border-radius: 5px;
            color: white;
            text-align: center;
        }

        .dist-total { background: #1e3c72; }
        .dist-internal { background: #2e7d32; }
        .dist-external { background: #b76e1e; }

        .dist-label {
            font-size: 11px;
            opacity: 0.9;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .dist-value {
            font-size: 18px;
            font-weight: bold;
        }

        .dist-sub {
            font-size: 10px;
            opacity: 0.8;
        }

        /* Statistics */
        .stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .stat-card {
            flex: 1;
            margin: 0 5px;
            padding: 8px;
            background: #f5f5f5;
            border: 1px solid #ddd;
            text-align: center;
        }

        .stat-label {
            font-size: 10px;
            color: #666;
        }

        .stat-value {
            font-size: 16px;
            font-weight: bold;
            color: #1e3c72;
        }

        /* Main Table */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-bottom: 20px;
        }

        th {
            background: #1e3c72;
            color: #fff;
            padding: 6px;
            border: 1px solid #1e3c72;
        }

        td {
            border: 1px solid #ccc;
            padding: 5px;
            text-align: center;
        }

        td:nth-child(3) {
            text-align: left;
        }

        .pass-row {
            background: #f0fff4;
        }

        .fail-row {
            background: #fbeaeb;
        }

        .grade-badge {
            display: inline-block;
            padding: 2px 8px;
            background: #6c757d;
            color: white;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
        }

        /* Summary */
        .summary {
            background: #e3f2fd;
            padding: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #1e3c72;
            font-size: 12px;
        }

        /* Signatures */
        .signature-table {
            width: 100%;
            margin-top: 45px;
            text-align: center;
        }

        .signature-line {
            width: 150px;
            border-top: 1.5px solid #000;
            margin: 0 auto 5px;
        }

        .signature-label {
            font-size: 12px;
            font-weight: bold;
        }

        /* Footer */
        .footer {
            width: 100%;
            font-size: 10px;
            margin-top: 20px;
        }

        .footer td {
            border: none;
        }

        .right {
            text-align: right;
        }
    </style>
</head>
<body>

<div class="marksheet">

    <!-- Watermark -->
    @if(!empty($logoBase64))
    <div class="watermark">
        <img src="{{ $logoBase64 }}" alt="Watermark">
    </div>
    @endif

    <!-- Header -->
    <div class="header">
        @if(!empty($logoBase64))
            <img src="{{ $logoBase64 }}" class="logo">
        @endif
        <h1>SRM TRICHY ARTS & SCIENCE COLLEGE</h1>
        <h3>STATEMENT OF MARKS</h3>
        <div class="exam-type">
            {{ strtoupper($exam->exam_type) }} EXAMINATION – {{ \Carbon\Carbon::parse($exam->exam_date)->format('Y') }}
        </div>
    </div>

    <!-- Exam Info -->
    <table class="info-table">
        <tr>
            <td>
                <div class="info-label">Exam Date</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($exam->exam_date)->format('d M Y') }}</div>
            </td>
            <td>
                <div class="info-label">Time</div>
                <div class="info-value">{{ $exam->formatted_time ?? \Carbon\Carbon::parse($exam->exam_time)->format('h:i A') }} ({{ $exam->time_session }})</div>
            </td>
            <td>
                <div class="info-label">Subject</div>
                <div class="info-value">{{ $exam->subject->name ?? $exam->subject_name }} ({{ $exam->subject->code ?? $exam->subject_code }})</div>
            </td>
            <td>
                <div class="info-label">Class</div>
                <div class="info-value">{{ $exam->class_name ?? ($exam->subject->classModel->full_name ?? 'N/A') }}</div>
            </td>
        </tr>
    </table>
    <!-- Marks Table -->
    <table>
        <thead>
        <tr>
            <th width="5%">#</th>
            <th width="10%">Roll No</th>
            <th width="25%">Student Name</th>
            <th width="10%">Internal</th>
            <th width="10%">External</th>
            <th width="10%">Total</th>
            <th width="10%">Percentage</th>
            <th width="8%">Grade</th>
            <th width="12%">Result</th>
        </tr>
        </thead>

        <tbody>
        @forelse($marks as $index => $mark)
            @php
                $totalObtained = $mark->total_marks_obtained ?? 0;
                $percentage = $mark->percentage ?? 0;
                $result = $percentage >= 35 ? 'PASS' : 'FAIL';
                $rowClass = $result == 'PASS' ? 'pass-row' : 'fail-row';
            @endphp

            <tr class="{{ $rowClass }}">
                <td>{{ $index + 1 }}</td>
                <td><strong>{{ $mark->student->roll_no ?? 'N/A' }}</strong></td>
                <td>{{ $mark->student->name ?? 'N/A' }}</td>
                <td>{{ $mark->internal_marks ?? '-' }}</td>
                <td>{{ $mark->external_marks ?? '-' }}</td>
                <td><strong>{{ $totalObtained }}</strong></td>
                <td><strong>{{ number_format($percentage, 2) }}%</strong></td>
                <td>
                    <span class="grade-badge">{{ $mark->grade ?? 'F' }}</span>
                </td>
                <td><strong style="color: {{ $result == 'PASS' ? '#2e7d32' : '#c62828' }}">{{ $result }}</strong></td>
            </tr>
        @empty
            <tr>
                <td colspan="9" style="text-align: center; padding: 20px;">
                    <strong>No marks found for this exam.</strong>
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <!-- Summary -->
    @if($marks->isNotEmpty())
    <div class="summary">
        <strong>SUMMARY REPORT</strong><br>
        Total Students: {{ $statistics['total_students'] }} | 
        Marks Entered: {{ $statistics['marks_entered'] }} | 
        Passed: {{ $statistics['passed'] }} | 
        Failed: {{ $statistics['failed'] }} | 
        Pass Percentage: {{ number_format($statistics['pass_percentage'] ?? 0, 2) }}% |
        Overall Average: {{ number_format($statistics['average'], 2) }}%
    </div>
    @endif

    <!-- Signatures -->
    <table class="signature-table">
        <tr>
            <td>
                <div class="signature-line"></div>
                <div class="signature-label">Class Teacher</div>
            </td>
            <td>
                <div class="signature-line"></div>
                <div class="signature-label">Controller of Examinations</div>
            </td>
            <td>
                <div class="signature-line"></div>
                <div class="signature-label">Principal</div>
            </td>
        </tr>
    </table>

    <!-- Footer -->
    <table class="footer">
        <tr>
            <td class="left">
                <strong>SRM College of Education</strong> • Established 1995
            </td>
            <td class="right">
                Generated: {{ now()->format('d M Y, h:i A') }}
            </td>
        </tr>
    </table>

</div>

</body>
</html>