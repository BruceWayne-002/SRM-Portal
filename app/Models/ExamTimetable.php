<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamTimetable extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'class_id',
        'subject_id',
        'exam_date',
        'start_time',
        'end_time',
        'duration',
        'school_code'
    ];

    protected $casts = [
        'exam_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function hallAllocations()
    {
        return $this->hasMany(HallAllocation::class, 'exam_timetable_id');
    }

    public function studentAllocations()
    {
        return $this->hasManyThrough(
            StudentHallAllocation::class,
            HallAllocation::class,
            'exam_timetable_id',
            'hall_allocation_id'
        );
    }
}