@extends('layouts.teacher')

@section('main')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="bi bi-building me-2"></i>My Hall Allocations</h4>
                    <span class="badge bg-light text-dark fs-6">Total: {{ $allocations->total() }}</span>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Filter Form -->
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Filter Allocations</h6>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('teacher.halls.index') }}" class="row g-3">
                                <div class="col-md-3">
                                    <label for="exam_id" class="form-label">Exam</label>
                                    <select name="exam_id" id="exam_id" class="form-select">
                                        <option value="">All Exams</option>
                                        @foreach($exams as $exam)
                                            <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
                                                {{ $exam->subject_name ?? 'Exam' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="hall_id" class="form-label">Hall</label>
                                    <select name="hall_id" id="hall_id" class="form-select">
                                        <option value="">All Halls</option>
                                        @foreach($halls as $hall)
                                            <option value="{{ $hall->id }}" {{ request('hall_id') == $hall->id ? 'selected' : '' }}>
                                                {{ $hall->hall_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="date_from" class="form-label">Date From</label>
                                    <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                                </div>
                                <div class="col-md-2">
                                    <label for="date_to" class="form-label">Date To</label>
                                    <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-search me-2"></i>Filter
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    @if($allocations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Hall</th>
                                        <th>Code</th>
                                        <th>Exam</th>
                                        <th>Date</th>
                                        <th>Students</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allocations as $index => $allocation)
                                        <tr>
                                            <td>{{ $allocations->firstItem() + $index }}</td>
                                            <td>
                                                <strong>{{ $allocation->hall->hall_name ?? 'N/A' }}</strong>
                                                @if($allocation->hall->building)
                                                    <br><small class="text-muted">{{ $allocation->hall->building }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $allocation->exam->subject_code ?? 'N/A' }}
                                            </td>
                                            <td>
                                                <strong>{{ $allocation->exam->subject_name ?? 'N/A' }}</strong>
                                                <br><small class="text-muted">{{ $allocation->exam->exam_type ?? '' }}</small>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($allocation->exam_date)->format('d-m-Y') }}</td>
                                            
                                            <td class="text-center">
                                                <span class="badge bg-primary rounded-pill fs-6">{{ $allocation->students->count() }}</span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('teacher.halls.show', $allocation->id) }}" class="btn btn-sm btn-info" title="View Hall Layout">
                                                        <i class="bi bi-grid-3x3"></i>
                                                    </a>
                                                    <a href="{{ route('teacher.halls.student-list', $allocation->id) }}" class="btn btn-sm btn-primary" title="View Students">
                                                        <i class="bi bi-people"></i>
                                                    </a>
                                                    <a href="{{ route('teacher.halls.attendance-sheet', $allocation->id) }}" class="btn btn-sm btn-success" title="Download Attendance">
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $allocations->links() }}
                        </div>
                    @else
                        <div class="alert alert-info text-center py-4">
                            <i class="bi bi-info-circle fs-1 d-block mb-3"></i>
                            <h5>No Hall Allocations Found</h5>
                            <p class="mb-0">There are currently no hall allocations assigned to you.</p>
                            @if(isset($message))
                                <p class="mt-2 text-muted">{{ $message }}</p>
                            @endif
                            
                            <!-- Debug Info (Remove in production) -->
                            <div class="mt-3 small text-muted">
                                <p class="mb-1">Your Teacher ID: <strong>{{ $teacher->id }}</strong></p>
                                <p class="mb-0">Total Allocations in System: <strong>{{ \App\Models\ExamHallAllocation::count() }}</strong></p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table th {
        font-weight: 600;
    }
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
    }
    .pagination {
        margin-bottom: 0;
    }
</style>
@endpush