@extends('layouts.app')

@section('title', 'Hall Allocation - ' . $allocation->examHall->hall_name)

@section('main')
<div class="container-fluid px-4">
    <div class="card">
        <div class="card-header bg-info text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-chalkboard-teacher me-2"></i>
                    Hall Allocation - {{ $allocation->examHall->hall_name }}
                </h4>
                <div class="btn-group">
                    <a href="{{ route('admin.allocations.exam', $allocation->examTimetable->exam) }}" 
                       class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                    <button class="btn btn-light btn-sm" onclick="window.print()">
                        <i class="fas fa-print me-1"></i> Print
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Exam Information -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-calendar-alt me-2"></i>Exam Details
                            </h5>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th width="40%">Exam:</th>
                                    <td>{{ $allocation->examTimetable->exam->name }}</td>
                                </tr>
                                <tr>
                                    <th>Date:</th>
                                    <td>{{ $allocation->examTimetable->exam_date->format('l, d F Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Time:</th>
                                    <td>
                                        {{ $allocation->examTimetable->start_time->format('h:i A') }} - 
                                        {{ $allocation->examTimetable->end_time->format('h:i A') }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Subject:</th>
                                    <td>
                                        {{ $allocation->examTimetable->subject->name }}
                                        ({{ $allocation->examTimetable->subject->code }})
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-chalkboard me-2"></i>Hall Details
                            </h5>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th width="40%">Hall:</th>
                                    <td>{{ $allocation->examHall->hall_name }}</td>
                                </tr>
                                <tr>
                                    <th>Code:</th>
                                    <td>{{ $allocation->examHall->hall_code }}</td>
                                </tr>
                                <tr>
                                    <th>Location:</th>
                                    <td>{{ $allocation->examHall->building }} - {{ $allocation->examHall->floor }}</td>
                                </tr>
                                <tr>
                                    <th>Invigilator:</th>
                                    <td>
                                        {{ $allocation->teacher->name }}
                                        <span class="badge bg-secondary ms-2">{{ $allocation->teacher_role }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seating Arrangement -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chair me-2"></i>Seating Arrangement
                        <small class="opacity-75 ms-2">
                            {{ $seatingArrangement->count() }} / {{ $hall->capacity }} seats occupied
                        </small>
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Seating Grid -->
                    <div class="text-center mb-4">
                        <div class="d-inline-block bg-light p-2 rounded mb-3">
                            <i class="fas fa-door-open fa-lg text-primary"></i>
                            <div class="small mt-1">ENTRANCE</div>
                        </div>
                    </div>

                    <div class="seating-grid mx-auto" style="max-width: 900px;">
                        @php
                            $alphabet = range('A', 'Z');
                            $occupiedSeats = [];
                            foreach($seatingArrangement as $seat) {
                                $occupiedSeats[$seat['seat_position']] = $seat;
                            }
                        @endphp
                        
                        @for($row = 0; $row < $hall->rows; $row++)
                            <div class="row mb-2 justify-content-center">
                                <!-- Row Label -->
                                <div class="col-auto d-flex align-items-center me-2">
                                    <span class="badge bg-secondary">Row {{ $alphabet[$row] }}</span>
                                </div>
                                
                                <!-- Seats -->
                                @for($col = 1; $col <= $hall->columns; $col++)
                                    @php
                                        $seatPosition = $alphabet[$row] . $col;
                                        $isOccupied = isset($occupiedSeats[$seatPosition]);
                                        $seatInfo = $isOccupied ? $occupiedSeats[$seatPosition] : null;
                                    @endphp
                                    <div class="col-auto p-1">
                                        <div class="seat position-relative" 
                                             data-seat="{{ $seatPosition }}"
                                             data-bs-toggle="tooltip" 
                                             data-bs-placement="top" 
                                             title="{{ $isOccupied ? $seatInfo['student']->name : 'Empty Seat' }}">
                                            <div class="border rounded text-center seat-box {{ $isOccupied ? 'bg-success text-white' : 'bg-light' }}">
                                                <div class="seat-label">{{ $seatPosition }}</div>
                                                @if($isOccupied)
                                                    <div class="seat-number small opacity-75">
                                                        {{ $seatInfo['student']->roll_no ?? 'N/A' }}
                                                    </div>
                                                @else
                                                    <div class="seat-number small text-muted">Empty</div>
                                                @endif
                                            </div>
                                            @if($isOccupied)
                                                <div class="position-absolute top-0 start-100 translate-middle">
                                                    <span class="badge bg-danger rounded-pill">
                                                        {{ $seatInfo['seat_number'] }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endfor
                            </div>
                            
                            <!-- Spacing between rows -->
                            @if($row < $hall->rows - 1)
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <hr class="my-1">
                                    </div>
                                </div>
                            @endif
                        @endfor
                    </div>

                    <!-- Legend -->
                    <div class="row mt-4 justify-content-center">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-key me-2"></i>Seating Legend</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="seat-box border rounded bg-success me-2" 
                                                     style="width: 40px; height: 40px;"></div>
                                                <div>
                                                    <div>Occupied Seat</div>
                                                    <small class="text-muted">Student assigned</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="seat-box border rounded bg-light me-2" 
                                                     style="width: 40px; height: 40px;"></div>
                                                <div>
                                                    <div>Empty Seat</div>
                                                    <small class="text-muted">Available</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="position-relative me-2">
                                                    <div class="seat-box border rounded bg-success" 
                                                         style="width: 40px; height: 40px;"></div>
                                                    <span class="position-absolute top-0 start-100 translate-middle badge bg-danger rounded-pill" 
                                                          style="font-size: 8px; padding: 2px 4px;">
                                                        5
                                                    </span>
                                                </div>
                                                <div>
                                                    <div>Seat Number</div>
                                                    <small class="text-muted">Allocation order</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student List -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>Student List
                        <small class="opacity-75 ms-2">
                            {{ $seatingArrangement->count() }} students allocated
                        </small>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">#</th>
                                    <th>Seat</th>
                                    <th>Roll No</th>
                                    <th>Student Name</th>
                                    <th>Class</th>
                                    <th>Section</th>
                                    <th>Hall Ticket</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($seatingArrangement->sortBy('seat_number') as $seat)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $seat['seat_position'] }}</span>
                                        <br>
                                        <small class="text-muted">Seat #{{ $seat['seat_number'] }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $seat['student']->roll_no }}</strong>
                                    </td>
                                    <td>
                                        <strong>{{ $seat['student']->name }}</strong>
                                    </td>
                                    <td>{{ $seat['student']->class }}</td>
                                    <td>{{ $seat['student']->section }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $seat['ticket_number'] }}</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-info" 
                                                onclick="viewStudent({{ $seat['student']->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning" 
                                                onclick="changeSeat({{ $seat['student']->id }}, '{{ $seat['seat_position'] }}')">
                                            <i class="fas fa-exchange-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($seatingArrangement->count() == 0)
                        <div class="text-center py-4">
                            <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                            <h5>No students allocated yet</h5>
                            <p class="text-muted">Allocate students to this hall from the allocations page.</p>
                            <a href="{{ route('admin.allocations.exam', $allocation->examTimetable->exam) }}" 
                               class="btn btn-primary">
                                <i class="fas fa-arrow-left me-1"></i> Back to Allocations
                            </a>
                        </div>
                    @endif
                </div>
                @if($seatingArrangement->count() > 0)
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <button class="btn btn-outline-success" onclick="printSeatingChart()">
                                    <i class="fas fa-print me-1"></i> Print Seating Chart
                                </button>
                                <button class="btn btn-outline-primary ms-2" onclick="exportStudentList()">
                                    <i class="fas fa-download me-1"></i> Export List
                                </button>
                            </div>
                            <div>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Total: {{ $seatingArrangement->count() }} students | 
                                    Available: {{ $hall->capacity - $seatingArrangement->count() }} seats
                                </small>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Student View Modal -->
<div class="modal fade" id="studentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user-graduate me-2"></i>Student Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="studentModalBody">
                <!-- Content loaded via JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Change Seat Modal -->
<div class="modal fade" id="changeSeatModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exchange-alt me-2"></i>Change Seat
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="changeSeatModalBody">
                <!-- Content loaded via JavaScript -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .seating-grid {
        position: relative;
    }
    
    .seat-box {
        width: 70px;
        height: 70px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        transition: all 0.2s;
        cursor: pointer;
    }
    
    .seat-box:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .seat-label {
        font-weight: bold;
        font-size: 14px;
    }
    
    .seat-number {
        font-size: 11px;
    }
    
    .bg-success.text-white .seat-number {
        opacity: 0.8;
    }
    
    @media print {
        .card-header, .card-footer, .btn, .modal {
            display: none !important;
        }
        
        .card {
            border: 1px solid #000 !important;
            break-inside: avoid;
        }
        
        .seating-grid {
            max-width: 100% !important;
        }
        
        .seat-box {
            border: 1px solid #000 !important;
            background-color: #fff !important;
            color: #000 !important;
        }
        
        .bg-success.text-white {
            background-color: #fff !important;
            color: #000 !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    
    // View student details
    function viewStudent(studentId) {
        fetch(`/admin/students/${studentId}`)
            .then(response => response.json())
            .then(data => {
                const modalBody = document.getElementById('studentModalBody');
                modalBody.innerHTML = `
                    <div class="text-center mb-3">
                        <i class="fas fa-user-graduate fa-3x text-primary"></i>
                    </div>
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Name:</th>
                            <td>${data.name}</td>
                        </tr>
                        <tr>
                            <th>Roll No:</th>
                            <td>${data.roll_no}</td>
                        </tr>
                        <tr>
                            <th>Class:</th>
                            <td>${data.class} - ${data.section}</td>
                        </tr>
                        <tr>
                            <th>Hall Ticket:</th>
                            <td>
                                <span class="badge bg-info">${data.hall_ticket || 'Not Generated'}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Contact:</th>
                            <td>${data.contact || 'N/A'}</td>
                        </tr>
                    </table>
                `;
                
                const modal = new bootstrap.Modal(document.getElementById('studentModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading student details');
            });
    }
    
    // Change seat
    function changeSeat(studentId, currentSeat) {
        const modalBody = document.getElementById('changeSeatModalBody');
        
        // Get available seats
        const availableSeats = [];
        const occupiedSeats = @json($seatingArrangement->pluck('seat_position')->toArray());
        const hallRows = {{ $hall->rows }};
        const hallColumns = {{ $hall->columns }};
        const alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        
        for (let row = 0; row < hallRows; row++) {
            for (let col = 1; col <= hallColumns; col++) {
                const seatPosition = alphabet[row] + col;
                if (!occupiedSeats.includes(seatPosition) || seatPosition === currentSeat) {
                    availableSeats.push(seatPosition);
                }
            }
        }
        
        let seatsHTML = '';
        availableSeats.forEach(seat => {
            const isCurrent = seat === currentSeat;
            seatsHTML += `
                <div class="col-2 mb-2">
                    <button class="btn btn-sm w-100 ${isCurrent ? 'btn-warning' : 'btn-outline-secondary'}"
                            onclick="selectSeat('${seat}', ${studentId}, '${currentSeat}')"
                            ${isCurrent ? 'disabled' : ''}>
                        ${seat}
                    </button>
                </div>
            `;
        });
        
        modalBody.innerHTML = `
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Current seat: <strong>${currentSeat}</strong>
            </div>
            <div class="mb-3">
                <label class="form-label">Select new seat:</label>
                <div class="row" id="availableSeats">
                    ${seatsHTML}
                </div>
            </div>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Changing seat will update the student's hall ticket.
            </div>
        `;
        
        const modal = new bootstrap.Modal(document.getElementById('changeSeatModal'));
        modal.show();
    }
    
    // Select seat for change
    function selectSeat(newSeat, studentId, currentSeat) {
        if (confirm(`Change seat from ${currentSeat} to ${newSeat}?`)) {
            fetch(`/admin/allocations/{{ $allocation->id }}/change-seat`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    student_id: studentId,
                    current_seat: currentSeat,
                    new_seat: newSeat
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert(data.message || 'Error changing seat');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error changing seat');
            });
        }
    }
    
    // Print seating chart
    function printSeatingChart() {
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Seating Chart - {{ $allocation->examHall->hall_name }}</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    .header { text-align: center; margin-bottom: 30px; }
                    .hall-info { margin-bottom: 20px; }
                    .seating-grid { display: grid; grid-template-columns: repeat({{ $hall->columns }}, 1fr); gap: 10px; margin-bottom: 30px; }
                    .seat { border: 1px solid #000; padding: 10px; text-align: center; min-height: 60px; display: flex; flex-direction: column; justify-content: center; }
                    .occupied { background-color: #d4edda; }
                    .empty { background-color: #f8f9fa; }
                    .seat-label { font-weight: bold; }
                    .student-info { font-size: 10px; }
                    @page { size: landscape; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h2>Seating Chart - {{ $allocation->examHall->hall_name }}</h2>
                    <h4>{{ $allocation->examTimetable->exam->name }}</h4>
                    <p>{{ $allocation->examTimetable->exam_date->format('l, d F Y') }} | {{ $allocation->examTimetable->start_time->format('h:i A') }} - {{ $allocation->examTimetable->end_time->format('h:i A') }}</p>
                </div>
                
                <div class="hall-info">
                    <p><strong>Hall:</strong> {{ $allocation->examHall->hall_name }} ({{ $allocation->examHall->hall_code }})</p>
                    <p><strong>Location:</strong> {{ $allocation->examHall->building }} - {{ $allocation->examHall->floor }}</p>
                    <p><strong>Invigilator:</strong> {{ $allocation->teacher->name }} ({{ $allocation->teacher_role }})</p>
                    <p><strong>Students:</strong> {{ $seatingArrangement->count() }} / {{ $hall->capacity }}</p>
                </div>
                
                <div class="seating-grid">
                    @php
                        $alphabet = range('A', 'Z');
                        $occupiedSeats = [];
                        foreach($seatingArrangement as $seat) {
                            $occupiedSeats[$seat['seat_position']] = $seat;
                        }
                    @endphp
                    
                    @for($row = 0; $row < $hall->rows; $row++)
                        @for($col = 1; $col <= $hall->columns; $col++)
                            @php
                                $seatPosition = $alphabet[$row] . $col;
                                $isOccupied = isset($occupiedSeats[$seatPosition]);
                                $seatInfo = $isOccupied ? $occupiedSeats[$seatPosition] : null;
                            @endphp
                            <div class="seat {{ $isOccupied ? 'occupied' : 'empty' }}">
                                <div class="seat-label">{{ $seatPosition }}</div>
                                @if($isOccupied)
                                    <div class="student-info">
                                        {{ $seatInfo['student']->roll_no }}<br>
                                        {{ $seatInfo['student']->name }}
                                    </div>
                                @else
                                    <div class="student-info">Empty</div>
                                @endif
                            </div>
                        @endfor
                    @endfor
                </div>
                
                <div style="margin-top: 30px; font-size: 12px;">
                    <p><strong>Legend:</strong> Green = Occupied | White = Empty</p>
                    <p>Generated on: {{ now()->format('d/m/Y H:i') }}</p>
                </div>
            </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    }
    
    // Export student list
    function exportStudentList() {
        window.location.href = `/admin/allocations/{{ $allocation->id }}/export-students`;
    }
</script>
@endpush