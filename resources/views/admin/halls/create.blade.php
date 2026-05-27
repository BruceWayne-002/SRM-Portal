@extends('layouts.app')

@section('title', 'Create Exam Hall')
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('main')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-plus-circle me-2"></i>Create Exam Hall
                        </h4>
                        <a href="{{ route('admin.halls.storedhalls') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.halls.store') }}" method="POST" id="hallForm">
                        @csrf
                        
                        <input type="hidden" name="classroom_id" value="{{ $class->id ?? '' }}">
                        
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
                                            value="{{ old('room_number', $class->room_number ?? '') }}"
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
                                            value="{{ old('capacity', $class->capacity ?? '') }}"
                                            min="1"
                                            readonly>
                                        @error('capacity')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">
                                            Maximum number of students allowed
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
                                               value="{{ old('rows', 0) }}"
                                               min="1" 
                                               max="50"
                                               placeholder="e.g., 10"
                                               required>
                                        @error('rows')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="columns" class="form-label">
                                            <i class="fas fa-border-all me-1"></i> Columns *
                                        </label>
                                        <input type="number" 
                                               class="form-control @error('columns') is-invalid @enderror" 
                                               id="columns" 
                                               name="columns" 
                                               value="{{ old('columns',0) }}"
                                               min="1" 
                                               max="20"
                                               placeholder="e.g., 10"
                                               required>
                                        @error('columns')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-calculator me-1"></i> Total Seats
                                        </label>
                                        <input type="text" 
                                               class="form-control bg-light" 
                                               id="totalSeats" 
                                               readonly
                                               value="0">
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
                                               value="{{ old('building', $class->building ?? '') }}"
                                               placeholder="e.g., Main Building"
                                               required>
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
                                               value="{{ old('floor', $class->floor ?? '') }}"
                                               placeholder="e.g., Ground Floor"
                                               required>
                                        @error('floor')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Date and Session Filter -->
                        <div class="card mb-4">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-filter me-2"></i>Filter Exams by Date & Session
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-5">
                                        <label for="exam_date" class="form-label">
                                            <i class="fas fa-calendar me-1"></i> Select Exam Date
                                        </label>
                                        <select class="form-control" id="exam_date" name="exam_date">
                                            <option value="">All Dates</option>
                                            @foreach($availableDates as $date)
                                                <option value="{{ $date['date'] }}" {{ $selectedDate == $date['date'] ? 'selected' : '' }}>
                                                    {{ $date['formatted'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-5">
                                        <label for="session" class="form-label">
                                            <i class="fas fa-clock me-1"></i> Select Session
                                        </label>
                                        <select class="form-control" id="session" name="session">
                                            <option value="">All Sessions</option>
                                            <option value="FN" {{ $selectedSession == 'FN' ? 'selected' : '' }}>FN (Morning) - 6 AM to 12 PM</option>
                                            <option value="AN" {{ $selectedSession == 'AN' ? 'selected' : '' }}>AN (Afternoon) - 12 PM to 6 PM</option>
                                            <option value="EV" {{ $selectedSession == 'EV' ? 'selected' : '' }}>EV (Evening) - After 6 PM</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="button" class="btn btn-secondary w-100" id="resetFilterBtn">
                                            <i class="fas fa-undo me-1"></i> Reset
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Exam Selection Section -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">
                                    <i class="fas fa-file-alt me-2"></i>Select Exams
                                </h5>
                                <small class="text-muted" id="filterInfo">
                                    @if($selectedDate || $selectedSession)
                                        Showing exams for 
                                        @if($selectedDate)
                                            {{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}
                                        @endif
                                        @if($selectedSession)
                                            @if($selectedSession == 'FN') Morning @elseif($selectedSession == 'AN') Afternoon @else Evening @endif Session
                                        @endif
                                    @else
                                        Select exams to allocate students to this hall
                                    @endif
                                </small>
                            </div>
                            <div class="card-body" id="examsContainer">
                                @if($availableGroups->isNotEmpty())
                                    @foreach($availableGroups as $key => $examsInGroup)
                                        @php
                                            $firstExam = $examsInGroup->first();
                                            $keyParts = explode('_', $key);
                                            $examDate = $keyParts[0];
                                            $session = $keyParts[1] ?? '';
                                            
                                            // Get session full name
                                            $sessionFull = '';
                                            if ($session == 'FN') {
                                                $sessionFull = 'Morning (FN)';
                                            } elseif ($session == 'AN') {
                                                $sessionFull = 'Afternoon (AN)';
                                            } elseif ($session == 'EV') {
                                                $sessionFull = 'Evening (EV)';
                                            }
                                            
                                            $formattedDate = \Carbon\Carbon::parse($examDate)->format('d M Y');
                                            $examCount = $examsInGroup->count();
                                            $groupTotalStudents = $examsInGroup->sum(function($exam) {
                                                return $exam->remaining_students ?? $exam->student_count ?? 0;
                                            });
                                        @endphp
                                        
                                        <div class="card mb-3 exam-group-card" data-date="{{ $examDate }}" data-session="{{ $session }}">
                                            <div class="card-header bg-success text-white">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <i class="far fa-calendar me-1"></i>{{ $formattedDate }}
                                                        <span class="badge bg-warning ms-2">{{ $sessionFull }}</span>
                                                        <span class="badge bg-info ms-2">{{ $examCount }} exams</span>
                                                        <span class="badge bg-primary ms-2">
                                                            <i class="fas fa-users me-1"></i>{{ $groupTotalStudents }} total students
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <button type="button" class="btn btn-sm btn-light select-all-exams" 
                                                                data-group="{{ $key }}">
                                                            <i class="fas fa-check-square me-1"></i>Select All
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-light deselect-all-exams" 
                                                                data-group="{{ $key }}">
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
                                                                           data-group="{{ $key }}">
                                                                </th>
                                                                <th>Subject</th>
                                                                <th>Class</th>
                                                                <th>Exam Time</th>
                                                                <th>Exam Type</th>
                                                                <th>Duration</th>
                                                                <th>Total Students</th>
                                                                <th>Already Allocated</th>
                                                                <th>Available Now</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($examsInGroup as $exam)
                                                            @php
                                                                $totalStudents = $exam->student_count ?? 0;
                                                                $allocatedStudents = $exam->allocated_students ?? 0;
                                                                $remainingStudents = $exam->remaining_students ?? $totalStudents;
                                                                $examTimeFormatted = \Carbon\Carbon::parse($exam->exam_time)->format('h:i A');
                                                            @endphp
                                                            <tr class="{{ $exam->hall_id ? 'table-info' : '' }}">
                                                                <td>
                                                                    <input class="form-check-input exam-checkbox" 
                                                                           type="checkbox" 
                                                                           name="exam_ids[]" 
                                                                           id="exam_{{ $exam->id }}" 
                                                                           value="{{ $exam->id }}"
                                                                           data-exam-id="{{ $exam->id }}"
                                                                           data-exam-date="{{ $exam->exam_date->format('Y-m-d') }}"
                                                                           data-exam-time="{{ $exam->exam_time }}"
                                                                           data-session="{{ $session }}"
                                                                           data-date-time-key="{{ $key }}"
                                                                           data-total-students="{{ $totalStudents }}"
                                                                           data-allocated-students="{{ $allocatedStudents }}"
                                                                           data-remaining-students="{{ $remainingStudents }}"
                                                                           data-hall-id="{{ $exam->hall_id }}"
                                                                           data-subject-name="{{ $exam->subject->name ?? 'N/A' }}"
                                                                           data-class-name="{{ $exam->subject->classModel->full_name ?? $exam->subject->classModel->name ?? 'N/A' }}"
                                                                           data-exam-type="{{ ucfirst($exam->exam_type) }}"
                                                                           data-exam-time-formatted="{{ $examTimeFormatted }}"
                                                                           {{ in_array($exam->id, old('exam_ids', [])) ? 'checked' : '' }}>
                                                                </td>
                                                                <td>
                                                                    <label for="exam_{{ $exam->id }}" class="form-check-label">
                                                                        <strong>{{ $exam->subject->name ?? 'N/A' }}</strong>
                                                                    </label>
                                                                </td>
                                                                <td>{{ $exam->subject->classModel->full_name ?? $exam->subject->classModel->name ?? 'N/A' }}</td>
                                                                <td>{{ $examTimeFormatted }}</td>
                                                                <td>
                                                                    <span class="badge bg-primary">{{ ucfirst($exam->exam_type) }}</span>
                                                                </td>
                                                                <td>{{ $exam->duration_minutes ?? 120 }} mins</td>
                                                                <td><span class="badge bg-secondary">{{ $totalStudents }}</span></td>
                                                                <td><span class="badge bg-info">{{ $allocatedStudents }}</span></td>
                                                                <td>
                                                                    <span class="badge bg-{{ $remainingStudents > 0 ? 'success' : 'secondary' }}">
                                                                        {{ $remainingStudents }}
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    @if($exam->hall_id)
                                                                        <span class="badge bg-warning">Partial</span>
                                                                    @else
                                                                        <span class="badge bg-success">Available</span>
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
                                        No exams found for the selected filters. Please try different filters or create exams first.
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
                                                        {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                                    {{ $teacher->name }} - {{ $teacher->email }} 
                                                    (ID: {{ $teacher->employee_id }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('teacher_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div id="teacherAvailabilityMessage" class="form-text mt-2"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Selected Exams Summary with Total, Allocated, Balance -->
                        <div class="card mb-4">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-list-check me-2"></i>Selected Exams Allocation
                                    <span class="badge bg-warning float-end" id="selectedCount">0</span>
                                </h6>
                            </div>
                            <div class="card-body">
                                <div id="selectedExamsContainer">
                                    <p class="text-muted mb-0" id="noExamsMessage">
                                        <i class="fas fa-info-circle me-2"></i>
                                        No exams selected yet. Select exams from above.
                                    </p>
                                    <div class="d-none" id="selectedExamsList">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Subject</th>
                                                        <th>Class</th>
                                                        <th>Date</th>
                                                        <th>Session</th>
                                                        <th>Time</th>
                                                        <th>Total Students</th>
                                                        <th>Allocate to This Hall</th>
                                                        <th>Balance for Others</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="selectedExamsBody"></tbody>
                                                <tfoot class="table-info">
                                                    <tr>
                                                        <td colspan="5"><strong>GRAND TOTAL</strong></td>
                                                        <td><strong id="totalStudents">0</strong></td>
                                                        <td><strong id="totalAllocated">0</strong></td>
                                                        <td><strong id="totalBalance">0</strong></td>
                                                        <td colspan="2"></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                        <div class="alert alert-info mt-2 mb-0">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>Balance Students:</strong> <span id="balanceInfo">0</span> students will remain in original exams for allocation to other halls.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Allocation Summary -->
                        <div class="card mb-4">
                            <div class="card-header bg-warning">
                                <h6 class="mb-0">
                                    <i class="fas fa-chart-pie me-2"></i>Allocation Summary
                                </h6>
                            </div>
                            <div class="card-body" id="allocationSummary">
                                <p class="text-muted text-center mb-0">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Select exams and enter allocation numbers to see summary
                                </p>
                            </div>
                        </div>

                        <!-- Capacity Alert -->
                        <div id="capacityAlert" class="alert d-none mb-4"></div>

                        <!-- Form Buttons -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary w-100" id="submitBtn">
                                    <i class="fas fa-save me-1"></i> Create & Next (Seat Layout)
                                </button>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ route('admin.halls.storedhalls') }}" class="btn btn-secondary w-100">
                                    <i class="fas fa-times me-1"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
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
    .table-info {
        background-color: rgba(13, 202, 240, 0.1);
    }
    .allocation-progress {
        height: 10px;
        border-radius: 5px;
    }
    .form-check-label {
        cursor: pointer;
        display: block;
        padding: 0.25rem 0;
    }
    .exam-checkbox, .group-checkbox {
        cursor: pointer;
    }
    #teacher_id option[disabled] {
        background-color: #f8d7da;
        color: #721c24;
    }
    .allocation-input {
        width: 80px;
        text-align: center;
    }
    .balance-positive {
        color: #dc3545;
        font-weight: bold;
    }
    .balance-zero {
        color: #28a745;
        font-weight: bold;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const examCheckboxes = document.querySelectorAll('.exam-checkbox');
    const teacherSelect = document.getElementById('teacher_id');
    const teacherAvailabilityMessage = document.getElementById('teacherAvailabilityMessage');
    const selectedExamsBody = document.getElementById('selectedExamsBody');
    const selectedExamsList = document.getElementById('selectedExamsList');
    const noExamsMessage = document.getElementById('noExamsMessage');
    const selectedCountBadge = document.getElementById('selectedCount');
    const totalStudentsSpan = document.getElementById('totalStudents');
    const totalAllocatedSpan = document.getElementById('totalAllocated');
    const totalBalanceSpan = document.getElementById('totalBalance');
    const balanceInfo = document.getElementById('balanceInfo');
    const submitBtn = document.getElementById('submitBtn');
    const hallForm = document.getElementById('hallForm');
    const capacityInput = document.getElementById('capacity');
    const rowsInput = document.getElementById('rows');
    const columnsInput = document.getElementById('columns');
    const totalSeatsInput = document.getElementById('totalSeats');
    const capacityAlert = document.getElementById('capacityAlert');
    const allocationSummary = document.getElementById('allocationSummary');
    const examDateSelect = document.getElementById('exam_date');
    const sessionSelect = document.getElementById('session');
    const filterInfo = document.getElementById('filterInfo');

    // Store allocation values for each exam
    const examAllocations = new Map();

    // Calculate total seats
    function calculateTotalSeats() {
        const rows = parseInt(rowsInput.value) || 0;
        const columns = parseInt(columnsInput.value) || 0;
        const total = rows * columns;
        totalSeatsInput.value = total;
        return total;
    }

    // Format time
    function formatTime(timeStr) {
        if (!timeStr) return 'N/A';
        try {
            const [hours, minutes] = timeStr.split(':');
            const date = new Date();
            date.setHours(parseInt(hours), parseInt(minutes));
            return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
        } catch (e) {
            return timeStr;
        }
    }

    // Get session full name
    function getSessionFull(session) {
        if (session === 'FN') return 'Morning (FN)';
        if (session === 'AN') return 'Afternoon (AN)';
        if (session === 'EV') return 'Evening (EV)';
        return session;
    }

    // Filter exams by date and session
    function filterExams() {
        const selectedDate = examDateSelect.value;
        const selectedSession = sessionSelect.value;
        
        // Update URL with filters
        const url = new URL(window.location.href);
        if (selectedDate) {
            url.searchParams.set('exam_date', selectedDate);
        } else {
            url.searchParams.delete('exam_date');
        }
        if (selectedSession) {
            url.searchParams.set('session', selectedSession);
        } else {
            url.searchParams.delete('session');
        }
        window.history.pushState({}, '', url);
        
        // Update filter info text
        let filterText = 'Showing exams for ';
        if (selectedDate) {
            const dateObj = new Date(selectedDate);
            const formattedDate = dateObj.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
            filterText += formattedDate + ' ';
        }
        if (selectedSession) {
            filterText += getSessionFull(selectedSession) + ' Session';
        }
        if (!selectedDate && !selectedSession) {
            filterText = 'Select exams to allocate students to this hall';
        }
        filterInfo.textContent = filterText;

        // Show/hide exam groups based on filters
        const examGroups = document.querySelectorAll('.exam-group-card');
        let visibleGroups = 0;

        examGroups.forEach(group => {
            const groupDate = group.dataset.date;
            const groupSession = group.dataset.session;
            
            let showGroup = true;
            
            if (selectedDate && groupDate !== selectedDate) {
                showGroup = false;
            }
            
            if (selectedSession && groupSession !== selectedSession) {
                showGroup = false;
            }
            
            if (showGroup) {
                group.style.display = 'block';
                visibleGroups++;
            } else {
                group.style.display = 'none';
                
                // Uncheck any checkboxes in hidden groups
                const checkboxes = group.querySelectorAll('.exam-checkbox:checked');
                checkboxes.forEach(cb => {
                    cb.checked = false;
                });
            }
        });

        // Show message if no groups visible
        const examsContainer = document.getElementById('examsContainer');
        const existingAlert = examsContainer.querySelector('.alert-warning:not(.no-exams-alert)');
        
        if (visibleGroups === 0 && examGroups.length > 0) {
            if (existingAlert) {
                existingAlert.remove();
            }
            
            const alert = document.createElement('div');
            alert.className = 'alert alert-warning no-exams-alert';
            alert.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>No exams found for the selected filters. Please try different filters.';
            examsContainer.appendChild(alert);
        } else {
            if (existingAlert) {
                existingAlert.remove();
            }
        }

        // Update selected exams display after filtering
        updateSelectedExamsDisplay();
    }

    // Check teacher availability
    async function checkTeacherAvailability(examDate, examTime) {
        if (!examDate || !examTime) return { unavailableTeachers: [] };
        
        try {
            const response = await fetch(`/admin/halls/unavailable-teachers?date=${examDate}&time=${examTime}`);
            if (!response.ok) throw new Error('Network response was not ok');
            return await response.json();
        } catch (error) {
            console.error('Error checking teacher availability:', error);
            return { unavailableTeachers: [] };
        }
    }

    // Update teacher dropdown
    async function updateTeacherDropdown() {
        const selectedExams = Array.from(document.querySelectorAll('.exam-checkbox:checked'));
        
        if (selectedExams.length === 0) {
            teacherAvailabilityMessage.innerHTML = '<span class="text-info"><i class="fas fa-info-circle me-1"></i>Select exams to check teacher availability</span>';
            document.querySelectorAll('#teacher_id option').forEach(opt => {
                if (opt.value) {
                    opt.disabled = false;
                    opt.style.opacity = '1';
                }
            });
            return;
        }

        const firstExam = selectedExams[0];
        const examDate = firstExam.dataset.examDate;
        const examTime = firstExam.dataset.examTime;
        const dateTimeKey = firstExam.dataset.dateTimeKey;
        const session = firstExam.dataset.session;

        const allSameDateTime = selectedExams.every(exam => exam.dataset.dateTimeKey === dateTimeKey);
        
        if (!allSameDateTime) {
            teacherAvailabilityMessage.innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-triangle me-1"></i>All selected exams must be from the same date/time session</span>';
            return;
        }

        const availabilityData = await checkTeacherAvailability(examDate, examTime);
        const unavailableTeachers = availabilityData.unavailableTeachers || [];

        const options = document.querySelectorAll('#teacher_id option');
        let availableCount = 0;
        
        options.forEach(option => {
            if (option.value) {
                const teacherId = parseInt(option.value);
                const isUnavailable = unavailableTeachers.includes(teacherId);
                option.disabled = isUnavailable;
                option.style.opacity = isUnavailable ? '0.5' : '1';
                if (!isUnavailable) availableCount++;
            }
        });

        const formattedDate = new Date(examDate).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
        const formattedTime = formatTime(examTime);
        
        teacherAvailabilityMessage.innerHTML = `
            <span class="text-success"><i class="fas fa-calendar-alt me-1"></i>${formattedDate} <i class="fas fa-clock ms-2 me-1"></i>${formattedTime} (${getSessionFull(session)})</span><br>
            <span class="${availableCount > 0 ? 'text-success' : 'text-danger'}"><i class="fas fa-users me-1"></i>${availableCount} teachers available</span>
        `;
    }

    // Create allocation input handler
    function createAllocationInputHandler(input) {
        return function() {
            const examId = input.dataset.examId;
            const remaining = parseInt(input.dataset.remaining);
            let value = parseInt(input.value) || 0;
            
            // Validate input
            if (value < 0) value = 0;
            if (value > remaining) value = remaining;
            
            input.value = value;
            
            // Update map
            if (value > 0) {
                examAllocations.set(examId, value);
            } else {
                examAllocations.delete(examId);
            }
            
            // Recalculate all totals
            recalculateTotals();
            validateCapacity();
            updateAllocationSummary();
            
            // Update the input's dataset to reflect current value
            input.dataset.currentValue = value;
        };
    }

    // Update selected exams display with Total, Allocated, Balance
    function updateSelectedExamsDisplay() {
        const selectedExams = Array.from(document.querySelectorAll('.exam-checkbox:checked'));
        const selectedCount = selectedExams.length;
        
        selectedCountBadge.textContent = selectedCount;

        if (selectedCount > 0) {
            let grandTotal = 0;
            let grandAllocated = 0;
            
            selectedExamsBody.innerHTML = '';
            
            selectedExams.forEach(cb => {
                const examId = cb.value;
                const totalStudents = parseInt(cb.dataset.totalStudents) || 0;
                const remainingStudents = parseInt(cb.dataset.remainingStudents) || 0;
                
                grandTotal += totalStudents;
                
                // Get current allocation value from map or default to 0
                let currentAllocation = examAllocations.get(examId) || 0;
                
                // Ensure allocation doesn't exceed remaining
                if (currentAllocation > remainingStudents) {
                    currentAllocation = remainingStudents;
                    examAllocations.set(examId, currentAllocation);
                }
                
                grandAllocated += currentAllocation;
                const balance = remainingStudents - currentAllocation;
                
                const examDate = cb.dataset.examDate;
                const formattedDate = new Date(examDate).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
                const session = cb.dataset.session;
                const examTime = cb.dataset.examTimeFormatted || formatTime(cb.dataset.examTime);
                
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>
                        <strong>${cb.dataset.subjectName || 'N/A'}</strong>
                        <br><small class="text-muted">${cb.dataset.examType || 'Exam'}</small>
                    </td>
                    <td>${cb.dataset.className || 'N/A'}</td>
                    <td>${formattedDate}</td>
                    <td><span class="badge bg-info">${getSessionFull(session)}</span></td>
                    <td>${examTime}</td>
                    <td><span class="badge bg-secondary">${totalStudents}</span></td>
                    <td>
    <input type="number"
           class="form-control form-control-sm allocation-input"
           data-exam-id="${examId}"
           data-remaining="${remainingStudents}"
           data-total="${totalStudents}"
           value="${currentAllocation ?? 0}"
           min="0"
           step="1"
           style="width:80px; text-align:center;">
</td>

                    <td class="${balance > 0 ? 'balance-positive' : 'balance-zero'}">
                        ${balance}
                    </td>
                    <td>
                        ${cb.dataset.hallId ? '<span class="badge bg-info">Partial</span>' : '<span class="badge bg-success">New</span>'}
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-exam" data-exam-id="${cb.value}">
                            <i class="fas fa-times"></i>
                        </button>
                    </td>
                `;
                selectedExamsBody.appendChild(row);
            });
            
            // Update grand totals
            totalStudentsSpan.textContent = grandTotal;
            totalAllocatedSpan.textContent = grandAllocated;
            const grandBalance = grandTotal - grandAllocated;
            totalBalanceSpan.textContent = grandBalance;
            balanceInfo.textContent = grandBalance;
            
            if (grandBalance > 0) {
                balanceInfo.className = 'balance-positive';
            } else {
                balanceInfo.className = 'balance-zero';
            }
            
            // Add event listeners to allocation inputs
            document.querySelectorAll('.allocation-input').forEach(input => {
                // Remove any existing listeners by cloning and replacing
                const newInput = input.cloneNode(true);
                input.parentNode.replaceChild(newInput, input);
                
                // Add new listener
                newInput.addEventListener('input', createAllocationInputHandler(newInput));
                newInput.addEventListener('blur', function() {
                    // Ensure value is valid on blur
                    if (this.value === '' || this.value < 0) {
                        this.value = 0;
                        const examId = this.dataset.examId;
                        examAllocations.delete(examId);
                        recalculateTotals();
                        validateCapacity();
                        updateAllocationSummary();
                    }
                });
            });
            
            noExamsMessage.classList.add('d-none');
            selectedExamsList.classList.remove('d-none');
            
            // Update group checkboxes
            updateGroupCheckboxes();
            validateCapacity();
            updateAllocationSummary();
        } else {
            noExamsMessage.classList.remove('d-none');
            selectedExamsList.classList.add('d-none');
            totalStudentsSpan.textContent = '0';
            totalAllocatedSpan.textContent = '0';
            totalBalanceSpan.textContent = '0';
            balanceInfo.textContent = '0';
            examAllocations.clear();
            updateAllocationSummary();
            capacityAlert.classList.add('d-none');
        }
        
        updateTeacherDropdown();
    }

    // Recalculate all totals
    function recalculateTotals() {
        let grandTotal = 0;
        let grandAllocated = 0;
        
        document.querySelectorAll('.exam-checkbox:checked').forEach(cb => {
            const examId = cb.value;
            const total = parseInt(cb.dataset.totalStudents) || 0;
            const allocated = examAllocations.get(examId) || 0;
            
            grandTotal += total;
            grandAllocated += allocated;
        });
        
        totalStudentsSpan.textContent = grandTotal;
        totalAllocatedSpan.textContent = grandAllocated;
        const grandBalance = grandTotal - grandAllocated;
        totalBalanceSpan.textContent = grandBalance;
        balanceInfo.textContent = grandBalance;
        
        if (grandBalance > 0) {
            balanceInfo.className = 'balance-positive';
        } else {
            balanceInfo.className = 'balance-zero';
        }
        
        return { grandTotal, grandAllocated, grandBalance };
    }

    // Update group checkboxes
    function updateGroupCheckboxes() {
        const groupCheckboxes = document.querySelectorAll('.group-checkbox');
        
        groupCheckboxes.forEach(groupCb => {
            const group = groupCb.dataset.group;
            const groupExams = document.querySelectorAll(`.exam-checkbox[data-date-time-key="${group}"]`);
            const checkedExams = document.querySelectorAll(`.exam-checkbox[data-date-time-key="${group}"]:checked`);
            
            if (groupExams.length > 0) {
                groupCb.checked = groupExams.length === checkedExams.length;
                groupCb.indeterminate = checkedExams.length > 0 && checkedExams.length < groupExams.length;
            }
        });
    }

    // Validate capacity and allocations
    function validateCapacity() {
        const capacity = parseInt(capacityInput.value) || 0;
        const totalSeats = calculateTotalSeats();
        const { grandAllocated } = recalculateTotals();
        
        if (grandAllocated === 0) {
            showAlert('warning', 'Please enter number of students to allocate from each exam.');
            submitBtn.disabled = true;
            return false;
        }

        if (capacity <= 0) {
            showAlert('danger', 'Hall capacity must be greater than 0.');
            submitBtn.disabled = true;
            return false;
        }

        if (totalSeats < capacity) {
            showAlert('danger', `Not enough seats! Total seats: ${totalSeats}, Hall capacity: ${capacity}`);
            submitBtn.disabled = true;
            return false;
        }

        if (grandAllocated > capacity) {
            showAlert('danger', `Total allocated students (${grandAllocated}) exceeds hall capacity (${capacity}). Please reduce allocations.`);
            submitBtn.disabled = true;
            return false;
        }

        capacityAlert.classList.add('d-none');
        submitBtn.disabled = false;
        return true;
    }

    // Show alert
    function showAlert(type, message) {
        capacityAlert.className = `alert alert-${type} mb-4`;
        capacityAlert.innerHTML = `<i class="fas fa-exclamation-triangle me-2"></i>${message}`;
        capacityAlert.classList.remove('d-none');
    }

    // Update allocation summary
    function updateAllocationSummary() {
        const selectedExams = Array.from(document.querySelectorAll('.exam-checkbox:checked'));
        const capacity = parseInt(capacityInput.value) || 0;
        const { grandTotal, grandAllocated, grandBalance } = recalculateTotals();
        
        if (selectedExams.length === 0 || capacity === 0 || grandAllocated === 0) {
            allocationSummary.innerHTML = '<p class="text-muted text-center mb-0"><i class="fas fa-info-circle me-2"></i>Select exams and enter allocation numbers to see summary</p>';
            return;
        }

        const percentage = grandTotal > 0 ? (grandAllocated / grandTotal * 100).toFixed(1) : 0;

        // Build allocation details
        const allocationDetails = [];
        selectedExams.forEach(cb => {
            const examId = cb.value;
            const total = parseInt(cb.dataset.totalStudents) || 0;
            const remaining = parseInt(cb.dataset.remainingStudents) || 0;
            const allocated = examAllocations.get(examId) || 0;
            const balance = remaining - allocated;
            
            if (allocated > 0 || balance > 0) {
                allocationDetails.push({
                    subject: cb.dataset.subjectName,
                    total: total,
                    remaining: remaining,
                    allocated: allocated,
                    balance: balance
                });
            }
        });

        let html = `
            <div class="text-center mb-3">
                <div class="display-4 mb-2">${grandAllocated}/${grandTotal}</div>
                <span class="badge bg-${grandBalance > 0 ? 'warning' : 'success'} fs-6">
                    <i class="fas fa-${grandBalance > 0 ? 'exclamation-triangle' : 'check-circle'} me-1"></i>
                    ${grandBalance > 0 ? 'Partial' : 'Complete'} Allocation
                </span>
            </div>
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                    <small>Allocation Progress</small>
                    <small>${percentage}%</small>
                </div>
                <div class="progress allocation-progress">
                    <div class="progress-bar bg-${grandBalance > 0 ? 'warning' : 'success'}" 
                         role="progressbar" 
                         style="width: ${percentage}%;"
                         aria-valuenow="${percentage}" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                    </div>
                </div>
            </div>
            
            <h6 class="mt-3 mb-2">Detailed Breakdown:</h6>
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Total</th>
                        <th>Available Now</th>
                        <th>Allocate Here</th>
                        <th>Balance for Others</th>
                    </tr>
                </thead>
                <tbody>
        `;
        
        allocationDetails.forEach(detail => {
            html += `
                <tr>
                    <td>${detail.subject}</td>
                    <td>${detail.total}</td>
                    <td>${detail.remaining}</td>
                    <td class="text-success fw-bold">${detail.allocated}</td>
                    <td class="${detail.balance > 0 ? 'text-danger fw-bold' : 'text-muted'}">${detail.balance}</td>
                </tr>
            `;
        });
        
        html += `
                </tbody>
                <tfoot>
                    <tr class="table-info">
                        <td><strong>TOTAL</strong></td>
                        <td><strong>${grandTotal}</strong></td>
                        <td><strong>${grandTotal}</strong></td>
                        <td><strong class="text-success">${grandAllocated}</strong></td>
                        <td><strong class="${grandBalance > 0 ? 'text-danger' : 'text-muted'}">${grandBalance}</strong></td>
                    </tr>
                </tfoot>
            </table>
            
            <div class="alert ${grandBalance > 0 ? 'alert-warning' : 'alert-success'} mt-2">
                <i class="fas fa-${grandBalance > 0 ? 'exclamation-triangle' : 'check-circle'} me-2"></i>
                <strong>${grandAllocated}</strong> students will be allocated to this hall. 
                <strong>${grandBalance}</strong> students will remain for allocation to other halls.
            </div>
        `;
        
        allocationSummary.innerHTML = html;
    }

    // Prepare form data before submission
    function prepareFormData() {
        // Remove old allocation inputs
        document.querySelectorAll('input[name^="allocations["]').forEach(input => input.remove());
        
        // Create hidden inputs for allocations
        examAllocations.forEach((value, examId) => {
            if (value > 0) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `allocations[${examId}]`;
                input.value = value;
                hallForm.appendChild(input);
            }
        });
    }

    // Event Listeners
    rowsInput.addEventListener('input', function() {
        calculateTotalSeats();
        validateCapacity();
        updateAllocationSummary();
    });
    
    columnsInput.addEventListener('input', function() {
        calculateTotalSeats();
        validateCapacity();
        updateAllocationSummary();
    });
    
    capacityInput.addEventListener('input', function() {
        validateCapacity();
        updateAllocationSummary();
    });

    // Auto filter on select change
    examDateSelect.addEventListener('change', function() {
        filterExams();
    });

    sessionSelect.addEventListener('change', function() {
        filterExams();
    });

    // Select All exams in a group
    document.querySelectorAll('.select-all-exams').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const group = this.dataset.group;
            document.querySelectorAll(`.exam-checkbox[data-date-time-key="${group}"]`).forEach(cb => {
                cb.checked = true;
            });
            updateSelectedExamsDisplay();
        });
    });

    // Deselect All exams in a group
    document.querySelectorAll('.deselect-all-exams').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const group = this.dataset.group;
            document.querySelectorAll(`.exam-checkbox[data-date-time-key="${group}"]`).forEach(cb => {
                cb.checked = false;
                
                // Clear allocation for deselected exams
                const examId = cb.value;
                examAllocations.delete(examId);
            });
            updateSelectedExamsDisplay();
        });
    });

    // Group checkbox
    document.querySelectorAll('.group-checkbox').forEach(cb => {
        cb.addEventListener('change', function(e) {
            const group = this.dataset.group;
            const isChecked = e.target.checked;
            
            document.querySelectorAll(`.exam-checkbox[data-date-time-key="${group}"]`).forEach(examCb => {
                examCb.checked = isChecked;
                
                // Clear allocation if unchecked
                if (!isChecked) {
                    const examId = examCb.value;
                    examAllocations.delete(examId);
                }
            });
            updateSelectedExamsDisplay();
        });
    });

    // Individual exam checkbox
    examCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const examId = this.value;
            
            // Clear allocation if unchecked
            if (!this.checked) {
                examAllocations.delete(examId);
            }
            
            updateSelectedExamsDisplay();
        });
    });

    // Remove exam from selected list
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-exam')) {
            const examId = e.target.closest('.remove-exam').dataset.examId;
            const checkbox = document.querySelector(`.exam-checkbox[value="${examId}"]`);
            if (checkbox) {
                checkbox.checked = false;
                examAllocations.delete(examId);
                updateSelectedExamsDisplay();
            }
        }
    });

    // Form submission
    hallForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const selectedExams = Array.from(document.querySelectorAll('.exam-checkbox:checked'));
        
        if (selectedExams.length === 0) {
            alert('Please select at least one exam.');
            return false;
        }

        const dateTimeGroups = new Set();
        selectedExams.forEach(cb => dateTimeGroups.add(cb.dataset.dateTimeKey));
        
        if (dateTimeGroups.size > 1) {
            alert('All selected exams must be from the same date and time session.');
            return false;
        }

        const { grandAllocated } = recalculateTotals();
        if (grandAllocated === 0) {
            alert('Please enter number of students to allocate from each exam.');
            return false;
        }

        const capacity = parseInt(capacityInput.value);
        if (capacity <= 0) {
            alert('Hall capacity must be greater than 0.');
            return false;
        }

        if (grandAllocated > capacity) {
            alert(`Total allocated students (${grandAllocated}) exceeds hall capacity (${capacity}). Please reduce allocations.`);
            return false;
        }

        const totalSeats = calculateTotalSeats();
        if (totalSeats < capacity) {
            alert(`Not enough seats! Total seats: ${totalSeats}, Hall capacity: ${capacity}`);
            return false;
        }

        if (!teacherSelect.value) {
            alert('Please select an invigilator.');
            return false;
        }

        const firstExam = selectedExams[0];
        const examDate = firstExam.dataset.examDate;
        const examTime = firstExam.dataset.examTime;
        
        const availabilityData = await checkTeacherAvailability(examDate, examTime);
        const unavailableTeachers = availabilityData.unavailableTeachers || [];
        
        if (unavailableTeachers.includes(parseInt(teacherSelect.value))) {
            alert('Selected invigilator is unavailable for this time slot.');
            return false;
        }

        // Prepare form data with allocations
        prepareFormData();

        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Creating...';
        submitBtn.disabled = true;
        
        this.submit();
    });

    // Initialize
    calculateTotalSeats();
    updateSelectedExamsDisplay();
    
    // Auto filter on page load if filters are selected
    if (examDateSelect.value || sessionSelect.value) {
        setTimeout(() => {
            filterExams();
        }, 100);
    }

    // Reset filter button
    const resetFilterBtn = document.getElementById('resetFilterBtn');
    if (resetFilterBtn) {
        resetFilterBtn.addEventListener('click', function() {
            examDateSelect.value = '';
            sessionSelect.value = '';
            filterExams();
            
            // Clear all selections and allocations
            examCheckboxes.forEach(cb => {
                cb.checked = false;
            });
            examAllocations.clear();
            updateSelectedExamsDisplay();
        });
    }
});
</script>
@endpush