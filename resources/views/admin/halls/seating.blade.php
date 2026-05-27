@extends('layouts.app')

@section('title', 'Seating Arrangement - ' . $hall->hall_name)

@section('main')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-chair me-2"></i>Seating Arrangement: {{ $hall->hall_name }}
                        </h4>
                        <div>
                            <a href="{{ route('admin.halls.storedhalls') }}" class="btn btn-light btn-sm me-2">
                                <i class="fas fa-arrow-left me-1"></i> Back to Halls
                            </a>
                            <a href="{{ route('admin.halls.edit', $hall->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-1"></i> Edit Hall
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Hall Information Summary -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="text-muted mb-2">Hall Code</h6>
                                    <h5 class="mb-0">{{ $hall->hall_code }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="text-muted mb-2">Total Capacity</h6>
                                    <h5 class="mb-0">{{ $hall->capacity }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="text-muted mb-2">Seating Layout</h6>
                                    <h5 class="mb-0">{{ $hall->rows }} Rows × {{ $hall->columns }} Columns</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="text-muted mb-2">Total Seats</h6>
                                    <h5 class="mb-0">{{ $hall->rows * $hall->columns }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Exam Information with Assigned Exams -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-file-alt me-2"></i>Exam Details & Student Allocation
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong>Exam Date:</strong> 
                                            {{ \Carbon\Carbon::parse($hall->exam_date)->format('d M Y') }}
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Exam Time:</strong> 
                                            {{ \Carbon\Carbon::parse($hall->start_time)->format('h:i A') }} - 
                                            {{ \Carbon\Carbon::parse($hall->end_time)->format('h:i A') }}
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Exam Type:</strong> 
                                            <span class="badge bg-primary">{{ ucfirst($hall->exam_type) }}</span>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <strong>Invigilator:</strong> 
                                            {{ $hall->teacher->name ?? 'Not Assigned' }}
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Location:</strong> 
                                            {{ $hall->building }} - Floor {{ $hall->floor }}
                                        </div>
                                    </div>
                                    
                                    <!-- Assigned Exams List -->
                                    @php
                                        $examIds = json_decode($hall->exam_ids, true) ?? [];
                                        $assignedExams = \App\Models\Exam::whereIn('id', $examIds)
                                            ->with(['subject', 'class'])
                                            ->get();
                                        $totalStudents = $assignedExams->sum(function($exam) {
                                            return $exam->class->students_count ?? 0;
                                        });
                                    @endphp
                                    
                                    <div class="mt-3">
                                        <strong>Assigned Exams ({{ $assignedExams->count() }}):</strong>
                                        <div class="table-responsive mt-2">
                                            <table class="table table-sm table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Subject</th>
                                                        <th>Class</th>
                                                        <th>Students</th>
                                                        <th>Seat Allocation</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($assignedExams as $exam)
                                                    @php
                                                        $classStudents = $exam->class->students_count ?? 0;
                                                        $percentage = $totalStudents > 0 ? round(($classStudents / $totalStudents) * 100, 1) : 0;
                                                        $allocatedSeats = $totalStudents > 0 ? floor(($classStudents / $totalStudents) * ($hall->rows * $hall->columns)) : 0;
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $exam->subject->name ?? 'N/A' }}</td>
                                                        <td>{{ $exam->class->name ?? 'N/A' }}</td>
                                                        <td>
                                                            <span class="badge bg-secondary">{{ $classStudents }} students</span>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-info">{{ $allocatedSeats }} seats</span>
                                                            <small class="text-muted ms-2">({{ $percentage }}%)</small>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                    <tr class="table-primary">
                                                        <td colspan="2"><strong>Total</strong></td>
                                                        <td><strong>{{ $totalStudents }} students</strong></td>
                                                        <td><strong>{{ $hall->rows * $hall->columns }} seats</strong></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                        @if($totalStudents > ($hall->rows * $hall->columns))
                                            <div class="alert alert-warning mt-2">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                <strong>Warning:</strong> Total students ({{ $totalStudents }}) exceed available seats ({{ $hall->rows * $hall->columns }}).
                                                Please adjust the hall capacity or reassign exams.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seating Arrangement with Student Allocation -->
                    <div class="card">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-chair me-2"></i>Student Seating Allocation
                            </h5>
                            <div>
                                <span class="badge bg-success me-2">Available</span>
                                <span class="badge bg-primary me-2">Class A</span>
                                <span class="badge bg-warning me-2">Class B</span>
                                <span class="badge bg-info me-2">Class C</span>
                                <span class="badge bg-danger me-2">Class D</span>
                                <span class="badge bg-secondary">Teacher</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="seating-grid-container p-4 bg-light rounded">
                                <!-- Teacher's Desk -->
                                <div class="text-center mb-4">
                                    <div class="d-inline-block p-3 bg-secondary text-white rounded" style="width: 150px;">
                                        <i class="fas fa-chalkboard-teacher me-2"></i>Teacher's Desk
                                    </div>
                                    <div class="border-bottom w-50 mx-auto mt-2"></div>
                                </div>
                                
                                <!-- Seating Grid -->
                                <div class="seating-grid">
                                    @php
                                        $rows = $hall->rows;
                                        $columns = $hall->columns;
                                        $alphabet = range('A', 'Z');
                                        
                                        // Get assigned exams and their classes
                                        $examIds = json_decode($hall->exam_ids, true) ?? [];
                                        $assignedExams = \App\Models\Exam::whereIn('id', $examIds)
                                            ->with(['subject', 'class'])
                                            ->get();
                                        
                                        // Prepare class data for seat allocation
                                        $classes = [];
                                        $classColors = ['primary', 'warning', 'info', 'success', 'danger', 'dark'];
                                        $colorIndex = 0;
                                        
                                        foreach($assignedExams as $exam) {
                                            if($exam->class) {
                                                $className = $exam->class->name;
                                                $studentCount = $exam->class->students_count ?? 0;
                                                
                                                if(!isset($classes[$className])) {
                                                    $classes[$className] = [
                                                        'name' => $className,
                                                        'subject' => $exam->subject->name ?? 'N/A',
                                                        'students' => $studentCount,
                                                        'color' => $classColors[$colorIndex % count($classColors)],
                                                        'remaining' => $studentCount
                                                    ];
                                                    $colorIndex++;
                                                }
                                            }
                                        }
                                        
                                        // Calculate seats per class based on proportion
                                        $totalSeats = $rows * $columns;
                                        $totalStudents = array_sum(array_column($classes, 'students'));
                                        
                                        if($totalStudents > 0) {
                                            foreach($classes as &$class) {
                                                $class['allocated'] = $totalStudents > 0 
                                                    ? floor(($class['students'] / $totalStudents) * $totalSeats)
                                                    : 0;
                                                $class['remaining'] = $class['allocated'];
                                            }
                                        }
                                        
                                        // Distribute remaining seats
                                        $allocatedTotal = array_sum(array_column($classes, 'allocated'));
                                        $remainingSeats = $totalSeats - $allocatedTotal;
                                        
                                        if($remainingSeats > 0 && count($classes) > 0) {
                                            $classKeys = array_keys($classes);
                                            for($i = 0; $i < $remainingSeats; $i++) {
                                                $classIndex = $classKeys[$i % count($classKeys)];
                                                $classes[$classIndex]['allocated']++;
                                                $classes[$classIndex]['remaining']++;
                                            }
                                        }
                                    @endphp
                                    
                                    @for($row = 0; $row < $rows; $row++)
                                        <div class="row justify-content-center mb-2">
                                            <div class="col-auto d-flex align-items-center me-2">
                                                <span class="fw-bold text-primary" style="width: 45px;">
                                                    Row {{ $alphabet[$row] }}
                                                </span>
                                            </div>
                                            
                                            @for($col = 1; $col <= $columns; $col++)
                                                @php
                                                    // Assign seat to a class
                                                    $assignedClass = null;
                                                    $seatStatus = 'available';
                                                    $seatColor = 'success';
                                                    $seatLabel = $alphabet[$row] . $col;
                                                    
                                                    if(count($classes) > 0) {
                                                        // Find a class that still needs seats
                                                        foreach($classes as $className => &$classData) {
                                                            if($classData['remaining'] > 0) {
                                                                $assignedClass = $classData;
                                                                $classData['remaining']--;
                                                                $seatStatus = 'occupied';
                                                                $seatColor = $classData['color'];
                                                                break;
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                
                                                <div class="col-auto p-0">
                                                    <div class="seat" 
                                                         title="{{ $seatStatus == 'available' ? 'Available Seat' : $assignedClass['name'] . ' - ' . $assignedClass['subject'] }}"
                                                         data-row="{{ $alphabet[$row] }}"
                                                         data-column="{{ $col }}"
                                                         data-seat="{{ $seatLabel }}">
                                                        <div class="seat-inner bg-{{ $seatColor }} position-relative">
                                                            {{ $seatLabel }}
                                                            @if($seatStatus == 'occupied')
                                                                <span class="seat-badge">{{ substr($assignedClass['name'], 0, 2) }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endfor
                                        </div>
                                    @endfor
                                    
                                    <!-- Entrance -->
                                    <div class="text-center mt-4">
                                        <div class="d-inline-block bg-secondary text-white p-3 rounded" style="width: 180px;">
                                            <i class="fas fa-door-open me-2"></i>ENTRANCE / EXIT
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Class Allocation Summary -->
                    <div class="row mt-4">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="fas fa-users me-2"></i>Class-wise Seat Allocation Summary
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Class</th>
                                                    <th>Subject</th>
                                                    <th>Students</th>
                                                    <th>Seats Allocated</th>
                                                    <th>Utilization</th>
                                                    <th>Seat Range</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $startSeat = 1;
                                                @endphp
                                                @foreach($classes as $class)
                                                    @php
                                                        $seatRangeStart = $startSeat;
                                                        $seatRangeEnd = $startSeat + ($class['allocated'] ?? 0) - 1;
                                                        $utilization = $class['students'] > 0 ? round(($class['allocated'] / $class['students']) * 100, 1) : 0;
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            <span class="badge bg-{{ $class['color'] }} p-2">{{ $class['name'] }}</span>
                                                        </td>
                                                        <td>{{ $class['subject'] }}</td>
                                                        <td>{{ $class['students'] }}</td>
                                                        <td><strong>{{ $class['allocated'] ?? 0 }}</strong></td>
                                                        <td>
                                                            <div class="progress" style="height: 20px;">
                                                                <div class="progress-bar bg-{{ $class['color'] }}" 
                                                                     role="progressbar" 
                                                                     style="width: {{ min($utilization, 100) }}%;"
                                                                     aria-valuenow="{{ $utilization }}" 
                                                                     aria-valuemin="0" 
                                                                     aria-valuemax="100">
                                                                    {{ $utilization }}%
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            @if($class['allocated'] > 0)
                                                                <span class="badge bg-secondary">Seat {{ $seatRangeStart }} - {{ $seatRangeEnd }}</span>
                                                            @else
                                                                <span class="badge bg-secondary">No seats</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @php
                                                        $startSeat += ($class['allocated'] ?? 0);
                                                    @endphp
                                                @endforeach
                                                <tr class="table-primary">
                                                    <td colspan="3"><strong>Total</strong></td>
                                                    <td><strong>{{ $totalStudents }}</strong></td>
                                                    <td><strong>{{ $hall->rows * $hall->columns }} seats</strong></td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="fas fa-print me-2"></i>Actions
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <button onclick="window.print()" class="btn btn-primary">
                                            <i class="fas fa-print me-2"></i>Print Seating Plan
                                        </button>
                                        <a href="{{ route('admin.halls.edit', $hall->id) }}" class="btn btn-warning">
                                            <i class="fas fa-edit me-2"></i>Edit Hall Configuration
                                        </a>
                                        <button onclick="exportSeatingPlan()" class="btn btn-success">
                                            <i class="fas fa-download me-2"></i>Export as PDF
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Statistics Card -->
                            <div class="card mt-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="fas fa-chart-pie me-2"></i>Seat Statistics
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Total Seats:</span>
                                        <span class="fw-bold">{{ $hall->rows * $hall->columns }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Occupied Seats:</span>
                                        <span class="fw-bold text-success">{{ $totalStudents > ($hall->rows * $hall->columns) ? $hall->rows * $hall->columns : $totalStudents }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Available Seats:</span>
                                        <span class="fw-bold text-info">{{ max(0, ($hall->rows * $hall->columns) - $totalStudents) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Seat Utilization:</span>
                                        @php
                                            $utilizationPercent = $totalStudents > 0 ? round(($totalStudents / ($hall->rows * $hall->columns)) * 100, 1) : 0;
                                        @endphp
                                        <span class="fw-bold">{{ min($utilizationPercent, 100) }}%</span>
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
@endsection

@push('styles')
<style>
.seating-grid-container {
    overflow-x: auto;
    padding: 20px;
    background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
}

.seat {
    width: 55px;
    height: 55px;
    margin: 5px;
    cursor: pointer;
    transition: all 0.2s;
    position: relative;
}

.seat-inner {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: white;
    border-radius: 10px;
    font-size: 13px;
    font-weight: bold;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transition: all 0.3s;
    position: relative;
}

.seat-inner.bg-success {
    background: linear-gradient(135deg, #28a745, #20c997);
}

.seat-inner.bg-primary {
    background: linear-gradient(135deg, #007bff, #0056b3);
}

.seat-inner.bg-warning {
    background: linear-gradient(135deg, #ffc107, #ff9800);
}

.seat-inner.bg-info {
    background: linear-gradient(135deg, #17a2b8, #138496);
}

.seat-inner.bg-danger {
    background: linear-gradient(135deg, #dc3545, #bd2130);
}

.seat-inner.bg-secondary {
    background: linear-gradient(135deg, #6c757d, #545b62);
}

.seat-inner:hover {
    transform: scale(1.1);
    box-shadow: 0 8px 16px rgba(0,0,0,0.2);
    z-index: 10;
}

.seat-badge {
    position: absolute;
    bottom: 2px;
    right: 2px;
    background: rgba(255,255,255,0.9);
    color: #333;
    font-size: 9px;
    padding: 1px 4px;
    border-radius: 4px;
    font-weight: bold;
}

.seat-label {
    font-size: 11px;
    margin-top: 2px;
}

.progress {
    border-radius: 10px;
    background-color: #e9ecef;
}

.progress-bar {
    border-radius: 10px;
    transition: width 0.6s ease;
}

@media print {
    .btn, .btn-group, .card-header, nav, footer, .breadcrumb, .modal, .alert {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .seat-inner {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    
    .seating-grid-container {
        background: white !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
function exportSeatingPlan() {
    // Create a printable version
    const printWindow = window.open('', '_blank');
    printWindow.document.write('<html><head><title>Seating Plan - {{ $hall->hall_name }}</title>');
    printWindow.document.write('<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">');
    printWindow.document.write('<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">');
    printWindow.document.write('<style>@media print { .no-print { display: none !important; } .seat-inner { -webkit-print-color-adjust: exact; print-color-adjust: exact; } }</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write(document.querySelector('.seating-grid').outerHTML);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}

// Tooltip for seats
document.querySelectorAll('.seat').forEach(seat => {
    seat.addEventListener('mouseenter', function() {
        const title = this.getAttribute('title');
        if (title) {
            // You can implement a custom tooltip here
        }
    });
});
</script>
@endpush