<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Attendance Sheet - {{ $allocation->hall->hall_name ?? 'Hall' }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            margin: 15px;
            line-height: 1.3;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 5px 0;
            font-size: 22px;
            color: #333;
        }
        .header h3 {
            margin: 3px 0;
            font-size: 16px;
            color: #666;
            font-weight: normal;
        }
        .header h4 {
            margin: 3px 0;
            font-size: 14px;
            color: #444;
        }
        .info-section {
            margin-bottom: 20px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 5px;
            border: none;
            font-size: 11px;
        }
        .info-table td:first-child {
            font-weight: bold;
            width: 100px;
        }
        .students-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 10px;
        }
        .students-table th {
            background: #333;
            color: white;
            padding: 6px 3px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #444;
            font-size: 10px;
        }
        .students-table td {
            padding: 5px 3px;
            border: 1px solid #ddd;
            text-align: center;
            vertical-align: middle;
        }
        .students-table tr:nth-child(even) {
            background: #f9f9f9;
        }
        .signature-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }
        .signature-box {
            text-align: center;
            width: 45%;
        }
        .signature-line {
            width: 100%;
            border-top: 1px solid #000;
            margin: 30px 0 5px;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }
        .logo {
            max-height: 60px;
            margin-bottom: 5px;
        }
        .summary-box {
            background: #e9ecef;
            border-left: 3px solid #007bff;
            padding: 8px;
            margin-bottom: 15px;
            font-size: 10px;
        }
        /* Adjust column widths for portrait */
        .students-table th:nth-child(1) { width: 4%; }  /* # */
        .students-table th:nth-child(2) { width: 10%; } /* Roll No */
        .students-table th:nth-child(3) { width: 18%; } /* Student Name */
        .students-table th:nth-child(4) { width: 6%; }  /* Class */
        .students-table th:nth-child(5) { width: 6%; }  /* Section */
        .students-table th:nth-child(6) { width: 5%; }  /* Table */
        .students-table th:nth-child(7) { width: 5%; }  /* Seat */
        .students-table th:nth-child(8) { width: 20%; } /* Signature */
        .students-table th:nth-child(9) { width: 26%; } /* Remarks */
        
        @media print {
            body {
                margin: 10px;
            }
            .header {
                border-bottom: 1px solid #000;
            }
            .students-table th {
                background: #333 !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .signature-line {
                border-top: 1px solid #000;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        @if($logo)
            <img src="{{ $logo }}" class="logo" alt="Logo">
        @endif
        <h1>ATTENDANCE SHEET</h1>
        <h3>{{ $allocation->exam->subject_name ?? 'Examination' }}</h3>
        <h4>{{ ucfirst($allocation->exam->exam_type ?? '') }} Examination</h4>
    </div>

    <div class="info-section">
        <table class="info-table">
            <tr>
                <td>Date:</td>
                <td><strong>{{ \Carbon\Carbon::parse($allocation->exam_date)->format('d F Y') }}</strong></td>

            </tr>
            <tr>
                <td>Hall:</td>
                <td><strong>{{ $allocation->hall->hall_name ?? 'N/A' }}</strong></td>

            </tr>
            <tr>
                <td>Invigilator:</td>
                <td><strong>{{ $teacher->name ?? 'N/A' }}</strong></td>
            </tr>
            <tr>
                <td>Exam Time:</td>
                <td><strong>{{ $allocation->exam->exam_time ? \Carbon\Carbon::parse($allocation->exam->exam_time)->format('h:i A') : 'N/A' }}</strong></td>
            </tr>
        </table>
    </div>

    <table class="students-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Roll No</th>
                <th>Student Name</th>
                <th>Table</th>
                <th>Seat</th>
                <th>Signature</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $item->student->roll_no ?? 'N/A' }}</strong></td>
                    <td style="text-align: left;">{{ $item->student->name ?? 'N/A' }}</td>
                    <td>{{ $item->table_number }}</td>
                    <td>{{ $item->seat_number }}</td>
                    <td></td>
                    <td></td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 20px;">
                        <em>No students allocated to this hall</em>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line"></div>
            <div><strong>Invigilator's Signature</strong></div>
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            <div><strong>Chief Superintendent's Signature</strong></div>
        </div>
    </div>

    <div class="footer">
        Generated on: {{ $date }} | System Generated Report | Page 1 of 1
    </div>
</body>
</html>