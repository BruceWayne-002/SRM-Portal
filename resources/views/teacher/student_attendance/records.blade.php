@extends('layouts.teacher')

@section('page-title', 'Attendance Records')

@section('main')
<div class="container mt-5">
    <h3 class="mb-4">Attendance Records</h3>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb bg-light rounded-3 p-2">
            <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}" class="text-primary-custom">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Attendance Records</li>
        </ol>
    </nav>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('teacher.studentAttendance.records') }}" class="row g-2 mb-3">
        <div class="col-12 col-md-5">
            <label>Select Student</label>
            <select name="student_roll_no" id="student_select" class="form-select">
                <option value="">-- All Students --</option>
                @foreach($students as $student)
                    <option value="{{ $student->roll_no }}" {{ request('student_roll_no') == $student->roll_no ? 'selected' : '' }}>
                        {{ $student->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-12 col-md-2">
            <label>Select Month</label>
            <input type="text" name="month" id="month_picker" class="form-control" value="{{ request('month') }}" placeholder="Select Month">
        </div>

        <div class="col-12 col-md-2">
            <label>Select Date</label>
            <input type="text" name="selected_date" id="date_picker" class="form-control" value="{{ request('selected_date') }}" placeholder="Select Date">
        </div>

        <div class="col-12 col-md-3 align-self-end d-flex gap-2">
            <button class="btn btn-primary w-100">Filter</button>
            <a href="{{ route('teacher.studentAttendance.records', array_merge(request()->all(), ['export' => 'csv'])) }}" class="btn btn-success w-100">
                Export CSV
            </a>
        </div>
    </form>

    @if($records && $records->count())
        <div class="card shadow-sm border-0">
            <div class="card-body table-responsive">
                @php
                    $totalPresent = 0;
                    $totalAbsent = 0;
                    $counter = 1;
                @endphp
                <table id="attendanceTable" class="table table-hover table-bordered align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Roll No</th>
                            <th>Name</th>
                            <th>Date</th>
                            <th>Morning</th>
                            <th>Afternoon</th>
                            <th>Full/Half Day</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($records as $record)
                        @php
                            $morning = $record->morning;
                            $afternoon = $record->afternoon;

                            if($morning == 'Present' && $afternoon == 'Present'){
                                $dayStatus = 'Full Day';
                                $totalPresent += 1;
                            } elseif($morning == 'Present' || $afternoon == 'Present'){
                                $dayStatus = 'Half Day';
                                $totalPresent += 0.5;
                            } else {
                                $dayStatus = 'Absent';
                                $totalAbsent += 1;
                            }

                            // Count half absent if only one session absent
                            if(($morning == 'Absent' && $afternoon == 'Present') || ($morning == 'Present' && $afternoon == 'Absent')){
                                $totalAbsent += 0.5;
                            }
                        @endphp
                        <tr>
                            <td>{{ $counter++ }}</td>
                            <td>{{ $record->student_roll_no }}</td>
                            <td>{{ $record->student->name ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($record->date)->format('d M Y') }}</td>
                            <td>
                                <span class="badge {{ $morning == 'Present' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $morning }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $afternoon == 'Present' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $afternoon }}
                                </span>
                            </td>
                            <td>{{ $dayStatus }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-primary">
                        <tr>
                            <td colspan="6" class="text-end"><strong>Total Present Days:</strong></td>
                            <td>{{ $totalPresent }}</td>
                        </tr>
                        <tr>
                            <td colspan="6" class="text-end"><strong>Total Absent Days:</strong></td>
                            <td>{{ $totalAbsent }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @else
        <div class="alert alert-info mt-3">No attendance records found for selected filter.</div>
    @endif
</div>

{{-- CSS --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.2.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css" />

{{-- JS --}}
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"></script>

<script>
$(document).ready(function () {
    // Initialize DataTable
    $('#attendanceTable').DataTable({
        pageLength: 10,
        ordering: true,
        lengthChange: true,
        searching: true,
        responsive: true,
        dom: 'Bfrtip',
        buttons: [
            { extend: 'csv', className: 'btn btn-success btn-sm' },
            { extend: 'excel', className: 'btn btn-primary btn-sm' },
            { extend: 'pdf', className: 'btn btn-danger btn-sm' },
            { extend: 'print', className: 'btn btn-dark btn-sm' }
        ],
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ records per page",
            zeroRecords: "No matching records found",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "No records available",
            infoFiltered: "(filtered from _MAX_ total records)"
        }
    });

    // Initialize Select2 for student dropdown
    $('#student_select').select2({
        width: '100%',
        theme: 'bootstrap-5',
        placeholder: "Select Student",
    });

    // Initialize Bootstrap Datepicker
    $('#date_picker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true,
        endDate: new Date()
    });
    
    $('form').on('submit', function() {
        var $form = $(this);
        var $btn = $form.find('button[type="submit"]');
        $btn.prop('disabled', true);
        $btn.text('Submitting...'); // Optional: show feedback
    });



    $('#month_picker').datepicker({
        format: "yyyy-mm",
        startView: "months",
        minViewMode: "months",
        autoclose: true
    });
});
</script>

<style>
    .table th {
    color: #fff !important;
    background-color: #640d3c !important;
}
</style>
@endsection
