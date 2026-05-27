@extends('layouts.app')

@section('main')
@php use Carbon\Carbon; @endphp
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Attendance Records</h4>
        <div>
            <a href="{{ route('admin.attendance.export-pdf', request()->query()) }}" class="btn btn-success me-2">
                <i class="bi bi-file-pdf"></i> Export to PDF
            </a>
            <a href="{{ route('admin.attendance.clear-filters') }}" class="btn btn-secondary">
                <i class="bi bi-x-circle"></i> Clear Filters
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Records</h5>
                    <h2>{{ $statistics['total'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Present</h5>
                    <h2>{{ $statistics['present'] }}</h2>
                    <small>{{ $statistics['present_percentage'] }}%</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h5 class="card-title">Absent</h5>
                    <h2>{{ $statistics['absent'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Late</h5>
                    <h2>{{ $statistics['late'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Filters -->
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Filter Attendance Records</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.attendance.index') }}" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Exam</label>
                        <select name="exam_id" class="form-select">
                            <option value="">All Exams</option>
                            @foreach($exams as $exam)
                                <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
                                    {{ $exam->subject_name }} ({{ Carbon::parse($exam->exam_date)->format('d-m-Y') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Hall</label>
                        <select name="hall_id" class="form-select">
                            <option value="">All Halls</option>
                            @foreach($halls as $hall)
                                <option value="{{ $hall->id }}" {{ request('hall_id') == $hall->id ? 'selected' : '' }}>
                                    {{ $hall->hall_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Teacher</label>
                        <select name="teacher_id" class="form-select">
                            <option value="">All Teachers</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Present</option>
                            <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Absent</option>
                            <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Late</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Single Date</label>
                        <input type="date" name="date" value="{{ request('date') }}" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Date From</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Date To</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Roll No</label>
                        <input type="text" name="roll_no" value="{{ request('roll_no') }}" class="form-control" placeholder="Enter roll no">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Student Name</label>
                        <input type="text" name="student_name" value="{{ request('student_name') }}" class="form-control" placeholder="Enter student name">
                    </div>

                    <div class="col-12 mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-filter"></i> Apply Filters
                        </button>
                        <a href="{{ route('admin.attendance.clear-filters') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Active Filters -->
    @if(request()->anyFilled(['exam_id', 'hall_id', 'date', 'status', 'teacher_id', 'roll_no', 'student_name', 'date_from', 'date_to']))
    <div class="alert alert-info">
        <strong>Active Filters:</strong>
        <ul class="mb-0 mt-2">
            @foreach(request()->all() as $key => $value)
                @if(!empty($value) && $key != 'page')
                    <li class="d-inline-block me-3"><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}</li>
                @endif
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Table -->
    <div class="card">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Attendance Records ({{ $attendances->total() }})</h5>
            <span>Page {{ $attendances->currentPage() }} of {{ $attendances->lastPage() }}</span>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Exam</th>
                        <th>Hall</th>
                        <th>Student</th>
                        <th>Roll No</th>
                        <th>Teacher</th>
                        <th>Status</th>
                        <th>Marked At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $i => $att)
                        <tr>
                            <td>{{ $attendances->firstItem() + $i }}</td>
                            <td>{{ Carbon::parse($att->exam_date)->format('d-m-Y') }}</td>
                            <td>
                                    {{ $att->exam->subject_name ?? 'N/A' }}
                            </td>
                            <td>{{ $att->hall->hall_name ?? 'N/A' }}</td>
                            <td>{{ $att->student->name ?? 'N/A' }}</td>
                            <td>{{ $att->student->roll_no ?? 'N/A' }}</td>
                            <td>{{ $att->teacher->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge 
                                    {{ $att->status == 'present' ? 'bg-success' : ($att->status == 'absent' ? 'bg-danger' : 'bg-warning') }}">
                                    {{ ucfirst($att->status) }}
                                </span>
                            </td>
                            <td>{{ $att->marked_at ? Carbon::parse($att->marked_at)->format('d-m-Y H:i') : '-' }}</td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center">No attendance records found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    Showing {{ $attendances->firstItem() }} to {{ $attendances->lastItem() }} of {{ $attendances->total() }} entries
                </div>
                <div>
                    {{ $attendances->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .table th {
        white-space: nowrap;
    }
    .badge {
        font-size: 0.85em;
    }
    .form-label {
        font-weight: 500;
        margin-bottom: 0.25rem;
    }
</style>
@endpush