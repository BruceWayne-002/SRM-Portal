    @extends('layouts.app')

    @section('main')
    <div class="container-fluid exam-allocation-page py-4">
        {{-- Header with Progress Steps --}}
        <div class="allocation-header-card mb-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3 mb-3">
                <div>
                    <h2 class="h3 mb-1 fw-bold">📋 Exam Seat Allocation</h2>
                    <p class="text-muted mb-0">Select exam, choose students, and allocate seats in the hall.</p>
                </div>
                @if(isset($selectedHall))
                    <div class="header-hall-badge">
                        <i class="bi bi-building me-1"></i> {{ $selectedHall->hall_name }}
                    </div>
                @endif
            </div>
            
            {{-- Progress Steps --}}
            <div class="progress-steps d-flex align-items-center flex-wrap gap-2 mb-0">
                <div class="step-item {{ request('exam_id') ? 'completed' : 'active' }}">
                    <span class="step-number">1</span>
                    <span class="step-label">Select Exam</span>
                </div>
                <div class="step-line {{ request('exam_id') ? 'active' : '' }}"></div>
                <div class="step-item {{ request('exam_id') && isset($selectedHall) ? 'completed' : (request('exam_id') ? 'active' : '') }}">
                    <span class="step-number">2</span>
                    <span class="step-label">Choose Hall</span>
                </div>
                <div class="step-line {{ request('exam_id') && isset($selectedHall) ? 'active' : '' }}"></div>
                <div class="step-item {{ request('exam_id') && isset($selectedHall) && isset($students) && $students->count() ? 'active' : '' }}">
                    <span class="step-number">3</span>
                    <span class="step-label">Allocate Seats</span>
                </div>
            </div>
        </div>

        {{-- Success/Error Messages --}}
        @if(session('success'))
            <div id="autoHideAlert" class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <strong>{{ session('success') }}</strong>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>{{ session('error') }}</strong>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- ================= STEP 1: SELECT EXAM DATE & EXAM ================= --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.exam-allocation.create') }}" class="row g-3 align-items-end allocation-filter-form">
                    <input type="hidden" name="hall_id" value="{{ request('hall_id') }}">

                    <div class="col-md-6 col-lg-3">
    <label class="form-label fw-semibold text-secondary">
        <i class="bi bi-calendar-date me-1"></i>Select Exam Date
    </label>

    <select name="exam_date"
            class="form-select form-select-lg"
            onchange="this.form.submit()">
        <option value="">-- Choose exam date --</option>

        @foreach($examDates as $date)
            @php
                $dateValue = \Carbon\Carbon::parse($date)->format('Y-m-d');
            @endphp

            <option value="{{ $dateValue }}"
                {{ request('exam_date') == $dateValue ? 'selected' : '' }}>
                {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
            </option>
        @endforeach
    </select>

    @if(request('exam_date') && $exams->count() == 0)
        <small class="text-danger">No exams found for this date.</small>
    @elseif(request('exam_date'))
        <small class="text-muted">{{ $exams->count() }} exams found for selected date.</small>
    @else
        <small class="text-muted">First select exam date, then select exam.</small>
    @endif
</div>

                    <div class="col-md-6 col-lg-4">
                        <label class="form-label fw-semibold text-secondary">
                            <i class="bi bi-calendar-check me-1"></i>Select Examination
                        </label>
                        <select name="exam_id" class="form-select form-select-lg" onchange="this.form.submit()" {{ !request('exam_date') ? 'disabled' : '' }}>
                            <option value="">-- Choose an exam --</option>
                           
                            @foreach($exams as $exam)
                                <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
                                    {{ $exam->subject->name ?? $exam->subject_name }}
                                    @if($exam->subject_code)
                                        [{{ $exam->subject_code }}]
                                    @endif
                                    - {{ strtoupper($exam->time_session ?? 'Session TBD') }}
                                </option>
                            @endforeach
                        </select>

                        @if(request('exam_date') && $exams->count() == 0)
                            <small class="text-danger">No exams found for this date.</small>
                        @elseif(request('exam_date'))
                            <small class="text-muted">{{ $exams->count() }} exams found for selected date.</small>
                        @else
                            <small class="text-muted">First select exam date, then select exam.</small>
                        @endif
                    </div>

                    <div class="col-md-6 col-lg-5">
    <label class="form-label fw-semibold text-secondary">
        <i class="bi bi-mortarboard me-1"></i>Select Course
    </label>

    <select name="course" id="courseSelect"
        class="form-select form-select-lg"
        onchange="this.form.submit()"
        {{ !request('exam_date') ? 'disabled' : '' }}>
        
        <option value="">-- Choose Course --</option>
@php
    $courses = \App\Models\Student::whereNotNull('class_name')
        ->distinct()
        ->pluck('class_name');
@endphp

        @foreach($courses as $course)
            <option value="{{ $course }}"
                {{ request('course') == $course ? 'selected' : '' }}>
                {{ $course }}
            </option>
        @endforeach
    </select>

    <small class="text-muted">
        Select course for seat allocation
    </small>
</div>


<div class="col-md-6 col-lg-2">
    <label class="form-label fw-semibold text-secondary">
        <i class="bi bi-mortarboard-fill me-1"></i>Select Year
    </label>

    <select name="year"
            class="form-select form-select-lg"
            onchange="this.form.submit()">

        <option value="">-- All Years --</option>

        <option value="1" {{ request('year') == '1' ? 'selected' : '' }}>
            1st Year
        </option>

        <option value="2" {{ request('year') == '2' ? 'selected' : '' }}>
            2nd Year
        </option>

        <option value="3" {{ request('year') == '3' ? 'selected' : '' }}>
            3rd Year
        </option>

        <option value="4" {{ request('year') == '4' ? 'selected' : '' }}>
            4th Year
        </option>
    </select>
</div>






                </form>
            </div>
        </div>

        @if(isset($students) && $students->count())
        <form method="POST" id="allocationForm" action="{{ route('admin.exam-allocation.store') }}">
            @csrf
            <input type="hidden" name="exam_id" value="{{ $selectedExam->id }}">
            <input type="hidden" name="hall_id" value="{{ $selectedHall->id }}">

            <div class="row g-4 align-items-start">
                {{-- ================= LEFT PANEL: STUDENTS ================= --}}
                <div class="col-xl-4 col-lg-5">
                    <div class="card shadow-sm border-0 h-100 student-panel">
                        <div class="card-header bg-white py-3 border-0">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                                <h5 class="mb-0 fw-semibold">
                                    <i class="bi bi-people-fill me-2 text-primary"></i>
                                    Students Pool
                                </h5>
                                <div class="d-flex gap-2">
                                    <span class="badge bg-primary rounded-pill px-3 py-2" id="remainingCount">
                                        {{ $students->count() }} remaining
                                    </span>
                                    <span class="badge bg-info rounded-pill px-3 py-2" id="selectedCount">
                                        0 selected
                                    </span>
                                </div>
                            </div>
                            
                            {{-- Search and Filter --}}
                            <div class="mt-3">
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0">
                                        <i class="bi bi-search"></i>
                                    </span>
                                    <input type="text" class="form-control bg-light border-0" 
                                        id="studentSearch" placeholder="Search students...">
                                </div>
                            </div>
                            
                            {{-- Selection Controls --}}
                            <div class="d-flex flex-wrap gap-2 mt-3">
                                <button type="button" class="btn btn-sm btn-outline-primary flex-fill" id="selectAllBtn">
                                    <i class="bi bi-check-all"></i> Select All
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary flex-fill" id="clearSelectionBtn">
                                    <i class="bi bi-x"></i> Clear
                                </button>
                            </div>
                        </div>
                        
                        <div class="card-body p-0">
                            <div id="studentList" class="list-group list-group-flush" style="max-height:520px; overflow-y:auto;">
                                 

                           @php

if(request('course')) {
    $students = $students->filter(function($student) {
        return $student->class_name == request('course');
    });
}

if(request('year')) {
    $students = $students->filter(function($student) {
        return $student->current_year == request('year');
    });
}

@endphp





                                @forelse($students as $student)
                                    <div class="student-item list-group-item list-group-item-action d-flex align-items-center gap-3 border-0" 
                                        data-id="{{ $student->id }}" 
                                        data-roll="{{ $student->roll_no }}" 
                                        data-name="{{ $student->name }}">
                                        <div class="form-check">
                                            <input class="form-check-input student-checkbox" type="checkbox" 
                                                value="{{ $student->id }}" id="student_{{ $student->id }}">
                                        </div>
                                        <div class="student-avatar bg-light rounded-circle d-flex align-items-center justify-content-center" 
                                            style="width: 40px; height: 40px;">
                                            <span class="fw-bold text-primary">{{ substr($student->name, 0, 1) }}</span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold student-name">{{ $student->name }}</div>
                                            <small class="text-muted student-roll">Roll No: {{ $student->roll_no }}</small>
                                        </div>
                                        <i class="bi bi-grip-vertical text-muted handle" style="cursor: move;"></i>
                                    </div>
                                @empty
                                    <div class="text-center text-muted py-5">
                                        <i class="bi bi-emoji-frown fs-1 d-block mb-3"></i>
                                        <p>No remaining students for this exam</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                        
                        {{-- Quick Actions --}}
                        <div class="card-footer bg-white border-0 py-3">
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-primary" id="autoAllocateBtn">
                                    <i class="bi bi-magic"></i> Auto-allocate Selected (Different Tables)
                                </button>
                                <button type="button" class="btn btn-outline-danger" id="clearAllocationsBtn">
                                    <i class="bi bi-eraser"></i> Clear All Allocations
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ================= RIGHT PANEL: HALL LAYOUT ================= --}}
                <div class="col-xl-8 col-lg-7">
                    {{-- Hall Header with Controls --}}
                    <div class="card shadow-sm border-0 mb-4 hall-control-card">
                        <div class="card-body">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary">
                                        <i class="bi bi-person-badge me-1"></i>Invigilator
                                    </label>
                                    <select name="teacher_id" class="form-select" required>
                                        <option value="">-- Select Teacher --</option>
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}" {{ isset($allocatedTeacherId) && $allocatedTeacherId == $teacher->id ? 'selected' : '' }}>
                                                {{ $teacher->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    
                                    {{-- Show slot teacher information if exists --}}
                                    @if(isset($slotTeacher) && $slotTeacher)
                                        @php
                                            $slotTeacherName = $teachers->firstWhere('id', $slotTeacher)->name ?? 'Unknown';
                                        @endphp
                                        <div class="mt-2 text-info">
                                            <i class="bi bi-info-circle-fill me-1"></i>
                                            <small>This time slot already has teacher: <strong>{{ $slotTeacherName }}</strong> assigned. The teacher cannot be changed.</small>
                                        </div>
                                    @endif
                                </div>

                                @if(isset($selectedHall))
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary">
                                        <i class="bi bi-building me-1"></i>Selected Hall
                                    </label>
                                    <div class="hall-info-card p-3 bg-light rounded-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="fw-bold fs-5">{{ $selectedHall->hall_name }}</span>
                                                <div class="mt-2">
                                                    <span class="badge bg-info me-2">
                                                        <i class="bi bi-people"></i> {{ $selectedHall->capacity }}
                                                    </span>
                                                    <span class="badge bg-secondary">
                                                        {{ $selectedHall->rows }}×{{ $selectedHall->columns }}
                                                    </span>
                                                    @if(isset($selectedExam) && $selectedExam->time_session)
                                                        <span class="badge bg-warning text-dark ms-2">
                                                            <i class="bi bi-clock"></i> {{ strtoupper($selectedExam->time_session) }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Hall Layout with Action Toolbar --}}
                    <div class="card shadow-sm border-0 hall-layout-card">
                        <div class="card-header bg-white py-3 border-0">
                            <div class="d-flex flex-column flex-xl-row justify-content-between align-items-start align-items-xl-center gap-3">
                                <h5 class="mb-0 fw-semibold">
                                    <i class="bi bi-grid-3x3-gap-fill me-2 text-primary"></i>
                                    Hall Layout
                                </h5>
                                <div class="d-flex flex-column flex-md-row gap-3 align-items-start align-items-md-center w-100 w-xl-auto">
                                    <div class="seat-legend d-flex flex-wrap gap-2">
                                        <span><span class="seat-dot available"></span> Available</span>
                                        <span><span class="seat-dot occupied"></span> Current Exam</span>
                                        <span><span class="seat-dot selected"></span> Selected</span>
                                        <span><span class="seat-dot other-exam"></span> Other Exam</span>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-tools"></i> Actions
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#" id="swapModeBtn"><i class="bi bi-arrow-left-right"></i> Swap Mode</a></li>
                                            <li><a class="dropdown-item" href="#" id="distributeEvenlyBtn"><i class="bi bi-grid-3x3"></i> Distribute Evenly</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item" href="#" id="resetSelectionBtn"><i class="bi bi-arrow-counterclockwise"></i> Reset Selection</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            @if(isset($selectedHall))
                                <div id="hallLayout" class="hall-layout"></div>
                                
                                {{-- Allocation Summary --}}
                                <div class="mt-4 p-3 bg-light rounded-3 allocation-summary" id="allocationSummary">
                                    <div class="row text-center">
                                        <div class="col-3">
                                            <div class="small text-muted">Total Seats</div>
                                            <div class="h5 mb-0" id="totalSeats">{{ $selectedHall->capacity }}</div>
                                        </div>
                                        <div class="col-3">
                                            <div class="small text-muted">Allocated</div>
                                            <div class="h5 mb-0 text-success" id="allocatedSeats">0</div>
                                        </div>
                                        <div class="col-3">
                                            <div class="small text-muted">Available</div>
                                            <div class="h5 mb-0 text-primary" id="availableSeats">{{ $selectedHall->capacity }}</div>
                                        </div>
                                        <div class="col-3">
                                            <div class="small text-muted">Selected Students</div>
                                            <div class="h5 mb-0 text-info" id="selectedStudentsCount">0</div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning bg-light border-0 d-flex align-items-center">
                                    <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
                                    <div>
                                        <strong>No hall selected!</strong><br>
                                        Please select a hall from the hall index page to continue.
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="action-footer d-flex flex-column flex-md-row justify-content-end gap-3 mt-4">
                <button type="button" class="btn btn-light px-4" onclick="window.history.back()">
                    <i class="bi bi-arrow-left me-2"></i>Back
                </button>
                <button type="submit" class="btn btn-success px-5">
                    <i class="bi bi-check-circle me-2"></i>Save Allocation
                </button>
            </div>
        </form>
        @endif
    </div>

    <style>
        :root {
            --primary-purple: #640d3c;
            --soft-purple: #EEDFFF;
            --page-bg: #f6f7fb;
            --card-border: rgba(100, 13, 60, 0.08);
        }

        .exam-allocation-page {
            background: var(--page-bg);
            min-height: calc(100vh - 70px);
        }

        .allocation-header-card,
        .card {
            border-radius: 18px !important;
        }

        .allocation-header-card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            box-shadow: 0 8px 22px rgba(0,0,0,0.05);
            padding: 20px;
        }

        .allocation-header-card h2 {
            color: var(--primary-purple);
        }

        .header-hall-badge {
            background: linear-gradient(135deg, var(--primary-purple), #9D71C9);
            color: #fff;
            border-radius: 50px;
            padding: 10px 18px;
            font-weight: 700;
            box-shadow: 0 8px 18px rgba(100, 13, 60, 0.18);
        }

        .card {
            border: 1px solid var(--card-border) !important;
            box-shadow: 0 8px 22px rgba(0,0,0,0.05) !important;
            overflow: hidden;
        }

        .card-header {
            background: #fff !important;
        }

        .form-label {
            margin-bottom: 8px;
        }

        .form-select,
        .form-control,
        .input-group-text {
            border-radius: 12px !important;
            border: 1px solid #e5e7eb !important;
        }

        .form-select:focus,
        .form-control:focus {
            border-color: var(--primary-purple) !important;
            box-shadow: 0 0 0 0.18rem rgba(100,13,60,0.15) !important;
        }

        .allocation-filter-form .form-select-lg {
            min-height: 48px;
        }

        .progress-steps {
            width: 100%;
        }

        .step-item {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #6c757d;
            background: #f8f9fa;
            border: 1px solid #eef0f3;
            border-radius: 50px;
            padding: 7px 12px;
            white-space: nowrap;
        }

        .step-item.active {
            color: #0d6efd;
            background: #e7f1ff;
            border-color: #cfe2ff;
        }

        .step-item.completed {
            color: #198754;
            background: #d1e7dd;
            border-color: #badbcc;
        }

        .step-number {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background-color: #e9ecef;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 13px;
            flex-shrink: 0;
        }

        .step-item.active .step-number {
            background-color: #0d6efd;
            color: white;
        }

        .step-item.completed .step-number {
            background-color: #198754;
            color: white;
        }

        .step-line {
            width: 42px;
            height: 2px;
            background-color: #dee2e6;
        }

        .step-line.active {
            background-color: #0d6efd;
        }

        .student-panel {
            position: sticky;
            top: 16px;
        }

        .student-panel .card-header,
        .student-panel .card-footer {
            background: #fff !important;
        }

        #studentList {
            scrollbar-width: thin;
            scrollbar-color: #c9c9c9 transparent;
        }

        #studentList::-webkit-scrollbar,
        .hall-layout::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }

        #studentList::-webkit-scrollbar-thumb,
        .hall-layout::-webkit-scrollbar-thumb {
            background: #c9c9c9;
            border-radius: 50px;
        }

        .student-item {
            cursor: pointer;
            transition: all 0.2s;
            border-left: 4px solid transparent !important;
            user-select: none;
            padding: 13px 16px;
        }

        .student-item:hover {
            background-color: #f8f9fa;
        }

        .student-item.active {
            background-color: #e7f1ff;
            border-left-color: #0d6efd !important;
        }

        .student-item.active .student-avatar {
            background-color: #0d6efd !important;
        }

        .student-item.active .student-avatar span {
            color: white !important;
        }

        .student-item.dragging {
            opacity: 0.5;
        }

        .student-avatar {
            min-width: 40px;
        }

        .badge {
            font-weight: 700;
        }

        .btn {
            border-radius: 12px;
            font-weight: 600;
        }

        .hall-control-card .hall-info-card {
            border: 1px solid #e9ecef;
            background: #fbfbfd !important;
        }

        .seat-legend {
            font-size: 13px;
            color: #495057;
        }

        .seat-legend span {
            display: inline-flex;
            align-items: center;
            background: #f8f9fa;
            border: 1px solid #edf0f2;
            border-radius: 50px;
            padding: 6px 10px;
        }

        .hall-layout {
            background-color: #f8f9fa;
            border: 1px solid #edf0f2;
            border-radius: 16px;
            padding: 22px;
            min-height: 500px;
            max-height: 650px;
            overflow: auto;
        }

        .table-container {
            background: white;
            border: 1px solid #edf0f2;
            border-radius: 14px;
            padding: 14px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.04);
            min-width: 185px;
            transition: all 0.25s;
        }

        .table-container:hover {
            box-shadow: 0 8px 18px rgba(0,0,0,0.08);
            transform: translateY(-1px);
        }

        .table-container.compact {
            min-width: 150px;
            padding: 10px;
        }

        .table-header {
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 10px;
            margin-bottom: 12px;
            color: #495057;
            font-weight: 800;
        }

        .seat {
            width: 68px;
            height: 68px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            border: 2px solid #dee2e6;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 12px;
            font-weight: 600;
            color: #495057;
            padding: 4px;
            word-break: break-word;
            text-align: center;
            position: relative;
        }

        .compact .seat {
            width: 50px;
            height: 50px;
            font-size: 10px;
        }

        .seat:hover:not(.occupied) {
            border-color: #0d6efd;
            background-color: #e7f1ff;
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(13,110,253,0.18);
        }

        .seat.occupied {
            background: #d1e7dd;
            border-color: #198754;
            color: #0f5132;
        }

        .seat.selected {
            border-color: #ffc107;
            background-color: #fff3cd;
            box-shadow: 0 0 0 3px rgba(255,193,7,0.25);
        }

        .seat.swap-mode {
            cursor: crosshair;
        }

        .remove-student {
            position: absolute;
            top: -7px;
            right: -7px;
            width: 20px;
            height: 20px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            cursor: pointer;
            z-index: 10;
            border: 2px solid #fff;
        }

        .seat.occupied:hover .remove-student {
            display: flex;
        }

        .remove-student:hover {
            background: #bb2d3b;
            transform: scale(1.1);
        }

        .seat-dot {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 6px;
            flex-shrink: 0;
        }

        .seat-dot.available { background-color: #0d6efd; }
        .seat-dot.occupied { background-color: #198754; }
        .seat-dot.selected { background-color: #ffc107; }
        .seat-dot.other-exam { background-color: #dc3545; }

        .seat.other-exam {
            background: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
            opacity: 0.88;
            cursor: not-allowed;
            position: relative;
        }

        .seat.other-exam:hover {
            transform: none;
            box-shadow: none;
            border-color: #dc3545;
        }

        .seat.other-exam .other-exam-badge {
            position: absolute;
            top: -7px;
            right: -7px;
            font-size: 11px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: help;
            border: 2px solid #fff;
        }

        .seat.other-exam .other-exam-badge:hover::after {
            content: 'Occupied by another exam on same date';
            position: absolute;
            top: -34px;
            right: 0;
            background: #333;
            color: white;
            padding: 5px 9px;
            border-radius: 6px;
            font-size: 12px;
            white-space: nowrap;
            z-index: 1000;
        }

        .allocation-summary .row > div {
            border-right: 1px solid #e6e8eb;
        }

        .allocation-summary .row > div:last-child {
            border-right: 0;
        }

        .action-footer {
            background: #fff;
            border: 1px solid var(--card-border);
            border-radius: 18px;
            padding: 16px;
            box-shadow: 0 8px 22px rgba(0,0,0,0.05);
        }

        .toast-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1050;
        }

        .form-select-lg { font-size: 15px !important; }
        .list-group-item.active { color: #000000 !important; }

        @media (min-width: 1200px) {
            .w-xl-auto { width: auto !important; }
        }

        @media (max-width: 1199px) {
            .student-panel { position: static; }
            .hall-layout { max-height: none; }
        }

        @media (max-width: 768px) {
            .exam-allocation-page {
                padding-left: 10px;
                padding-right: 10px;
            }

            .allocation-header-card { padding: 16px; }
            .step-line { display: none; }

            .step-item {
                width: 100%;
                justify-content: flex-start;
            }

            .hall-layout {
                padding: 14px;
                min-height: 360px;
            }

            .table-container { min-width: 100%; }

            .seat {
                width: 58px;
                height: 58px;
                font-size: 11px;
            }

            .allocation-summary .row > div {
                border-right: 0;
                border-bottom: 1px solid #e6e8eb;
                padding-top: 10px;
                padding-bottom: 10px;
            }

            .allocation-summary .row > div:last-child { border-bottom: 0; }
        }
    </style>

    {{-- Add SortableJS for drag-drop --}}
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                let alert = document.getElementById('autoHideAlert');
                if (alert) {
                    let bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 3000); // 3000ms = 3 seconds
        });
    </script>

    <script>
    // Initialize data from server
    let selectedStudents = new Set();
    let currentAllocations = new Map();
    let swapMode = false;
    let seatToSwap = null;

    // Parse existing allocations if available
    let existingAllocations = [];
    @if(isset($existingAllocations) && $existingAllocations && $existingAllocations->count() > 0)
        @php
            $allocationsData = [];
            foreach($existingAllocations as $allocation) {
                $allocationsData[] = [
                    'student_id' => $allocation['student_id'],
                    'student_name' => $allocation['student_name'],
                    'student_roll' => $allocation['student_roll'],
                    'table' => $allocation['table'],
                    'seat' => $allocation['seat'],
                    'is_other_exam' => $allocation['is_other_exam'] ?? false
                ];
            }
        @endphp
        existingAllocations = @json($allocationsData);
    @endif

    // Parse allocated teacher
    let allocatedTeacherId = null;
    @if(isset($allocatedTeacherId) && $allocatedTeacherId)
        allocatedTeacherId = {{ $allocatedTeacherId }};
    @endif


    // Student selection with checkboxes
    document.addEventListener('change', function(e) {
        if(e.target.classList.contains('student-checkbox')) {
            const studentItem = e.target.closest('.student-item');
            if(!studentItem) return;
            
            const studentId = studentItem.dataset.id;
            
            if(e.target.checked) {
                selectedStudents.add(studentId);
                studentItem.classList.add('active');
            } else {
                selectedStudents.delete(studentId);
                studentItem.classList.remove('active');
            }
            
            updateSelectedCount();
        }
    });

    // Student item click (for quick selection)
    document.addEventListener('click', function(e) {
        if(e.target.closest('.student-item') && !e.target.closest('.form-check') && !e.target.closest('.remove-student')) {
            const studentItem = e.target.closest('.student-item');
            const checkbox = studentItem.querySelector('.student-checkbox');
            if(checkbox) {
                checkbox.checked = !checkbox.checked;
                
                // Trigger change event
                const event = new Event('change', { bubbles: true });
                checkbox.dispatchEvent(event);
            }
        }
    });

    // Search functionality
    document.getElementById('studentSearch')?.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        document.querySelectorAll('.student-item').forEach(item => {
            const nameEl = item.querySelector('.student-name');
            const rollEl = item.querySelector('.student-roll');
            
            if(nameEl && rollEl) {
                const name = nameEl.textContent.toLowerCase();
                const roll = rollEl.textContent.toLowerCase();
                
                if(name.includes(searchTerm) || roll.includes(searchTerm)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            }
        });
    });

    // Select all functionality - only hall available capacity students select
    document.getElementById('selectAllBtn')?.addEventListener('click', function() {
        const availableSeats = document.querySelectorAll('.seat:not(.occupied)').length;
        const alreadySelected = selectedStudents.size;
        const canSelectCount = availableSeats - alreadySelected;

        if (canSelectCount <= 0) {
            showToast('Hall capacity is full. No more students can be selected.', 'warning');
            return;
        }

        let selectedNow = 0;

        document.querySelectorAll('.student-checkbox:not(:checked)').forEach(checkbox => {
            if (selectedNow >= canSelectCount) return;

            const studentItem = checkbox.closest('.student-item');

            if (studentItem && studentItem.style.display !== 'none') {
                checkbox.checked = true;
                const event = new Event('change', { bubbles: true });
                checkbox.dispatchEvent(event);
                selectedNow++;
            }
        });

        showToast(`Selected ${selectedNow} students based on hall available capacity`, 'success');
    });

    // Clear selection
    document.getElementById('clearSelectionBtn')?.addEventListener('click', function() {
        document.querySelectorAll('.student-checkbox:checked').forEach(checkbox => {
            checkbox.checked = false;
            const event = new Event('change', { bubbles: true });
            checkbox.dispatchEvent(event);
        });
    });

    // Generate hall layout
    // Generate hall layout
    function generateHallLayout() {
        @if(isset($selectedHall))
            const rows = {{ $selectedHall->rows }};
            const cols = {{ $selectedHall->columns }};
            const perTable = {{ $selectedHall->students_per_table }};
            const layout = document.getElementById('hallLayout');
            
            if(!layout) return;

            layout.innerHTML = '';
            layout.className = 'hall-layout ' + (document.getElementById('viewMode')?.value || 'grid');

            // Create layout in column-major order (down each column, then next column)
            for(let c = 1; c <= cols; c++) {
                for(let r = 1; r <= rows; r++) {
                    const tableNumber = ((c - 1) * rows) + r;

                    // Find or create row div
                    let rowDiv = layout.querySelector(`[data-row="${r}"]`);
                    if(!rowDiv) {
                        rowDiv = document.createElement('div');
                        rowDiv.className = 'd-flex flex-wrap gap-4 justify-content-center mb-4';
                        rowDiv.dataset.row = r;
                        layout.appendChild(rowDiv);
                    }

                    // Table container
                    const tableDiv = document.createElement('div');
                    tableDiv.className = 'table-container';
                    tableDiv.dataset.table = tableNumber;
                    tableDiv.dataset.row = r;
                    tableDiv.dataset.col = c;

                    // Table header
                    const header = document.createElement('div');
                    header.className = 'table-header d-flex justify-content-between align-items-center';
                    header.innerHTML = `
                        <span>Table ${tableNumber}</span>
                        <span class="badge bg-secondary">${perTable}</span>
                    `;
                    tableDiv.appendChild(header);

                    // Seats container
                    const seatContainer = document.createElement('div');
                    seatContainer.className = 'd-flex flex-wrap gap-2 justify-content-center';

                    for(let s = 1; s <= perTable; s++) {
                        const seat = document.createElement('div');
                        seat.className = 'seat';
                        seat.dataset.table = tableNumber;
                        seat.dataset.seat = s;
                        seat.dataset.row = r;
                        seat.dataset.col = c;

                        // Check if this seat is allocated in ANY exam on this date
                        const allocation = existingAllocations.find(a => 
                            parseInt(a.table) === tableNumber && 
                            parseInt(a.seat) === s
                        );
                        
                        if(allocation) {
                            seat.classList.add('occupied');
                            seat.dataset.studentId = allocation.student_id;
                            seat.dataset.studentName = allocation.student_name;
                            seat.dataset.studentRoll = allocation.student_roll;
                            
                            // 🔥 NEW: Mark if this is from another exam
                            if(allocation.is_other_exam) {
                                seat.classList.add('other-exam');
                                seat.dataset.otherExam = 'true';
                                
                                // Get last name for display
                                const displayName = allocation.student_name;
                                
                                seat.innerHTML = `
                                    <span class="small fw-bold">${displayName}</span>
                                    <span class="other-exam-badge" title="Occupied by another exam">🔒</span>
                                `;
                                
                                // Make these seats unclickable
                                seat.style.cursor = 'not-allowed';
                                seat.classList.add('no-interact');
                            } else {
                                // Current exam allocation
                                const displayName = allocation.student_name;    
                                
                                
                                seat.innerHTML = `
                                    <span class="small fw-bold">${displayName}</span>
                                    <span class="remove-student" title="Remove student">×</span>
                                `;

                                const removeBtn = seat.querySelector('.remove-student');
                                if(removeBtn) {
                                    removeBtn.addEventListener('click', (e) => {
                                        e.stopPropagation();
                                        removeStudentFromSeat(seat);
                                    });
                                }
                            }

                            if(!allocation.is_other_exam) {
                                currentAllocations.set(allocation.student_id.toString(), seat);
                                addHiddenInput(allocation.student_id.toString(), tableNumber, s);
                            }
                        } else {
                            seat.innerHTML = `<span class="small">Seat ${s}</span>`;
                        }

                        // Only add click handler if not occupied by another exam
                        if(!seat.classList.contains('other-exam')) {
                            seat.addEventListener('click', function(e) {
                                if(e.target.classList.contains('remove-student')) return;

                                if(swapMode) {
                                    handleSwapMode(this);
                                } else {
                                    handleSeatClick(this);
                                }
                            });
                        }

                        seatContainer.appendChild(seat);
                    }

                    tableDiv.appendChild(seatContainer);
                    rowDiv.appendChild(tableDiv);
                }
            }

            updateRemainingCount();
            updateAllocationSummary();
        @endif
    }

    // Handle seat click for allocation
    function handleSeatClick(seat) {
        if(seat.classList.contains('occupied')) {
            // Show options for occupied seat
            showSeatOptions(seat);
            return;
        }
        
        if(selectedStudents.size === 0) {
            showToast('Please select at least one student', 'warning');
            return;
        }
        
        if(selectedStudents.size === 1) {
            // Single allocation
            const studentId = Array.from(selectedStudents)[0];
            allocateStudentToSeat(studentId, seat);
        } else {
            // Multiple allocation - show dialog
            const studentIds = Array.from(selectedStudents);
            if(confirm(`Allocate ${studentIds.length} students starting from this seat?`)) {
                allocateMultipleStudents(studentIds, seat);
            }
        }
    }

    // Allocate multiple students
    function allocateMultipleStudents(studentIds, startSeat) {
        @if(isset($selectedHall))
        const startTable = parseInt(startSeat.dataset.table);
        const startSeatNum = parseInt(startSeat.dataset.seat);
        const perTable = {{ $selectedHall->students_per_table ?? 0 }};
        
        let currentTable = startTable;
        let currentSeat = startSeatNum;
        let allocated = 0;
        
        for(let studentId of studentIds) {
            // Find the next available seat
            let seat = findNextAvailableSeat(currentTable, currentSeat);
            
            if(seat) {
                allocateStudentToSeat(studentId, seat);
                allocated++;
                
                // Move to next seat
                currentSeat++;
                if(currentSeat > perTable) {
                    currentTable++;
                    currentSeat = 1;
                }
            } else {
                showToast(`No more seats available after allocating ${allocated} students`, 'warning');
                break;
            }
        }
        
        showToast(`Allocated ${allocated} students`, 'success');
        @endif
    }

    // Find next available seat
    function findNextAvailableSeat(startTable, startSeat) {
        const seats = Array.from(document.querySelectorAll('.seat:not(.occupied)'));
        return seats.find(seat => {
            const table = parseInt(seat.dataset.table);
            const seatNum = parseInt(seat.dataset.seat);
            return table > startTable || (table === startTable && seatNum >= startSeat);
        });
    }

    // Allocate student to seat
    function allocateStudentToSeat(studentId, seat) {
        const studentItem = document.querySelector(`.student-item[data-id="${studentId}"]`);
        if(!studentItem) return;
        
        const studentName = studentItem.dataset.name;
        const studentRoll = studentItem.dataset.roll;
        
        // Get last name for display
        const displayName = studentName;
        
        // Update seat
        seat.innerHTML = `
            <span class="small fw-bold">${displayName}</span>
            <span class="remove-student" title="Remove student">×</span>
        `;
        seat.classList.add('occupied');
        seat.dataset.studentId = studentId;
        seat.dataset.studentName = studentName;
        seat.dataset.studentRoll = studentRoll;
        
        // Add remove button functionality
        const removeBtn = seat.querySelector('.remove-student');
        if(removeBtn) {
            removeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                removeStudentFromSeat(seat);
            });
        }
        
        // Add hidden input
        addHiddenInput(studentId, seat.dataset.table, seat.dataset.seat);
        
        // Remove student from list and selection
        studentItem.remove();
        selectedStudents.delete(studentId);
        
        // Track allocation
        currentAllocations.set(studentId, seat);
        
        // Update counts
        updateRemainingCount();
        updateSelectedCount();
        updateAllocationSummary();
        
        showToast(`${studentName} allocated successfully`, 'success');
    }

    // Remove student from seat
    function removeStudentFromSeat(seat) {
        const studentId = seat.dataset.studentId;
        const studentName = seat.dataset.studentName;
        const studentRoll = seat.dataset.studentRoll;
        
        if(!studentId) return;
        
        // Show confirmation
        if(confirm(`Remove ${studentName} from this seat?`)) {
            // Check if this was an existing allocation (from database)
            const wasExistingAllocation = existingAllocations.some(a => a.student_id == studentId);
            
            if(wasExistingAllocation) {
                // Add a flag to indicate this allocation should be deleted
                const deleteInput = document.createElement('input');
                deleteInput.type = 'hidden';
                deleteInput.name = 'delete_allocations[]';
                deleteInput.value = studentId;
                deleteInput.id = `delete_${studentId}`;
                document.getElementById('allocationForm').appendChild(deleteInput);
            }
            
            // Recreate student item
            const studentList = document.getElementById('studentList');
            if(studentList) {
                const studentItem = createStudentItem(studentId, studentName, studentRoll);
                studentList.appendChild(studentItem);
            }
            
            // Reset seat
            seat.innerHTML = `<span class="small">Seat ${seat.dataset.seat}</span>`;
            seat.classList.remove('occupied');
            delete seat.dataset.studentId;
            delete seat.dataset.studentName;
            delete seat.dataset.studentRoll;
            
            // Remove hidden input
            removeHiddenInput(studentId);
            
            // Update tracking
            currentAllocations.delete(studentId);
            
            // Update counts
            updateRemainingCount();
            updateAllocationSummary();
            
            showToast('Student removed from seat', 'info');
        }
    }

    // Create student item element
    function createStudentItem(studentId, studentName, studentRoll) {
        const div = document.createElement('div');
        div.className = 'student-item list-group-item list-group-item-action d-flex align-items-center gap-3 border-0';
        div.dataset.id = studentId;
        div.dataset.name = studentName;
        div.dataset.roll = studentRoll;
        
        div.innerHTML = `
            <div class="form-check">
                <input class="form-check-input student-checkbox" type="checkbox" value="${studentId}" id="student_${studentId}">
            </div>
            <div class="student-avatar bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <span class="fw-bold text-primary">${studentName.charAt(0)}</span>
            </div>
            <div class="flex-grow-1">
                <div class="fw-semibold student-name">${studentName}</div>
                <small class="text-muted student-roll">Roll No: ${studentRoll}</small>
            </div>
            <i class="bi bi-grip-vertical text-muted handle" style="cursor: move;"></i>
        `;
        
        // Add checkbox event listener
        const checkbox = div.querySelector('.student-checkbox');
        if(checkbox) {
            checkbox.addEventListener('change', function(e) {
                if(this.checked) {
                    selectedStudents.add(studentId);
                    div.classList.add('active');
                } else {
                    selectedStudents.delete(studentId);
                    div.classList.remove('active');
                }
                updateSelectedCount();
            });
        }
        
        return div;
    }

    // Add hidden input
    function addHiddenInput(studentId, table, seat) {
        // Remove existing input for this student if any
        removeHiddenInput(studentId);
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'assignments[]';
        input.value = `${studentId}|${table}|${seat}`;
        input.id = `alloc_${studentId}`;
        
        const form = document.getElementById('allocationForm');
        if(form) {
            form.appendChild(input);
        }
    }

    // Remove hidden input
    function removeHiddenInput(studentId) {
        const existingInput = document.getElementById(`alloc_${studentId}`);
        if(existingInput) existingInput.remove();
        
        // Also remove any delete flag for this student
        const deleteInput = document.getElementById(`delete_${studentId}`);
        if(deleteInput) deleteInput.remove();
    }

    // Auto-allocate selected students (ensuring different tables)
    document.getElementById('autoAllocateBtn')?.addEventListener('click', function() {
        if(selectedStudents.size === 0) {
            showToast('No students selected', 'warning');
            return;
        }

        // Get all available seats grouped by table
        const tables = new Map();
        document.querySelectorAll('.seat:not(.occupied)').forEach(seat => {
            const tableNum = parseInt(seat.dataset.table);
            if(!tables.has(tableNum)) tables.set(tableNum, []);
            tables.get(tableNum).push(seat);
        });

        const totalAvailable = Array.from(tables.values()).reduce((sum, seats) => sum + seats.length, 0);
        if(totalAvailable < selectedStudents.size) {
            showToast('Not enough available seats', 'error');
            return;
        }

        // Sort table numbers in column-major order
        const tableNumbers = Array.from(tables.keys()).sort((a, b) => a - b);

        const studentIds = Array.from(selectedStudents);
        let allocated = 0;
        let tableIndex = 0;

        for(let studentId of studentIds) {
            const tableNum = tableNumbers[tableIndex % tableNumbers.length];
            const seatList = tables.get(tableNum);
            if(seatList && seatList.length > 0) {
                const seat = seatList.shift();
                allocateStudentToSeat(studentId, seat);
                allocated++;
            }
            tableIndex++;
        }

        showToast(`Allocated ${allocated} students in column-major order`, 'success');
    });

    // Distribute students evenly across tables
    document.getElementById('distributeEvenlyBtn')?.addEventListener('click', function() {
        if(selectedStudents.size === 0) {
            showToast('No students selected', 'warning');
            return;
        }

        const tables = new Map();
        document.querySelectorAll('.table-container').forEach(table => {
            const tableNum = parseInt(table.dataset.table);
            const seats = [...table.querySelectorAll('.seat:not(.occupied)')];
            if(seats.length > 0) tables.set(tableNum, seats);
        });

        if(tables.size === 0) {
            showToast('No available seats', 'error');
            return;
        }

        // Sort table numbers column-major
        const tableNumbers = Array.from(tables.keys()).sort((a, b) => a - b);

        const studentIds = Array.from(selectedStudents);
        let allocated = 0;
        let studentIndex = 0;

        // Calculate students per table
        const studentsPerTable = Math.floor(studentIds.length / tableNumbers.length);
        let remainingStudents = studentIds.length % tableNumbers.length;

        for(let tableNum of tableNumbers) {
            const seats = tables.get(tableNum);
            const seatsToAllocate = studentsPerTable + (remainingStudents > 0 ? 1 : 0);
            remainingStudents--;

            for(let i = 0; i < Math.min(seatsToAllocate, seats.length) && studentIndex < studentIds.length; i++) {
                allocateStudentToSeat(studentIds[studentIndex], seats[i]);
                studentIndex++;
                allocated++;
            }
        }

        showToast(`Distributed ${allocated} students evenly in column-major order`, 'success');
    });

    // Handle swap mode
    function handleSwapMode(seat) {
        if(!seat.classList.contains('occupied')) {
            showToast('Select an occupied seat to swap', 'warning');
            return;
        }
        
        if(!seatToSwap) {
            // First seat selected
            seatToSwap = seat;
            seat.classList.add('selected');
            showToast('Select another seat to swap with', 'info');
        } else {
            // Second seat selected
            if(seat === seatToSwap) {
                seatToSwap.classList.remove('selected');
                seatToSwap = null;
                return;
            }
            
            // Perform swap
            swapStudents(seatToSwap, seat);
            
            // Reset swap mode
            seatToSwap.classList.remove('selected');
            seatToSwap = null;
            swapMode = false;
            const swapBtn = document.getElementById('swapModeBtn');
            if(swapBtn) swapBtn.classList.remove('active');
        }
    }

    // Swap students between seats
    function swapStudents(seat1, seat2) {
        const student1Id = seat1.dataset.studentId;
        const student1Name = seat1.dataset.studentName;
        const student1Roll = seat1.dataset.studentRoll;
        const student2Id = seat2.dataset.studentId;
        const student2Name = seat2.dataset.studentName;
        const student2Roll = seat2.dataset.studentRoll;
        
        // Store seat1 content
        const seat1Html = seat1.innerHTML;
        
        if(seat2.classList.contains('occupied')) {
            // Both seats occupied - swap
            seat1.innerHTML = seat2.innerHTML;
            seat2.innerHTML = seat1Html;
            
            // Update data attributes
            seat1.dataset.studentId = student2Id;
            seat1.dataset.studentName = student2Name;
            seat1.dataset.studentRoll = student2Roll;
            
            seat2.dataset.studentId = student1Id;
            seat2.dataset.studentName = student1Name;
            seat2.dataset.studentRoll = student1Roll;
            
            // Update remove buttons
            const removeBtn1 = seat1.querySelector('.remove-student');
            const removeBtn2 = seat2.querySelector('.remove-student');
            
            if(removeBtn1) {
                removeBtn1.remove();
                const newRemoveBtn = document.createElement('span');
                newRemoveBtn.className = 'remove-student';
                newRemoveBtn.innerHTML = '×';
                newRemoveBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    removeStudentFromSeat(seat1);
                });
                seat1.appendChild(newRemoveBtn);
            }
            
            if(removeBtn2) {
                removeBtn2.remove();
                const newRemoveBtn = document.createElement('span');
                newRemoveBtn.className = 'remove-student';
                newRemoveBtn.innerHTML = '×';
                newRemoveBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    removeStudentFromSeat(seat2);
                });
                seat2.appendChild(newRemoveBtn);
            }
            
            // Update hidden inputs
            removeHiddenInput(student1Id);
            removeHiddenInput(student2Id);
            addHiddenInput(student1Id, seat2.dataset.table, seat2.dataset.seat);
            addHiddenInput(student2Id, seat1.dataset.table, seat1.dataset.seat);
            
            showToast('Students swapped successfully', 'success');
        } else {
            // Moving from occupied to empty
            seat2.innerHTML = seat1Html;
            seat2.classList.add('occupied');
            seat2.dataset.studentId = student1Id;
            seat2.dataset.studentName = student1Name;
            seat2.dataset.studentRoll = student1Roll;
            
            // Update remove button
            const removeBtn = seat2.querySelector('.remove-student');
            if(removeBtn) {
                removeBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    removeStudentFromSeat(seat2);
                });
            }
            
            // Clear seat1
            seat1.innerHTML = `<span class="small">Seat ${seat1.dataset.seat}</span>`;
            seat1.classList.remove('occupied');
            delete seat1.dataset.studentId;
            delete seat1.dataset.studentName;
            delete seat1.dataset.studentRoll;
            
            // Update hidden inputs
            removeHiddenInput(student1Id);
            addHiddenInput(student1Id, seat2.dataset.table, seat2.dataset.seat);
            
            showToast('Student moved successfully', 'success');
        }
    }

    // Show seat options
    function showSeatOptions(seat) {
        if(confirm('Remove this student from the seat?')) {
            removeStudentFromSeat(seat);
        }
    }

    // Update selected count
    function updateSelectedCount() {
        const count = selectedStudents.size;
        const selectedCountEl = document.getElementById('selectedCount');
        const selectedStudentsCountEl = document.getElementById('selectedStudentsCount');
        
        if(selectedCountEl) selectedCountEl.innerText = count + ' selected';
        if(selectedStudentsCountEl) selectedStudentsCountEl.innerText = count;
    }

    // Update remaining count
    function updateRemainingCount() {
        const remaining = document.querySelectorAll('.student-item').length;
        const countElement = document.getElementById('remainingCount');
        if(countElement) {
            countElement.innerText = remaining + ' remaining';
        }
    }

    // Update allocation summary
    function updateAllocationSummary() {
        const allocated = document.querySelectorAll('.seat.occupied').length;
        const total = parseInt(document.getElementById('totalSeats')?.innerText || '0');
        const available = total - allocated;
        
        const allocatedEl = document.getElementById('allocatedSeats');
        const availableEl = document.getElementById('availableSeats');
        
        if(allocatedEl) allocatedEl.innerText = allocated;
        if(availableEl) availableEl.innerText = available;
    }

    // Swap mode toggle
    document.getElementById('swapModeBtn')?.addEventListener('click', function(e) {
        e.preventDefault();
        swapMode = !swapMode;
        this.classList.toggle('active', swapMode);
        
        if(swapMode) {
            showToast('Swap mode activated. Click two seats to swap', 'info');
        } else {
            if(seatToSwap) {
                seatToSwap.classList.remove('selected');
                seatToSwap = null;
            }
        }
    });

    // Reset selection
    document.getElementById('resetSelectionBtn')?.addEventListener('click', function(e) {
        e.preventDefault();
        if(seatToSwap) {
            seatToSwap.classList.remove('selected');
            seatToSwap = null;
        }
        swapMode = false;
        const swapBtn = document.getElementById('swapModeBtn');
        if(swapBtn) swapBtn.classList.remove('active');
        showToast('Selection reset', 'info');
    });

    // Clear all allocations
    document.getElementById('clearAllocationsBtn')?.addEventListener('click', function() {
        if(confirm('Are you sure you want to clear all allocations?')) {
            document.querySelectorAll('.seat.occupied').forEach(seat => {
                removeStudentFromSeat(seat);
            });
        }
    });

    // Toast notification
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type === 'error' ? 'danger' : type} border-0 show`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        let container = document.querySelector('.toast-container');
        if(!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }
        
        container.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    // Initialize layout
    document.addEventListener('DOMContentLoaded', function() {
        generateHallLayout();
        updateRemainingCount();
        updateAllocationSummary();
    });

    // Form validation
    document.getElementById('allocationForm')?.addEventListener('submit', function(e) {
        const teacherSelect = this.querySelector('[name="teacher_id"]');
        if(!teacherSelect || !teacherSelect.value) {
            e.preventDefault();
            showToast('Please select an invigilator', 'warning');
            return false;
        }
        
        const allocations = document.querySelectorAll('input[name="assignments[]"]').length;
        if(allocations === 0) {
            if(!confirm('No students allocated. Are you sure you want to continue?')) {
                e.preventDefault();
                return false;
            }
        }
    });

    // Drag and drop reordering
    if(typeof Sortable !== 'undefined') {
        const studentList = document.getElementById('studentList');
        if(studentList) {
            new Sortable(studentList, {
                animation: 150,
                handle: '.handle',
                ghostClass: 'dragging',
                dragClass: 'drag',
            });
        }
    }
    </script>

    {{-- Add Bootstrap Icons --}}
    @push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    @endpush
    @endsection