@extends('layouts.app')

@section('title', 'Show Exam Hall')

@section('main')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Exam Hall Details</h2>
        <div>
            <a href="{{ route('admin.halls.storedhalls') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Halls
            </a>
            <a href="{{ route('admin.halls.edit', $hall->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit Hall
            </a>
            <a href="{{ route('admin.halls.seating', $hall->id) }}" class="btn btn-info">
                <i class="fas fa-chair"></i> View Seating
            </a>
        </div>
    </div>

    <div class="row">
        {{-- Hall Information --}}
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Hall Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 40%">Hall Name</th>
                            <td>{{ $hall->hall_name }}</td>
                        </tr>
                        <tr>
                            <th>Hall Code</th>
                            <td><span class="badge bg-secondary">{{ $hall->hall_code }}</span></td>
                        </tr>
                        <tr>
                            <th>Building / Floor</th>
                            <td>{{ $hall->building }} - Floor {{ $hall->floor }}</td>
                        </tr>
                        <tr>
                            <th>Room Number</th>
                            <td>{{ str_replace(' Exam Hall', '', $hall->hall_name) }}</td>
                        </tr>
                        <tr>
                            <th>Capacity</th>
                            <td>{{ $hall->capacity }} ({{ $hall->rows }} rows x {{ $hall->columns }} columns)</td>
                        </tr>
                        <tr>
                            <th>Exam Date</th>
                            <td>{{ Carbon\Carbon::parse($hall->exam_date)->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <th>Exam Time</th>
                            <td>{{ Carbon\Carbon::parse($hall->start_time)->format('h:i A') }} - 
                                {{ Carbon\Carbon::parse($hall->end_time)->format('h:i A') }}</td>
                        </tr>
                        <tr>
                            <th>Exam Type</th>
                            <td><span class="badge bg-primary">{{ $hall->exam_type }}</span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- Teacher Information --}}
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Invigilator Information</h5>
                </div>
                <div class="card-body">
                    @if($hall->teacher)
                        <div class="text-center mb-3">
                            <div class="avatar-lg bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 80px; height: 80px; font-size: 32px;">
                                {{ strtoupper(substr($hall->teacher->name, 0, 1)) }}
                            </div>
                            <h4>{{ $hall->teacher->name }}</h4>
                            <p class="text-muted">{{ $hall->teacher->email }}</p>
                            @if($hall->teacher->phone)
                                <p><i class="fas fa-phone"></i> {{ $hall->teacher->phone }}</p>
                            @endif
                        </div>
                        <table class="table table-bordered">
                            <tr>
                                <th>Qualification</th>
                                <td>{{ $hall->teacher->qualification ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Experience</th>
                                <td>{{ $hall->teacher->experience ?? 'N/A' }} years</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    @if($hall->teacher->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    @else
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-triangle"></i> No invigilator assigned to this hall.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Assigned Exams --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Assigned Exams ({{ $assignedExams->count() }})</h5>
                </div>
                <div class="card-body">
                    @if($assignedExams->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Subject</th>
                                        <th>Class</th>
                                        <th>Students</th>
                                        <th>Exam Date</th>
                                        <th>Exam Time</th>
                                        <th>Duration</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assignedExams as $index => $exam)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $exam->subject->name ?? 'N/A' }}</td>
                                        <td>{{ $exam->class->name ?? 'N/A' }}</td>
                                        <td>{{ $exam->class->students_count ?? 0 }}</td>
                                        <td>{{ Carbon\Carbon::parse($exam->exam_date)->format('d M Y') }}</td>
                                        <td>{{ Carbon\Carbon::parse($exam->exam_time)->format('h:i A') }}</td>
                                        <td>{{ $exam->duration ?? 2 }} hours</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        {{-- Seat Utilization Summary --}}
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6>Seat Utilization</h6>
                                        @php
                                            $totalStudents = $assignedExams->sum(function($exam) {
                                                return $exam->class->students_count ?? 0;
                                            });
                                            $utilization = $hall->capacity > 0 ? round(($totalStudents / $hall->capacity) * 100, 1) : 0;
                                            $utilizationClass = $utilization <= 100 ? 'success' : 'danger';
                                        @endphp
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Total Students:</span>
                                            <strong>{{ $totalStudents }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Hall Capacity:</span>
                                            <strong>{{ $hall->capacity }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Utilization:</span>
                                            <strong class="text-{{ $utilizationClass }}">{{ $utilization }}%</strong>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar bg-{{ $utilization <= 100 ? 'success' : 'danger' }}" 
                                                role="progressbar" 
                                                style="width: {{ min($utilization, 100) }}%" 
                                                aria-valuenow="{{ min($utilization, 100) }}" 
                                                aria-valuemin="0" 
                                                aria-valuemax="100">
                                            </div>
                                        </div>
                                        @if($utilization > 100)
                                            <div class="alert alert-warning mt-2 mb-0 py-2">
                                                <i class="fas fa-exclamation-triangle"></i> 
                                                Hall capacity is exceeded by {{ $totalStudents - $hall->capacity }} students!
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6>Seating Preview</h6>
                                        <div class="seating-mini-preview">
                                            @php
                                                $previewRows = min(5, $hall->rows);
                                                $previewCols = min(8, $hall->columns);
                                            @endphp
                                            <div class="d-flex flex-column gap-1">
                                                @for($i = 0; $i < $previewRows; $i++)
                                                    <div class="d-flex gap-1 justify-content-center">
                                                        @for($j = 1; $j <= $previewCols; $j++)
                                                            <div class="seat-mini" 
                                                                 style="width: 25px; height: 25px; background-color: #e9ecef; border: 1px solid #dee2e6; display: inline-block;">
                                                            </div>
                                                        @endfor
                                                    </div>
                                                @endfor
                                                @if($hall->rows > 5 || $hall->columns > 8)
                                                    <div class="text-center text-muted small mt-1">
                                                        Showing {{ $previewRows }}x{{ $previewCols }} of {{ $hall->rows }}x{{ $hall->columns }}
                                                        <a href="{{ route('admin.halls.seating', $hall->id) }}" class="ms-2">View Full</a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle"></i> No exams assigned to this hall.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.avatar-lg {
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #007bff;
    color: white;
    font-weight: bold;
}
.seat-mini {
    border-radius: 3px;
}
.seat-mini:hover {
    background-color: #007bff !important;
    border-color: #0056b3 !important;
}
</style>
@endpush
@endsection