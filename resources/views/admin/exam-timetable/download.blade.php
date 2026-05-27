<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Timetable - {{ $class->name }}</title>
    <style>
        @page {
            margin: 1.5cm 1cm 1.5cm 1cm;
            size: A4 portrait;
        }
        
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #1a237e;
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 10px;
        }
        
        .logo {
            max-width: 80px;
            max-height: 80px;
            object-fit: contain;
        }
        
        .school-name {
            font-size: 18pt;
            font-weight: bold;
            color: #1a237e;
            margin-bottom: 5px;
        }
        
        .document-title {
            font-size: 16pt;
            color: #444;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .class-info {
            font-size: 14pt;
            color: #666;
            margin-top: 5px;
        }
        
        .info-box {
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }
        
        .info-item {
            font-size: 10pt;
        }
        
        .info-label {
            font-weight: bold;
            color: #1a237e;
        }
        
        .date-header {
            background-color: #1a237e;
            color: white;
            padding: 8px 12px;
            margin: 15px 0 10px 0;
            font-weight: bold;
            font-size: 12pt;
            border-radius: 4px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9pt;
        }
        
        th {
            background-color: #2c3e50;
            color: white;
            padding: 8px 5px;
            text-align: left;
            font-weight: bold;
        }
        
        td {
            border: 1px solid #ddd;
            padding: 6px 5px;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .badge {
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: bold;
            display: inline-block;
        }
        
        .badge-final { background-color: #dc3545; color: white; }
        .badge-midterm { background-color: #ffc107; color: #333; }
        .badge-quiz { background-color: #17a2b8; color: white; }
        .badge-assignment { background-color: #6c757d; color: white; }
        
        .session-fn { background-color: #ffc107; color: #333; padding: 2px 6px; border-radius: 10px; }
        .session-an { background-color: #17a2b8; color: white; padding: 2px 6px; border-radius: 10px; }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #1a237e;
            font-size: 8pt;
            color: #666;
        }
        
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(0deg);
            opacity: 0.1;
            z-index: -1;
            pointer-events: none;
        }
        
        .watermark img {
            max-width: 350px;
            max-height: 350px;
            width: 400px;
            height: 400px;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    @php
        $logoPath = public_path('images/srmlogo1.jpg');
        if (file_exists($logoPath)) {
            $logoData = base64_encode(file_get_contents($logoPath));
            $logoMime = mime_content_type($logoPath);
            $logoBase64 = "data:{$logoMime};base64,{$logoData}";
        } else {
            $logoBase64 = '';
        }
    @endphp

    <!-- Watermark Logo -->
    @if(!empty($logoBase64))
    <div class="watermark">
        <img src="{{ $logoBase64 }}" alt="Watermark Logo">
    </div>
    @endif
    
    <!-- Header with Logo -->
    <div class="header">
        <div class="logo-container">
            @if(!empty($logoBase64))
                <img src="{{ $logoBase64 }}" alt="SRM Logo" class="logo">
            @else
                <div style="font-size: 24pt; font-weight: bold; color: #1a237e; margin-bottom: 10px;">SRM</div>
            @endif
        </div>
        
        <div class="school-name">SRM TRICHY ARTS AND SCIENCE COLLEGE</div>
        <div class="document-title">EXAMINATION TIMETABLE</div>
        <div class="class-info">{{ $class->name }} - Section {{ $class->section_name }}</div>
    </div>
    
    <!-- Info Box -->
    <div class="info-box">
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Class:</span> {{ $class->name }}
            </div>
            <div class="info-item">
                <span class="info-label">Section:</span> {{ $class->section_name }}
            </div>
            <div class="info-item">
                <span class="info-label">Current Year:</span> {{ $currentYear ?? date('Y') }}
            </div>
            <div class="info-item">
                <span class="info-label">Total Exams:</span> {{ $exams_count }}
            </div>
        </div>
    </div>
    
    <!-- Timetable -->
    @if($exams_count > 0)
        @php
            $groupedExams = $exams->groupBy(function($exam) {
                return $exam->exam_date ? \Carbon\Carbon::parse($exam->exam_date)->format('Y-m-d') : 'No Date';
            })->sortKeys();
            $dayCounter = 1;
        @endphp
        
        @foreach($groupedExams as $date => $dayExams)
            <div class="date-header">
                Day {{ $dayCounter++ }}: {{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th width="10%">Time</th>
                        <th width="20%">Subject</th>
                        <th width="12%">Exam Code</th>
                        <th width="10%">Year</th>
                        <th width="12%">Semester</th>
                        <th width="12%">Exam Type</th>
                        <th width="12%">Session</th>
                        <th width="12%">Duration</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dayExams->sortBy('exam_time') as $exam)
                        @php
                            $badgeClass = 'badge-' . $exam->exam_type;
                            $sessionClass = 'session-' . strtolower($exam->time_session);
                            $semester = $exam->subject->semester ?? null;
                            $semesterDisplay = $semester ? $semester . ($semester == 1 ? 'st' : ($semester == 2 ? 'nd' : ($semester == 3 ? 'rd' : 'th'))) : '-';
                            $currentYear = $exam->subject->classModel ? 
                                \App\Models\Student::where('class_id', $exam->subject->classModel->id)
                                    ->whereNotNull('current_year')
                                    ->value('current_year') : date('Y');
                        @endphp
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($exam->exam_time)->format('h:i A') }}</td>
                            <td><strong>{{ $exam->subject->name ?? 'N/A' }}</strong></td>
                            <td><code>{{ $exam->subject->code ?? 'N/A' }}</code></td>
                            <td>{{ $currentYear ?? date('Y') }}</td>
                            <td><span class="badge" style="background-color: #6c757d; color: white;">{{ $semesterDisplay }}</span></td>
                            <td><span class="badge {{ $badgeClass }}">{{ ucfirst($exam->exam_type) }}</span></td>
                            <td><span class="{{ $sessionClass }}">{{ $exam->time_session == 'FN' ? 'Forenoon' : 'Afternoon' }}</span></td>
                            <td>{{ $exam->duration_minutes }} mins</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    @else
        <div style="text-align: center; padding: 40px; border: 2px dashed #ccc;">
            <h3 style="color: #666;">No Exams Scheduled</h3>
            <p style="color: #888;">No examinations have been scheduled for this class yet.</p>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <div>Generated on: {{ $downloadDate }}</div>
        <div style="font-size: 7pt; color: #999; margin-top: 5px;">
            © {{ date('Y') }} SRM TRICHY ARTS AND SCIENCE COLLEGE. All rights reserved.
        </div>
    </div>
</body>
</html>