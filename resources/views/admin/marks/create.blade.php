{{-- resources/views/admin/marks/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Enter Marks - ' . ($exam->subject_name ?? $exam->subject->name ?? 'Exam'))

@section('main')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary">
            <i class="fas fa-pen-alt me-2"></i>Enter Marks - {{ $exam->subject_name ?? $exam->subject->name ?? 'Exam' }}
        </h2>
        <div>
            <a href="{{ route('admin.marks.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Exam Info Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Exam Details</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="text-muted small mb-1">Exam Date</label>
                        <p class="h6">{{ $exam->exam_date ? \Carbon\Carbon::parse($exam->exam_date)->format('d M Y') : 'N/A' }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="text-muted small mb-1">Time</label>
                        <p class="h6">{{ $exam->formatted_time ?? ($exam->exam_time ? \Carbon\Carbon::parse($exam->exam_time)->format('h:i A') : 'N/A') }} ({{ $exam->time_session == 'FN' ? 'Forenoon' : 'Afternoon' }})</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="text-muted small mb-1">Subject</label>
                        <p class="h6">{{ $exam->subject->name ?? $exam->subject_name }} ({{ $exam->subject->code ?? $exam->subject_code }})</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="text-muted small mb-1">Class</label>
                        <p class="h6">{{ $exam->class_name ?? ($exam->subject->class_name ?? 'N/A') }} - {{ $exam->subject->section ?? '' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Marks Distribution Info -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body text-center py-3">
                    <h6 class="small text-uppercase mb-2">Total Marks</h6>
                    <h3 class="mb-0">{{ ($subject->internal_marks ?? 0) + ($subject->external_marks ?? 0) }}</h3>
                    <small>Passing: 35 marks total</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body text-center py-3">
                    <h6 class="small text-uppercase mb-2">Internal Marks</h6>
                    <h3 class="mb-0">{{ $subject->internal_marks ?? 0 }}</h3>
                    <small>Passing: 10 marks minimum</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-white shadow-sm">
                <div class="card-body text-center py-3">
                    <h6 class="small text-uppercase mb-2">External Marks</h6>
                    <h3 class="mb-0">{{ $subject->external_marks ?? 0 }}</h3>
                    <small>Passing: 20 marks minimum</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Marks Entry Form -->
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Marks Entry</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.marks.store', $exam->id) }}" id="marksForm">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="draft">Save as Draft</option>
                            <option value="published">Publish Immediately</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="button" class="btn btn-info me-2" onclick="calculateAll()">
                                <i class="fas fa-calculator me-1"></i> Calculate All
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="clearAll()">
                                <i class="fas fa-eraser me-1"></i> Clear All
                            </button>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="3%">#</th>
                                <th width="7%">Roll No</th>
                                <th width="15%">Student Name</th>
                                <th width="10%">Internal (Max: {{ $subject->internal_marks ?? 0 }})</th>
                                <th width="10%">External (Max: {{ $subject->external_marks ?? 0 }})</th>
                                <th width="10%">Absent</th>
                                <th width="7%">Total</th>
                                <th width="7%">%</th>
                                <th width="5%">Grade</th>
                                <th width="10%">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $index => $student)
                                @php
                                    $existingMark = $existingMarks[$student->id] ?? null;
                                    $internalValue = $existingMark ? $existingMark->internal_marks : '';
                                    $externalValue = $existingMark ? $existingMark->external_marks : '';
                                    $absentStatus = $existingMark ? $existingMark->absent_status : '';
                                    $remarks = $existingMark ? $existingMark->remarks : '';
                                    $grade = $existingMark ? $existingMark->grade : '';
                                @endphp
                                <tr id="row_{{ $student->id }}" class="{{ $grade == 'AB' ? 'table-secondary' : ($grade == 'F' ? 'table-danger' : ($grade && $grade != '-' ? 'table-success' : '')) }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $student->roll_no ?? 'N/A' }}</strong></td>
                                    <td>
                                        {{ $student->name }}
                                        <br><small class="text-muted">{{ $student->class_name ?? '' }} - {{ $student->section ?? '' }}</small>
                                    </td>
                                    <td>
                                        <input type="number" 
                                               name="internal_marks[{{ $student->id }}]" 
                                               id="internal_{{ $student->id }}"
                                               class="form-control form-control-sm internal-input" 
                                               data-student="{{ $student->id }}"
                                               value="{{ old('internal_marks.' . $student->id, $internalValue) }}"
                                               min="0" 
                                               max="{{ $subject->internal_marks ?? 25 }}" 
                                               step="0.01"
                                               onchange="calculateRow({{ $student->id }})"
                                               onkeyup="calculateRow({{ $student->id }})"
                                               {{ $absentStatus == 'internal' || $absentStatus == 'both' ? 'disabled' : '' }}>
                                    </td>
                                    <td>
                                        <input type="number" 
                                               name="external_marks[{{ $student->id }}]" 
                                               id="external_{{ $student->id }}"
                                               class="form-control form-control-sm external-input" 
                                               data-student="{{ $student->id }}"
                                               value="{{ old('external_marks.' . $student->id, $externalValue) }}"
                                               min="0" 
                                               max="{{ $subject->external_marks ?? 75 }}" 
                                               step="0.01"
                                               onchange="calculateRow({{ $student->id }})"
                                               onkeyup="calculateRow({{ $student->id }})"
                                               {{ $absentStatus == 'external' || $absentStatus == 'both' ? 'disabled' : '' }}>
                                    </td>
                                    <td>
                                        <select name="absent[{{ $student->id }}]" 
                                                id="absent_{{ $student->id }}"
                                                class="form-select form-select-sm absent-select"
                                                data-student="{{ $student->id }}"
                                                onchange="handleAbsent({{ $student->id }})">
                                            <option value="">Present</option>
                                            <option value="internal" {{ $absentStatus == 'internal' ? 'selected' : '' }}>Absent (Internal)</option>
                                            <option value="external" {{ $absentStatus == 'external' ? 'selected' : '' }}>Absent (External)</option>
                                            <option value="both" {{ $absentStatus == 'both' ? 'selected' : '' }}>Absent (Both)</option>
                                        </select>
                                    </td>
                                    <td class="total-cell align-middle fw-bold" id="total_{{ $student->id }}">
                                        @if($grade == 'AB')
                                            ABSENT
                                        @elseif($existingMark && $existingMark->total_marks_obtained)
                                            {{ number_format($existingMark->total_marks_obtained, 2) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="percentage-cell align-middle" id="percentage_{{ $student->id }}">
                                        @if($grade == 'AB')
                                            -
                                        @elseif($existingMark && $existingMark->percentage)
                                            {{ number_format($existingMark->percentage, 2) }}%
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="grade-cell align-middle" id="grade_{{ $student->id }}">
                                        @if($grade)
                                            @if($grade == 'AB')
                                                <span class="badge bg-secondary">AB</span>
                                            @elseif($grade == 'F')
                                                <span class="badge bg-danger">F</span>
                                            @elseif($grade != '-')
                                                <span class="badge bg-success">{{ $grade }}</span>
                                            @else
                                                -
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <input type="text" 
                                               name="remarks[{{ $student->id }}]" 
                                               class="form-control form-control-sm remark-input"
                                               value="{{ old('remarks.' . $student->id, $remarks) }}"
                                               placeholder="Optional">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="10" class="text-end">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="me-3">
                                                <strong>Total Students:</strong> <span class="badge bg-primary" id="totalStudents">{{ $students->count() }}</span>
                                            </span>
                                            <span class="me-3">
                                                <strong>Entered:</strong> <span class="badge bg-success" id="enteredCount">0</span>
                                            </span>
                                            <span>
                                                <strong>Pending:</strong> <span class="badge bg-danger" id="pendingCount">{{ $students->count() }}</span>
                                            </span>
                                        </div>
                                        <div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-1"></i> Save Marks
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Absent Legend -->
<div class="row mt-3">
    <div class="col-12">
        <div class="card bg-light">
            <div class="card-body py-2">
                <div class="d-flex align-items-center flex-wrap">
                    <span class="me-3"><strong>Passing Criteria:</strong></span>
                    <span class="badge bg-success me-2">Internal ≥ 10</span>
                    <span class="badge bg-warning me-2">External ≥ 20</span>
                    <span class="badge bg-primary me-3">Total ≥ 35</span>
                    
                    <span class="me-3 ms-3"><strong>Rules:</strong></span>
                    <span class="badge bg-danger me-2">F</span> = Failed (absent in any component or below passing)
                    <span class="badge bg-secondary me-2">AB</span> = Both components absent
                    
                    <span class="me-3 ms-3"><strong>Result:</strong></span>
                    <span class="badge bg-danger me-2">⚠️ Absent in either Internal OR External = Automatic FAIL (F)</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    updateCounts();
    
    // Initialize absent selects and disable inputs
    document.querySelectorAll('.absent-select').forEach(select => {
        if (select.value) {
            handleAbsent(select.dataset.student);
        }
    });
});

function handleAbsent(studentId) {
    const absentSelect = document.getElementById(`absent_${studentId}`);
    const internalInput = document.getElementById(`internal_${studentId}`);
    const externalInput = document.getElementById(`external_${studentId}`);
    const row = document.getElementById(`row_${studentId}`);
    
    const absentValue = absentSelect.value;
    
    // Reset disabled state and background
    internalInput.disabled = false;
    externalInput.disabled = false;
    internalInput.classList.remove('bg-light');
    externalInput.classList.remove('bg-light');
    internalInput.style.backgroundColor = '';
    externalInput.style.backgroundColor = '';
    
    if (absentValue === 'internal') {
        // Internal absent
        internalInput.value = '';
        internalInput.disabled = true;
        internalInput.classList.add('bg-light');
        internalInput.style.backgroundColor = '#f8d7da';
        
    } else if (absentValue === 'external') {
        // External absent
        externalInput.value = '';
        externalInput.disabled = true;
        externalInput.classList.add('bg-light');
        externalInput.style.backgroundColor = '#f8d7da';
        
    } else if (absentValue === 'both') {
        // Both absent
        internalInput.value = '';
        externalInput.value = '';
        internalInput.disabled = true;
        externalInput.disabled = true;
        internalInput.classList.add('bg-light');
        externalInput.classList.add('bg-light');
        internalInput.style.backgroundColor = '#f8d7da';
        externalInput.style.backgroundColor = '#f8d7da';
    }
    
    // Recalculate the row
    calculateRow(studentId);
}

function calculateRow(studentId) {
    const internalInput = document.getElementById(`internal_${studentId}`);
    const externalInput = document.getElementById(`external_${studentId}`);
    const absentSelect = document.getElementById(`absent_${studentId}`);
    const totalCell = document.getElementById(`total_${studentId}`);
    const percentageCell = document.getElementById(`percentage_${studentId}`);
    const gradeCell = document.getElementById(`grade_${studentId}`);
    const row = document.getElementById(`row_${studentId}`);
    
    const absentValue = absentSelect ? absentSelect.value : '';
    const maxInternal = parseFloat(internalInput.max) || 0;
    const maxExternal = parseFloat(externalInput.max) || 0;
    const maxTotal = maxInternal + maxExternal;
    
    // Passing marks criteria
    const internalPassing = 10;  // Minimum 10 marks in internal
    const externalPassing = 20;  // Minimum 20 marks in external
    const totalPassing = 35;      // Minimum 35 marks total
    
    const internal = parseFloat(internalInput.value) || 0;
    const external = parseFloat(externalInput.value) || 0;
    const total = internal + external;
    
    // Calculate percentages
    let internalPercent = maxInternal > 0 ? (internal / maxInternal) * 100 : 0;
    let externalPercent = maxExternal > 0 ? (external / maxExternal) * 100 : 0;
    let overallPercent = maxTotal > 0 ? (total / maxTotal) * 100 : 0;
    
    // CRITICAL RULE: If student is absent in either internal OR external, they FAIL the entire exam
    let grade = 'F';
    let statusClass = 'danger';
    
    if (absentValue === 'both') {
        // Both components absent
        totalCell.textContent = '0';
        percentageCell.textContent = '0%';
        grade = 'AB';
        statusClass = 'secondary';
        row.classList.remove('table-success', 'table-danger', 'table-warning');
        row.classList.add('table-secondary');
        
    } else if (absentValue === 'internal' || absentValue === 'external') {
        // Absent in either internal OR external - AUTOMATIC FAIL
        totalCell.textContent = total.toFixed(2);
        percentageCell.textContent = overallPercent.toFixed(2) + '%';
        grade = 'F';
        statusClass = 'danger';
        row.classList.remove('table-success', 'table-warning', 'table-secondary');
        row.classList.add('table-danger');
        
    } else {
        // No absence - both components present
        if (internal > 0 || external > 0) {
            totalCell.textContent = total.toFixed(2);
            percentageCell.textContent = overallPercent.toFixed(2) + '%';
            
            // Check passing criteria
            let internalPass = internal >= internalPassing;
            let externalPass = external >= externalPassing;
            let totalPass = total >= totalPassing;
            
            if (internalPass && externalPass && totalPass) {
                grade = calculateGrade(overallPercent);
                statusClass = 'success';
                row.classList.remove('table-danger', 'table-warning', 'table-secondary');
                row.classList.add('table-success');
            } else {
                grade = 'F';
                statusClass = 'danger';
                row.classList.remove('table-success', 'table-warning', 'table-secondary');
                row.classList.add('table-danger');
            }
        } else {
            // No marks entered
            totalCell.textContent = '-';
            percentageCell.textContent = '-';
            grade = '-';
            row.classList.remove('table-success', 'table-danger', 'table-warning', 'table-secondary');
        }
    }
    
    // Update grade cell
    if (grade === 'AB') {
        gradeCell.innerHTML = '<span class="badge bg-secondary">AB</span>';
    } else if (grade === 'F') {
        gradeCell.innerHTML = '<span class="badge bg-danger">F</span>';
    } else if (grade !== '-') {
        gradeCell.innerHTML = `<span class="badge bg-success">${grade}</span>`;
    } else {
        gradeCell.innerHTML = '-';
    }
    
    updateCounts();
}

function calculateAll() {
    const internalInputs = document.querySelectorAll('.internal-input');
    internalInputs.forEach(input => {
        const studentId = input.dataset.student;
        calculateRow(studentId);
    });
}

function clearAll() {
    if (confirm('Clear all marks entries?')) {
        const internalInputs = document.querySelectorAll('.internal-input');
        const externalInputs = document.querySelectorAll('.external-input');
        const absentSelects = document.querySelectorAll('.absent-select');
        const remarkInputs = document.querySelectorAll('.remark-input');
        
        internalInputs.forEach(input => {
            input.value = '';
            input.disabled = false;
            input.classList.remove('bg-light');
            input.style.backgroundColor = '';
        });
        
        externalInputs.forEach(input => {
            input.value = '';
            input.disabled = false;
            input.classList.remove('bg-light');
            input.style.backgroundColor = '';
        });
        
        absentSelects.forEach(select => {
            select.value = '';
        });
        
        remarkInputs.forEach(input => {
            input.value = '';
        });
        
        // Reset all cells
        document.querySelectorAll('.total-cell').forEach(cell => cell.textContent = '-');
        document.querySelectorAll('.percentage-cell').forEach(cell => cell.textContent = '-');
        document.querySelectorAll('.grade-cell').forEach(cell => cell.innerHTML = '-');
        
        // Remove all row classes
        document.querySelectorAll('tbody tr').forEach(row => {
            row.classList.remove('table-success', 'table-danger', 'table-warning', 'table-secondary');
        });
        
        updateCounts();
    }
}

function calculateGrade(percentage) {
    if (percentage >= 90) return 'A+';
    if (percentage >= 80) return 'A';
    if (percentage >= 70) return 'B+';
    if (percentage >= 60) return 'B';
    if (percentage >= 50) return 'C+';
    if (percentage >= 40) return 'C';
    if (percentage >= 35) return 'D';
    return 'F';
}

function updateCounts() {
    const internalInputs = document.querySelectorAll('.internal-input');
    
    // Count unique students with any marks or absent
    const uniqueStudents = new Set();
    internalInputs.forEach(input => {
        const studentId = input.dataset.student;
        const absentSelect = document.getElementById(`absent_${studentId}`);
        
        if (input.value !== '' || 
            document.getElementById(`external_${studentId}`).value !== '' ||
            (absentSelect && absentSelect.value !== '')) {
            uniqueStudents.add(studentId);
        }
    });
    
    document.getElementById('enteredCount').textContent = uniqueStudents.size;
    document.getElementById('pendingCount').textContent = internalInputs.length - uniqueStudents.size;
}
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {

    document.querySelectorAll('.internal-input').forEach(function(input) {
        input.addEventListener('input', function () {
            let max = parseFloat(this.getAttribute('max'));
            let value = parseFloat(this.value);

            if (value > max) {
                this.value = max;
            }

            if (value < 0) {
                this.value = 0;
            }
        });
    });

    document.querySelectorAll('.external-input').forEach(function(input) {
        input.addEventListener('input', function () {
            let max = parseFloat(this.getAttribute('max'));
            let value = parseFloat(this.value);

            if (value > max) {
                this.value = max;
            }

            if (value < 0) {
                this.value = 0;
            }
        });
    });

});
</script>
@endpush