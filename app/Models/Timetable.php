<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    protected $fillable = [
        'class',
        'section',
        'day',
        'period',
        'subject',
        'teacher_id',
        'school_code', // <-- added for multi-school
    ];

    // Relationship to Teacher
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'employee_id');
    }

    // Relationship to School
    public function school()
    {
        return $this->belongsTo(School::class, 'school_code', 'code');
    }
}
