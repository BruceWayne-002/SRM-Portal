<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentHallAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'hall_allocation_id',
        'seat_number',
        'seat_position',
        'hall_ticket_number',
        'qr_code_path'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function hallAllocation()
    {
        return $this->belongsTo(HallAllocation::class);
    }

    public function examTimetable()
    {
        return $this->hasOneThrough(ExamTimetable::class, HallAllocation::class);
    }

    public function examHall()
    {
        return $this->hasOneThrough(ExamHall::class, HallAllocation::class);
    }
}