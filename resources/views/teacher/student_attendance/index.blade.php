@extends('layouts.teacher')

@section('page-title', 'Take Attendance')

@section('main')
<div class="container mt-5">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb bg-light rounded-3 p-2">
            <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}" class="text-primary-custom">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Attendance</li>
        </ol>
    </nav>

    <div class="card shadow-lg rounded-4">
        <div class="card-header d-flex justify-content-between align-items-center bg-gradient text-white">
            <h4 class="mb-0">Take Attendance ({{ ucfirst($shift) }})</h4>
            <a href="{{ route('teacher.studentAttendance.records') }}" class="btn btn-gradient btn-sm">View Records</a>
        </div>

        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Filter Form --}}
            <form method="GET" action="{{ route('teacher.studentAttendance.index') }}" class="row g-2 mb-4">
                <div class="col-md-4">
                    <input type="text" name="date" id="attendance_date" value="{{ $date }}" class="form-control shadow-sm" placeholder="Select Date">
                </div>
                <div class="col-md-4">
                    <select name="shift" id="shift" class="form-select shadow-sm">
                        <option value="morning" {{ $shift == 'morning' ? 'selected' : '' }}>Morning</option>
                        <option value="afternoon" {{ $shift == 'afternoon' ? 'selected' : '' }}>Afternoon</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-dark w-100 shadow-sm">Load</button>
                </div>
            </form>

            {{-- Attendance Table --}}
            <form method="POST" action="{{ route('teacher.studentAttendance.store') }}">
                @csrf
                <input type="hidden" name="date" value="{{ $date }}">
                <input type="hidden" name="shift" value="{{ $shift }}">

                <div class="table-responsive">
                    <table class="table table-hover align-middle text-center">
                        <thead class="table-gradient text-white">
                            <tr>
                                <th>#</th>
                                <th>Roll No</th>
                                <th>Name</th>
                                <th>Attendance ({{ ucfirst($shift) }})</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $index => $student)
                                @php
                                    $att = $attendances[$student->roll_no] ?? null;
                                    $status = $att?->$shift ?? 'Present';
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><span class="badge bg-secondary shadow-sm">{{ $student->roll_no }}</span></td>
                                    <td class="fw-semibold">{{ $student->name }}</td>
                                    <td>
                                        <div class="form-check form-switch d-flex justify-content-center align-items-center">
                                            <input type="hidden" name="attendance[{{ $student->roll_no }}]" value="Absent">
                                            <input class="form-check-input toggle-present shadow-sm" type="checkbox"
                                                   name="attendance[{{ $student->roll_no }}]"
                                                   value="Present" id="toggle-{{ $student->roll_no }}"
                                                   {{ $status == 'Present' ? 'checked' : '' }}>
                                            <label class="form-check-label ms-2" for="toggle-{{ $student->roll_no }}">
                                                {{ $status }}
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No students found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <span class="badge bg-info shadow-sm">Total: {{ $total }}</span>
                        <span class="badge bg-success shadow-sm">Present: {{ $present }}</span>
                        <span class="badge bg-danger shadow-sm">Absent: {{ $absent }}</span>
                    </div>
                    <button type="submit" class="btn btn-gradient px-4 shadow-sm">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- CSS --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.2.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

{{-- JS --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Datepicker
    $('#attendance_date').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        todayHighlight: true,
        endDate: new Date()
    });

    // Initialize Select2
    $('#shift').select2({
        width: '100%',
        theme: 'bootstrap-5',
        placeholder: "Select Shift",
    });

    $('form').on('submit', function() {
        var $form = $(this);
        var $btn = $form.find('button[type="submit"]');
        $btn.prop('disabled', true);
        $btn.text('Submitting...'); // Optional: show feedback
    });
    
    // Toggle Present/Absent
    document.querySelectorAll('.toggle-present').forEach(toggle => {
        toggle.addEventListener('change', function () {
            const label = this.nextElementSibling;
            if (this.checked) {
                label.innerText = "Present";
                label.classList.remove("text-danger");
                label.classList.add("text-success");
            } else {
                label.innerText = "Absent";
                label.classList.remove("text-success");
                label.classList.add("text-danger");
            }
        });
    });
});
</script>

<style>
.text-primary-custom { color: #640d3c !important; }
.table-gradient { background: linear-gradient(135deg, #640d3c, #5a3a87); }
.btn-gradient { background: linear-gradient(135deg, #640d3c, #5a3a87); color: #fff; border: none; transition: 0.3s; }
.btn-gradient:hover { background: linear-gradient(135deg, #5a3a87, #640d3c); transform: scale(1.05); }
.card-header.bg-gradient { background: linear-gradient(135deg, #640d3c, #5a3a87); }
.table-hover tbody tr:hover { background-color: rgba(121, 82, 179, 0.1); }
</style>
@endsection
