@extends('layouts.teacher')

@section('main')
<div class="container-fluid py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Exam Timetable</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">
            <i class="bi bi-calendar-week"></i> Exam Timetable
            <small class="text-muted fs-6">({{ $teacher->name }})</small>
        </h2>
        <div>
            <span class="badge bg-primary fs-6">{{ $stats['total'] }} Total Exams</span>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card bg-primary text-white">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Today</h6>
                            <h3 class="mt-2 mb-0">{{ $stats['today'] }}</h3>
                        </div>
                        <i class="bi bi-calendar-day fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card bg-success text-white">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Upcoming</h6>
                            <h3 class="mt-2 mb-0">{{ $stats['upcoming'] }}</h3>
                        </div>
                        <i class="bi bi-calendar-plus fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card bg-info text-white">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">This Week</h6>
                            <h3 class="mt-2 mb-0">{{ $stats['this_week'] }}</h3>
                        </div>
                        <i class="bi bi-calendar-week fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card bg-warning text-white">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">This Month</h6>
                            <h3 class="mt-2 mb-0">{{ $stats['this_month'] }}</h3>
                        </div>
                        <i class="bi bi-calendar-month fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card bg-secondary text-white">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Completed</h6>
                            <h3 class="mt-2 mb-0">{{ $stats['completed'] }}</h3>
                        </div>
                        <i class="bi bi-calendar-check fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card bg-dark text-white">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Total</h6>
                            <h3 class="mt-2 mb-0">{{ $stats['total'] }}</h3>
                        </div>
                        <i class="bi bi-calendar2-range fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-funnel"></i> Filter Exams</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('teacher.timetable.index') }}" id="filterForm">
                <div class="row g-3">
                    <div class="col-12 col-md-3">
                        <label class="form-label">Filter By</label>
                        <select name="filter" class="form-select" onchange="this.form.submit()">
                            <option value="upcoming" {{ ($filter ?? 'upcoming') == 'upcoming' ? 'selected' : '' }}>Upcoming Exams</option>
                            <option value="today" {{ ($filter ?? '') == 'today' ? 'selected' : '' }}>Today's Exams</option>
                            <option value="week" {{ ($filter ?? '') == 'week' ? 'selected' : '' }}>This Week</option>
                            <option value="month" {{ ($filter ?? '') == 'month' ? 'selected' : '' }}>This Month</option>
                            <option value="completed" {{ ($filter ?? '') == 'completed' ? 'selected' : '' }}>Completed Exams</option>
                            <option value="all" {{ ($filter ?? '') == 'all' ? 'selected' : '' }}>All Exams</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label">Hall</label>
                        <select name="hall_id" class="form-select" onchange="this.form.submit()">
                            <option value="">All Halls</option>
                            @foreach($halls as $hall)
                                <option value="{{ $hall->id }}" {{ ($hallId ?? '') == $hall->id ? 'selected' : '' }}>
                                    {{ $hall->hall_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label">Specific Date</label>
                        <input type="date" name="date" class="form-control" value="{{ $date ?? '' }}" onchange="this.form.submit()">
                    </div>
                    <div class="col-12 col-md-3 d-flex align-items-end">
                        <a href="{{ route('teacher.timetable.index') }}" class="btn btn-secondary w-100">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset Filters
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Exams Display -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-list-check"></i> 
                @switch($filter ?? 'upcoming')
                    @case('today') Today's Hall Allocations @break
                    @case('week') This Week's Hall Allocations @break
                    @case('month') This Month's Hall Allocations @break
                    @case('completed') Completed Hall Allocations @break
                    @case('all') All Hall Allocations @break
                    @default Upcoming Hall Allocations
                @endswitch
            </h5>
            <span class="badge bg-light text-dark">{{ $allocations->count() }} allocations found</span>
        </div>
        <div class="card-body">
            @if($allocations->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x display-1 text-muted"></i>
                    <h4 class="mt-3 text-muted">No hall allocations found</h4>
                    <p class="text-muted">No hall allocations match your current filter criteria.</p>
                </div>
            @else
                <!-- Desktop/Tablet View -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Hall</th>
                                <th>Subject</th>
                                <th>Code</th>
                                <th>Session</th>
                                <th>Students</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allocations as $allocation)
                            @php
                                $examDate = Carbon\Carbon::parse($allocation->exam_date);
                                $isToday = $examDate->isToday();
                                $isPast = $examDate->isPast() && !$isToday;
                                $examTime = $allocation->exam ? $allocation->exam->exam_time : null;
                            @endphp
                            <tr class="{{ $isToday ? 'table-warning' : ($isPast ? 'table-secondary' : '') }}">
                                <td>
                                    <strong>{{ $examDate->format('d M Y') }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $examDate->format('l') }}</small>
                                </td>
                                <td>
                                    @if($examTime)
                                        {{ Carbon\Carbon::parse($examTime)->format('h:i A') }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $allocation->hall->hall_name ?? 'N/A' }}</strong>
                                    @if($allocation->hall->building)
                                        <br><small class="text-muted">{{ $allocation->hall->building }}</small>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $allocation->exam->subject_name ?? ($allocation->exam->subject->name ?? 'N/A') }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $allocation->exam->exam_type ?? 'Regular' }}</small>
                                </td>
                                <td>{{ $allocation->exam->subject_code ?? 'N/A' }}</td>
                                <td>
                                    @if($allocation->session == 'MORNING')
                                        <span class="badge bg-info">Morning</span>
                                    @elseif($allocation->session == 'AFTERNOON')
                                        <span class="badge bg-warning">Afternoon</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $allocation->session ?? 'N/A' }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary rounded-pill">{{ $allocation->students->count() }}</span>
                                    <br>
                                    <small class="text-muted">/{{ $allocation->hall->capacity ?? 0 }}</small>
                                </td>
                                <td>
                                    @if($isToday)
                                        <span class="badge bg-warning text-dark">Today</span>
                                    @elseif($isPast)
                                        <span class="badge bg-secondary">Completed</span>
                                    @else
                                        <span class="badge bg-success">Upcoming</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('teacher.halls.show', $allocation->id) }}" class="btn btn-sm btn-info" title="View Hall Layout">
                                            <i class="bi bi-grid-3x3"></i>
                                        </a>
                                        <a href="{{ route('teacher.halls.student-list', $allocation->id) }}" class="btn btn-sm btn-primary" title="View Students">
                                            <i class="bi bi-people"></i>
                                        </a>
                                        <a href="{{ route('teacher.halls.attendance-sheet', $allocation->id) }}" class="btn btn-sm btn-success" title="Download Attendance">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-secondary" onclick="viewAllocationDetails({{ $allocation->id }})" title="Details">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile View -->
                <div class="d-md-none">
                    @foreach($allocations->groupBy(function($allocation) {
                        return Carbon\Carbon::parse($allocation->exam_date)->format('Y-m-d');
                    }) as $date => $dateAllocations)
                        <div class="mb-4">
                            <h6 class="bg-light p-2 rounded">
                                <i class="bi bi-calendar"></i> 
                                {{ Carbon\Carbon::parse($date)->format('l, d M Y') }}
                                <span class="badge bg-primary float-end">{{ $dateAllocations->count() }} exams</span>
                            </h6>
                            @foreach($dateAllocations as $allocation)
                                @php
                                    $examDate = Carbon\Carbon::parse($allocation->exam_date);
                                    $isToday = $examDate->isToday();
                                    $examTime = $allocation->exam ? $allocation->exam->exam_time : null;
                                @endphp
                                <div class="card mb-2 {{ $isToday ? 'border-warning' : '' }}">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">{{ $allocation->hall->hall_name ?? 'N/A' }}</h6>
                                                <small class="text-muted d-block">
                                                    <i class="bi bi-clock"></i> 
                                                    {{ $examTime ? Carbon\Carbon::parse($examTime)->format('h:i A') : 'N/A' }}
                                                </small>
                                                <small class="text-muted d-block">
                                                    <i class="bi bi-book"></i> 
                                                    {{ $allocation->exam->subject_name ?? ($allocation->exam->subject->name ?? 'N/A') }}
                                                </small>
                                                <small class="text-muted d-block">
                                                    <i class="bi bi-people"></i> 
                                                    {{ $allocation->students->count() }} Students
                                                </small>
                                            </div>
                                            <div>
                                                @if($isToday)
                                                    <span class="badge bg-warning">Today</span>
                                                @elseif($examDate->isPast())
                                                    <span class="badge bg-secondary">Past</span>
                                                @else
                                                    <span class="badge bg-success">Upcoming</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="mt-2 text-end">
                                            <a href="{{ route('teacher.halls.show', $allocation->id) }}" class="btn btn-sm btn-info">
                                                <i class="bi bi-grid-3x3"></i>
                                            </a>
                                            <a href="{{ route('teacher.halls.student-list', $allocation->id) }}" class="btn btn-sm btn-primary">
                                                <i class="bi bi-people"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-secondary" onclick="viewAllocationDetails({{ $allocation->id }})">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Allocation Details Modal -->
