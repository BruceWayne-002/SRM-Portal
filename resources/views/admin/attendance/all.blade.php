@extends('layouts.app')

@section('main')
@php use Carbon\Carbon; @endphp
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>All Attendance Records</h4>
        <a href="{{ route('admin.attendance.index') }}" class="btn btn-primary">
            <i class="bi bi-funnel"></i> Go to Filtered View
        </a>
    </div>

    <div class="card">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Complete Attendance History</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Exam</th>
                        <th>Student</th>
                        <th>Roll No</th>
                        <th>Hall</th>
                        <th>Status</th>
                        <th>Marked By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $i => $att)
                        <tr>
                            <td>{{ $attendances->firstItem() + $i }}</td>
                            <td>{{ Carbon::parse($att->exam_date)->format('d-m-Y') }}</td>
                            <td>{{ $att->exam->subject_name ?? 'N/A' }}</td>
                            <td>{{ $att->student->name ?? 'N/A' }}</td>
                            <td>{{ $att->student->roll_no ?? 'N/A' }}</td>
                            <td>{{ $att->hall->hall_name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge 
                                    {{ $att->status == 'present' ? 'bg-success' : ($att->status == 'absent' ? 'bg-danger' : 'bg-warning') }}">
                                    {{ ucfirst($att->status) }}
                                </span>
                            </td>
                            <td>{{ $att->teacher->name ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No attendance records found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    Showing {{ $attendances->firstItem() }} to {{ $attendances->lastItem() }} of {{ $attendances->total() }} entries
                </div>
                <div>
                    {{ $attendances->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection