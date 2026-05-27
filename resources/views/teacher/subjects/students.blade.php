@extends('layouts.teacher')

@section('main')
<div class="container-fluid py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('teacher.subjects.index') }}">My Subjects</a></li>
            <li class="breadcrumb-item"><a href="{{ route('teacher.subjects.show', $subject->id) }}">{{ $subject->name }}</a></li>
            <li class="breadcrumb-item active">Students List</li>
        </ol>
    </nav>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
            <h4 class="mb-2 mb-md-0">
                <i class="bi bi-people-fill"></i> 
                Students List - {{ $subject->name }} 
                <small class="text-white-50 d-block d-md-inline mt-1 mt-md-0">({{ $subject->class_name }} - Section {{ $subject->section }})</small>
            </h4>
            <div>
                <span class="badge bg-light text-dark fs-6 me-2">Total: {{ $students->count() }}</span>
                <span class="badge bg-success fs-6">Academic Year: {{ $subject->academic_year ?? 'Current' }}</span>
            </div>
        </div>
        
        <div class="card-body">
            @if($students->isEmpty())
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> No active students found in this class for the current academic year.
                </div>
            @else
                <!-- Search and Actions Row -->
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-8 col-lg-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" id="searchInput" class="form-control" placeholder="Search students by name, roll no, or parent name...">
                            <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 col-lg-6 text-md-end">
                        <div class="btn-group w-100 w-md-auto" role="group">
                            <button class="btn btn-outline-primary" onclick="exportToCSV()">
                                <i class="bi bi-download"></i> <span class="d-none d-sm-inline">Export</span>
                            </button>
                            <button class="btn btn-outline-secondary" onclick="window.print()">
                                <i class="bi bi-printer"></i> <span class="d-none d-sm-inline">Print</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Academic Year Info Banner -->
                <div class="alert alert-success mb-4">
                    <i class="bi bi-calendar-check"></i> 
                    <strong>Showing active students for academic year:</strong> {{ $subject->academic_year ?? 'Current Academic Year' }}
                </div>

                <!-- Responsive Table - Cards on Mobile, Table on Desktop -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-hover table-striped align-middle" id="studentsTable">
                        <thead class="table-bg-primary">
                            <tr>
                                <th>#</th>
                                <th>Roll No</th>
                                <th>Student Name</th>
                                <th>Class & Section</th>
                                <th>Year</th>
                                <th>Semester</th>
                                <th>Father's Name</th>
                                <th>Contact</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $index => $student)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <span class="badge bg-primary rounded-pill px-3">{{ $student->roll_no }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="text-truncate" style="max-width: 200px;">
                                            <strong class="d-block text-truncate">{{ $student->name }}</strong>
                                            <small class="text-muted text-truncate">{{ $student->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $student->class_name ?? 'N/A' }} - {{ $student->section ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-success">
                                        {{ $student->current_year }}<sup>{{ $student->current_year_suffix }}</sup>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-warning text-dark">
                                        {{ $student->current_semester }}<sup>{{ $student->semester_suffix }}</sup>
                                    </span>
                                </td>
                                <td class="text-truncate" style="max-width: 150px;">{{ $student->parent_name ?? 'N/A' }}</td>
                                <td>
                                    @if($student->contact)
                                        <a href="tel:{{ $student->contact }}" class="text-decoration-none">
                                            <i class="bi bi-telephone-fill text-success me-1"></i>
                                            <span class="d-none d-lg-inline">{{ $student->contact }}</span>
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-success rounded-pill px-3">
                                        Active
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="d-md-none">
                    @foreach($students as $index => $student)
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="d-flex align-items-center">
                                    @if($student->image)
                                        <img src="{{ asset('storage/' . $student->image) }}" 
                                             alt="{{ $student->name }}" 
                                             class="rounded-circle me-2" 
                                             width="45" height="45"
                                             style="object-fit: cover;">
                                    @else
                                        <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center" 
                                             style="width: 45px; height: 45px;">
                                            <i class="bi bi-person text-white fs-5"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ $student->name }}</h6>
                                        <small class="text-muted">{{ $student->email }}</small>
                                    </div>
                                </div>
                                <span class="badge bg-primary rounded-pill px-3">#{{ $student->roll_no }}</span>
                            </div>
                            
                            <div class="row g-2 mt-2">
                                <div class="col-6">
                                    <small class="text-muted d-block">Class & Section</small>
                                    <span class="badge bg-info">{{ $student->class_name ?? 'N/A' }} - {{ $student->section ?? 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Status</small>
                                    <span class="badge bg-success rounded-pill px-3">Active</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Current Year</small>
                                    <span class="badge bg-success">{{ $student->current_year }}{{ $student->current_year_suffix }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Semester</small>
                                    <span class="badge bg-warning text-dark">{{ $student->current_semester }}{{ $student->semester_suffix }}</span>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted d-block">Father's Name</small>
                                    <span>{{ $student->parent_name ?? 'N/A' }}</span>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted d-block">Contact</small>
                                    @if($student->contact)
                                        <a href="tel:{{ $student->contact }}" class="text-decoration-none">
                                            <i class="bi bi-telephone-fill text-success me-1"></i>
                                            {{ $student->contact }}
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Summary Cards - Responsive Grid -->
                <div class="row g-3 mt-4">
                    <div class="col-6 col-md-3">
                        <div class="card bg-light border-0">
                            <div class="card-body text-center p-3">
                                <h3 class="text-primary mb-1">{{ $students->count() }}</h3>
                                <small class="text-muted">Total Active</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card bg-light border-0">
                            <div class="card-body text-center p-3">
                                <h3 class="text-success mb-1">
                                    {{ $students->where('gender', 'Male')->count() }}
                                </h3>
                                <small class="text-muted">Boys</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card bg-light border-0">
                            <div class="card-body text-center p-3">
                                <h3 class="text-info mb-1">
                                    {{ $students->where('gender', 'Female')->count() }}
                                </h3>
                                <small class="text-muted">Girls</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card bg-light border-0">
                            <div class="card-body text-center p-3">
                                <h3 class="text-warning mb-1">
                                    {{ $students->pluck('current_year')->unique()->count() }}
                                </h3>
                                <small class="text-muted">Years</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="alert alert-info mt-4 mb-0">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-info-circle fs-5 me-2"></i>
                        <div class="flex-grow-1">
                            <strong>Academic Year:</strong> {{ $subject->academic_year ?? 'Current' }} | 
                            <strong>Class:</strong> {{ $subject->class_name }} | 
                            <strong>Section:</strong> {{ $subject->section }} | 
                            <strong>Semester:</strong> {{ $subject->semester_display }} | 
                            <strong>Status:</strong> <span class="badge bg-success">Active Students Only</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Global Styles */
    .container-fluid {
        max-width: 1600px;
        margin: 0 auto;
    }
    
    /* Table Styles */
    .table th {
        white-space: nowrap;
        background-color: #f8f9fa;
        font-weight: 600;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .table .badge {
        font-size: 0.8rem;
        padding: 0.4rem 0.8rem;
        font-weight: 500;
    }
    
    /* Card Styles */
    .card-header small {
        font-size: 0.9rem;
        opacity: 0.9;
    }
    
    /* Mobile Card View */
    .d-md-none .card {
        transition: transform 0.2s;
        border-left: 4px solid #28a745 !important;
    }
    
    .d-md-none .card:hover {
        transform: translateY(-2px);
    }
    
    .d-md-none .badge {
        font-size: 0.75rem;
        padding: 0.3rem 0.6rem;
    }
    
    /* Button Groups */
    .btn-group {
        gap: 5px;
    }
    
    .btn-group .btn {
        border-radius: 5px !important;
        margin: 0;
    }
    
    /* Responsive Text */
    .text-truncate {
        max-width: 100%;
    }
    
    /* Summary Cards */
    .bg-light .card-body {
        padding: 1rem;
    }
    
    .bg-light h3 {
        font-size: calc(1.3rem + 0.6vw);
        font-weight: 600;
    }
    
    /* Active Student Indicator */
    .badge.bg-success {
        background-color: #28a745 !important;
    }
    
    /* Print Styles */
    @media print {
        .btn, .input-group, nav, .breadcrumb, .d-md-none .btn-group {
            display: none !important;
        }
        
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        
        .card-header {
            background-color: #000 !important;
            color: white !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        .badge {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            border: 1px solid #000 !important;
        }
        
        .table {
            width: 100% !important;
            border-collapse: collapse !important;
        }
        
        .table th {
            background-color: #f0f0f0 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
    
    /* Tablet and Desktop */
    @media (min-width: 768px) {
        .d-md-none {
            display: none !important;
        }
        
        .d-none.d-md-block {
            display: block !important;
        }
        
        .w-md-auto {
            width: auto !important;
        }
    }
    
    /* Small Mobile */
    @media (max-width: 575px) {
        .btn-group {
            width: 100%;
        }
        
        .btn-group .btn {
            flex: 1;
        }
        
        .card-header h4 {
            font-size: 1.2rem;
        }
        
        .badge.fs-6 {
            font-size: 0.9rem !important;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>


// Export to CSV function
function exportToCSV() {
    var students = @json($students);
    var subject = @json($subject);
    
    var csv = [];
    
    // Headers
    csv.push(['Roll No', 'Student Name', 'Email', 'Class', 'Section', 'Current Year', 'Semester', 'Father\'s Name', 'Contact']);
    
    // Data
    students.forEach(function(student) {
        csv.push([
            student.roll_no,
            student.name,
            student.email,
            student.class_name || 'N/A',
            student.section || 'N/A',
            student.current_year + (student.current_year_suffix || 'th'),
            student.current_semester + (student.semester_suffix || 'th'),
            student.parent_name || 'N/A',
            student.contact || 'N/A'
        ]);
    });
    
    // Convert to CSV string
    var csvString = csv.map(row => row.join(',')).join('\n');
    
    // Download
    var blob = new Blob([csvString], { type: 'text/csv' });
    var url = window.URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = subject.name + '_active_students.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}
</script>
@endpush