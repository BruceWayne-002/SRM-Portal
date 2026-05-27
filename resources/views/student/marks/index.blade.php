@extends('layouts.student')

@section('title', 'My Marks')

@section('main')
<style>
:root {
    --primary: #640d3c;
    --primary-light: #8b1b5a;
    --primary-soft: rgba(100, 13, 60, 0.1);
    --primary-gradient: linear-gradient(135deg, #640d3c, #8b1b5a);
}

/* Card Styles */
.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(100, 13, 60, 0.1);
    margin-bottom: 1.5rem;
    overflow: hidden;
}

.card-header {
    border-bottom: none;
    padding: 1rem 1.5rem;
}

.card-header.bg-primary {
    background: var(--primary-gradient) !important;
}

.card-header.bg-info {
    background: var(--primary) !important;
}

.card-header.bg-light {
    background: #f8f9fa !important;
    border-bottom: 2px solid var(--primary-soft);
}

.card-header h4, .card-header h5 {
    font-weight: 600;
}

/* Form Elements */
.form-select, .form-control {
    border-radius: 10px;
    border: 1px solid rgba(100, 13, 60, 0.2);
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
}

.form-select:focus, .form-control:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 0.25rem rgba(100, 13, 60, 0.25);
}

.form-select-lg {
    font-size: 1rem;
}

.form-label {
    color: var(--primary);
    margin-bottom: 0.5rem;
    font-weight: 600;
}

/* Buttons */
.btn-primary {
    background: var(--primary-gradient);
    border: none;
    border-radius: 10px;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(100, 13, 60, 0.3);
}

.btn-primary:disabled {
    background: #ccc;
    opacity: 0.7;
}

.btn-success {
    background: linear-gradient(135deg, #28a745, #20c997);
    border: none;
    border-radius: 10px;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
}

.btn-secondary {
    background: linear-gradient(135deg, #6c757d, #5a6268);
    border: none;
    border-radius: 10px;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-secondary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(108, 117, 125, 0.3);
}

/* Statistics Cards */
.stat-card {
    border-radius: 15px;
    padding: 1.5rem;
    height: 100%;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

.stat-card.bg-primary {
    background: var(--primary-gradient) !important;
}

.stat-card.bg-success {
    background: linear-gradient(135deg, #28a745, #20c997) !important;
}

.stat-card.bg-info {
    background: linear-gradient(135deg, #17a2b8, #0dcaf0) !important;
}

.stat-card.bg-warning {
    background: linear-gradient(135deg, #ffc107, #fd7e14) !important;
}

.stat-card .card-title {
    font-size: 1rem;
    opacity: 0.9;
    margin-bottom: 1rem;
}

.stat-card h2 {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
}

@media (max-width: 768px) {
    .stat-card h2 {
        font-size: 2rem;
    }
}

/* Tables */
.table-responsive {
    border-radius: 10px;
    overflow-x: auto;
}

.table {
    margin-bottom: 0;
}

.table thead th {
    background: var(--primary);
    color: white;
    font-weight: 600;
    border: none;
    padding: 1rem;
    white-space: nowrap;
}

.table tbody td {
    padding: 1rem;
    vertical-align: middle;
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(100, 13, 60, 0.02);
}

.table-bordered {
    border: 1px solid rgba(100, 13, 60, 0.1);
}

.table-bordered td, .table-bordered th {
    border: 1px solid rgba(100, 13, 60, 0.1);
}

.table tfoot {
    background: var(--primary-soft);
}

.table tfoot td {
    padding: 1rem;
    font-weight: 600;
}

/* Badges */
.badge {
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-weight: 500;
    font-size: 0.85rem;
}

.badge.bg-success {
    background: linear-gradient(135deg, #28a745, #20c997) !important;
}

.badge.bg-danger {
    background: linear-gradient(135deg, #dc3545, #c82333) !important;
}

.badge.bg-warning {
    background: linear-gradient(135deg, #ffc107, #fd7e14) !important;
}

.badge.bg-info {
    background: linear-gradient(135deg, #17a2b8, #0dcaf0) !important;
}

/* Progress Bars */
.progress {
    height: 25px;
    border-radius: 50px;
    background-color: #e9ecef;
    margin: 0.5rem 0;
    overflow: hidden;
}

.progress-bar {
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.85rem;
    transition: width 0.6s ease;
}

.progress-bar.bg-success {
    background: linear-gradient(135deg, #28a745, #20c997) !important;
}

.progress-bar.bg-warning {
    background: linear-gradient(135deg, #ffc107, #fd7e14) !important;
}

.progress-bar.bg-danger {
    background: linear-gradient(135deg, #dc3545, #c82333) !important;
}

/* Alerts */
.alert {
    border: none;
    border-radius: 15px;
    padding: 1rem 1.5rem;
    margin-bottom: 1.5rem;
}

.alert-warning {
    background: rgba(255, 193, 7, 0.1);
    color: #856404;
    border-left: 4px solid #ffc107;
}

.alert-info {
    background: rgba(100, 13, 60, 0.1);
    color: var(--primary);
    border-left: 4px solid var(--primary);
}

.alert-danger {
    background: rgba(220, 53, 69, 0.1);
    color: #721c24;
    border-left: 4px solid #dc3545;
}

.alert-success {
    background: rgba(40, 167, 69, 0.1);
    color: #155724;
    border-left: 4px solid #28a745;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .container-fluid {
        padding-left: 1rem;
        padding-right: 1rem;
    }

    .card-header {
        padding: 1rem;
    }

    .card-body {
        padding: 1rem;
    }

    .form-select-lg, .btn-lg {
        font-size: 0.95rem;
        padding: 0.6rem 1rem;
    }

    .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }

    .d-flex.align-items-end {
        margin-top: 1rem;
    }

    .row.mb-4 > div {
        margin-bottom: 1rem;
    }
}
</style>

@php
// Helper function to format numbers without .00
if (!function_exists('formatNumber')) {
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
}

// Helper function to calculate grade
if (!function_exists('calculateGrade')) {
    function calculateGrade($percentage) {
        if ($percentage >= 90) return ['A+', 'success'];
        if ($percentage >= 80) return ['A', 'success'];
        if ($percentage >= 70) return ['B+', 'info'];
        if ($percentage >= 60) return ['B', 'info'];
        if ($percentage >= 50) return ['C', 'warning'];
        if ($percentage >= 40) return ['D', 'warning'];
        return ['F', 'danger'];
    }
}
@endphp

<div class="container-fluid px-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>My Academic Performance
                    </h4>
                </div>
                <div class="card-body">
                    
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if(isset($error))
                        <div class="alert alert-danger">{{ $error }}</div>
                    @endif

                    <!-- Student Information Card -->
                    @if(isset($student) && $student)
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-user-graduate me-2"></i>Student Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-2 mb-md-0">
                                    <strong>Name:</strong> {{ $student->name ?? 'N/A' }}
                                </div>
                                <div class="col-md-4 mb-2 mb-md-0">
                                    <strong>Roll No:</strong> {{ $student->roll_no ?? 'N/A' }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Class:</strong> {{ $student->class_name ?? 'N/A' }} - {{ $student->section ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Semester and Exam Type Selector Card -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Select Semester and Exam Type</h5>
                                </div>
                                <div class="card-body">
                                    <form method="GET" action="{{ route('student.marks.index') }}" id="filterForm">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-5">
                                                <label for="semester" class="form-label fw-bold">Select Semester:</label>
                                                <select name="semester" id="semester" class="form-select form-select-lg" required>
                                                    <option value="">-- Choose Semester --</option>
                                                    @foreach($semesters as $semester)
                                                        <option value="{{ $semester }}" {{ $selectedSemester == $semester ? 'selected' : '' }}>
                                                            Semester {{ $semester }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="exam_type" class="form-label fw-bold">Select Exam Type:</label>
                                                <select name="exam_type" id="exam_type" class="form-select form-select-lg" required {{ !$selectedSemester ? 'disabled' : '' }}>
                                                    <option value="">-- Choose Exam Type --</option>
                                                    @foreach($examTypes as $type)
                                                        <option value="{{ $type }}" {{ $selectedExamType == $type ? 'selected' : '' }}>
                                                            {{ ucfirst($type) }} Examination
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3 d-flex align-items-end">
                                                <button type="submit" class="btn btn-primary" id="showBtn" {{ !$selectedSemester ? 'disabled' : '' }}>
                                                    </i>Show Marks
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                    
                                    @if($selectedSemester && $selectedExamType && $marks->isNotEmpty())
                                        <div class="row mt-3">
                                            <div class="col-md-12 text-end">
                                                <a href="{{ route('student.marks.download-pdf', ['semester' => $selectedSemester, 'exam_type' => $selectedExamType]) }}" 
                                                   class="btn btn-success">
                                                    <i class="fas fa-download me-2"></i>Download PDF (Semester {{ $selectedSemester }} - {{ ucfirst($selectedExamType) }})
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics and Marks Display -->
                    @if($selectedSemester && $selectedExamType)
                        @if($marks->isNotEmpty())

                            <!-- Detailed Marks Table -->
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Detailed Marks - Semester {{ $selectedSemester }} ({{ ucfirst($selectedExamType) }} Examination)</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>S.No</th>
                                                    <th>Subject Code</th>
                                                    <th>Subject Name</th>
                                                    <th>Internal</th>
                                                    <th>External</th>
                                                    <th>Total</th>
                                                    <th>Percentage</th>
                                                    <th>Grade</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($marks as $index => $mark)
                                                @php
                                                    $internal = $mark->internal_marks ?? 0;
                                                    $external = $mark->external_marks ?? 0;
                                                    $total = $mark->marks_obtained ?? ($internal + $external);
                                                    $maxMarks = $mark->total_marks ?? 100;
                                                    $percentage = $maxMarks > 0 ? round(($total / $maxMarks) * 100, 2) : 0;
                                                    
                                                    list($grade, $gradeClass) = calculateGrade($percentage);
                                                    
                                                    $isPassed = $percentage >= 40;
                                                    $status = $isPassed ? 'PASS' : 'FAIL';
                                                    $statusClass = $isPassed ? 'success' : 'danger';
                                                @endphp
                                                <tr class="{{ $isPassed ? '' : 'table-danger' }}">
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $mark->exam->subject->code ?? $mark->exam->subject_code ?? 'N/A' }}</td>
                                                    <td>{{ $mark->exam->subject->name ?? $mark->exam->title ?? 'N/A' }}</td>
                                                    <td>{{ formatNumber($internal) }}</td>
                                                    <td>{{ formatNumber($external) }}</td>
                                                    <td><strong>{{ formatNumber($total) }}/{{ formatNumber($maxMarks) }}</strong></td>
                                                    <td>{{ formatNumber($percentage) }}%</td>
                                                    <td>
                                                        <span class="badge bg-{{ $gradeClass }}">
                                                            {{ $grade }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $statusClass }}">{{ $status }}</span>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>

                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                No published marks found for Semester {{ $selectedSemester }} - {{ ucfirst($selectedExamType) }} examination.
                            </div>
                        @endif
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Please select a semester and exam type from the dropdowns above to view your marks.
                        </div>
                    @endif

                    <div class="mt-4">
                        <a href="{{ route('student.dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const semesterSelect = document.getElementById('semester');
    const examTypeSelect = document.getElementById('exam_type');
    const showBtn = document.getElementById('showBtn');
    
    if (semesterSelect) {
        // Handle semester change - load exam types via AJAX
        semesterSelect.addEventListener('change', function() {
            const semester = this.value;
            
            if (semester) {
                // Show loading state
                examTypeSelect.disabled = true;
                examTypeSelect.innerHTML = '<option value="">Loading exam types...</option>';
                showBtn.disabled = true;
                
                // Fetch exam types for selected semester
                fetch(`{{ route("student.marks.get-exam-types") }}?semester=${semester}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    examTypeSelect.innerHTML = '<option value="">-- Choose Exam Type --</option>';
                    
                    if (data.exam_types && data.exam_types.length > 0) {
                        data.exam_types.forEach(type => {
                            const option = document.createElement('option');
                            option.value = type;
                            option.textContent = type.charAt(0).toUpperCase() + type.slice(1) + ' Examination';
                            examTypeSelect.appendChild(option);
                        });
                        examTypeSelect.disabled = false;
                        showBtn.disabled = false;
                    } else {
                        examTypeSelect.innerHTML = '<option value="">No exam types available</option>';
                        examTypeSelect.disabled = true;
                        showBtn.disabled = true;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    examTypeSelect.innerHTML = '<option value="">Error loading exam types</option>';
                    examTypeSelect.disabled = true;
                    showBtn.disabled = true;
                });
            } else {
                // Disable exam type select and show button
                examTypeSelect.disabled = true;
                showBtn.disabled = true;
                examTypeSelect.innerHTML = '<option value="">-- Choose Exam Type --</option>';
            }
        });
    }
});
</script>
@endpush
@endsection