<div class="modal fade" id="allocationDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Exam Allocation Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="allocationDetailsContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        border: none;
        border-radius: 10px;
    }
    .card-header {
        border-radius: 10px 10px 0 0 !important;
    }
    .table th {
        font-weight: 600;
    }
    .badge {
        padding: 0.5rem 0.8rem;
    }
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
    }
    @media (max-width: 768px) {
        .stats-card h3 {
            font-size: 1.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Debug in browser console
console.log('=== Timetable Page Loaded ===');
console.log('Filter:', '{{ $filter ?? 'upcoming' }}');
console.log('Total Allocations:', {{ $allocations->count() }});
console.log('Statistics:', @json($stats ?? []));

function viewAllocationDetails(allocationId) {
    console.log('Viewing allocation details for ID:', allocationId);
    
    $('#allocationDetailsModal').modal('show');
    $('#allocationDetailsContent').html(`
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `);
    
    $.ajax({
        url: '/teacher/timetable/allocation/' + allocationId + '/details',
        type: 'GET',
        success: function(response) {
            console.log('Allocation details loaded successfully:', response);
            if (response.success) {
                displayAllocationDetails(response.allocation);
            } else {
                showError('Failed to load allocation details');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading allocation details:', error);
            console.error('Response:', xhr.responseText);
            showError('Error loading allocation details: ' + error);
        }
    });
}

function displayAllocationDetails(allocation) {
    console.log('Displaying allocation details:', allocation);
    
    const exam = allocation.exam || {};
    const hall = allocation.hall || {};
    const examDate = allocation.exam_date ? new Date(allocation.exam_date) : null;
    
    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-IN', { 
            day: 'numeric', 
            month: 'long', 
            year: 'numeric' 
        });
    }
    
    function formatTime(timeString) {
        if (!timeString) return 'N/A';
        const time = timeString.split(':');
        const hours = parseInt(time[0]);
        const minutes = time[1];
        const ampm = hours >= 12 ? 'PM' : 'AM';
        const hour12 = hours % 12 || 12;
        return hour12 + ':' + minutes + ' ' + ampm;
    }
    
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    let statusHtml = '<span class="badge bg-secondary">N/A</span>';
    if (examDate) {
        const examDateStr = examDate.toDateString();
        const todayStr = today.toDateString();
        
        if (examDateStr === todayStr) {
            statusHtml = '<span class="badge bg-warning">Today</span>';
        } else if (examDate > today) {
            statusHtml = '<span class="badge bg-success">Upcoming</span>';
        } else {
            statusHtml = '<span class="badge bg-secondary">Completed</span>';
        }
    }
    
    var html = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="border-bottom pb-2">Exam Information</h6>
                <table class="table table-sm table-borderless">
                    <tr>
                        <th width="40%">Subject:</th>
                        <td><strong>${exam.subject_name || (exam.subject ? exam.subject.name : 'N/A')}</strong></td>
                    </tr>
                    <tr>
                        <th>Subject Code:</th>
                        <td>${exam.subject_code || (exam.subject ? exam.subject.code : 'N/A')}</td>
                    </tr>
                    <tr>
                        <th>Exam Type:</th>
                        <td><span class="badge bg-info">${exam.exam_type || 'Regular'}</span></td>
                    </tr>
                    <tr>
                        <th>Exam Date:</th>
                        <td><strong>${formatDate(allocation.exam_date)}</strong></td>
                    </tr>
                    <tr>
                        <th>Exam Time:</th>
                        <td>${formatTime(exam.exam_time)}</td>
                    </tr>
                    <tr>
                        <th>Session:</th>
                        <td><span class="badge bg-secondary">${allocation.session || 'N/A'}</span></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="border-bottom pb-2">Hall Information</h6>
                <table class="table table-sm table-borderless">
                    <tr>
                        <th width="40%">Hall Name:</th>
                        <td><strong>${hall.hall_name || 'N/A'}</strong></td>
                    </tr>
                    <tr>
                        <th>Building:</th>
                        <td>${hall.building || 'N/A'}</td>
                    </tr>
                    <tr>
                        <th>Floor:</th>
                        <td>${hall.floor || 'N/A'}</td>
                    </tr>
                    <tr>
                        <th>Capacity:</th>
                        <td>${hall.capacity || 0} seats</td>
                    </tr>
                    <tr>
                        <th>Layout:</th>
                        <td>${hall.rows || 0} rows × ${hall.columns || 0} columns</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <h6 class="border-bottom pb-2">Student Allocation</h6>
                <div class="d-flex justify-content-between align-items-center">
                    <span><strong>Total Students:</strong> ${allocation.students ? allocation.students.length : 0} / ${hall.capacity || 0}</span>
                    <span><strong>Status:</strong> ${statusHtml}</span>
                </div>
                <div class="progress mt-2" style="height: 20px;">
                    <div class="progress-bar bg-success" role="progressbar" 
                         style="width: ${((allocation.students ? allocation.students.length : 0) / (hall.capacity || 1)) * 100}%">
                        ${Math.round(((allocation.students ? allocation.students.length : 0) / (hall.capacity || 1)) * 100)}%
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('#allocationDetailsContent').html(html);
}

