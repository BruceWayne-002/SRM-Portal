@extends('layouts.app')

@section('page-title', 'View Exam Allocation')

@section('main')
<div class="container-fluid py-4">
    {{-- Header with Breadcrumb --}}
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.halls.index') }}" class="text-decoration-none">Allocations</a></li>
                        <li class="breadcrumb-item active" aria-current="page">View Hall</li>
                    </ol>
                </nav>
                <h2 class="h3 mb-0">
                    <i class="bi bi-eye-fill me-2 text-primary"></i>
                    View Allocation - {{ $hall->hall_name }}
                </h2>
                <p class="text-muted mt-2">View seat allocations for selected examination</p>
            </div>
            
            {{-- Hall Quick Info --}}
            <div class="hall-quick-stats d-flex gap-3">
                <div class="stat-item bg-light rounded-3 p-3 text-center">
                    <div class="stat-value h4 mb-0">{{ $hall->capacity }}</div>
                    <div class="stat-label small text-muted">Total Capacity</div>
                </div>
                <div class="stat-item bg-light rounded-3 p-3 text-center">
                    <div class="stat-value h4 mb-0">{{ $hall->rows }} × {{ $hall->columns }}</div>
                    <div class="stat-label small text-muted">Layout</div>
                </div>
                <div class="stat-item bg-light rounded-3 p-3 text-center">
                    <div class="stat-value h4 mb-0">{{ $hall->students_per_table }}</div>
                    <div class="stat-label small text-muted">Per Table</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-0">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-calendar-check me-2 text-primary"></i>
                        Select Examination to View
                    </h5>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-end gap-3">
                        {{-- Legend --}}
                        <div class="legend d-flex gap-3">
                            <span class="d-flex align-items-center">
                                <span class="legend-dot available me-2"></span>
                                <span class="small">Available</span>
                            </span>
                            <span class="d-flex align-items-center">
                                <span class="legend-dot occupied me-2"></span>
                                <span class="small">Occupied</span>
                            </span>
                        </div>
                        
                        {{-- Export Options --}}
                        <!--<div class="dropdown">-->
                        <!--    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">-->
                        <!--        <i class="bi bi-download me-1"></i> Export-->
                        <!--    </button>-->
                        <!--    <ul class="dropdown-menu">-->
                        <!--        <li><a class="dropdown-item" href="#" id="exportPDF"><i class="bi bi-file-pdf me-2 text-danger"></i>PDF</a></li>-->
                        <!--        <li><a class="dropdown-item" href="#" id="exportExcel"><i class="bi bi-file-excel me-2 text-success"></i>Excel</a></li>-->
                        <!--        <li><a class="dropdown-item" href="#" id="printLayout"><i class="bi bi-printer me-2"></i>Print</a></li>-->
                        <!--    </ul>-->
                        <!--</div>-->
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            {{-- Exam Selection --}}
            <div class="row mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-secondary">
                        <i class="bi bi-search me-1"></i>Select Examination
                    </label>
                    <select id="examSelect" class="form-select form-select-lg">
                        <option value="" selected disabled>-- Choose an exam to view --</option>
                        @foreach($exams as $exam)
                            @php
                                // Get subject name safely
                                $subjectName = '';
                                if ($exam->subject) {
                                    $subjectName = $exam->subject->name;
                                } elseif ($exam->subject_name) {
                                    $subjectName = $exam->subject_name;
                                } else {
                                    $subjectName = 'Unknown Subject';
                                }
                                
                                // Get exam type with proper formatting
                                $examType = $exam->exam_type ? ucfirst($exam->exam_type) : 'Exam';
                                
                                // Format date
                                $formattedDate = '';
                                if ($exam->exam_date) {
                                    $formattedDate = \Carbon\Carbon::parse($exam->exam_date)->format('d M Y');
                                }
                            @endphp
                            <option value="{{ $exam->id }}" 
                                    data-exam='@json($exam)'
                                    data-date="{{ $exam->exam_date ?? '' }}">
                                📝 {{ $subjectName }} - {{ $examType }} Examination
                                @if($formattedDate)
                                    <small>({{ $formattedDate }})</small>
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Loading Indicator --}}
            <div id="loadingIndicator" class="text-center py-5 d-none">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="text-muted">Loading hall layout...</p>
            </div>

            {{-- Hall Layout Container --}}
            <div id="hallLayout" class="hall-layout-container"></div>

            {{-- Summary Card --}}
            <div id="allocationSummary" class="row g-3 mt-4 d-none">
                <div class="col-md-3">
                    <div class="summary-card bg-light rounded-3 p-3">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                                <i class="bi bi-people-fill text-primary fs-4"></i>
                            </div>
                            <div>
                                <span class="text-muted small">Total Students</span>
                                <h3 class="mb-0" id="totalStudents">0</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card bg-light rounded-3 p-3">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon bg-success bg-opacity-10 rounded-3 p-3 me-3">
                                <i class="bi bi-check-circle-fill text-success fs-4"></i>
                            </div>
                            <div>
                                <span class="text-muted small">Allocated</span>
                                <h3 class="mb-0" id="allocatedCount">0</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card bg-light rounded-3 p-3">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon bg-warning bg-opacity-10 rounded-3 p-3 me-3">
                                <i class="bi bi-hourglass-split text-warning fs-4"></i>
                            </div>
                            <div>
                                <span class="text-muted small">Available Seats</span>
                                <h3 class="mb-0" id="availableSeats">0</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card bg-light rounded-3 p-3">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon bg-info bg-opacity-10 rounded-3 p-3 me-3">
                                <i class="bi bi-grid-3x3-gap-fill text-info fs-4"></i>
                            </div>
                            <div>
                                <span class="text-muted small">Tables</span>
                                <h3 class="mb-0" id="totalTables">0</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hall-quick-stats .stat-item {
        min-width: 100px;
        transition: all 0.2s;
    }
    .hall-quick-stats .stat-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .legend-dot {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }
    .legend-dot.available {
        background-color: #e8f0ff;
        border: 2px solid #0d6efd;
    }
    .legend-dot.occupied {
        background-color: #f7d3c3;
        border: 2px solid #dc3545;
    }

    .hall-layout-container {
        background: #f8f9fa;
        border-radius: 16px;
        padding: 24px;
        min-height: 400px;
    }

    .table-card {
        background: white;
        border-radius: 12px;
        padding: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        transition: all 0.3s;
        min-width: 200px;
        flex: 1;
    }
    .table-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .table-header {
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 10px;
        margin-bottom: 12px;
    }

    .view-seat {
        width: 70px;
        height: 70px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 500;
        padding: 4px;
        text-align: center;
        transition: all 0.2s;
        border: 2px solid transparent;
        word-break: break-word;
    }
    .view-seat.available {
        background: #e8f0ff;
        border-color: #0d6efd;
        color: #0d6efd;
    }
    .view-seat.occupied {
        background: #f7d3c3;
        border-color: #dc3545;
        color: #721c24;
        font-weight: 600;
        font-size: 14px;
    }
    .view-seat.occupied:hover {
        background: #f5c2b7;
        transform: scale(1.05);
    }

    .summary-card {
        transition: all 0.2s;
    }
    .summary-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }

    .summary-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .breadcrumb {
        background: none;
        padding: 0;
        margin-bottom: 10px;
    }

    .layout-note {
        font-size: 12px;
        color: #6c757d;
        margin-top: 8px;
        text-align: center;
    }
    
    .form-select-lg {
        font-size: 15px !important;
    }
