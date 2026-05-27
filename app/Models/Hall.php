<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hall extends Model
{
    use HasFactory;

    protected $fillable = [
        'hall_name',
        'building',
        'floor',
        'capacity',
        'students_per_table',
        'rows',
        'columns',
    ];

    public function setHallNameAttribute($value)
    {
        $this->attributes['hall_name'] = strtoupper($value);
    }
    public function allocations()
    {
        return $this->hasMany(ExamHallAllocation::class, 'hall_id');
    }

    /**
     * Get the exam allocations through the allocations.
     */
    public function examAllocations()
    {
        return $this->hasMany(ExamHallAllocation::class, 'hall_id');
    }

    /**
     * Get the exams through allocations.
     */
    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'exam_hall_allocations', 'hall_id', 'exam_id')
                    ->withPivot('teacher_id', 'exam_date', 'session')
                    ->withTimestamps();
    }

    /**
     * Get the teachers through allocations.
     */
    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'exam_hall_allocations', 'hall_id', 'teacher_id')
                    ->withPivot('exam_id', 'exam_date', 'session')
                    ->withTimestamps();
    }

    /**
     * Get the current active allocations.
     */
    public function currentAllocations()
    {
        return $this->allocations()->where('exam_date', '>=', now()->format('Y-m-d'));
    }


}