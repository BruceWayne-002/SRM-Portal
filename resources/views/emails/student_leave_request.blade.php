<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Student Leave Request</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f7f7f7; color: #333; padding: 20px; }
        .container { max-width: 600px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; }
        h2 { color: #640d3c; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { text-align: left; padding: 8px; border-bottom: 1px solid #ddd; }
        th { background-color: #f0f0f0; }
        .footer { margin-top: 20px; font-size: 0.9em; color: #666; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 5px; color: #fff; font-size: 0.85em; }
        .badge-pending { background-color: #ffc107; color: #212529; }
    </style>
</head>
<body>
    <div class="container">
        <h2>New Leave Request Submitted</h2>

        <p>Dear Staff,</p>
        <p>A new leave request has been submitted by the following student:</p>

        <table>
            <tr>
                <th>Name</th>
                <td>{{ $student->name }}</td>
            </tr>
            <tr>
                <th>Roll No</th>
                <td>{{ $student->roll_no }}</td>
            </tr>
            <tr>
                <th>Class & Section</th>
                <td>{{ $student->class }} - {{ $student->section }}</td>
            </tr>
            <tr>
                <th>Type of Leave</th>
                <td>{{ $leave->type }}</td>
            </tr>
            <tr>
                <th>From Date</th>
                <td>{{ \Carbon\Carbon::parse($leave->from_date)->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <th>To Date</th>
                <td>{{ $leave->to_date ? \Carbon\Carbon::parse($leave->to_date)->format('d-m-Y') : '-' }}</td>
            </tr>
            <tr>
                <th>Contact Number</th>
                <td>{{ $leave->contact_no }}</td>
            </tr>
            <tr>
                <th>Reason</th>
                <td>{{ $leave->reason }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td><span class="badge badge-pending">Pending</span></td>
            </tr>
        </table>

        <p>Please review the request at your earliest convenience.</p>

        <p class="footer">
            This is an automated notification from your school management system.
        </p>
    </div>
</body>
</html>