</style>

<script>
let hallId = {{ $hall->id }};
let currentData = null;

document.getElementById('examSelect').addEventListener('change', function() {
    const examId = this.value;
    if(!examId) return;

    // Show loading
    document.getElementById('loadingIndicator').classList.remove('d-none');
    document.getElementById('hallLayout').innerHTML = '';
    document.getElementById('allocationSummary').classList.add('d-none');

    fetch(`/admin/exam-allocation/view-layout/${hallId}/${examId}`)
        .then(res => res.json())
        .then(data => {
            currentData = data;
            renderHallLayout(data);
            updateSummary(data);
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('hallLayout').innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Error loading hall layout. Please try again.
                </div>
            `;
        })
        .finally(() => {
            document.getElementById('loadingIndicator').classList.add('d-none');
        });
});

function renderHallLayout(data) {
    const layout = document.getElementById('hallLayout');
    layout.innerHTML = '';

    if(!data.hall) {
        layout.innerHTML = `
            <div class="alert alert-warning d-flex align-items-center">
                <i class="bi bi-info-circle-fill me-3 fs-4"></i>
                <div>
                    <strong>No allocation found!</strong><br>
                    No seat allocations exist for the selected examination.
                </div>
            </div>
        `;
        return;
    }

    const { rows, columns, students_per_table, allocations } = data.hall;
    const totalTables = rows * columns;

    // Create layout note
    const noteDiv = document.createElement('div');
    noteDiv.className = 'layout-note mb-3';
    noteDiv.innerHTML = `<i class="bi bi-info-circle me-1"></i> Tables are arranged column-wise: Table 1,2 | Table 3,4 | Table 5,6 etc.`;
    layout.appendChild(noteDiv);

    // Initialize empty rows array for column-major layout
    const rowDivs = Array.from({ length: rows }, (_, index) => {
        const div = document.createElement('div');
        div.className = 'd-flex flex-wrap gap-4 justify-content-center mb-4';
        div.dataset.rowNumber = index + 1;
        return div;
    });

    // Loop columns first, then rows (column-major)
    for(let c = 1; c <= columns; c++) {
        for(let r = 1; r <= rows; r++) {
            const tableNumber = ((c - 1) * rows) + r; // Column-major ordering

            // Table Card
            const tableDiv = document.createElement('div');
            tableDiv.className = 'table-card';
            tableDiv.dataset.tableNumber = tableNumber;

            // Table Header
            const header = document.createElement('div');
            header.className = 'table-header d-flex justify-content-between align-items-center';
            header.innerHTML = `
                <span class="fw-bold">Table ${tableNumber}</span>
                <span class="badge bg-secondary">${students_per_table}</span>
            `;
            tableDiv.appendChild(header);

            // Seats Container
            const seatContainer = document.createElement('div');
            seatContainer.className = 'd-flex flex-wrap gap-2 justify-content-center';

            for(let s = 1; s <= students_per_table; s++) {
                const seatDiv = document.createElement('div');
                seatDiv.className = 'view-seat';

                const key = tableNumber + '-' + s;

                if(allocations && allocations[key]) {
                    const student = allocations[key];
                    seatDiv.classList.add('occupied');

                    // Show only roll number in the seat
                    seatDiv.innerHTML = `<div class="fw-bold">${student.roll_no}</div>`;
                    
                    // Optional: Add tooltip with student name
                    seatDiv.title = `${student.name} - ${student.roll_no}`;

                    // Keep the click event for details if needed
                    seatDiv.addEventListener('click', (e) => {
                        e.stopPropagation();
                        // You can still show details if needed, but we're removing the modal for now
                        // showStudentDetails(student);
                    });
                } else {
                    seatDiv.classList.add('available');
                    seatDiv.innerHTML = `<span class="small">Seat ${s}</span>`;
                }

                seatContainer.appendChild(seatDiv);
            }

            tableDiv.appendChild(seatContainer);
            rowDivs[r - 1].appendChild(tableDiv); // append to proper row
        }
    }

    // Append all rows to layout
    rowDivs.forEach(row => layout.appendChild(row));

    // Add layout explanation
    const layoutExample = document.createElement('div');
    layoutExample.className = 'layout-note mt-3';
    layoutExample.innerHTML = `<i class="bi bi-arrow-right-circle me-1"></i> Reading order: Read down each column, then move to next column`;
    layout.appendChild(layoutExample);
}

function updateSummary(data) {
    if(!data.hall) return;

    const { rows, columns, students_per_table, allocations } = data.hall;
    const totalTables = rows * columns;
    const totalSeats = totalTables * students_per_table;
    const allocatedCount = allocations ? Object.keys(allocations).length : 0;
    
    document.getElementById('totalStudents').innerText = allocatedCount;
    document.getElementById('allocatedCount').innerText = allocatedCount;
    document.getElementById('availableSeats').innerText = totalSeats - allocatedCount;
    document.getElementById('totalTables').innerText = totalTables;
    
    document.getElementById('allocationSummary').classList.remove('d-none');
}

// Optional: Remove or comment out the modal function if you don't want details
function showStudentDetails(student) {
    // This function is intentionally left empty as per requirement
    // No student details should be shown
    return;
}

// Print functionality
document.getElementById('printLayout')?.addEventListener('click', function(e) {
    e.preventDefault();
    window.print();
});

// Export to Excel
document.getElementById('exportExcel')?.addEventListener('click', function(e) {
    e.preventDefault();
    if(!currentData) {
        alert('Please select an exam first');
        return;
    }
    exportToExcel(currentData);
});

function exportToExcel(data) {
    // Simple CSV export
    const { hall } = data;
    if(!hall || !hall.allocations) return;
    
    let csv = "Table,Seat,Roll No\n"; // Removed Student Name column
    
    Object.entries(hall.allocations).forEach(([key, student]) => {
        const [table, seat] = key.split('-');
        csv += `${table},${seat},${student.roll_no}\n`; // Only roll number
    });
    
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `hall_${hallId}_allocation.csv`;
    a.click();
}

// Auto-load if exam is preselected
document.addEventListener('DOMContentLoaded', function() {
    const examSelect = document.getElementById('examSelect');
    if(examSelect.value) {
        examSelect.dispatchEvent(new Event('change'));
    }
});
</script>

{{-- Add Bootstrap Icons --}}
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
@endpush

{{-- Print Styles --}}
@push('styles')
<style media="print">
    .btn, .dropdown, .breadcrumb, .hall-quick-stats, .legend, #loadingIndicator, .layout-note {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    .hall-layout-container {
        background: white !important;
        padding: 0 !important;
    }
    .table-card {
        break-inside: avoid;
        page-break-inside: avoid;
        box-shadow: none !important;
        border: 1px solid #dee2e6 !important;
    }
    .view-seat {
        border: 1px solid #dee2e6 !important;
    }
</style>
@endpush
@endsection