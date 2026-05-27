<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Teacher;

class StudentTimetable extends Model
{
    use HasFactory;

    protected $table = 'student_timetables';

    protected $fillable = [
        'class',
        'section',
        'group',
        'day',
        'period',
        'subject',
        'teacher_id',
        'school_code', // <-- multi-school
    ];

    // Optional: relation to teacher
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'id');
    }

    // Relation to School
    public function school()
    {
        return $this->belongsTo(School::class, 'school_code', 'code');
    }

    /**
     * Scope to filter timetables by school
     */
    public function scopeForSchool($query, $schoolCode)
    {
        return $query->where('school_code', $schoolCode);
    }
}
