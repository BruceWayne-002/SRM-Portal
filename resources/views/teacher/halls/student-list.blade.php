@extends('layouts.teacher')

@section('main')
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('teacher.halls.index') }}">My Halls</a></li>
            <li class="breadcrumb-item"><a href="{{ route('teacher.halls.show', $allocation->id) }}">{{ $allocation->hall->hall_name ?? 'Hall' }}</a></li>
            <li class="breadcrumb-item active">Student List</li>
        </ol>
    </nav>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-people me-2"></i>Student List - {{ $allocation->exam->subject_name ?? 'Exam' }}
                <small class="text-white-50 ms-2">({{ $allocation->hall->hall_name ?? 'Hall' }})</small>
            </h5>
            <div>
                <span class="badge bg-light text-dark me-2">Total: {{ $allocatedStudents->count() }} Students</span>
                <span class="badge bg-light text-dark">Date: {{ \Carbon\Carbon::parse($allocation->exam_date)->format('d M Y') }}</span>
            </div>
        </div>
        <div class="card-body">
            <!-- Exam Info Alert -->
            <div class="alert alert-info d-flex align-items-center mb-4">
                <i class="bi bi-info-circle-fill me-3 fs-4"></i>
                <div class="row w-100">
                    <div class="col-md-3">
                        <strong>Exam:</strong> {{ $allocation->exam->subject_name ?? 'N/A' }}
                    </div>
                    <div class="col-md-3">
                        <strong>Hall:</strong> {{ $allocation->hall->hall_name ?? 'N/A' }}
                    </div>
                    <div class="col-md-3">
                        <strong>Date:</strong> {{ \Carbon\Carbon::parse($allocation->exam_date)->format('d M Y') }}
                    </div>

                </div>
            </div>

            <!-- Attendance Summary -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card bg-light">
                        <div class="card-body py-2">
                            <div class="row text-center">
                                <div class="col-4">
                                    <small class="text-muted">Present</small>
                                    <h5 class="mb-0 text-success" id="summaryPresent">0</h5>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted">Absent</small>
                                    <h5 class="mb-0 text-danger" id="summaryAbsent">0</h5>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted">Total</small>
                                    <h5 class="mb-0 text-primary">{{ $allocatedStudents->count() }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Actions -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" id="searchInput" class="form-control" placeholder="Search by name, roll no, table or seat...">
                        <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('teacher.halls.attendance-sheet', $allocation->id) }}" class="btn btn-success me-2">
                        <i class="bi bi-download me-2"></i>Download Attendance Sheet
                    </a>
                    <a href="{{ route('teacher.halls.show', $allocation->id) }}" class="btn btn-info">
                        <i class="bi bi-grid-3x3 me-2"></i>View Layout
                    </a>
                    <a href="{{ route('teacher.halls.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back
                    </a>
                </div>
            </div>

            <!-- Students Table -->
            @if($allocatedStudents->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle" id="studentsTable">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%">#</th>
                                <th width="12%">Roll No</th>
                                <th width="25%">Student Name</th>
                                <th width="8%">Code</th>
                                <th width="8%">Table</th>
                                <th width="8%">Seat</th>
                                <th width="15%">Attendance</th>
                                <th width="11%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allocatedStudents as $index => $item)
                                <tr id="student-row-{{ $item->student->id }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $item->student->roll_no ?? 'N/A' }}</strong></td>
                                    <td>{{ $item->student->name ?? 'N/A' }}</td>
                                    <td>{{ $allocation->exam->subject_code ?? 'N/A' }}</td>
                                    <td class="text-center"><span class="badge bg-primary">Table {{ $item->table_number }}</span></td>
                                    <td class="text-center"><span class="badge bg-secondary">Seat {{ $item->seat_number }}</span></td>
                                    <td>
                                        <select class="form-select form-select-sm attendance-select" 
                                                data-student-id="{{ $item->student->id }}"
                                                data-student-name="{{ $item->student->name }}"
                                                data-roll-no="{{ $item->student->roll_no }}"
                                                onchange="updateAttendance(this)">
                                            <option value="present" selected>Present</option>
                                            <option value="absent">Absent</option>
                                        </select>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info" onclick="viewStudentDetails({{ json_encode($item->student) }})" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-success" onclick="markPresent({{ $item->student->id }})" title="Mark Present">
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="markAbsent({{ $item->student->id }})" title="Mark Absent">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Save Attendance Button -->
                <div class="row mt-4">
                    <div class="col-12 text-center">
                        <button type="button" class="btn btn-lg btn-success px-5" onclick="saveAttendance()">
                            <i class="bi bi-check2-circle me-2"></i>Save Attendance
                        </button>
                        <!-- Add this near the other action buttons in the header -->
<button type="button" class="btn btn-warning me-2" onclick="debugAllocation()">
    <i class="bi bi-bug me-2"></i>Debug
</button>
                    </div>
                </div>
            @else
                <div class="alert alert-info text-center py-5">
                    <i class="bi bi-people fs-1 d-block mb-3"></i>
                    <h5>No Students Allocated</h5>
                    <p class="text-muted">There are no students allocated to this hall for this exam.</p>
                    <a href="{{ route('teacher.halls.show', $allocation->id) }}" class="btn btn-primary mt-3">
                        <i class="bi bi-grid-3x3 me-2"></i>View Hall Layout
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Student Details Modal -->
<div class="modal fade" id="studentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-person-badge me-2"></i>Student Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="studentModalBody">
                <!-- Content will be filled by JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container for Notifications -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100;"></div>
@endsection

@push('styles')
<style>
    .attendance-select {
        cursor: pointer;
    }
    .attendance-select option[value="present"] {
        background-color: #d4edda;
        color: #155724;
    }
    .attendance-select option[value="absent"] {
        background-color: #f8d7da;
        color: #721c24;
    }
    .attendance-select.present {
        background-color: #d4edda !important;
        border-color: #28a745 !important;
        color: #155724 !important;
    }
    .attendance-select.absent {
        background-color: #f8d7da !important;
        border-color: #dc3545 !important;
        color: #721c24 !important;
    }
    .table td {
        vertical-align: middle;
    }
    .btn-sm {
        padding: 0.25rem 0.5rem;
        margin: 0 2px;
    }
    .toast {
        min-width: 300px;
    }
</style>
@endpush

@push('scripts')
<script>
// Initialize attendance data
let attendanceData = {};
let originalAttendanceData = {};

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all students as present by default
    @foreach($allocatedStudents as $item)
        attendanceData[{{ $item->student->id }}] = 'present';
    @endforeach
    
    // Save original state for change detection
    originalAttendanceData = {...attendanceData};
    
    // Add search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        filterTable(this.value.toLowerCase());
    });
    
    // Update summary
    updateSummary();
    
    // Load existing attendance
    loadSavedAttendance();
});

