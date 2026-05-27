{{-- resources/views/admin/marks/download.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $exam->subject_name }} - Marksheet - SRM College</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            background: #e6e9ef;
            padding: 20px;
            display: flex;
            justify-content: center;
        }
        
        .container {
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
        }
        
        /* Action Buttons - Screen Only */
        .actions {
            margin-bottom: 20px;
            text-align: right;
            position: sticky;
            top: 20px;
            z-index: 1000;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 25px;
            margin-left: 10px;
            border: none;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            font-family: 'Segoe UI', sans-serif;
        }
        
        .btn-primary {
            background: #1e3c72;
            color: white;
        }
        
        .btn-primary:hover {
            background: #2a5298;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #545b62;
            transform: translateY(-2px);
        }
        
        /* Main Marksheet - A4 Size Optimized */
        .marksheet {
            background: white;
            padding: 0.5in 0.3in;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            border: 1px solid #d0d0d0;
            min-height: 29.7cm; /* A4 height */
            width: 100%;
            margin: 0 auto;
            position: relative;
        }
        
        /* Header with Logo - Fixed Position */
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #1e3c72;
        }
        
        .logo-container {
            flex: 0 0 120px;
            text-align: left;
        }
        
        .brand.logo {
            max-width: 110px;
            max-height: 110px;
            object-fit: contain;
            border-radius: 8px;
        }
        
        .title-container {
            flex: 1;
            text-align: center;
        }
        
        .title-container h1 {
            color: #1e3c72;
            font-size: 32px;
            margin-bottom: 5px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-family: 'Times New Roman', Times, serif;
        }
        
        .title-container h3 {
            color: #2a5298;
            font-weight: 600;
            font-size: 18px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        
        .title-container .exam-type {
            color: #6c757d;
            font-size: 16px;
            font-style: italic;
        }
        
        .date-container {
            flex: 0 0 150px;
            text-align: right;
            font-size: 14px;
            color: #495057;
        }
        
        .date-container div {
            margin-bottom: 5px;
        }
        
        .date-container strong {
            color: #1e3c72;
        }
        
        /* College Info */
        .college-info {
            background: #f0f4f8;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            text-align: center;
            border-left: 5px solid #1e3c72;
            border-right: 5px solid #1e3c72;
        }
        
        .college-info h2 {
            font-size: 24px;
            color: #1e3c72;
            margin-bottom: 5px;
            font-weight: 700;
        }
        
        .college-info p {
            font-size: 15px;
            color: #495057;
            font-style: italic;
        }
        
        /* Exam Info Grid */
        .exam-info {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .info-card {
            background: #f8f9fa;
            padding: 12px 15px;
            border-radius: 6px;
            text-align: center;
            border: 1px solid #dee2e6;
        }
        
        .info-card .label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .info-card .value {
            font-size: 18px;
            font-weight: 700;
            color: #1e3c72;
        }
        
        /* Marks Distribution */
        .marks-dist {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .dist-card {
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            color: white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .dist-card.total {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
        }
        
        .dist-card.internal {
            background: linear-gradient(135deg, #2e7d32, #4caf50);
        }
        
        .dist-card.external {
            background: linear-gradient(135deg, #b76e1e, #ff9800);
        }
        
        .dist-card .label {
            font-size: 14px;
            opacity: 0.95;
            margin-bottom: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .dist-card .value {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 5px;
        }
        
        .dist-card .sub {
            font-size: 13px;
            opacity: 0.9;
            font-weight: 500;
        }
        
        /* Statistics Cards */
        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            border: 1px solid #dee2e6;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .stat-card .label {
            font-size: 13px;
            color: #6c757d;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .stat-card .value {
            font-size: 28px;
            font-weight: 800;
        }
        
        .stat-card.total .value { color: #1e3c72; }
        .stat-card.passed .value { color: #2e7d32; }
        .stat-card.failed .value { color: #c62828; }
        .stat-card.average .value { color: #b76e1e; }
        
        /* Table Styles - Optimized for A4 */
        .table-container {
            overflow-x: auto;
            margin: 25px 0;
            border: 2px solid #1e3c72;
            border-radius: 8px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        
        th {
            background: #1e3c72;
            color: white;
            padding: 12px 8px;
            font-weight: 600;
            text-align: left;
            white-space: nowrap;
            font-size: 14px;
        }
        
        td {
            padding: 10px 8px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        tr:last-child td {
            border-bottom: none;
        }
        
        tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .pass-row {
            background-color: #e8f5e9 !important;
        }
        
        .fail-row {
            background-color: #ffebee !important;
        }
        
        .grade-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-align: center;
            min-width: 35px;
        }
        
        .grade-a { background: #2e7d32; color: white; }
        .grade-b { background: #1565c0; color: white; }
        .grade-c { background: #ff8f00; color: white; }
        .grade-d { background: #ef6c00; color: white; }
        .grade-f { background: #c62828; color: white; }
        
        /* Summary Section */
        .summary {
            margin-top: 20px;
            padding: 15px 20px;
            background: #e3f2fd;
            border-radius: 8px;
            font-size: 15px;
            border-left: 5px solid #1e3c72;
        }
        
        .summary strong {
            color: #1e3c72;
            font-size: 16px;
        }
        
        /* Signatures */
        .signatures {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px;
            margin-top: 50px;
            margin-bottom: 25px;
        }
        
        .signature {
            text-align: center;
        }
        
        .signature-line {
            border-top: 2px solid #1e3c72;
            margin-bottom: 10px;
            padding-top: 8px;
            width: 85%;
            margin-left: auto;
            margin-right: auto;
        }
        
        .signature-label {
            font-size: 14px;
            color: #1e3c72;
            font-weight: 600;
        }
        
        /* Footer */
        .footer {
            display: flex;
            justify-content: space-between;
            color: #6c757d;
            font-size: 12px;
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px dashed #1e3c72;
        }
        
        .footer-left, .footer-right {
            flex: 1;
        }
        
        .footer-right {
            text-align: right;
        }
        
        /* Watermark (Optional) */
        .watermark {
            position: absolute;
            bottom: 50px;
            right: 50px;
            opacity: 0.1;
            font-size: 60px;
            font-weight: 800;
            color: #1e3c72;
            transform: rotate(-15deg);
            pointer-events: none;
            z-index: 0;
        }
        
        /* Print Styles - A4 Perfect Format */
        @media print {
            @page {
                size: A4;
                margin: 0.5in;
            }
            
            body {
                background: white;
                padding: 0;
                margin: 0;
            }
            
            .actions {
                display: none;
            }
            
            .marksheet {
                box-shadow: none;
                padding: 0;
                border: none;
                min-height: auto;
            }
            
            .header {
                border-bottom: 3px solid #1e3c72;
            }
            
            .title-container h1 {
                color: #1e3c72;
            }
            
            .college-info {
                background: #f0f4f8;
                border: 2px solid #1e3c72;
            }
            
            .dist-card.total {
                background: #1e3c72;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .dist-card.internal {
                background: #2e7d32;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .dist-card.external {
                background: #b76e1e;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            th {
                background: #1e3c72 !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .pass-row {
                background-color: #e8f5e9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .fail-row {
                background-color: #ffebee !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .grade-a { background: #2e7d32; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .grade-b { background: #1565c0; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .grade-c { background: #ff8f00; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .grade-d { background: #ef6c00; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .grade-f { background: #c62828; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            
            .signature-line {
                border-top: 2px solid #1e3c72;
            }
            
            .watermark {
                opacity: 0.05;
            }
            
            /* Ensure no page breaks inside important elements */
            .header, .college-info, .marks-dist, .stats, .summary, .signatures {
                page-break-inside: avoid;
            }
            
            /* Table page break handling */
            table {
                page-break-inside: auto;
            }
            
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            
            thead {
                display: table-header-group;
            }
            
            tfoot {
                display: table-footer-group;
            }
        }
        
        /* Tip Section */
        .tip {
            margin-top: 15px;
            text-align: center;
            color: #495057;
            font-size: 13px;
            font-style: italic;
            background: #e9ecef;
            padding: 8px;
            border-radius: 50px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Action Buttons - Screen Only -->
        <div class="actions">
            <button onclick="window.print()" class="btn btn-primary">
                🖨️ Print / Save as PDF (A4)
            </button>
            <button onclick="window.close()" class="btn btn-secondary">
                ✖ Close Window
            </button>
        </div>

        <!-- Main Marksheet -->
        <div class="marksheet" id="marksheet">
            <!-- Watermark (Optional) -->
            <div class="watermark">SRM COLLEGE</div>
            
            <!-- Header with Logo - Fixed Position -->
            <div class="header">
                <div class="logo-container">
                    <img src="{{ asset('images/srmlogo1.jpg') }}" alt="SRM College Logo" class="brand logo">
                </div>
                
                <div class="title-container">
                    <h1>SRM COLLEGE OF EDUCATION</h1>
                    <h3>STATEMENT OF MARKS</h3>
                    <div class="exam-type">{{ strtoupper($exam->exam_type) }} EXAMINATION - {{ $exam->exam_date->format('Y') }}</div>
                </div>
                
                <div class="date-container">
                    <div><strong>Date:</strong> {{ now()->format('d/m/Y') }}</div>
                    <div><strong>Time:</strong> {{ now()->format('h:i A') }}</div>
                </div>
            </div>

            <!-- College Info -->
            <div class="college-info">
                <h2>Examination Department</h2>
                <p>Affiliated to SRM University • Quality Education for Excellence</p>
            </div>

            <!-- Exam Information -->
            <div class="exam-info">
                <div class="info-card">
                    <div class="label">Exam Date</div>
                    <div class="value">{{ $exam->exam_date->format('d M Y') }}</div>
                </div>
                <div class="info-card">
                    <div class="label">Time</div>
                    <div class="value">{{ $exam->formatted_time }} ({{ $exam->session_text }})</div>
                </div>
                <div class="info-card">
                    <div class="label">Subject</div>
                    <div class="value">{{ $exam->subject_name }} ({{ $exam->subject_code }})</div>
                </div>
                <div class="info-card">
                    <div class="label">Class</div>
                    <div class="value">{{ $exam->class_name }}</div>
                </div>
            </div>

            <!-- Marks Distribution -->
            <div class="marks-dist">
                <div class="dist-card total">
                    <div class="label">TOTAL MARKS</div>
                    <div class="value">{{ $subject->total_marks ?? 100 }}</div>
                    <div class="sub">Passing: {{ $subject->passing_marks ?? 35 }}</div>
                </div>
                <div class="dist-card internal">
                    <div class="label">INTERNAL MARKS</div>
                    <div class="value">{{ $subject->internal_marks ?? 0 }}</div>
                    <div class="sub">{{ $subject->internal_percentage ?? 0 }}% of Total</div>
                </div>
                <div class="dist-card external">
                    <div class="label">EXTERNAL MARKS</div>
                    <div class="value">{{ $subject->external_marks ?? 0 }}</div>
                    <div class="sub">{{ $subject->external_percentage ?? 0 }}% of Total</div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="stats">
                <div class="stat-card total">
                    <div class="label">Total Students</div>
                    <div class="value">{{ $statistics['total_students'] }}</div>
                </div>
                <div class="stat-card passed">
                    <div class="label">Passed</div>
                    <div class="value">{{ $statistics['passed'] }}</div>
                </div>
                <div class="stat-card failed">
                    <div class="label">Failed</div>
                    <div class="value">{{ $statistics['failed'] }}</div>
                </div>
                <div class="stat-card average">
                    <div class="label">Average</div>
                    <div class="value">{{ number_format($statistics['average'], 1) }}%</div>
                </div>
            </div>

            <!-- Students Marks Table -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Roll No</th>
                            <th>Student Name</th>
                            <th>Internal</th>
                            <th>External</th>
                            <th>Total</th>
                            <th>Percentage</th>
                            <th>Grade</th>
                            <th>Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($marks as $index => $mark)
                            @php
                                $totalObtained = $mark->total_marks_obtained ?? $mark->marks_obtained ?? 0;
                                $percentage = $mark->percentage ?? 0;
                                $result = $percentage >= 35 ? 'PASS' : 'FAIL';
                                $rowClass = $result == 'PASS' ? 'pass-row' : 'fail-row';
                                
                                $gradeLetter = substr($mark->grade ?? 'F', 0, 1);
                                $gradeClass = 'grade-' . strtolower($gradeLetter);
                            @endphp
                            <tr class="{{ $rowClass }}">
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $mark->student->roll_no ?? 'N/A' }}</strong></td>
                                <td>{{ $mark->student->name ?? 'N/A' }}</td>
                                <td>{{ $mark->internal_marks ?? '-' }}</td>
                                <td>{{ $mark->external_marks ?? '-' }}</td>
                                <td><strong>{{ $totalObtained }}/{{ $subject->total_marks ?? 100 }}</strong></td>
                                <td><strong>{{ number_format($percentage, 2) }}%</strong></td>
                                <td>
                                    <span class="grade-badge {{ $gradeClass }}">
                                        {{ $mark->grade ?? 'F' }}
                                    </span>
                                </td>
                                <td><strong>{{ $result }}</strong></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" style="text-align: center; padding: 30px;">
                                    <strong>No marks found for this exam.</strong>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Summary -->
            @if($marks->isNotEmpty())
            <div class="summary">
                <strong>📊 SUMMARY REPORT</strong><br>
                Total Students Appeared: {{ $statistics['total_students'] }} | 
                Number of Passes: {{ $statistics['passed'] }} | 
                Number of Fails: {{ $statistics['failed'] }} | 
                Pass Percentage: {{ $statistics['total_students'] > 0 ? number_format(($statistics['passed'] / $statistics['total_students']) * 100, 2) : 0 }}% |
                Overall Average: {{ number_format($statistics['average'], 2) }}%
            </div>
            @endif

            <!-- Signatures -->
            <div class="signatures">
                <div class="signature">
                    <div class="signature-line"></div>
                    <div class="signature-label">Class Teacher</div>
                </div>
                <div class="signature">
                    <div class="signature-line"></div>
                    <div class="signature-label">Principal</div>
                </div>
                <div class="signature">
                    <div class="signature-line"></div>
                    <div class="signature-label">Controller of Examinations</div>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <div class="footer-left">
                    <strong>SRM College of Education</strong> • Established 1995
                </div>
                <div class="footer-right">
                    Generated: {{ now()->format('d M Y, h:i A') }} | 
                    By: {{ Auth::user()->name ?? 'Admin' }}
                </div>
            </div>
        </div>
        
        <!-- Tip Section -->
        <div class="tip">
            💡 Click "Print / Save as PDF (A4)" → Choose "Save as PDF" → Select A4 size for perfect format
        </div>
    </div>

    <script>
    // Auto-detect and optimize for A4
    function optimizeForA4() {
        // Ensure table doesn't overflow
        const tables = document.querySelectorAll('table');
        tables.forEach(table => {
            const cells = table.querySelectorAll('td, th');
            cells.forEach(cell => {
                cell.style.whiteSpace = 'normal';
            });
        });
    }
    
    // Run optimization on load
    window.onload = optimizeForA4;
    
    // Keyboard shortcut for print
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'p') {
            e.preventDefault();
            window.print();
        }
    });
    
    // Add print dialog customization
    window.onbeforeprint = function() {
        // Any pre-print adjustments
        document.body.style.background = 'white';
    };
    
    window.onafterprint = function() {
        // Restore after print
        document.body.style.background = '#e6e9ef';
    };
    </script>
</body>
</html>