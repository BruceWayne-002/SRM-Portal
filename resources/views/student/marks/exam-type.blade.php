@extends('layouts.student')

@section('title', $displayName . ' Marks')

@section('main')
@php
// Helper function to format numbers without .00
function formatNumber($number) {
    if (is_numeric($number)) {
        // Check if it's a whole number (no decimal or .00)
        if (floor($number) == $number) {
            return (string) floor($number);
        } else {
            // For numbers with decimals, show only 2 decimal places
            return number_format($number, 2);
        }
    }
    return $number;
}
@endphp
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-chart-line me-2"></i>{{ $displayName }} Marks
                        </h4>
                        <div>
                            <a href="{{ route('student.marks.download-pdf', [$examType, $year ?? '']) }}" 
                               class="btn btn-light btn-sm me-2">
                                <i class="fas fa-download me-1"></i> Download PDF
                            </a>
                            <a href="{{ route('student.marks.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Back to Exam Types
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Total Exams</h6>
                                    <h2 class="mb-0">{{ $totalExams }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Average Percentage</h6>
                                    <h2 class="mb-0">{{ $averagePercentage }}%</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Total Marks</h6>
                                    <h2 class="mb-0">{{ $obtainedMarks }}/{{ $totalMarks }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Subjects</h6>
                                    <h2 class="mb-0">{{ $subjectWiseMarks->count() }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Subject-wise Performance -->
                    @if($subjectWiseMarks->isNotEmpty())
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Subject-wise Performance</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Subject</th>
                                            <th>Code</th>
                                            <th>Exams</th>
                                            <th>Total Marks</th>
                                            <th>Obtained</th>
                                            <th>Percentage</th>
                                            <th>Performance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($subjectWiseMarks as $subject)
                                        <tr>
                                            <td>{{ $subject['subject_name'] }}</td>
                                            <td>{{ $subject['subject_code'] }}</td>
                                            <td>{{ $subject['exams_count'] }}</td>
                                            <td>{{ $subject['total_marks'] }}</td>
                                            <td>{{ $subject['obtained_marks'] }}</td>
                                            <td>{{ $subject['percentage'] }}%</td>
                                            <td>
                                                <div class="progress">
                                                    @php
                                                        $progressClass = $subject['percentage'] >= 75 ? 'success' : ($subject['percentage'] >= 50 ? 'warning' : 'danger');
                                                    @endphp
                                                    <div class="progress-bar bg-{{ $progressClass }}" 
                                                         style="width: {{ $subject['percentage'] }}%">
                                                        {{ $subject['percentage'] }}%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Detailed Marks Table -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Detailed Marks</h5>
                        </div>
                        <div class="card-body">
                            @if($marks->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>S.No</th>
                                            <th>Exam</th>
                                            <th>Subject</th>
                                            <th>Class</th>
                                            <th>Date</th>
                                            <th>Marks</th>
                                            <th>Percentage</th>
                                            <th>Grade</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($marks as $index => $mark)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $mark->exam->title ?? 'N/A' }}</td>
                                            <td>{{ $mark->exam->subject->name ?? 'N/A' }}</td>
                                            <td>{{ $mark->exam->subject->classModel->full_name ?? 'N/A' }}</td>
                                            <td>{{ isset($mark->exam->exam_date) ? \Carbon\Carbon::parse($mark->exam->exam_date)->format('d M Y') : 'N/A' }}</td>
                                            <td>{{ $mark->total_marks_obtained }}/{{ $mark->total_marks }}</td>
                                            <td>
                                                @php
                                                    $percentage = $mark->total_marks > 0 ? round(($mark->total_marks_obtained / $mark->total_marks) * 100, 2) : 0;
                                                @endphp
                                                {{ $percentage }}%
                                            </td>
                                            <td>
                                                @php
                                                    if($percentage >= 90) $grade = 'A+';
                                                    elseif($percentage >= 80) $grade = 'A';
                                                    elseif($percentage >= 70) $grade = 'B+';
                                                    elseif($percentage >= 60) $grade = 'B';
                                                    elseif($percentage >= 50) $grade = 'C';
                                                    elseif($percentage >= 40) $grade = 'D';
                                                    else $grade = 'F';
                                                @endphp
                                                <span class="badge bg-{{ $percentage >= 50 ? 'success' : 'danger' }}">
                                                    {{ $grade }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($mark->status == 'published')
                                                    <span class="badge bg-success">Published</span>
                                                @else
                                                    <span class="badge bg-warning">Pending</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                No marks found for {{ $displayName }}.
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection