@extends('layouts.app')

@section('title', 'Marks - ' . $exam->subject_name)

@section('main')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-info">
            <i class="fas fa-chart-bar me-2"></i>Marks - {{ $exam->subject_name }}
        </h2>
        <div>
            <a href="{{ route('admin.marks.index') }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
            <a href="{{ route('admin.marks.download', $exam) }}" class="btn btn-primary">
                <i class="fas fa-download me-1"></i> Download PDF
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body text-center py-3">
                    <h6 class="small text-uppercase mb-2">Total Students</h6>
                    <h3 class="mb-0">{{ $statistics['total_students'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body text-center py-3">
                    <h6 class="small text-uppercase mb-2">Passed</h6>
                    <h3 class="mb-0">{{ $statistics['passed'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white shadow-sm">
                <div class="card-body text-center py-3">
                    <h6 class="small text-uppercase mb-2">Failed</h6>
                    <h3 class="mb-0">{{ $statistics['failed'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white shadow-sm">
                <div class="card-body text-center py-3">
                    <h6 class="small text-uppercase mb-2">Average</h6>
                    <h3 class="mb-0">{{ number_format($statistics['average'], 2) }}%</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Subject Marks Distribution -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Marks Distribution</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <strong>Total Marks:</strong> 
                                <span class="float-end">{{ $exam->subject->total_marks ?? ($exam->subject->internal_marks + $exam->subject->external_marks) }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <strong>Internal Marks:</strong> 
                                <span class="float-end">{{ $exam->subject->internal_marks ?? 0 }} ({{ $exam->subject->internal_percentage ?? 0 }}%)</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <strong>External Marks:</strong> 
                                <span class="float-end">{{ $exam->subject->external_marks ?? 0 }} ({{ $exam->subject->external_percentage ?? 0 }}%)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Marks Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-table me-2"></i>Student Marks</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">#</th>
                            <th width="10%">Roll No</th>
                            <th width="20%">Student Name</th>
                            <th width="15%">Class - Section</th>
                            <th width="12%">Internal Marks</th>
                            <th width="12%">External Marks</th>
                            <th width="10%">Total</th>
                            <th width="10%">Percentage</th>
                            <th width="8%">Grade</th>
                            <th width="8%">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($marks as $index => $mark)
                            @php
                                $rowClass = $mark->percentage < 35 ? 'table-danger' : 'table-success';
                                $classDisplay = 'N/A';
                                
                                // Safely get class information
                                if ($mark->student) {
                                    if ($mark->student->class) {
                                        if (is_object($mark->student->class)) {
                                            $className = $mark->student->class->name ?? '';
                                            $sectionName = $mark->student->class->section_name ?? '';
                                            $classDisplay = $className . ($sectionName ? ' - ' . $sectionName : '');
                                        } elseif (is_string($mark->student->class)) {
                                            $classDisplay = $mark->student->class;
                                        }
                                    } elseif ($mark->student->class_id) {
                                        $classDisplay = 'Class ID: ' . $mark->student->class_id;
                                    }
                                }
                                
                                // Get marks values
                                $internalMarks = $mark->internal_marks ?? 'N/A';
                                $externalMarks = $mark->external_marks ?? 'N/A';
                                $totalObtained = $mark->total_marks_obtained ?? 0;
                                $totalMarks = $mark->total_marks ?? ($exam->subject->total_marks ?? ($exam->subject->internal_marks + $exam->subject->external_marks));
                            @endphp
                            <tr class="{{ $rowClass }}">
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $mark->student->roll_no ?? 'N/A' }}</strong></td>
                                <td>{{ $mark->student->name ?? 'N/A' }}</td>
                                <td>{{ $exam->class_name ?? ($exam->subject->classModel->full_name ?? 'N/A') }} - {{ $exam->section_name ?? ($exam->subject->classModel->section_name ?? 'N/A') }}</td>
                                <td>
                                    @if($internalMarks !== 'N/A')
                                        {{ number_format($internalMarks, 2) }} / {{ $exam->subject->internal_marks ?? 'N/A' }}
                                        @if($mark->internal_percentage)
                                            <br><small class="text-muted">({{ number_format($mark->internal_percentage, 1) }}%)</small>
                                        @endif
                                    @else
                                        {{ $internalMarks }}
                                    @endif
                                </td>
                                <td>
                                    @if($externalMarks !== 'N/A')
                                        {{ number_format($externalMarks, 2) }} / {{ $exam->subject->external_marks ?? 'N/A' }}
                                        @if($mark->external_percentage)
                                            <br><small class="text-muted">({{ number_format($mark->external_percentage, 1) }}%)</small>
                                        @endif
                                    @else
                                        {{ $externalMarks }}
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ number_format($totalObtained, 2) }} / {{ $totalMarks }}</strong>
                                </td>
                                <td>
                                    @if($mark->percentage)
                                        <strong>{{ number_format($mark->percentage, 2) }}%</strong>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if($mark->grade)
                                        <span class="badge bg-secondary">{{ $mark->grade }}</span>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if($mark->percentage >= 35)
                                        <span class="badge bg-success">PASS</span>
                                    @elseif($mark->percentage !== null)
                                        <span class="badge bg-danger">FAIL</span>
                                    @else
                                        <span class="badge bg-warning">PENDING</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x mb-3 text-muted"></i>
                                    <p class="text-muted">No marks found for this exam.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection