@extends('layouts.app')

@section('main')
@php use Carbon\Carbon; @endphp
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Exam Attendance: {{ $exam->subject_name }}</h4>
            <p class="text-muted">
                Date: {{ Carbon::parse($exam->exam_date)->format('d-m-Y') }} | 
                Hall: {{ $exam->hall->hall_name ?? 'N/A' }} |
                Total Students: {{ $exam->students->count() }}
            </p>
        </div>
        <div>
            <a href="{{ route('admin.attendance.exam.export-pdf', $exam->id) }}" class="btn btn-success me-2">
                <i class="bi bi-file-pdf"></i> Export PDF
            </a>
            <a href="{{ route('admin.attendance.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Students</h5>
                    <h2>{{ $statistics['total'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Present</h5>
                    <h2>{{ $statistics['present'] }}</h2>
                    @if($statistics['total'] > 0)
                        <small>{{ round(($statistics['present']/$statistics['total'])*100, 2) }}%</small>
                    @endif
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

    <!-- Attendance Table -->
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Student Attendance Details</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Roll No</th>
                        <th>Student Name</th>
                        <th>Status</th>
                        <th>Marked By</th>
                        <th>Marked At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $i => $att)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $att->student->roll_no ?? 'N/A' }}</td>
                            <td>{{ $att->student->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge 
                                    {{ $att->status == 'present' ? 'bg-success' : ($att->status == 'absent' ? 'bg-danger' : 'bg-warning') }}">
                                    {{ ucfirst($att->status) }}
                                </span>
                            </td>
                            <td>{{ $att->teacher->name ?? 'N/A' }}</td>
                            <td>{{ $att->marked_at ? Carbon::parse($att->marked_at)->format('d-m-Y H:i') : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No attendance records found for this exam</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection