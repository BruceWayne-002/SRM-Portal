@extends('layouts.student')

@section('title', 'Exam Marks Details')

@section('main')

@php
// Helper function to format numbers without .00
function formatNumber($number) {
    if (is_numeric($number)) {
        if (floor($number) == $number) {
            return (string) floor($number);
        } else {
            return number_format($number, 2);
        }
    }
    return $number;
}

// Helper function to calculate grade
function calculateGrade($percentage) {
    if ($percentage >= 90) return ['A+', 'success'];
    if ($percentage >= 80) return ['A', 'success'];
    if ($percentage >= 70) return ['B+', 'info'];
    if ($percentage >= 60) return ['B', 'info'];
    if ($percentage >= 50) return ['C', 'warning'];
    if ($percentage >= 40) return ['D', 'warning'];
    return ['F', 'danger'];
}
@endphp

<div class="container-fluid px-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-file-alt me-2"></i>Exam Marks Details
                        </h4>
                        <a href="{{ route('student.marks.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to Marks
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <!-- Exam Information -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Exam Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <p><strong>Exam Title:</strong> {{ $exam->title ?? $exam->name ?? 'N/A' }}</p>
                                    <p><strong>Subject:</strong> {{ $exam->subject->name ?? 'N/A' }}</p>
                                    <p><strong>Subject Code:</strong> {{ $exam->subject->code ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <p><strong>Exam Type:</strong> {{ ucfirst($exam->exam_type ?? 'N/A') }}</p>
                                    <p><strong>Date:</strong> {{ $exam->exam_date ? \Carbon\Carbon::parse($exam->exam_date)->format('d M Y') : 'N/A' }}</p>
                                    <p><strong>Semester:</strong> {{ $exam->subject->semester ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <p><strong>Total Marks:</strong> {{ $totalMarks ?? $mark->total_marks ?? 'N/A' }}</p>
                                    <p><strong>Status:</strong> 
                                        @if($mark->status == 'published')
                                            <span class="badge bg-success">Published</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Marks Details -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Marks Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="40%">Internal Marks</th>
                                            <td>{{ formatNumber($internalMarks ?? 0) }}</td>
                                        </tr>
                                        <tr>
                                            <th>External Marks</th>
                                            <td>{{ formatNumber($externalMarks ?? 0) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Total Obtained</th>
                                            <td><strong>{{ formatNumber($totalObtained ?? 0) }}</strong></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="40%">Percentage</th>
                                            <td>
                                                @php
                                                    $percentage = $totalMarks > 0 ? round(($totalObtained / $totalMarks) * 100, 2) : 0;
                                                    list($grade, $gradeClass) = calculateGrade($percentage);
                                                @endphp
                                                <strong>{{ formatNumber($percentage) }}%</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Grade</th>
                                            <td>
                                                <span class="badge bg-{{ $gradeClass }}">{{ $grade }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Result</th>
                                            <td>
                                                @php
                                                    $isPassed = $percentage >= 40;
                                                @endphp
                                                @if($isPassed)
                                                    <span class="badge bg-success">PASS</span>
                                                @else
                                                    <span class="badge bg-danger">FAIL</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Comparison Statistics -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Class Statistics</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Your Rank</th>
                                            <td><strong>#{{ $rank ?? 'N/A' }}</strong> out of {{ $allMarks->count() ?? 0 }}</td>
                                        </tr>
                                        <tr>
                                            <th>Highest Mark</th>
                                            <td><strong class="text-success">{{ formatNumber($highestMark ?? 0) }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Lowest Mark</th>
                                            <td><strong class="text-danger">{{ formatNumber($lowestMark ?? 0) }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Class Average</th>
                                            <td><strong>{{ formatNumber($classAverage ?? 0) }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Your Marks</th>
                                            <td><strong>{{ formatNumber($totalObtained ?? 0) }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Difference from Average</th>
                                            <td>
                                                @php
                                                    $diff = ($totalObtained ?? 0) - ($classAverage ?? 0);
                                                @endphp
                                                <span class="{{ $diff >= 0 ? 'text-success' : 'text-danger' }}">
                                                    <strong>{{ formatNumber($diff) }}</strong>
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Performance Chart -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Performance Comparison</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="scoreChart" style="max-height: 300px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('scoreChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Your Score', 'Class Average', 'Highest Score'],
            datasets: [{
                label: 'Marks',
                data: [{{ $totalObtained ?? 0 }}, {{ $classAverage ?? 0 }}, {{ $highestMark ?? 0 }}],
                backgroundColor: [
                    'rgba(100, 13, 60, 0.8)',
                    'rgba(255, 193, 7, 0.8)',
                    'rgba(40, 167, 69, 0.8)'
                ],
                borderColor: [
                    'rgba(100, 13, 60, 1)',
                    'rgba(255, 193, 7, 1)',
                    'rgba(40, 167, 69, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: {{ $totalMarks ?? 100 }}
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});
</script>
@endpush
@endsection