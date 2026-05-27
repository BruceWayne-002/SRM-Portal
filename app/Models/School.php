<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    // Primary key settings
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';

    // Fillable fields
    protected $fillable = [
        'code',
        'name',
        'address',
        'email',
        'contact_number',
    ];

    // Relationships

    // Users (admins, teachers, students, parents)
    public function users()
    {
        return $this->hasMany(User::class, 'school_code', 'code');
    }

    // Teachers
    public function teachers()
    {
        return $this->hasMany(Teacher::class, 'school_code', 'code');
    }

    // Students
    public function students()
    {
        return $this->hasMany(Student::class, 'school_code', 'code');
    }

    // Student Attendances
    public function studentAttendances()
    {
        return $this->hasMany(StudentAttendance::class, 'school_code', 'code');
    }

    // Student Leaves
    public function studentLeaves()
    {
        return $this->hasMany(StudentLeave::class, 'school_code', 'code');
    }

    // Student Timetables
    public function studentTimetables()
    {
        return $this->hasMany(StudentTimetable::class, 'school_code', 'code');
    }

    // Teachers Attendance
    public function teacherAttendances()
    {
        return $this->hasMany(TeacherAttendance::class, 'school_code', 'code');
    }

    // Teacher Leaves
    public function teacherLeaves()
    {
        return $this->hasMany(TeacherLeave::class, 'school_code', 'code');
    }

    // Teacher Timetables
    public function teacherTimetables()
    {
        return $this->hasMany(TeacherTimetable::class, 'school_code', 'code');
    }

    // Timetables (general)
    public function timetables()
    {
        return $this->hasMany(Timetable::class, 'school_code', 'code');
    }

    // Fee Setups
    public function feeSetups()
    {
        return $this->hasMany(FeeSetup::class, 'school_code', 'code');
    }

    // Homeworks
    public function homeworks()
    {
        return $this->hasMany(Homework::class, 'school_code', 'code');
    }

    // Marks
    public function marks()
    {
        return $this->hasMany(Mark::class, 'school_code', 'code');
    }

    // Notices
    public function notices()
    {
        return $this->hasMany(Notice::class, 'school_code', 'code');
    }

    // Payments
    public function payments()
    {
        return $this->hasMany(Payment::class, 'school_code', 'code');
    }

    // Achievements
    public function achievements()
    {
        return $this->hasMany(Achievement::class, 'school_code', 'code');
    }

    // Books
    public function books()
    {
        return $this->hasMany(Book::class, 'school_code', 'code');
    }

    // You can add more relationships if you add more tables in the future
}
