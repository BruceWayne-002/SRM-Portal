<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamHallAllocation extends Model
{
    use HasFactory;

    protected $table = 'exam_hall_allocations';

    protected $fillable = [
        'exam_id',
        'hall_id',
        'teacher_id',
        'exam_date',
        'time_session',

    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // One allocation belongs to one exam
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    // One allocation belongs to one hall
    public function hall()
    {
        return $this->belongsTo(Hall::class);
    }

    // One allocation belongs to one teacher
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    // One allocation has many students
    public function students()
    {
        return $this->hasMany(ExamHallAllocationStudent::class, 'allocation_id');
    }
}