function filterTable(searchText) {
    const rows = document.querySelectorAll('#studentsTable tbody tr');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchText) || searchText === '') {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Show/hide empty message
    const tbody = document.querySelector('#studentsTable tbody');
    const existingNoResults = document.getElementById('no-results');
    
    if (visibleCount === 0) {
        if (!existingNoResults) {
            const noResults = document.createElement('tr');
            noResults.id = 'no-results';
            noResults.innerHTML = '<td colspan="8" class="text-center py-4">No matching students found</td>';
            tbody.appendChild(noResults);
        }
    } else {
        if (existingNoResults) {
            existingNoResults.remove();
        }
    }
}

function clearSearch() {
    document.getElementById('searchInput').value = '';
    filterTable('');
}

function updateAttendance(select) {
    const studentId = select.getAttribute('data-student-id');
    const value = select.value;
    
    attendanceData[studentId] = value;
    
    // Update select class
    select.classList.remove('present', 'absent');
    select.classList.add(value);
    
    // Update summary
    updateSummary();
}

function markPresent(studentId) {
    const select = document.querySelector(`.attendance-select[data-student-id="${studentId}"]`);
    if (select) {
        select.value = 'present';
        select.classList.remove('absent');
        select.classList.add('present');
        attendanceData[studentId] = 'present';
        updateSummary();
    }
}

