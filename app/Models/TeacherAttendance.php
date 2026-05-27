<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'date',
        'morning',
        'afternoon',
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
