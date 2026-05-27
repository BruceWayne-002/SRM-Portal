<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Semester {{ $semester ?? '' }} {{ $examType ?? '' }} Marksheet</title>
    <style>
        body {
            font-family: "Times New Roman", serif;
            margin: 30px;
            font-size: 12px;
        }
        .marksheet {
            width: 100%;
            position: relative;
        }
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
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #1e3c72;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .logo {
            width: 100px;
            margin-bottom: 6px;
        }
        h1 {
            font-size: 20px;
            margin: 2px 0;
            color: #1e3c72;
        }
        h3 {
            font-size: 16px;
            margin: 2px 0;
        }
        h4 {
            font-size: 14px;
            margin: 10px 0;
            color: #1e3c72;
        }
        .exam-type {
            font-size: 14px;
            color: #555;
            font-weight: bold;
        }
        .student-info {
            text-align: left;
            font-size: 12px;
            background: #f3f6fa;
            padding: 8px;
            margin-bottom: 15px;
            border-left: 4px solid #1e3c72;
            border-right: 4px solid #1e3c72;
        }
        .student-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .student-info td {
            padding: 5px;
            border: none;
        }
        .stats-grid {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .stat-card {
            flex: 1;
            padding: 10px;
            text-align: center;
            border-radius: 5px;
            font-weight: bold;
            color: white;
            font-size: 12px;
        }
        .total-exams { background-color: #17a2b8; }
        .avg-percentage { background-color: #28a745; }
        .total-marks { background-color: #ffc107; color: black; }
        .subjects { background-color: #007bff; }
        .subject-summary {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
        }
        .subject-summary th {
            background-color: #6c757d;
            color: white;
            padding: 6px;
            border: 1px solid #6c757d;
        }
        .subject-summary td {
            border: 1px solid #ccc;
            padding: 5px;
            text-align: center;
        }
        .progress {
            width: 100%;
            background-color: #f0f0f0;
            border-radius: 3px;
            height: 18px;
            overflow: hidden;
        }
        .progress-bar {
            height: 18px;
            line-height: 18px;
            color: white;
            text-align: center;
            font-size: 10px;
        }
        .bg-success { background-color: #28a745; }
        .bg-warning { background-color: #ffc107; color: black; }
        .bg-danger { background-color: #dc3545; }
        .marks-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-bottom: 20px;
        }
        .marks-table th {
            background: #1e3c72;
            color: #fff;
            padding: 6px;
            border: 1px solid #1e3c72;
            text-align: center;
        }
        .marks-table td {
            border: 1px solid #ccc;
            padding: 5px;
            text-align: center;
        }
        .marks-table td:nth-child(3) {
            text-align: left;
        }
        .pass-row {
            background: #f0fff4;
        }
        .fail-row {
            background: #fbeaeb;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        .badge-danger {
            background-color: #dc3545;
            color: white;
        }
        .badge-warning {
            background-color: #ffc107;
            color: black;
        }
        .badge-info {
            background-color: #17a2b8;
            color: white;
        }
        .signature-table {
            width: 100%;
            margin-top: 30px;
            text-align: center;
            border-collapse: collapse;
        }
        .signature-table td {
            border: none;
            padding: 10px;
        }
        .signature-line {
            width: 150px;
            border-top: 1.5px solid #000;
            margin: 0 auto 5px;
        }
        .signature-label {
            font-size: 11px;
            font-weight: bold;
        }
        .footer {
            width: 100%;
            font-size: 9px;
            margin-top: 15px;
        }
        .footer td {
            border: none;
        }
        .right {
            text-align: right;
        }
        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
    </style>
</head>
<body>

@php
// Helper function to format numbers without .00
if (!function_exists('formatNumber')) {
    function formatNumber($number) {
        if (is_numeric($number)) {
            if (floor($number) == $number) {
                return (string) floor($number);
            } else {
                return number_format($number, 2);
            }
        }
        return $number;
    }
}

// Helper function to calculate grade
if (!function_exists('calculateGrade')) {
    function calculateGrade($percentage) {
        if ($percentage >= 90) return ['A+', 'success'];
        if ($percentage >= 80) return ['A', 'success'];
        if ($percentage >= 70) return ['B+', 'info'];
        if ($percentage >= 60) return ['B', 'info'];
        if ($percentage >= 50) return ['C', 'warning'];
        if ($percentage >= 40) return ['D', 'warning'];
        return ['F', 'danger'];
    }
}
@endphp

<div class="marksheet">
    <!-- Watermark -->
    <div class="watermark">
        @if(isset($logoBase64) && $logoBase64)
            <img src="{{ $logoBase64 }}" alt="College Logo">
        @else
            <div style="width:420px;height:420px;background:#f0f0f0;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                <span style="font-size:60px;color:#ccc;">SRM</span>
            </div>
        @endif
    </div>

    <!-- Header -->
    <div class="header">
        @if(isset($logoBase64) && $logoBase64)
            <img src="{{ $logoBase64 }}" class="logo" alt="College Logo">
        @endif
        <h1>SRM TRICHY ARTS & SCIENCE COLLEGE</h1>
        <h3>STATEMENT OF MARKS</h3>
        <div class="exam-type">
            {{ strtoupper($examType ?? '') }} EXAMINATION – SEMESTER {{ $semester ?? '' }}
        </div>
    </div>

    <!-- Student Info -->
    <div class="student-info">
        <table>
            <tr>
                <td width="50%"><strong>Student Name:</strong> {{ $student->name ?? 'N/A' }}</td>
                <td width="50%"><strong>Roll No:</strong> {{ $student->roll_no ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>
                    <strong>Class:</strong> {{ $student->class_name ?? 'N/A' }} - {{ $student->section ?? 'N/A' }}
                </td>
                <td><strong>Semester:</strong> {{ $semester ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <h4>Detailed Marks - Semester {{ $semester ?? '' }} ({{ ucfirst($examType ?? '') }} Examination)</h4>
    
    <!-- Marks Table -->
    <table class="marks-table">
        <thead>
        <tr>
            <th width="5%">S.No</th>
            <th width="10%">Code</th>
            <th width="25%">Subject Name</th>
            <th width="8%">Int.</th>
            <th width="8%">Ext.</th>
            <th width="10%">Total</th>
            <th width="8%">%</th>
            <th width="8%">Grade</th>
            <th width="8%">Status</th>
        </tr>
        </thead>

        <tbody>
        @forelse($marks as $index => $mark)
            @php
                $internal = $mark->internal_marks ?? 0;
                $external = $mark->external_marks ?? 0;
                $total = $mark->marks_obtained ?? ($internal + $external);
                $maxMarks = $mark->total_marks ?? 100;
                $percentage = $maxMarks > 0 ? round(($total / $maxMarks) * 100, 2) : 0;
                
                list($grade, $gradeClass) = calculateGrade($percentage);
                
                $isPassed = $percentage >= 40;
                $status = $isPassed ? 'PASS' : 'FAIL';
                $statusClass = $isPassed ? 'success' : 'danger';
            @endphp

            <tr class="{{ $isPassed ? 'pass-row' : 'fail-row' }}">
                <td>{{ $index + 1 }}</td>
                <td>{{ $mark->exam->subject->code ?? $mark->exam->subject_code ?? 'N/A' }}</td>
                <td>{{ $mark->exam->subject->name ?? $mark->exam->title ?? 'N/A' }}</td>
                <td>{{ formatNumber($internal) }}</td>
                <td>{{ formatNumber($external) }}</td>
                <td><strong>{{ formatNumber($total) }}/{{ formatNumber($maxMarks) }}</strong></td>
                <td>{{ formatNumber($percentage) }}%</td>
                <td>
                    <span class="badge badge-{{ $gradeClass }}">{{ $grade }}</span>
                </td>
                <td>
                    <span class="badge badge-{{ $statusClass }}">{{ $status }}</span>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" style="text-align:center; padding:20px;">
                    <strong>No records found for the selected criteria</strong>
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <!-- Signatures -->
    <table class="signature-table">
        <tr>

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
            <td width="50%">Generated on: {{ $date ?? now()->format('d-m-Y H:i:s') }}</td>
            <td width="50%" class="right">This is a computer generated statement</td>
        </tr>
    </table>

</div>
</body>
</html>