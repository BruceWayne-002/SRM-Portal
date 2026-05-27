<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Exam Timetable - {{ $institution_name }}</title>
    <style>
        @page {
            margin: 1.5cm 1cm 1.5cm 1cm;
            size: A4 landscape;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 9px;
            margin: 0;
            padding: 0;
            line-height: 1.3;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            position: relative;
        }
        
        .logo-container {
            position: absolute;
            top: 0;
            left: 0;
        }
        
        .logo {
            max-height: 50px;
            width: auto;
        }
        
        .header h1 {
            margin: 5px 0 2px;
            font-size: 20px;
            color: #333;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .header h2 {
            margin: 2px 0;
            font-size: 16px;
            color: #555;
            font-weight: normal;
        }
        
        .header h3 {
            margin: 2px 0;
            font-size: 14px;
            color: #666;
            font-weight: normal;
        }
        
        .institution-name {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .summary-cards {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .summary-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 8px 12px;
            width: 15%;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .summary-card .label {
            font-size: 8px;
            color: #6c757d;
            text-transform: uppercase;
        }
        
        .summary-card .value {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
        }
        
        .filter-info {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 8px 15px;
            margin-bottom: 20px;
            font-size: 10px;
            border-radius: 0 4px 4px 0;
        }
        
        .timetable {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed;
        }
        
        .timetable th {
            background: #2c3e50;
            color: white;
            padding: 8px 5px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #1a252f;
            font-size: 9px;
        }
        
        .timetable td {
            padding: 6px 5px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        
        .timetable tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        .date-header {
            background: #e9ecef;
            font-weight: bold;
        }
        
        .date-header td {
            background: #e9ecef;
            color: #2c3e50;
            font-size: 10px;
            padding: 8px 10px;
        }
        
        .subject-code {
            font-size: 8px;
            color: #6c757d;
        }
        
        .teacher-name {
            font-size: 8px;
            color: #28a745;
        }
        
        .class-badge {
            display: inline-block;
            padding: 2px 4px;
            background: #17a2b8;
            color: white;
            border-radius: 3px;
            font-size: 7px;
            margin-right: 2px;
        }
        
        .semester-badge {
            display: inline-block;
            padding: 2px 4px;
            background: #ffc107;
            color: #333;
            border-radius: 3px;
            font-size: 7px;
        }
        
        .summary-section {
            margin-top: 25px;
            page-break-inside: avoid;
        }
        
        .summary-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #2c3e50;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 5px;
        }
        
        .class-summary {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .class-summary th {
            background: #6c757d;
            color: white;
            padding: 6px;
            text-align: center;
            font-size: 8px;
            border: 1px solid #5a6268;
        }
        
        .class-summary td {
            padding: 5px;
            border: 1px solid #dee2e6;
            font-size: 8px;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
            padding-top: 5px;
            margin-top: 20px;
        }
        
        .page-number:before {
            content: "Page " counter(page);
        }
        
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .timetable th {
                background: #2c3e50 !important;
                color: white !important;
            }
            
            .date-header td {
                background: #e9ecef !important;
            }
            
            .class-summary th {
                background: #6c757d !important;
                color: white !important;
            }
            
            .summary-card {
                background: #f8f9fa !important;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        @if(isset($logo) && $logo && file_exists($logo))
            <div class="logo-container">
                <img src="{{ $logo }}" class="logo" alt="Logo">
            </div>
        @endif
        <div class="institution-name">{{ $institution_name }}</div>
        <h1>COMPLETE EXAMINATION TIMETABLE</h1>
        <h2>Academic Year: {{ now()->format('Y') }}-{{ now()->format('Y')+1 }}</h2>
        <h3>All Classes • {{ $totalExams }} Exams • {{ $examDays }} Days</h3>
    </div>
    
    @if(isset($selectedSemester) && $selectedSemester)
        <div class="filter-info">
            <strong>Filtered by:</strong> Semester {{ $selectedSemester }}
        </div>
    @endif

    
    <table class="timetable">
        <thead>
            <tr>
                <th style="width: 10%">Date</th>
                <th style="width: 8%">Day</th>
                <th style="width: 8%">Time</th>
                <th style="width: 12%">Class</th>
                <th style="width: 25%">Subject</th>
                <th style="width: 10%">Code</th>
                <th style="width: 8%">Semester</th>
                <th style="width: 19%">Teacher</th>
            </tr>
        </thead>
        <tbody>
            @forelse($allGroupedExams as $date => $examsOnDate)
                <tr class="date-header">
                    <td colspan="8">
                        <strong>{{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}</strong>
                    </td>
                </tr>
                @foreach($examsOnDate as $exam)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($exam->exam_date)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($exam->exam_date)->format('D') }}</td>
                        <td>{{ $exam->exam_time ? \Carbon\Carbon::parse($exam->exam_time)->format('h:i A') : 'N/A' }}</td>
                        <td>
                            @if($exam->subject && $exam->subject->classModel)
                                {{ $exam->subject->classModel->name }} 
                                @if($exam->subject->classModel->section_name)
                                    - {{ $exam->subject->classModel->section_name }}
                                @endif
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            <strong>{{ $exam->subject_name ?? 'N/A' }}</strong>
                        </td>
                        <td>
                            @if($exam->subject)
                                <span class="subject-code">{{ $exam->subject->code ?? 'N/A' }}</span>
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            @if($exam->subject && $exam->subject->semester)
                                <span class="semester-badge">Sem {{ $exam->subject->semester }}</span>
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            @if($exam->subject && $exam->subject->teacher)
                                <span class="teacher-name">{{ $exam->subject->teacher->name ?? 'N/A' }}</span>
                            @else
                                N/A
                            @endif
                        </td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 30px;">
                        <em>No exams scheduled for the selected criteria</em>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    @if(count($examsByClass) > 0)
    <div class="summary-section">
        <div class="summary-title">Class-wise Examination Summary</div>
        <table class="class-summary">
            <thead>
                <tr>
                    <th>Class</th>
                    <th>Section</th>
                    <th>Current Year</th>
                    <th>Total Subjects</th>
                    <th>Exams Scheduled</th>
                    <th>Semesters</th>
                </tr>
            </thead>
            <tbody>
                @forelse($examsByClass as $item)
                    <tr>
                        <td><strong>{{ $item['class']->name }}</strong></td>
                        <td>{{ $item['class']->section_name ?? 'N/A' }}</td>
                        <td>{{ $item['current_year'] ?? 'N/A' }}</td>
                        <td>{{ $item['subjects_count'] }}</td>
                        <td>{{ $item['exams_count'] }}</td>
                        <td>
                            @foreach($item['semesters'] as $sem)
                                <span class="semester-badge">Sem {{ $sem }}</span>
                            @endforeach
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center;">No class data available</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif
    
    <div class="footer">
        <div>Generated on: {{ $downloadDate }} | {{ $institution_name }} | System Generated Document</div>
        <div class="page-number"></div>
    </div>
</body>
</html>