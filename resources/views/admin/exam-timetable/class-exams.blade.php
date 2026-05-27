@extends('layouts.app')

@section('title', 'Class Exam Timetable - ' . $class->name)

@section('main')
<div class="container-fluid px-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>
                        {{ $class->name }} - Exam Timetable
                    </h4>
                    <small class="opacity-75">
                        Section: {{ $class->section_name }}
                    </small>
                </div>
                <div class="btn-group">
                    <a href="{{ route('admin.exam-timetable.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back to Classes
                    </a>
                    <a href="{{ route('admin.exams.create') }}" class="btn btn-secondary  btn-sm me-2">
                        <i class="fas fa-plus-circle me-1"></i> Add More Exams
                    </a>
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

            <!-- Class Stats -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center py-3">
                            <h3 class="mb-1">{{ $subjects->count() }}</h3>
                            <small>Total Subjects</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center py-3">
                            <h3 class="mb-1">{{ $exams->count() }}</h3>
                            <small>Scheduled Exams</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center py-3">
                            <h3 class="mb-1">{{ $groupedExams->count() }}</h3>
                            <small>Exam Days</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subjects List -->
            <div class="card mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-book me-2"></i>Class Subjects
                    </h5>
                    <span class="badge bg-primary">{{ $subjects->count() }} subjects</span>
                </div>
                <div class="card-body">
                    @if($subjects->count() > 0)
                        <div class="row">
                            @foreach($subjects as $subject)
                                @php
                                    $subjectExams = $exams->where('subject_id', $subject->id);
                                @endphp
                                <div class="col-md-4 mb-3">
                                    <div class="card border {{ $subjectExams->count() > 0 ? 'border-success' : 'border-secondary' }}">
                                        <div class="card-body p-3">
                                            <h6 class="mb-1">{{ $subject->name }}</h6>
                                            <p class="mb-1 small">
                                                <strong>Code:</strong> {{ $subject->code }}<br>
                                                @if($subject->teacher)
                                                    <strong>Teacher:</strong> {{ $subject->teacher->name ?? 'N/A' }}
                                                @endif
                                                <br>
                                                <strong>Semester:</strong> 
                                                @if($subject->semester)
                                                    {{ $subject->semester }}{{ $subject->semester == 1 ? 'st' : ($subject->semester == 2 ? 'nd' : ($subject->semester == 3 ? 'rd' : 'th')) }} Semester
                                                @else
                                                    N/A
                                                @endif
                                            </p>
                                            <div class="d-flex justify-content-between align-items-center mt-2">
                                                <span class="badge {{ $subjectExams->count() > 0 ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $subjectExams->count() }} exam{{ $subjectExams->count() !== 1 ? 's' : '' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-book fa-3x text-muted mb-3"></i>
                            <h5>No Subjects Found</h5>
                            <p class="text-muted">This class doesn't have any subjects assigned yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Exam Timetable -->
            <div class="card">
                <div class="card-header bg-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-calendar-day me-2"></i>Exam Schedule
                        </h5>
                        <div class="d-flex align-items-center">
                            @if(isset($semesters) && $semesters->count() > 0)
                            <form method="GET" action="{{ route('admin.exam-timetable.class-exams', $class) }}" class="me-3">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light">Semester</span>
                                    <select name="semester" class="form-select" onchange="this.form.submit()">
                                        <option value="">All Semesters</option>
                                        @foreach($semesters as $sem)
                                            <option value="{{ $sem }}" {{ request('semester') == $sem ? 'selected' : '' }}>
                                                {{ $sem }}{{ $sem == 1 ? 'st' : ($sem == 2 ? 'nd' : ($sem == 3 ? 'rd' : 'th')) }} Semester
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </form>
                            @endif
                            <div class="btn-group">
                                <button type="button" class="btn btn-light btn-sm" onclick="printTimetable()">
                                    <i class="fas fa-print me-1"></i> Print
                                </button>
                                @if($exams->count() > 0)
                                    <a href="{{ route('admin.exam-timetable.download', $class) }}{{ request('semester') ? '?semester='.request('semester') : '' }}" 
                                       class="btn btn-success btn-sm ms-2">
                                        <i class="fas fa-download me-1"></i> Download PDF
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body" id="timetableContent">
                    @if($exams->count() > 0)
                        @foreach($groupedExams as $date => $dayExams)
                            <div class="card mb-3 timetable-day">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-calendar-day me-2"></i>
                                        {{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="10%">Time</th>
                                                    <th width="20%">Subject</th>
                                                    <th width="10%">Exam Code</th>
                                                    <th width="8%">Year</th>
                                                    <th width="10%">Semester</th>
                                                    <th width="12%">Exam Type</th>
                                                    <th width="10%">Session</th>
                                                    <th width="10%">Duration</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($dayExams->sortBy('exam_time') as $exam)
                                                    @php
                                                        $typeColors = [
                                                            'final' => 'danger',
                                                            'midterm' => 'warning',
                                                            'quiz' => 'info',
                                                            'assignment' => 'secondary'
                                                        ];
                                                        $semester = $exam->subject->semester ?? null;
                                                        $semesterDisplay = $semester ? $semester . ($semester == 1 ? 'st' : ($semester == 2 ? 'nd' : ($semester == 3 ? 'rd' : 'th'))) . ' Sem' : '-';
                                                        $currentYearValue = $currentYear ?? date('Y');
                                                    @endphp
                                                    <tr>
                                                        <td><strong>{{ \Carbon\Carbon::parse($exam->exam_time)->format('h:i A') }}</strong></td>
                                                        <td>
                                                            <strong>{{ $exam->subject->name ?? 'N/A' }}</strong>
                                                            <br>
                                                            <small class="text-muted">{{ $exam->subject->code ?? 'N/A' }}</small>
                                                        </td>
                                                        <td><code>{{ $exam->subject->code ?? 'N/A' }}</code></td>
                                                        <td>{{ $currentYearValue }}</td>
                                                        <td>
                                                            <span class="badge bg-secondary">{{ $semesterDisplay }}</span>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-{{ $typeColors[$exam->exam_type] ?? 'secondary' }}">
                                                                {{ ucfirst($exam->exam_type) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="badge {{ $exam->time_session == 'FN' ? 'bg-warning' : 'bg-info' }}">
                                                                {{ $exam->time_session == 'FN' ? 'Forenoon' : 'Afternoon' }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $exam->duration_minutes }} mins</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer bg-light">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        {{ $dayExams->count() }} exam{{ $dayExams->count() > 1 ? 's' : '' }} on this day
                                    </small>
                                </div>
                            </div>
                        @endforeach
                        
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h5>No Exams Scheduled</h5>
                            <p class="text-muted mb-4">No exams have been scheduled for this class yet.</p>
                            <a href="{{ route('admin.exams.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus-circle me-1"></i> Schedule New Exam
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .timetable-day {
        transition: all 0.3s;
    }
    
    .timetable-day:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    @media print {
        .card-header, .card-footer, .btn, .input-group, .modal,
        .alert, .stats-card, .subjects-card {
            display: none !important;
        }
        
        .card {
            border: 1px solid #000 !important;
            page-break-inside: avoid;
        }
        
        .timetable-day {
            margin-bottom: 20px !important;
            page-break-inside: avoid;
        }
        
        body {
            font-size: 12px !important;
        }
        
        table {
            font-size: 11px !important;
        }
        
        .table {
            width: 100% !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Print timetable
    function printTimetable() {
        window.print();
    }
    
    // Auto-hide alerts
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000);
    
    // Initialize tooltips
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endpush