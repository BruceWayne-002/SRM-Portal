@extends('layouts.teacher')

@section('main')
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('teacher.halls.index') }}">My Halls</a></li>
            <li class="breadcrumb-item active">{{ $allocation->hall->hall_name ?? 'Hall Details' }}</li>
        </ol>
    </nav>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Left Column - Hall & Exam Information -->
        <div class="col-md-4">
            <!-- Hall Information Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Hall Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Hall Name:</th>
                            <td><strong class="text-primary">{{ $allocation->hall->hall_name ?? 'N/A' }}</strong></td>
                        </tr>
                        <tr>
                            <th>Building:</th>
                            <td>{{ $allocation->hall->building ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Floor:</th>
                            <td>{{ $allocation->hall->floor ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Total Capacity:</th>
                            <td>{{ $allocation->hall->capacity ?? 0 }} seats</td>
                        </tr>
                        <tr>
                            <th>Layout:</th>
                            <td>{{ $allocation->hall->rows ?? 0 }} rows × {{ $allocation->hall->columns ?? 0 }} columns</td>
                        </tr>
                        <tr>
                            <th>Students per Table:</th>
                            <td>{{ $allocation->hall->students_per_table ?? 1 }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="card shadow-sm">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('teacher.halls.student-list', $allocation->id) }}" class="btn btn-info">
                            <i class="bi bi-people me-2"></i>View Student List
                        </a>
                        <a href="{{ route('teacher.halls.attendance-sheet', $allocation->id) }}" class="btn btn-success">
                            <i class="bi bi-download me-2"></i>Download Attendance Sheet
                        </a>
                        <a href="{{ route('teacher.halls.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Hall Layout -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-grid-3x3 me-2"></i>Hall Layout</h5>
                    <span class="badge bg-light text-dark fs-6">
                        <i class="bi bi-people me-1"></i>{{ $totalStudents }} Students Allocated
                    </span>
                </div>
                <div class="card-body">
                    <!-- Loading Spinner -->
                    <div id="hallLayout" class="hall-layout-container text-center py-5">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">Loading hall layout...</p>
                    </div>

                    <!-- Legend -->
                    <div class="d-flex justify-content-center gap-4 mt-4">
                        <div class="d-flex align-items-center">
                            <span class="legend-dot available me-2"></span>
                            <span>Available Seat</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="legend-dot occupied me-2"></span>
                            <span>Occupied Seat</span>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="row mt-4 g-3">
                        <div class="col-4">
                            <div class="card bg-light border-0">
                                <div class="card-body text-center py-3">
                                    <h6 class="text-muted mb-2">Total Seats</h6>
                                    <h3 class="mb-0 text-primary">{{ $allocation->hall->capacity ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card bg-light border-0">
                                <div class="card-body text-center py-3">
                                    <h6 class="text-muted mb-2">Allocated</h6>
                                    <h3 class="mb-0 text-success">{{ $totalStudents }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card bg-light border-0">
                                <div class="card-body text-center py-3">
                                    <h6 class="text-muted mb-2">Available</h6>
                                    <h3 class="mb-0 text-warning">{{ ($allocation->hall->capacity ?? 0) - $totalStudents }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hall-layout-container {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px;
    min-height: 500px;
    overflow-x: auto;
}

.legend-dot {
    display: inline-block;
    width: 16px;
    height: 16px;
    border-radius: 4px;
}

.legend-dot.available {
    background-color: #e8f4fd;
    border: 2px solid #0d6efd;
}

.legend-dot.occupied {
    background-color: #fce4e4;
    border: 2px solid #dc3545;
}

.seat {
    width: 80px;
    height: 80px;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    text-align: center;
    padding: 4px;
    border: 2px solid;
    margin: 4px;
    transition: all 0.2s;
}

.seat.available {
    background-color: #e8f4fd;
    border-color: #0d6efd;
    color: #0d6efd;
}

.seat.available:hover {
    background-color: #d0e8ff;
    transform: scale(1.05);
}

.seat.occupied {
    background-color: #fce4e4;
    border-color: #dc3545;
    color: #721c24;
    cursor: pointer;
}

.seat.occupied:hover {
    background-color: #f8d7da;
    transform: scale(1.05);
}

.seat small {
    font-size: 9px;
    line-height: 1.2;
    max-width: 70px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.table-header {
    font-weight: bold;
    margin-bottom: 10px;
    color: #495057;
}
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadHallLayout();
});

function loadHallLayout() {
    const layoutUrl = '{{ route("teacher.halls.layout", $allocation->id) }}';
    
    fetch(layoutUrl)
        .then(res => {
            if (!res.ok) {
                throw new Error('Network response was not ok');
            }
            return res.json();
        })
        .then(data => {
            if (data.success) {
                renderHallLayout(data.hall);
            } else {
                document.getElementById('hallLayout').innerHTML = 
                    '<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>' + 
                    (data.error || 'Failed to load hall layout') + '</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('hallLayout').innerHTML = 
                '<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>Error loading hall layout. Please try again.</div>';
        });
}

function renderHallLayout(hall) {
    const container = document.getElementById('hallLayout');
    container.innerHTML = '';
    container.classList.remove('text-center', 'py-5');

    const { rows, columns, students_per_table, allocations } = hall;

    if (!rows || !columns) {
        container.innerHTML = '<div class="alert alert-warning">Hall layout not configured properly.</div>';
        return;
    }

    // Create layout grid
    for(let r = 1; r <= rows; r++) {
        const rowDiv = document.createElement('div');
        rowDiv.className = 'd-flex flex-wrap gap-3 justify-content-center mb-4';
        
        for(let c = 1; c <= columns; c++) {
            const tableNumber = ((c - 1) * rows) + r;
            
            // Table container
            const tableDiv = document.createElement('div');
            tableDiv.className = 'bg-white rounded-3 p-3 shadow-sm';
            tableDiv.style.minWidth = '220px';
            tableDiv.style.flex = '1';

            // Table header
            const header = document.createElement('div');
            header.className = 'fw-bold text-center border-bottom pb-2 mb-2 table-header';
            header.textContent = `Table ${tableNumber}`;
            tableDiv.appendChild(header);

            // Seats container
            const seatContainer = document.createElement('div');
            seatContainer.className = 'd-flex flex-wrap gap-2 justify-content-center';

            for(let s = 1; s <= students_per_table; s++) {
                const key = tableNumber + '-' + s;
                const seat = document.createElement('div');
                seat.className = 'seat ' + (allocations && allocations[key] ? 'occupied' : 'available');

                if (allocations && allocations[key]) {
                    const student = allocations[key];
                    seat.setAttribute('title', `${student.name} (${student.roll_no})`);
                    seat.innerHTML = `
                        <strong>${student.roll_no || 'N/A'}</strong>
                        <br><small>${truncateString(student.name || 'Student', 10)}</small>
                    `;
                    
                    // Add click event to show student details
                    seat.addEventListener('click', function() {
                        showStudentDetails(student);
                    });
                } else {
                    seat.innerHTML = `
                        <small>Seat ${s}</small>
                        <br><small class="text-muted">Available</small>
                    `;
                }

                seatContainer.appendChild(seat);
            }

            tableDiv.appendChild(seatContainer);
            rowDiv.appendChild(tableDiv);
        }
        
        container.appendChild(rowDiv);
    }
}

function truncateString(str, maxLength) {
    if (!str) return '';
    return str.length > maxLength ? str.substr(0, maxLength) + '...' : str;
}

function showStudentDetails(student) {
    // You can implement a modal here to show student details
    alert(`Student: ${student.name}\nRoll No: ${student.roll_no}\nClass: ${student.class}\nSection: ${student.section}`);
}
</script>
@endpush