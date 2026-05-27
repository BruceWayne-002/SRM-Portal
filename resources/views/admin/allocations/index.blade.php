@extends('layouts.app')

@section('title', 'Hall Allocations - ' . $exam->name)

@section('main')
<div class="container-fluid px-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0">
                        <i class="fas fa-chalkboard-teacher me-2"></i>Hall Allocations - {{ $exam->name }}
                    </h4>
                    <small class="opacity-75">
                        @if($exam->start_date && $exam->end_date)
                            {{ $exam->start_date->format('d M Y') }} - {{ $exam->end_date->format('d M Y') }}
                        @else
                            Dates not set
                        @endif
                    </small>
                </div>
                <div class="btn-group">
                    @if($exam->id)
                        <a href="{{ route('admin.exam-timetable.class-exams', $exam) }}" class="btn btn-light">
                            <i class="fas fa-calendar-alt me-1"></i> Exam Timetable
                        </a>
                        <a href="{{ route('admin.exams.show', $exam) }}" class="btn btn-light">
                            <i class="fas fa-eye me-1"></i> Exam Details
                        </a>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Allocation Stats -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center py-3">
                            <h3 class="mb-1">
                                @php
                                    $allocatedSessions = 0;
                                    foreach($timetables as $timetable) {
                                        $allocatedSessions += $timetable->hallAllocations->count();
                                    }
                                @endphp
                                {{ $allocatedSessions }}
                            </h3>
                            <small>Allocated Sessions</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center py-3">
                            <h3 class="mb-1">
                                {{ $timetables->count() }}
                            </h3>
                            <small>Total Sessions</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center py-3">
                            <h3 class="mb-1">
                                @php
                                    $allocatedTeachers = 0;
                                    $teacherIds = [];
                                    foreach($timetables as $timetable) {
                                        foreach($timetable->hallAllocations as $allocation) {
                                            if (!in_array($allocation->teacher_id, $teacherIds)) {
                                                $teacherIds[] = $allocation->teacher_id;
                                                $allocatedTeachers++;
                                            }
                                        }
                                    }
                                @endphp
                                {{ $allocatedTeachers }}
                            </h3>
                            <small>Teachers Assigned</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-secondary text-white">
                        <div class="card-body text-center py-3">
                            <h3 class="mb-1">
                                @php
                                    $allocatedStudents = 0;
                                    foreach($timetables as $timetable) {
                                        foreach($timetable->hallAllocations as $allocation) {
                                            $allocatedStudents += $allocation->studentAllocations->count();
                                        }
                                    }
                                @endphp
                                {{ $allocatedStudents }}
                            </h3>
                            <small>Students Allocated</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timetable with Allocations -->
            @if($timetables->count() > 0)
                <div class="accordion" id="allocationAccordion">
                    @foreach($timetables as $timetable)
                        @php
                            $allocations = $timetable->hallAllocations ?? collect();
                            $hasAllocations = $allocations->count() > 0;
                            $cardClass = $hasAllocations ? 'border-success' : 'border-warning';
                            $headerClass = $hasAllocations ? 'bg-success text-white' : 'bg-warning text-dark';
                        @endphp
                        
                        <div class="accordion-item {{ $cardClass }} mb-3">
                            <h2 class="accordion-header" id="heading{{ $timetable->id }}">
                                <button class="accordion-button {{ $hasAllocations ? '' : 'collapsed' }} {{ $headerClass }}" 
                                        type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#collapse{{ $timetable->id }}" 
                                        aria-expanded="{{ $hasAllocations ? 'true' : 'false' }}" 
                                        aria-controls="collapse{{ $timetable->id }}">
                                    <div class="d-flex justify-content-between w-100 me-3">
                                        <div>
                                            <i class="fas fa-calendar-day me-2"></i>
                                            <strong>{{ $timetable->exam_date ? $timetable->exam_date->format('l, d F Y') : 'Date not set' }}</strong>
                                            <span class="ms-3">
                                                {{ $timetable->start_time ? \Carbon\Carbon::parse($timetable->start_time)->format('h:i A') : 'N/A' }} - 
                                                {{ $timetable->end_time ? \Carbon\Carbon::parse($timetable->end_time)->format('h:i A') : 'N/A' }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="badge {{ $hasAllocations ? 'bg-light text-success' : 'bg-light text-warning' }}">
                                                {{ $allocations->count() }} hall{{ $allocations->count() != 1 ? 's' : '' }} allocated
                                            </span>
                                        </div>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse{{ $timetable->id }}" 
                                 class="accordion-collapse collapse {{ $hasAllocations ? 'show' : '' }}" 
                                 aria-labelledby="heading{{ $timetable->id }}" 
                                 data-bs-parent="#allocationAccordion">
                                <div class="accordion-body">
                                    <!-- Subject Info -->
                                    <div class="row mb-4">
                                        <div class="col-md-8">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h5 class="card-title">
                                                        <i class="fas fa-book me-2"></i>
                                                        {{ $timetable->subject->name ?? 'Subject not found' }}
                                                        <small class="text-muted">({{ $timetable->subject->code ?? 'N/A' }})</small>
                                                    </h5>
                                                    <p class="card-text mb-2">
                                                        <i class="fas fa-clock me-1"></i>
                                                        @if($timetable->start_time && $timetable->end_time)
                                                            @php
                                                                $start = \Carbon\Carbon::parse($timetable->start_time);
                                                                $end = \Carbon\Carbon::parse($timetable->end_time);
                                                                $duration = $start->diff($end);
                                                            @endphp
                                                            Duration: {{ $duration->format('%h hr %i min') }}
                                                        @else
                                                            Duration: Not set
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            @if(!$hasAllocations)
                                                <div class="card bg-warning">
                                                    <div class="card-body text-center">
                                                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                                        <h6>No Halls Allocated</h6>
                                                        <p class="mb-3">Allocate halls for this exam session</p>
                                                        <button class="btn btn-light" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#allocateModal{{ $timetable->id }}">
                                                            <i class="fas fa-plus-circle me-1"></i> Allocate Hall
                                                        </button>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="d-grid gap-2">
                                                    <button class="btn btn-success" 
                                                            onclick="allocateStudents({{ $timetable->id }}, 'all')">
                                                        <i class="fas fa-robot me-1"></i> Auto-allocate Students
                                                    </button>
                                                    <button class="btn btn-light" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#allocateModal{{ $timetable->id }}">
                                                        <i class="fas fa-plus-circle me-1"></i> Add More Halls
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Allocations List -->
                                    @if($hasAllocations)
                                        <div class="card mb-4">
                                            <div class="card-header bg-success text-white">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h5 class="mb-0">
                                                        <i class="fas fa-chalkboard-teacher me-2"></i>Allocated Halls
                                                    </h5>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    @foreach($allocations as $allocation)
                                                        <div class="col-md-6 mb-3">
                                                            <div class="card border-success">
                                                                <div class="card-header bg-success text-white py-2">
                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                        <h6 class="mb-0">
                                                                            <i class="fas fa-chalkboard me-1"></i>
                                                                            {{ $allocation->examHall->hall_name ?? 'Unknown Hall' }}
                                                                        </h6>
                                                                        <span class="badge bg-light text-success">
                                                                            {{ $allocation->studentAllocations->count() }}/{{ $allocation->examHall->capacity ?? 0 }}
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <p class="mb-1">
                                                                                <i class="fas fa-user-tie me-1"></i>
                                                                                <strong>Invigilator:</strong>
                                                                            </p>
                                                                            <p class="ms-3">
                                                                                {{ $allocation->teacher->name ?? 'Not assigned' }}
                                                                                <br>
                                                                                <small class="text-muted">
                                                                                    {{ $allocation->teacher_role }}
                                                                                </small>
                                                                            </p>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <p class="mb-1">
                                                                                <i class="fas fa-info-circle me-1"></i>
                                                                                <strong>Hall Info:</strong>
                                                                            </p>
                                                                            <p class="ms-3">
                                                                                {{ $allocation->examHall->building ?? '' }} - {{ $allocation->examHall->floor ?? '' }}
                                                                                <br>
                                                                                <small class="text-muted">
                                                                                    Capacity: {{ $allocation->examHall->capacity ?? 0 }}
                                                                                </small>
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <!-- Student Allocation Status -->
                                                                    <div class="mt-3">
                                                                        @if($allocation->studentAllocations->count() > 0)
                                                                            <div class="alert alert-success py-2 mb-2">
                                                                                <i class="fas fa-users me-1"></i>
                                                                                {{ $allocation->studentAllocations->count() }} students allocated
                                                                            </div>
                                                                            <div class="d-grid gap-2">
                                                                                <a href="{{ route('admin.allocation.view', $allocation) }}" 
                                                                                   class="btn btn-sm btn-outline-success">
                                                                                    <i class="fas fa-eye me-1"></i> View Students
                                                                                </a>
                                                                            </div>
                                                                        @else
                                                                            <div class="alert alert-warning py-2 mb-2">
                                                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                                                No students allocated yet
                                                                            </div>
                                                                            <div class="d-grid">
                                                                                <button class="btn btn-sm btn-success" 
                                                                                        onclick="allocateStudents({{ $timetable->id }}, {{ $allocation->id }})">
                                                                                    <i class="fas fa-user-plus me-1"></i> Allocate Students
                                                                                </button>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="card-footer bg-light py-2">
                                                                    <div class="d-flex justify-content-between">
                                                                        <small class="text-muted">
                                                                            <i class="fas fa-hashtag me-1"></i>
                                                                            {{ $allocation->examHall->hall_code ?? 'N/A' }}
                                                                        </small>
                                                                        <button class="btn btn-sm btn-outline-danger" 
                                                                                data-bs-toggle="modal" 
                                                                                data-bs-target="#removeAllocationModal{{ $allocation->id }}">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Allocate Hall Modal -->
                        <div class="modal fade" id="allocateModal{{ $timetable->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title">
                                            <i class="fas fa-plus-circle me-2"></i>Allocate Hall
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('admin.allocate.hall', $timetable) }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Allocating hall for: 
                                                <strong>{{ $timetable->subject->name ?? 'Unknown Subject' }}</strong> on 
                                                <strong>{{ $timetable->exam_date ? $timetable->exam_date->format('d M Y') : 'Date not set' }}</strong> at 
                                                <strong>{{ $timetable->start_time ? \Carbon\Carbon::parse($timetable->start_time)->format('h:i A') : 'N/A' }}</strong>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="exam_hall_id{{ $timetable->id }}" class="form-label">
                                                        <i class="fas fa-chalkboard me-1"></i> Select Exam Hall *
                                                    </label>
                                                    <select class="form-select" 
                                                            id="exam_hall_id{{ $timetable->id }}" 
                                                            name="exam_hall_id" 
                                                            required>
                                                        <option value="" disabled selected>-- Select Hall --</option>
                                                        @foreach($halls as $hall)
                                                            @php
                                                                $allocated = $timetable->hallAllocations->where('exam_hall_id', $hall->id)->first();
                                                                $isAllocated = $allocated ? true : false;
                                                            @endphp
                                                            <option value="{{ $hall->id }}" 
                                                                    {{ $isAllocated ? 'disabled' : '' }}
                                                                    data-capacity="{{ $hall->capacity }}"
                                                                    data-building="{{ $hall->building }}"
                                                                    data-floor="{{ $hall->floor }}">
                                                                {{ $hall->hall_name }} ({{ $hall->hall_code }})
                                                                @if($isAllocated)
                                                                    - Already Allocated
                                                                @endif
                                                                - Capacity: {{ $hall->capacity }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label for="teacher_id{{ $timetable->id }}" class="form-label">
                                                        <i class="fas fa-user-tie me-1"></i> Select Teacher *
                                                    </label>
                                                    <select class="form-select" 
                                                            id="teacher_id{{ $timetable->id }}" 
                                                            name="teacher_id" 
                                                            required>
                                                        <option value="" disabled selected>-- Select Teacher --</option>
                                                        @foreach($teachers as $teacher)
                                                            @php
                                                                $allocated = $timetable->hallAllocations->where('teacher_id', $teacher->id)->first();
                                                                $isAllocated = $allocated ? true : false;
                                                            @endphp
                                                            <option value="{{ $teacher->id }}" 
                                                                    {{ $isAllocated ? 'disabled' : '' }}>
                                                                {{ $teacher->name }} ({{ $teacher->employee_id ?? 'N/A' }})
                                                                @if($isAllocated)
                                                                    - Already Assigned
                                                                @endif
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="teacher_role{{ $timetable->id }}" class="form-label">
                                                        <i class="fas fa-user-tag me-1"></i> Teacher Role *
                                                    </label>
                                                    <select class="form-select" 
                                                            id="teacher_role{{ $timetable->id }}" 
                                                            name="teacher_role" 
                                                            required>
                                                        <option value="invigilator" selected>Invigilator</option>
                                                        <option value="chief_invigilator">Chief Invigilator</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Allocate Hall</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    No timetable entries found for this exam. Please create exam timetable first.
                </div>
            @endif
        </div>
        <div class="card-footer bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        After allocations, students can view/download their hall tickets
                    </small>
                </div>
                <div>
                    <a href="{{ route('admin.exams.show', $exam) }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Exam
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto allocate students
    function allocateStudents(timetableId, allocationId) {
        let url = '';
        let message = '';
        
        if (allocationId === 'all') {
            url = `/admin/allocations/timetable/${timetableId}/auto-allocate`;
            message = 'Auto-allocate students to all halls for this session?';
        } else {
            url = `/admin/allocations/timetable/${timetableId}/auto-allocate`;
            message = 'Auto-allocate students to this hall?';
        }
        
        if (confirm(message)) {
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ allocation_id: allocationId !== 'all' ? allocationId : null })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert(data.message || 'Error allocating students');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error allocating students');
            });
        }
    }

    // Auto-hide alerts
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000);
</script>
@endpush