<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'employee_id',
        'department',
        'designation',
        'dob',
        'gender',
        'email',
        'phone',
        'address',
        'qualification',
        'parent_name',
        'joining_date',
        'status',
        'aadhar_no',
        'blood_group',
        'photo',
        'user_id', // Added user_id field
    ];

    protected $casts = [
        'dob' => 'date',
        'joining_date' => 'date',
    ];

    // Relation to User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // Optional: teacher attendances
    public function attendances()
    {
        return $this->hasMany(TeacherAttendance::class, 'teacher_id', 'id');
    }

    // Optional: teacher leaves
    public function leaves()
    {
        return $this->hasMany(TeacherLeave::class, 'teacher_id', 'id');
    }

    // Optional: teacher timetables
    public function timetables()
    {
        return $this->hasMany(TeacherTimetable::class, 'teacher_id', 'id');
    }

    // Subjects taught by this teacher
    public function subjects()
    {
        return $this->hasMany(Subject::class, 'teacher_id', 'id');
    }

    public function allocations()
    {
        return $this->hasMany(ExamHallAllocation::class, 'teacher_id');
    }

    /**
     * Get the exams through allocations.
     */
    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'exam_hall_allocations', 'teacher_id', 'exam_id')
                    ->withPivot('hall_id', 'exam_date', 'session')
                    ->withTimestamps();
    }

    /**
     * Get the halls through allocations.
     */
    public function halls()
    {
        return $this->belongsToMany(Hall::class, 'exam_hall_allocations', 'teacher_id', 'hall_id')
                    ->withPivot('exam_id', 'exam_date', 'session')
                    ->withTimestamps();
    }
}