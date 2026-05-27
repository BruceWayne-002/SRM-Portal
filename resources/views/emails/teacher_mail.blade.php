<h2>New Leave Request</h2>

<p><strong>Teacher:</strong> {{ $teacher->name }} ({{ $teacher->employee_id }})</p>
<p><strong>Type:</strong> {{ $leave->type }}</p>
<p><strong>From:</strong> {{ $leave->from_date }} 
    @if($leave->to_date) to {{ $leave->to_date }} @endif
</p>

@if($leave->permission_from && $leave->permission_to)
    <p><strong>Time:</strong> {{ $leave->permission_from }} - {{ $leave->permission_to }}</p>
@endif

<p><strong>Contact No:</strong> {{ $leave->contact_no }}</p>
<p><strong>Reason:</strong> {{ $leave->reason }}</p>

<hr>
<p><strong>Sent from:</strong> {{ $senderEmail }}</p>
