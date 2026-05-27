@extends('layouts.teacher')

@section('main')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">My Subjects</h2>
        <div>
            <span class="badge bg-primary fs-6">Total Subjects: {{ $subjects->count() }}</span>
        </div>
    </div>

    @if($subjects->isEmpty())
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> No subjects have been assigned to you yet.
        </div>
    @else
        @foreach($groupedSubjects as $classSection => $classSubjects)
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-building"></i> {{ $classSection }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Subject Name</th>
                                    <th>Code</th>
                                    <th>Semester</th>
                                    <th>Total Marks</th>
                                    <th>Passing Marks</th>
                                    <th>Internal/External</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($classSubjects as $subject)
                                <tr>
                                    <td>
                                        <strong>{{ $subject->name }}</strong>
                                    </td>
                                    <td>{{ $subject->code ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $subject->semester_display }}</span>
                                    </td>
                                    <td>{{ $subject->total_marks ?? 'N/A' }}</td>
                                    <td>{{ $subject->passing_marks ?? 'N/A' }}</td>
                                    <td>
                                        @if($subject->internal_marks || $subject->external_marks)
                                            <small>
                                                Int: {{ $subject->internal_marks ?? 0 }}<br>
                                                Ext: {{ $subject->external_marks ?? 0 }}
                                            </small>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('teacher.subjects.show', $subject->id) }}" 
                                               class="btn btn-sm btn-info" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('teacher.subjects.students', $subject->id) }}" 
                                               class="btn btn-sm btn-primary" title="View Students">
                                                <i class="bi bi-people"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Summary Card -->
        <div class="card mt-4 bg-light">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <h3 class="text-primary">{{ $subjects->count() }}</h3>
                            <small>Total Subjects</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h3 class="text-success">{{ $groupedSubjects->count() }}</h3>
                            <small>Classes/Sections</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h3 class="text-info">
                                {{ $subjects->pluck('semester')->unique()->count() }}
                            </h3>
                            <small>Semesters</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h3 class="text-warning">
                                {{ $subjects->where('internal_marks', '>', 0)->count() }}
                            </h3>
                            <small>With Internal Marks</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.btn-group .btn {
    margin: 0 2px;
    border-radius: 4px !important;
}
.btn-group .btn:hover {
    transform: translateY(-2px);
    transition: all 0.3s;
}
</style>
@endpush