<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Exam Hall Ticket</title>

<style>
@page {
    size: A4;
    margin: 1.5cm;
}

body {
    font-family: Arial, sans-serif;
    font-size: 11pt;
    color: #000;
    margin: 0;
}

/* ===== HEADER ===== */
.header {
    text-align: center;
    border-bottom: 3px solid #640d3c;
    padding-bottom: 15px;
    margin-bottom: 20px;
}

.header h1 {
    color: #640d3c;
    font-size: 18pt;
    margin: 5px 0;
    font-weight: bold;
}

.header h2 {
    font-size: 14pt;
    margin: 3px 0;
    color: #333;
}

.ticket-badge {
    background: #640d3c;
    color: white;
    padding: 6px 20px;
    border-radius: 20px;
    display: inline-block;
    margin-top: 10px;
    font-size: 10pt;
}

/* ===== STUDENT INFO ===== */
.student-info {
    background: #f4f0fa;
    border-left: 5px solid #640d3c;
    padding: 12px;
    margin-bottom: 20px;
}

.student-info table {
    width: 100%;
    border-collapse: collapse;
}

.student-info td {
    padding: 6px;
    font-size: 10pt;
}

/* ===== EXAM TABLE ===== */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

th {
    background: #640d3c;
    color: white;
    padding: 8px;
    text-align: center;
    font-size: 10pt;
}

td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: center;
    font-size: 10pt;
}

tbody tr:nth-child(even) {
    background-color: #f9f9f9;
}

/* ===== IMPORTANT NOTE ===== */
.note-box {
    background: #fff3cd;
    border: 1px solid #ffc107;
    color: #856404;
    padding: 10px;
    border-radius: 5px;
    margin-top: 20px;
    font-size: 9pt;
}

/* ===== SIGNATURE ===== */
.signature-section {
    margin-top: 50px;
    width: 100%;
}

.signature-table {
    width: 100%;
    margin-top: 30px;
}

.signature-table td {
    border: none;
    text-align: center;
    padding-top: 40px;
}

.signature-line {
    border-top: 1px solid #000;
    width: 70%;
    margin: 0 auto;
}

/* ===== FOOTER ===== */
.footer {
    margin-top: 40px;
    text-align: right;
    font-size: 8pt;
    color: #777;
    border-top: 1px dashed #ccc;
    padding-top: 10px;
}
</style>
</head>

<body>

@php
    $exam = $ticket->allocation->exam;
    $hall = $ticket->allocation->hall;

    // Convert FN / AN to full text
    $session = '-';
    if ($exam->time_session === 'FN') {
        $session = 'Forenoon';
    } elseif ($exam->time_session === 'AN') {
        $session = 'Afternoon';
    }
@endphp

<!-- HEADER -->
<div class="header">
    <h1>SRM TRICHY ARTS & SCIENCE COLLEGE</h1>
    <h2>EXAM HALL TICKET</h2>
    <div class="ticket-badge">
        {{ \Carbon\Carbon::parse($ticket->allocation->exam_date)->format('d M Y') }}
    </div>
</div>

<!-- STUDENT INFO -->
<div class="student-info">
    <table>
        <tr>
            <td width="20%"><strong>Student Name:</strong></td>
            <td width="30%">{{ $student->name }}</td>
            <td width="20%"><strong>Roll No:</strong></td>
            <td width="30%">{{ $student->roll_no }}</td>
        </tr>
        <tr>
            <td><strong>Hall Name:</strong></td>
            <td>{{ $hall->hall_name }}</td>
            <td><strong>Building:</strong></td>
            <td>{{ $hall->building }}</td>
        </tr>
        <tr>
            <td><strong>Floor:</strong></td>
            <td>{{ $hall->floor ?? 'N/A' }}</td>
            <td></td>
            <td></td>
        </tr>
    </table>
</div>

<!-- EXAM DETAILS TABLE -->
<table>
    <thead>
        <tr>
            <th>Subject</th>
            <th>Code</th>
            <th>Session</th>
            <th>Exam Time</th>
            <th>Table No</th>
            <th>Seat No</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $exam->subject->name ?? '-' }}</td>
            <td>{{ $exam->subject->code ?? '-' }}</td>
            <td>{{ $session }}</td>
            <td>
                {{ $exam->exam_time ? \Carbon\Carbon::parse($exam->exam_time)->format('h:i A') : 'N/A' }}
            </td>
            <td>{{ $ticket->table_number }}</td>
            <td>{{ $ticket->seat_number }}</td>
        </tr>
    </tbody>
</table>

<!-- IMPORTANT NOTE -->
<div class="note-box">
    <strong>Important Instructions:</strong><br>
    • Bring this hall ticket to the examination hall.<br>
    • Reach the hall at least 30 minutes before exam time.<br>
    • Electronic gadgets are strictly prohibited.<br>
    • Follow all examination rules and regulations.
</div>

<!-- SIGNATURE SECTION -->
<div class="signature-section">
    <table class="signature-table">
        <tr>
            <td width="50%">
                <div class="signature-line"></div>
                Student Signature
            </td>
            <td width="50%">
                <div class="signature-line"></div>
                Controller of Examination
            </td>
        </tr>
    </table>
</div>

<!-- FOOTER -->
<div class="footer">
    Generated on: {{ $downloadDate }}
</div>

</body>
</html>