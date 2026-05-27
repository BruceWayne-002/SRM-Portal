@extends('layouts.app')

@section('title', 'Edit Exam Hall')

@section('main')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-edit me-2"></i>Edit Exam Hall: {{ $hall->hall_name }}
                        </h4>
                        <a href="{{ route('admin.halls.storedhalls') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.halls.update', $hall->id) }}" method="POST" id="hallForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Basic Hall Information -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0">
            <i class="fas fa-building me-2"></i>Hall Information
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="room_number" class="form-label">
                    <i class="fas fa-chalkboard me-1"></i> Hall Name *
                </label>
                <input type="text"
                    class="form-control @error('room_number') is-invalid @enderror"
                    id="room_number"
                    name="room_number"
                    value="{{ old('room_number', str_replace(' Exam Hall', '', $hall->hall_name)) }}"
                    readonly>
                @error('room_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="col-md-4 mb-3">
                <label for="capacity" class="form-label">
                    <i class="fas fa-users me-1"></i> Capacity *
                </label>
                <input type="number"
                    class="form-control @error('capacity') is-invalid @enderror"
                    id="capacity"
                    name="capacity"
                    value="{{ old('capacity', $hall->capacity) }}"
                    readonly>
                @error('capacity')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">
                    Maximum number of students
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="rows" class="form-label">
                    <i class="fas fa-border-all me-1"></i> Rows *
                </label>
                <input type="number" 
                       class="form-control @error('rows') is-invalid @enderror" 
                       id="rows" 
                       name="rows" 
                       value="{{ old('rows', $hall->rows) }}"
                       readonly>
                @error('rows')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Number of rows for seating</div>
            </div>

            <div class="col-md-4 mb-3">
                <label for="columns" class="form-label">
                    <i class="fas fa-border-all me-1"></i> Columns *
                </label>
                <input type="number" 
                       class="form-control @error('columns') is-invalid @enderror" 
                       id="columns" 
                       name="columns" 
                       value="{{ old('columns', $hall->columns) }}"
                       readonly>
                @error('columns')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Number of columns for seating</div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="building" class="form-label">
                    <i class="fas fa-building me-1"></i> Building *
                </label>
                <input type="text" 
                       class="form-control @error('building') is-invalid @enderror" 
                       id="building" 
                       name="building" 
                       value="{{ old('building', $hall->building) }}"
                       readonly>
                @error('building')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="floor" class="form-label">
                    <i class="fas fa-layer-group me-1"></i> Floor *
                </label>
                <input type="text" 
                       class="form-control @error('floor') is-invalid @enderror" 
                       id="floor" 
                       name="floor" 
                       value="{{ old('floor', $hall->floor) }}"
                       readonly>
                @error('floor')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>

                        <!-- Exam Selection Section -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">
                                    <i class="fas fa-file-alt me-2"></i>Select Exams (Same Date & Time)
                                </h5>
                            </div>
                            <div class="card-body">
                                @if($availableGroups->isNotEmpty())
                                    @foreach($availableGroups as $key => $examsInGroup)
                                        @php
                                            $firstExam = $examsInGroup->first();
                                            $dateTimeKey = $firstExam->exam_date->format('Y-m-d') . '_' . $firstExam->exam_time;
                                            $formattedDate = $firstExam->exam_date->format('d M Y');
                                            $formattedTime = \Carbon\Carbon::parse($firstExam->exam_time)->format('h:i A');
                                            $examCount = $examsInGroup->count();
                                        @endphp
                                        
                                        <div class="card mb-3 exam-group-card" data-date-time-key="{{ $dateTimeKey }}">
                                            <div class="card-header bg-success text-white">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <i class="far fa-calendar me-1"></i>{{ $formattedDate }}
                                                        <i class="far fa-clock ms-3 me-1"></i>{{ $formattedTime }}
                                                        <span class="badge bg-warning ms-2">{{ $examCount }} exams available</span>
                                                    </div>
                                                    <div>
                                                        <button type="button" class="btn btn-sm btn-light select-all-exams" 
                                                                data-group="{{ $dateTimeKey }}">
                                                            <i class="fas fa-check-square me-1"></i>Select All
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-light deselect-all-exams" 
                                                                data-group="{{ $dateTimeKey }}">
                                                            <i class="fas fa-times-circle me-1"></i>Deselect All
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th width="30">
                                                                    <input type="checkbox" class="form-check-input group-checkbox" 
                                                                           data-group="{{ $dateTimeKey }}"
                                                                           {{ count(array_intersect($examsInGroup->pluck('id')->toArray(), $assignedExamIds)) == count($examsInGroup) ? 'checked' : '' }}>
                                                                </th>
                                                                <th>Subject</th>
                                                                <th>Class</th>
                                                                <th>Exam Type</th>
                                                                <th>Duration</th>
                                                                <th>Students</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($examsInGroup as $exam)
                                                            <tr>
                                                                <td>
                                                                    <input class="form-check-input exam-checkbox" 
                                                                           type="checkbox" 
                                                                           name="exam_ids[]" 
                                                                           id="exam_{{ $exam->id }}" 
                                                                           value="{{ $exam->id }}"
                                                                           data-exam-date="{{ $exam->exam_date->format('Y-m-d') }}"
                                                                           data-exam-time="{{ $exam->exam_time }}"
                                                                           data-date-time-key="{{ $dateTimeKey }}"
                                                                           {{ in_array($exam->id, old('exam_ids', $assignedExamIds)) ? 'checked' : '' }}>
                                                                </td>
                                                                <td>
                                                                    <label for="exam_{{ $exam->id }}" class="form-check-label">
                                                                        <strong>{{ $exam->subject->name ?? 'N/A' }}</strong>
                                                                    </label>
                                                                </td>
                                                                <td>{{ $exam->class->name ?? 'N/A' }}</td>
                                                                <td>
                                                                    <span class="badge bg-primary">{{ ucfirst($exam->exam_type) }}</span>
                                                                </td>
                                                                <td>{{ $exam->duration ?? 120 }} mins</td>
                                                                <td>
                                                                    <span class="badge bg-secondary">
                                                                        {{ $exam->class->students_count ?? 0 }} students
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    @if($exam->hall_id == $hall->id)
                                                                        <span class="badge bg-success">Already Assigned</span>
                                                                    @elseif($exam->hall_id && $exam->hall_id != $hall->id)
                                                                        <span class="badge bg-danger">Assigned to Another Hall</span>
                                                                    @else
                                                                        <span class="badge bg-info">Available</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                    @error('exam_ids')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        No available exams found.
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Teacher Selection -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">
                                    <i class="fas fa-chalkboard-teacher me-2"></i>Select Invigilator
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="teacher_id" class="form-label">
                                            <i class="fas fa-user-tie me-1"></i> Assign Invigilator *
                                        </label>
                                        <select class="form-control @error('teacher_id') is-invalid @enderror" 
                                                id="teacher_id" 
                                                name="teacher_id" required>
                                            <option value="">Select an Invigilator</option>
                                            @foreach($teachers as $teacher)
                                                <option value="{{ $teacher->id }}" 
                                                        {{ old('teacher_id', $hall->teacher_id) == $teacher->id ? 'selected' : '' }}
                                                        data-teacher-id="{{ $teacher->id }}">
                                                    {{ $teacher->name }} - {{ $teacher->email }} 
                                                    (ID: {{ $teacher->employee_id }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('teacher_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text mt-2">
                                            <span id="teacherAvailabilityMessage" class="d-block mb-2"></span>
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Teachers cannot be assigned to multiple halls within 4 hours of each other.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Summary Cards -->
                        <div class="row mb-4">
                            <!-- Selected Exams Summary -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-list-check me-2"></i>Selected Exams 
                                            <span class="badge bg-warning float-end" id="selectedCount">{{ count($assignedExamIds) }}</span>
                                        </h6>
                                    </div>
                                    <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                                        <div id="selectedExamsContainer">
                                            <p class="text-muted mb-0 {{ count($assignedExamIds) > 0 ? 'd-none' : '' }}" id="noExamsMessage">
                                                <i class="fas fa-info-circle me-2"></i>
                                                No exams selected yet. Select exams from above.
                                            </p>
                                            <div class="{{ count($assignedExamIds) > 0 ? '' : 'd-none' }}" id="selectedExamsList">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Subject</th>
                                                            <th>Class</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="selectedExamsBody">
                                                        @foreach($assignedExams as $exam)
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fas fa-check-circle text-success me-2"></i>
                                                                    <div>
                                                                        <strong>{{ $exam->subject->name ?? 'N/A' }}</strong>
                                                                        <br>
                                                                        <small class="text-muted">{{ ucfirst($exam->exam_type) }}</small>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>{{ $exam->class->name ?? 'N/A' }}</td>
                                                            <td>
                                                                <button type="button" class="btn btn-sm btn-outline-danger remove-exam" 
                                                                        data-exam-id="{{ $exam->id }}">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Seating Preview -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">
                                            <i class="fas fa-chair me-1"></i>Seating Arrangement Preview
                                        </h6>
                                    </div>
                                    <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                                        <div id="seatingPreview" class="text-center">
                                            <!-- Will be populated by JavaScript -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Buttons -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary w-100" id="submitBtn">
                                    <i class="fas fa-save me-1"></i> Update Exam Hall
                                </button>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-danger w-100" id="deleteBtn"
                                        data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="fas fa-trash me-1"></i> Delete Hall
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirm Deletion
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this exam hall?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Warning:</strong> This action will unassign all exams from this hall!
                </div>
                <ul>
                    <li>Hall Name: <strong>{{ $hall->hall_name }}</strong></li>
                    <li>Assigned Exams: <strong>{{ count($assignedExamIds) }}</strong></li>
                    <li>Teacher: <strong>{{ $hall->teacher->name ?? 'N/A' }}</strong></li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.halls.destroy', $hall->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i> Delete Hall
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .exam-group-card {
        border: 2px solid #dee2e6;
        transition: all 0.3s;
    }
    .exam-group-card:hover {
        border-color: #0d6efd;
        box-shadow: 0 0 10px rgba(13, 110, 253, 0.1);
    }
    .exam-group-card.active {
        border-color: #198754;
        background-color: rgba(25, 135, 84, 0.05);
    }
    .seat-box {
        width: 35px;
        height: 35px;
        line-height: 35px;
        display: inline-block;
        margin: 2px;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        font-size: 12px;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get DOM elements
    const examCheckboxes = document.querySelectorAll('.exam-checkbox');
    const teacherSelect = document.getElementById('teacher_id');
    const teacherAvailabilityMessage = document.getElementById('teacherAvailabilityMessage');
    const selectedExamsBody = document.getElementById('selectedExamsBody');
    const selectedExamsList = document.getElementById('selectedExamsList');
    const noExamsMessage = document.getElementById('noExamsMessage');
    const selectedCountBadge = document.getElementById('selectedCount');
    const submitBtn = document.getElementById('submitBtn');
    const hallForm = document.getElementById('hallForm');
    
    // Store exam data
    const examData = {};
    
    // Initialize exam data
    examCheckboxes.forEach(checkbox => {
        const examId = checkbox.value;
        const row = checkbox.closest('tr');
        const cells = row.querySelectorAll('td');
        
        examData[examId] = {
            subject: cells[1]?.querySelector('strong')?.textContent?.trim() || 'N/A',
            className: cells[2]?.textContent?.trim() || 'N/A',
            examType: cells[3]?.querySelector('.badge')?.textContent?.trim() || 'Exam',
            duration: cells[4]?.textContent?.replace('mins', '').trim() || '120',
            studentCount: cells[5]?.querySelector('.badge')?.textContent?.replace('students', '').trim() || '0',
            examDate: checkbox.dataset.examDate,
            examTime: checkbox.dataset.examTime,
            dateTimeKey: checkbox.dataset.dateTimeKey
        };
    });
    
    // Function to format time for display
    function formatTimeForDisplay(timeStr) {
        if (!timeStr) return 'N/A';
        const time = new Date(`2000-01-01T${timeStr}`);
        return time.toLocaleTimeString('en-US', { 
            hour: '2-digit', 
            minute: '2-digit',
            hour12: true 
        });
    }
    
    // Function to check teacher availability
    async function checkTeacherAvailability(examDate, examTime, excludeHallId = null) {
        if (!examDate || !examTime) return { unavailableTeachers: [] };
        
        try {
            let url = `/admin/halls/unavailable-teachers?date=${examDate}&time=${examTime}`;
            if (excludeHallId) {
                url += `&exclude=${excludeHallId}`;
            }
            
            const response = await fetch(url);
            if (!response.ok) throw new Error('Network response was not ok');
            return await response.json();
        } catch (error) {
            console.error('Error checking teacher availability:', error);
            return { unavailableTeachers: [] };
        }
    }
    
    // Update teacher dropdown based on selected exams
    async function updateTeacherDropdown() {
        if (!teacherSelect) return;
        
        const selectedExams = Array.from(examCheckboxes).filter(cb => cb.checked);
        
        if (selectedExams.length === 0) {
            teacherAvailabilityMessage.innerHTML = `
                <span class="text-info">
                    <i class="fas fa-info-circle me-1"></i>
                    Select exams first to check teacher availability
                </span>
            `;
            return;
        }
        
        // Get date/time from selected exams
        const firstExam = selectedExams[0];
        const examDate = firstExam.dataset.examDate;
        const examTime = firstExam.dataset.examTime;
        const dateTimeKey = firstExam.dataset.dateTimeKey;
        
        // Check if all selected exams have same date/time
        const allSameDateTime = selectedExams.every(exam => 
            exam.dataset.dateTimeKey === dateTimeKey
        );
        
        if (!allSameDateTime) {
            teacherAvailabilityMessage.innerHTML = `
                <span class="text-danger">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    All selected exams must be from the same date/time
                </span>
            `;
            return;
        }
        
        // Get unavailable teachers (exclude current hall)
        const availabilityData = await checkTeacherAvailability(examDate, examTime, {{ $hall->id }});
        const unavailableTeachers = availabilityData.unavailableTeachers || [];
        
        // Update teacher options
        const options = teacherSelect.querySelectorAll('option');
        let availableCount = 0;
        
        options.forEach(option => {
            if (option.value) {
                const teacherId = parseInt(option.value);
                const isUnavailable = unavailableTeachers.includes(teacherId);
                
                option.disabled = isUnavailable;
                option.style.opacity = isUnavailable ? '0.5' : '1';
                option.style.textDecoration = isUnavailable ? 'line-through' : 'none';
                
                if (!isUnavailable) availableCount++;
            }
        });
        
        // Update availability message
        const formattedDate = new Date(examDate).toLocaleDateString('en-GB', {
            day: 'numeric', month: 'short', year: 'numeric'
        });
        const formattedTime = formatTimeForDisplay(examTime);
        
        teacherAvailabilityMessage.innerHTML = `
            <span class="text-success">
                <i class="fas fa-calendar-alt me-1"></i>${formattedDate} 
                <i class="fas fa-clock ms-2 me-1"></i>${formattedTime}
            </span>
            <br>
            <span class="${availableCount > 0 ? 'text-success' : 'text-danger'}">
                <i class="fas fa-users me-1"></i>${availableCount} teachers available
                ${unavailableTeachers.length > 0 ? `(${unavailableTeachers.length} busy)` : ''}
            </span>
        `;
        
        // Check if current teacher is unavailable
        const selectedTeacherId = parseInt(teacherSelect.value);
        if (selectedTeacherId && unavailableTeachers.includes(selectedTeacherId) && selectedTeacherId != {{ $hall->teacher_id }}) {
            teacherSelect.value = '';
            teacherAvailabilityMessage.innerHTML += `
                <br><span class="text-warning">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Selected teacher is unavailable for this time
                </span>
            `;
        }
    }
    
    // Update selected exams display
    function updateSelectedExamsDisplay() {
        if (!selectedExamsBody) return;
        
        selectedExamsBody.innerHTML = '';
        const selectedExams = Array.from(examCheckboxes).filter(cb => cb.checked);
        const selectedCount = selectedExams.length;
        
        // Update count badge
        if (selectedCountBadge) {
            selectedCountBadge.textContent = selectedCount;
        }
        
        if (selectedCount > 0) {
            // Hide "no exams" message, show list
            noExamsMessage.classList.add('d-none');
            selectedExamsList.classList.remove('d-none');
            
            // Group by date/time to check consistency
            const dateTimeGroups = new Set();
            selectedExams.forEach(cb => dateTimeGroups.add(cb.dataset.dateTimeKey));
            const hasMixedGroups = dateTimeGroups.size > 1;
            
            // Add selected exams to table
            selectedExams.forEach(cb => {
                const examId = cb.value;
                const data = examData[examId];
                
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <div>
                                <strong>${data.subject}</strong>
                                <br>
                                <small class="text-muted">${data.examType}</small>
                            </div>
                        </div>
                    </td>
                    <td>${data.className}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-exam" 
                                data-exam-id="${examId}">
                            <i class="fas fa-times"></i>
                        </button>
                    </td>
                `;
                
                // Highlight if mixed groups
                if (hasMixedGroups) {
                    row.classList.add('table-warning');
                }
                
                selectedExamsBody.appendChild(row);
            });
            
            // Add warning if mixed groups
            if (hasMixedGroups) {
                const warningRow = document.createElement('tr');
                warningRow.classList.add('table-danger');
                warningRow.innerHTML = `
                    <td colspan="3" class="text-center">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> Exams from different time slots selected!
                    </td>
                `;
                selectedExamsBody.appendChild(warningRow);
            }
        } else {
            // Show "no exams" message, hide list
            noExamsMessage.classList.remove('d-none');
            selectedExamsList.classList.add('d-none');
        }
        
        validateExamSelection();
        updateTeacherDropdown();
    }
    
    // Validate exam selection
    function validateExamSelection() {
        const selectedCount = Array.from(examCheckboxes).filter(cb => cb.checked).length;
        
        // Check if all selected exams are from same date/time
        const selectedExams = Array.from(examCheckboxes).filter(cb => cb.checked);
        const dateTimeGroups = new Set();
        selectedExams.forEach(cb => dateTimeGroups.add(cb.dataset.dateTimeKey));
        const hasMixedGroups = dateTimeGroups.size > 1;
        
        let isValid = true;
        let errorMsg = '';
        
        if (selectedCount < 1) {
            errorMsg = 'Please select at least 1 exam';
            isValid = false;
        } else if (selectedCount > 6) {
            errorMsg = 'Maximum 6 exams allowed per hall';
            isValid = false;
        } else if (hasMixedGroups) {
            errorMsg = 'All exams must be from same date/time';
            isValid = false;
        }
        
        if (submitBtn) {
            submitBtn.disabled = !isValid;
            submitBtn.title = errorMsg;
        }
        
        return isValid;
    }
    
    // Group selection handlers
    document.querySelectorAll('.select-all-exams').forEach(btn => {
        btn.addEventListener('click', function() {
            const group = this.dataset.group;
            document.querySelectorAll(`.exam-checkbox[data-date-time-key="${group}"]`).forEach(cb => {
                cb.checked = true;
            });
            updateSelectedExamsDisplay();
        });
    });
    
    document.querySelectorAll('.deselect-all-exams').forEach(btn => {
        btn.addEventListener('click', function() {
            const group = this.dataset.group;
            document.querySelectorAll(`.exam-checkbox[data-date-time-key="${group}"]`).forEach(cb => {
                cb.checked = false;
            });
            updateSelectedExamsDisplay();
        });
    });
    
    // Group checkbox handlers
    document.querySelectorAll('.group-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            const group = this.dataset.group;
            const isChecked = this.checked;
            document.querySelectorAll(`.exam-checkbox[data-date-time-key="${group}"]`).forEach(examCb => {
                examCb.checked = isChecked;
            });
            updateSelectedExamsDisplay();
        });
    });
    
    // Remove exam button handler
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-exam')) {
            const removeBtn = e.target.closest('.remove-exam');
            const examId = removeBtn.dataset.examId;
            
            const checkbox = document.querySelector(`.exam-checkbox[value="${examId}"]`);
            if (checkbox) {
                checkbox.checked = false;
                updateSelectedExamsDisplay();
            }
        }
    });
    
    // Update display when checkboxes change
    examCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedExamsDisplay);
    });
    
    // Seating preview
    function updateSeatingPreview() {
        const rowsInput = document.getElementById('rows');
        const columnsInput = document.getElementById('columns');
        const capacityInput = document.getElementById('capacity');
        
        if (!rowsInput || !columnsInput || !capacityInput) return false;
        
        const rows = parseInt(rowsInput.value) || 0;
        const columns = parseInt(columnsInput.value) || 0;
        const capacity = parseInt(capacityInput.value) || 0;
        
        if (rows <= 0 || columns <= 0) {
            document.getElementById('seatingPreview').innerHTML = 
                '<p class="text-muted">Enter rows and columns to see seating preview</p>';
            return false;
        }

        const totalSeats = rows * columns;
        const fitsCapacity = totalSeats <= capacity;
        const alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        
        let gridHTML = '';
        for (let r = 0; r < rows; r++) {
            for (let c = 1; c <= columns; c++) {
                gridHTML += `<div class="seat-box">${alphabet[r]}${c}</div>`;
            }
            gridHTML += '<br>';
        }

        const html = `
            <div class="row">
                <div class="col-6">
                    <div class="card border-0">
                        <div class="card-body text-center p-2">
                            <h4 class="${fitsCapacity ? 'text-success' : 'text-danger'} mb-1">${totalSeats}</h4>
                            <small class="text-muted">Total Seats</small>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card border-0">
                        <div class="card-body text-center p-2">
                            <h4 class="text-primary mb-1">${capacity}</h4>
                            <small class="text-muted">Room Capacity</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                ${fitsCapacity ? 
                    '<div class="alert alert-success py-2 mb-2"><i class="fas fa-check me-2"></i>Fits within capacity</div>' : 
                    '<div class="alert alert-danger py-2 mb-2"><i class="fas fa-times me-2"></i>Exceeds capacity</div>'
                }
                <div class="mt-3">
                    <small class="text-muted d-block mb-2">Seating Layout:</small>
                    <div class="border rounded p-3 bg-light">
                        ${gridHTML}
                    </div>
                </div>
            </div>
        `;
        
        document.getElementById('seatingPreview').innerHTML = html;
        return fitsCapacity;
    }
    
    // Event listeners for seating preview
    document.getElementById('rows')?.addEventListener('input', updateSeatingPreview);
    document.getElementById('columns')?.addEventListener('input', updateSeatingPreview);
    
    // Form submission
    hallForm?.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Validate exam selection
        if (!validateExamSelection()) {
            alert('Please select 1-6 exams from the same date/time group.');
            return false;
        }
        
        // Validate seating
        if (!updateSeatingPreview()) {
            alert('Seating arrangement exceeds room capacity. Please adjust rows/columns.');
            return false;
        }
        
        // Validate teacher
        if (!teacherSelect.value) {
            alert('Please select an invigilator for this exam hall.');
            return false;
        }
        
        // Get selected exams date/time
        const selectedExams = Array.from(examCheckboxes).filter(cb => cb.checked);
        if (selectedExams.length === 0) {
            alert('Please select at least one exam.');
            return false;
        }
        
        const firstExam = selectedExams[0];
        const examDate = firstExam.dataset.examDate;
        const examTime = firstExam.dataset.examTime;
        
        // Verify teacher availability (excluding current hall)
        const availabilityData = await checkTeacherAvailability(examDate, examTime, {{ $hall->id }});
        const unavailableTeachers = availabilityData.unavailableTeachers || [];
        const selectedTeacherId = parseInt(teacherSelect.value);
        
        if (unavailableTeachers.includes(selectedTeacherId) && selectedTeacherId != {{ $hall->teacher_id }}) {
            alert('Selected invigilator is unavailable for this time slot. Please select another teacher.');
            return false;
        }
        
        // Submit the form
        this.submit();
    });
    
    // Initialize
    updateSelectedExamsDisplay();
    updateSeatingPreview();
    updateTeacherDropdown();
});
</script>
@endpush