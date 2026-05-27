<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamHallAllocationStudent extends Model
{
    use HasFactory;

    protected $table = 'exam_hall_allocation_students';

    protected $fillable = [
        'allocation_id',
        'student_id',
        'table_number',
        'seat_number',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Belongs to allocation
    public function allocation()
    {
        return $this->belongsTo(ExamHallAllocation::class, 'allocation_id');
    }

    // Belongs to student
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}