function markAbsent(studentId) {
    const select = document.querySelector(`.attendance-select[data-student-id="${studentId}"]`);
    if (select) {
        select.value = 'absent';
        select.classList.remove('present');
        select.classList.add('absent');
        attendanceData[studentId] = 'absent';
        updateSummary();
    }
}

function updateSummary() {
    let present = 0;
    let absent = 0;
    
    Object.values(attendanceData).forEach(status => {
        if (status === 'present') present++;
        else if (status === 'absent') absent++;
    });
    
    document.getElementById('summaryPresent').textContent = present;
    document.getElementById('summaryAbsent').textContent = absent;
}

function viewStudentDetails(student) {
    const modalBody = document.getElementById('studentModalBody');
    
    // Create student details HTML
    let detailsHtml = `
        <div class="text-center mb-3">
            <div class="bg-light rounded-circle d-inline-flex p-3">
                <i class="bi bi-person-circle fs-1"></i>
            </div>
            <h5 class="mt-2">${student.name || 'N/A'}</h5>
        </div>
        <table class="table table-bordered">
    `;
    
    // Add student details
    const fields = [
        ['Roll Number', student.roll_no],
        ['Class', student.class],
        ['Section', student.section],
        ['Email', student.email],
        ['Phone', student.phone]
    ];
    
    fields.forEach(([label, value]) => {
        if (value) {
            detailsHtml += `
                <tr>
                    <th width="40%">${label}</th>
                    <td>${value}</td>
                </tr>
            `;
        }
    });
    
    detailsHtml += '</table>';
    modalBody.innerHTML = detailsHtml;
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('studentModal'));
    modal.show();
}

function saveAttendance() {
    // Check if there are changes
    if (JSON.stringify(attendanceData) === JSON.stringify(originalAttendanceData)) {
        showToast('No changes to save', 'info');
        return;
    }
    
    // Count present and absent
    let present = 0;
    let absent = 0;
    
    Object.values(attendanceData).forEach(status => {
        if (status === 'present') present++;
        else if (status === 'absent') absent++;
    });
    
    // Prepare attendance data for saving
    const attendancePayload = [];
    Object.keys(attendanceData).forEach(studentId => {
        attendancePayload.push({
            student_id: parseInt(studentId),
            status: attendanceData[studentId]
        });
    });
    
    // Show confirmation with summary
    if (confirm(`Save attendance?\n\nPresent: ${present}\nAbsent: ${absent}\nTotal: ${present + absent}\n\nClick OK to confirm.`)) {
        // Show loading state
        const saveBtn = document.querySelector('.btn-success.px-5');
        const originalText = saveBtn.innerHTML;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
        saveBtn.disabled = true;
        
        // Send to server
        saveAttendanceToServer(attendancePayload)
            .then(response => {
                if (response.success) {
                    showNotification('success', response.message || 'Attendance saved successfully!');
                    // Update original data after successful save
                    originalAttendanceData = {...attendanceData};
                } else {
                    showNotification('error', response.error || 'Error saving attendance');
                }
            })
            .catch(error => {
    console.error('Error:', error);
    showNotification('error', error.message || 'Error saving attendance');
})
            .finally(() => {
                saveBtn.innerHTML = originalText;
                saveBtn.disabled = false;
            });
    }
}