function showError(message) {
    console.error('Error:', message);
    $('#allocationDetailsContent').html(`
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle"></i> ${message}
        </div>
    `);
}

// For backward compatibility
function viewExamDetails(examId) {
    console.log('Viewing exam details for ID:', examId);
    
    $('#allocationDetailsModal').modal('show');
    $('#allocationDetailsContent').html(`
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `);
    
    $.ajax({
        url: '/teacher/timetable/exam/' + examId + '/details',
        type: 'GET',
        success: function(response) {
            console.log('Exam details loaded successfully:', response);
            if (response.success) {
                displayExamDetails(response.exam);
            } else {
                showError('Failed to load exam details');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading exam details:', error);
            showError('Error loading exam details: ' + error);
        }
    });
}

function displayExamDetails(exam) {
    var html = `
        <div class="row">
            <div class="col-12">
                <h6 class="border-bottom pb-2">Exam Information</h6>
                <table class="table table-sm table-borderless">
                    <tr>
                        <th width="40%">Subject:</th>
                        <td><strong>${exam.subject ? exam.subject.name : exam.subject_name || 'N/A'}</strong></td>
                    </tr>
                    <tr>
                        <th>Subject Code:</th>
                        <td>${exam.subject ? exam.subject.code : exam.subject_code || 'N/A'}</td>
                    </tr>
                    <tr>
                        <th>Exam Type:</th>
                        <td><span class="badge bg-info">${exam.exam_type || 'Regular'}</span></td>
                    </tr>
                    <tr>
                        <th>Exam Date:</th>
                        <td><strong>${formatDate(exam.exam_date)}</strong></td>
                    </tr>
                    <tr>
                        <th>Exam Time:</th>
                        <td>${formatTime(exam.exam_time)}</td>
                    </tr>
                </table>
            </div>
        </div>
    `;
    
    $('#allocationDetailsContent').html(html);
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-IN', { 
        day: 'numeric', 
        month: 'long', 
        year: 'numeric' 
    });
}

function formatTime(timeString) {
    if (!timeString) return 'N/A';
    const time = timeString.split(':');
    const hours = parseInt(time[0]);
    const minutes = time[1];
    const ampm = hours >= 12 ? 'PM' : 'AM';
    const hour12 = hours % 12 || 12;
    return hour12 + ':' + minutes + ' ' + ampm;
}
</script>
@endpush