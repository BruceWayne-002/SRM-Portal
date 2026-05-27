<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeatAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_hall_id',
        'exam_id',
        'student_id',
        'seat_number',
        'row',
        'column',
        'allocation_type',
        'allocated_by',
        'allocated_at',
        'status',
    ];

    protected $casts = [
        'allocated_at' => 'datetime',
        'is_allocated' => 'boolean'

    ];

    public function examHall()
    {
        return $this->belongsTo(ExamHall::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function allocatedBy()
    {
        return $this->belongsTo(User::class, 'allocated_by');
    }
}