function saveAttendanceToServer(attendanceData) {
    const url = '{{ route("teacher.halls.attendance.save", $allocation->id) }}';
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    return fetch(url, {
        method: 'POST',
        credentials: 'same-origin', // 👈 IMPORTANT for Laravel session
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            attendance: attendanceData
        })
    })
    .then(async response => {
        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            // Show real Laravel error
            throw new Error(data.error || data.message || 'Server error');
        }

        return data;
    });
}

function loadSavedAttendance() {
    const url = '{{ route("teacher.halls.attendance.get", $allocation->id) }}';
    
    fetch(url, {
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.attendance && data.attendance.length > 0) {
            data.attendance.forEach(item => {
                const select = document.querySelector(`.attendance-select[data-student-id="${item.student_id}"]`);
                if (select) {
                    select.value = item.status;
                    select.classList.remove('present', 'absent');
                    select.classList.add(item.status);
                    attendanceData[item.student_id] = item.status;
                }
            });
            // Update original data after loading
            originalAttendanceData = {...attendanceData};
            updateSummary();
            
            if (data.attendance.length > 0) {
                showToast('Loaded saved attendance data', 'info');
            }
        }
    })
    .catch(error => console.error('Error loading attendance:', error));
}

function showNotification(type, message) {
    // Create toast container if not exists
    let toastContainer = document.querySelector('.toast-container');
    
    // Create toast element
    const toastId = 'toast-' + Date.now();
    const toastHtml = `
        <div id="${toastId}" class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, { delay: 5000 });
    toast.show();
    
    // Remove toast after hidden
    toastElement.addEventListener('hidden.bs.toast', function() {
        this.remove();
    });
}

function showToast(message, type = 'info') {
    const toastContainer = document.querySelector('.toast-container');
    
    const bgColor = type === 'success' ? 'bg-success' : 
                    type === 'error' ? 'bg-danger' : 
                    type === 'warning' ? 'bg-warning' : 'bg-info';
    
    const icon = type === 'success' ? 'check-circle' : 
                 type === 'error' ? 'exclamation-triangle' : 
                 type === 'warning' ? 'exclamation-circle' : 'info-circle';
    
    const toastId = 'toast-' + Date.now();
    const toastHtml = `
        <div id="${toastId}" class="toast align-items-center text-white ${bgColor} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-${icon} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, { delay: 3000 });
    toast.show();
    
    toastElement.addEventListener('hidden.bs.toast', function() {
        this.remove();
    });
}
function debugAllocation() {
    const url = '{{ route("teacher.halls.debug", $allocation->id) }}';
    
    fetch(url, {
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Debug data:', data);
        
        // Create a formatted message
        let message = '=== DEBUG INFORMATION ===\n\n';
        message += `Allocation ID: ${data.allocation.id}\n`;
        message += `Teacher ID: ${data.allocation.teacher_id}\n`;
        message += `Total Allocated Students: ${data.counts.allocated}\n`;
        message += `Saved Attendance Records: ${data.counts.attendance_saved}\n\n`;
        
        message += '=== ALLOCATED STUDENTS ===\n';
        data.allocated_students.forEach(s => {
            message += `Student ID: ${s.student_id} - ${s.student_name} (Roll: ${s.student_roll_no}) - Table ${s.table_number}, Seat ${s.seat_number}\n`;
        });
        
        message += '\n=== SAVED ATTENDANCE ===\n';
        if (data.saved_attendance.length > 0) {
            data.saved_attendance.forEach(a => {
                message += `Student ID: ${a.student_id} - Status: ${a.status}\n`;
            });
        } else {
            message += 'No attendance records saved yet\n';
        }
        
        // Show in alert (you can also show in a modal)
        alert(message);
        
        // Also show which student IDs your frontend is using
        const frontendStudentIds = Object.keys(attendanceData).map(id => parseInt(id));
        alert('Frontend Student IDs: ' + frontendStudentIds.join(', '));
    })
    .catch(error => {
        console.error('Debug error:', error);
        alert('Error getting debug data: ' + error.message);
    });
}
</script>
@endpush