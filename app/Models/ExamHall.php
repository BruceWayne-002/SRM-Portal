<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ExamHall extends Model
{
    use HasFactory;

    protected $fillable = [
        'hall_name',
        'hall_code',
        'capacity',
        'rows',
        'columns',
        'floor',
        'building',
        'teacher_id',
        'exam_ids',
        'exam_date',
        'start_time',
        'end_time',
        'exam_type',
        'school_code',
        'total_students',
        'allocated_students',
        'remaining_students',
        'allocation_status',
        'seat_assignments'
    ];

    protected $casts = [
        'exam_ids' => 'array',
        'seat_assignments' => 'array',
        'exam_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    // Get allocation percentage
    public function getAllocationPercentageAttribute()
    {
        if ($this->total_students > 0) {
            return round(($this->allocated_students / $this->total_students) * 100);
        }
        return 0;
    }

    // Get status color
    public function getStatusColorAttribute()
    {
        if ($this->allocation_status == 'complete') {
            return 'success';
        } elseif ($this->allocation_status == 'partial') {
            return 'warning';
        } else {
            return 'secondary';
        }
    }

    // Check if hall has available capacity
    public function hasAvailableCapacity()
    {
        return $this->capacity > ($this->allocated_students ?? 0);
    }

    // Get available seats count
    public function getAvailableSeatsCount()
    {
        return $this->capacity - ($this->allocated_students ?? 0);
    }

    // Relationships
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function seatAllocations()
    {
        return $this->hasMany(SeatAllocation::class);
    }

    public function exams()
    {
        return Exam::whereIn('id', $this->exam_ids ?? [])->get();
    }

    // Get exams with remaining students
    public function getExamsWithRemainingStudents()
    {
        $examIds = $this->exam_ids ?? [];
        return Exam::whereIn('id', $examIds)
            ->where('remaining_students', '>', 0)
            ->get();
    }

    // Generate seat layout
    public function generateSeatLayout($rows, $columns)
    {
        $layout = [];
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        
        for ($r = 0; $r < $rows; $r++) {
            $rowLabel = $r < 26 ? $alphabet[$r] : 'R' . ($r - 25);
            $layout[$rowLabel] = [];
            
            for ($c = 1; $c <= $columns; $c++) {
                $seatNumber = $rowLabel . $c;
                $layout[$rowLabel][$c] = [
                    'seat_number' => $seatNumber,
                    'status' => 'available',
                    'student_id' => null,
                    'exam_id' => null,
                    'allocated_at' => null,
                    'is_aisle' => ($c % 4 == 0) ? true : false,
                ];
            }
        }
        
        return $layout;
    }

    // Get available seats count from layout
    public function getAvailableSeatsFromLayout()
    {
        if (!$this->seat_layout) {
            return $this->capacity;
        }
        
        $count = 0;
        foreach ($this->seat_layout as $row => $seats) {
            foreach ($seats as $col => $seat) {
                if ($seat['status'] === 'available') {
                    $count++;
                }
            }
        }
        
        return $count;
    }

    // Get allocated seats count from layout
    public function getAllocatedSeatsFromLayout()
    {
        if (!$this->seat_layout) {
            return 0;
        }
        
        $count = 0;
        foreach ($this->seat_layout as $row => $seats) {
            foreach ($seats as $col => $seat) {
                if ($seat['status'] === 'allocated' || $seat['status'] === 'booked') {
                    $count++;
                }
            }
        }
        
        return $count;
    }

    // Auto allocate seats
    public function autoAllocateSeats($examId, $studentIds, $preferredSeats = [])
    {
        if (!$this->seat_layout) {
            $this->seat_layout = $this->generateSeatLayout($this->rows, $this->columns);
        }
        
        $allocations = [];
        $layout = $this->seat_layout;
        $allocatedCount = 0;
        
        // First allocate preferred seats
        foreach ($preferredSeats as $preferredSeat) {
            if ($allocatedCount >= count($studentIds)) break;
            
            foreach ($layout as $row => &$seats) {
                foreach ($seats as $col => &$seat) {
                    if ($seat['seat_number'] == $preferredSeat && $seat['status'] == 'available') {
                        $studentId = $studentIds[$allocatedCount];
                        $seat['status'] = 'allocated';
                        $seat['student_id'] = $studentId;
                        $seat['exam_id'] = $examId;
                        $seat['allocated_at'] = Carbon::now()->toDateTimeString();
                        
                        $allocations[] = [
                            'seat_number' => $seat['seat_number'],
                            'student_id' => $studentId,
                            'row' => $row,
                            'col' => $col
                        ];
                        
                        $allocatedCount++;
                        break 2;
                    }
                }
            }
        }
        
        // Then allocate remaining seats sequentially
        foreach ($layout as $row => &$seats) {
            foreach ($seats as $col => &$seat) {
                if ($allocatedCount >= count($studentIds)) break 2;
                
                if ($seat['status'] == 'available' && !$seat['is_aisle']) {
                    $studentId = $studentIds[$allocatedCount];
                    $seat['status'] = 'allocated';
                    $seat['student_id'] = $studentId;
                    $seat['exam_id'] = $examId;
                    $seat['allocated_at'] = Carbon::now()->toDateTimeString();
                    
                    $allocations[] = [
                        'seat_number' => $seat['seat_number'],
                        'student_id' => $studentId,
                        'row' => $row,
                        'col' => $col
                    ];
                    
                    $allocatedCount++;
                }
            }
        }
        
        $this->seat_layout = $layout;
        $this->available_seats = $this->getAvailableSeatsFromLayout();
        $this->save();
        
        return $allocations;
    }

    // Manual allocate single seat
    public function manualAllocateSeat($seatNumber, $studentId, $examId)
    {
        if (!$this->seat_layout) {
            $this->seat_layout = $this->generateSeatLayout($this->rows, $this->columns);
        }
        
        $layout = $this->seat_layout;
        $allocated = false;
        $seatData = null;
        
        foreach ($layout as $row => &$seats) {
            foreach ($seats as $col => &$seat) {
                if ($seat['seat_number'] == $seatNumber) {
                    if ($seat['status'] == 'available') {
                        $seat['status'] = 'allocated';
                        $seat['student_id'] = $studentId;
                        $seat['exam_id'] = $examId;
                        $seat['allocated_at'] = Carbon::now()->toDateTimeString();
                        
                        $allocated = true;
                        $seatData = [
                            'seat_number' => $seat['seat_number'],
                            'row' => $row,
                            'col' => $col
                        ];
                    }
                    break 2;
                }
            }
        }
        
        if ($allocated) {
            $this->seat_layout = $layout;
            $this->available_seats = $this->getAvailableSeatsFromLayout();
            $this->save();
        }
        
        return $allocated ? $seatData : false;
    }

    // Release seat
    public function releaseSeat($seatNumber)
    {
        if (!$this->seat_layout) return false;
        
        $layout = $this->seat_layout;
        $released = false;
        
        foreach ($layout as $row => &$seats) {
            foreach ($seats as $col => &$seat) {
                if ($seat['seat_number'] == $seatNumber) {
                    $seat['status'] = 'available';
                    $seat['student_id'] = null;
                    $seat['exam_id'] = null;
                    $seat['allocated_at'] = null;
                    $released = true;
                    break 2;
                }
            }
        }
        
        if ($released) {
            $this->seat_layout = $layout;
            $this->available_seats = $this->getAvailableSeatsFromLayout();
            $this->save();
        }
        
        return $released;
    }
}