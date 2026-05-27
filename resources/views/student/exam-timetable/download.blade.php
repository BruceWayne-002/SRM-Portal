<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Upcoming Exams - {{ $class->name }}</title>
    <style>
        /* A4 Size Setup */
        @page {
            size: A4;
            margin: 1.5cm;
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            width: 100%;
            font-size: 11pt;
            line-height: 1.3;
            background: white;
            color: black;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #28a745;
            padding-bottom: 15px;
            position: relative;
        }

        .logo-container {
            margin-bottom: 10px;
        }

        .logo {
            width: 80px;
            height: auto;
            max-height: 80px;
            object-fit: contain;
        }

        .logo-placeholder {
            width: 80px;
            height: 80px;
            background: #28a745;
            border-radius: 50%;
            margin: 0 auto 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
            font-weight: bold;
        }

        h1 {
            color: #1e3c72;
            font-size: 18pt;
            margin: 3px 0;
            font-weight: bold;
        }

        h2 {
            color: #333;
            font-size: 14pt;
            margin: 3px 0;
            font-weight: normal;
        }

        .upcoming-badge {
            background: #28a745;
            color: white;
            padding: 5px 15px;
            border-radius: 25px;
            display: inline-block;
            font-size: 12pt;
            margin-top: 10px;
        }

        .student-info {
            background: #f5f5f5;
            padding: 8px;
            margin-bottom: 15px;
            border-left: 4px solid #28a745;
            font-size: 10pt;
        }

        .student-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .student-info td {
            padding: 4px;
            border: none;
        }

        .stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 10px;
        }

        .stat-box {
            flex: 1;
            text-align: center;
            padding: 8px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }

        .stat-box h3 {
            margin: 0;
            color: #28a745;
            font-size: 16pt;
        }

        .stat-box div {
            font-size: 9pt;
            color: #666;
        }

        .date-header {
            background: #28a745;
            color: white;
            padding: 8px 10px;
            margin: 15px 0 8px 0;
            font-weight: bold;
            font-size: 11pt;
            border-radius: 3px;
            page-break-inside: avoid;
        }

        .exam-type {
            background: #e9ecef;
            padding: 6px 10px;
            margin: 5px 0 5px 0;
            font-weight: bold;
            color: #495057;
            font-size: 10pt;
            border-left: 3px solid #28a745;
            page-break-inside: avoid;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 10pt;
            page-break-inside: avoid;
        }

        th {
            background: #6c757d;
            color: white;
            padding: 8px 6px;
            text-align: left;
            font-size: 10pt;
            font-weight: bold;
        }

        td {
            border: 1px solid #dee2e6;
            padding: 6px;
            font-size: 10pt;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .footer {
            text-align: right;
            margin-top: 25px;
            padding-top: 10px;
            font-size: 8pt;
            color: #6c757d;
            border-top: 1px dashed #dee2e6;
            page-break-inside: avoid;
        }

        .days-remaining {
            background: #ffc107;
            color: #333;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 8pt;
            font-weight: bold;
            margin-left: 10px;
        }

        .note-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
            padding: 10px;
            border-radius: 5px;
            margin: 15px 0;
            font-size: 9pt;
            text-align: center;
        }

        .empty-state {
            text-align: center;
            padding: 50px;
            background: #f8f9fa;
            border-radius: 10px;
            margin-top: 20px;
        }

        .empty-state i {
            font-size: 48px;
            color: #28a745;
            margin-bottom: 15px;
        }

        .empty-state h3 {
            color: #28a745;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #666;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

@php
// Check multiple possible logo locations
$logoPaths = [
    public_path('images/srmlogo1.jpg'),
    public_path('images/college-logo.png'),
    public_path('images/logo.jpg'),
    public_path('images/logo.png'),
    public_path('assets/images/logo.jpg'),
    public_path('assets/images/logo.png'),
    public_path('storage/logo/college-logo.png'),
];

$logoExists = false;
$logoPath = '';

foreach ($logoPaths as $path) {
    if (file_exists($path)) {
        $logoExists = true;
        $logoPath = $path;
        break;
    }
}

// Calculate if we have any upcoming exams
$hasUpcomingExams = $exams_count > 0;
$today = now()->toDateString();
@endphp

    <div class="page-content">
        <div class="header">
            <div class="logo-container">
                @if($logoExists)
                    <img src="{{ $logoPath }}" class="logo" alt="College Logo">
                @else
                    <div class="logo-placeholder">
                        SRM
                    </div>
                @endif
            </div>
            <h1>SRM TRICHY ARTS & SCIENCE COLLEGE</h1>
            <h2>EXAM SCHEDULE - {{ $class->name }}</h2>
            <div class="upcoming-badge">
                <i class="fas fa-clock"></i>  SCHEDULED EXAMS
            </div>
        </div>

        <div class="student-info">
            <table>
                <tr>
                    <td style="width: 20%;"><strong>Student Name:</strong></td>
                    <td style="width: 30%;">{{ $student_name }}</td>
                    <td style="width: 20%;"><strong>Roll No:</strong></td>
                    <td style="width: 30%;">{{ $student_roll_number }}</td>
                </tr>
                <tr>
                    <td><strong>Class:</strong></td>
                    <td>{{ $class->name }}</td>
                    <td><strong>Generated On:</strong></td>
                    <td>{{ $downloadDate }}</td>
                </tr>
            </table>
        </div>


        @if($hasUpcomingExams)


            @foreach($groupedByDate as $date => $typeGroups)
                @php
                    $carbonDate = \Carbon\Carbon::parse($date);
                    $daysRemaining = $carbonDate->diffInDays(now());
                    $isToday = $date == $today;
                @endphp
                
                <div class="date-header">
                    {{ $carbonDate->format('l, d F Y') }}
                </div>

                @foreach($typeGroups as $examType => $typeExams)
                    <div class="exam-type">
                        <i class="fas fa-clock" style="margin-right: 5px;"></i>
                        {{ ucfirst($examType) }} Session
                        <span style="float: right; font-weight: normal;">{{ count($typeExams) }} Exam(s)</span>
                    </div>
                    
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 15%;">Time</th>
                                    <th style="width: 35%;">Subject</th>
                                    <th style="width: 15%;">Code</th>
                                    <th style="width: 15%;">Duration</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($typeExams as $exam)
                                    <tr>
                                        <td>
                                            <i class="far fa-clock" style="color: #28a745; margin-right: 3px;"></i>
                                            {{ $exam->exam_time ? \Carbon\Carbon::parse($exam->exam_time)->format('h:i A') : 'N/A' }}
                                        </td>
                                        <td>
                                            <strong>{{ $exam->subject->name ?? 'N/A' }}</strong>
                                        </td>
                                        <td>{{ $exam->subject->code ?? 'N/A' }}</td>
                                        <td>{{ $exam->duration ?? '3 Hours' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            @endforeach
        @else
            <div class="empty-state">
                <i class="fas fa-calendar-check"></i>
                <h3>No Upcoming Exams</h3>
                <p>You have no exams scheduled for the upcoming days.</p>
                <p style="font-size: 9pt; margin-top: 15px;">Check back later for updates to your exam schedule.</p>
            </div>
        @endif


    </div>

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</body>
</html>