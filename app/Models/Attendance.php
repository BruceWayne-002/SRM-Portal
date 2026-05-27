<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendances';

    protected $fillable = [
        'teacher_id',
        'student_id',
        'allocation_id',
        'exam_id',
        'hall_id',
        'exam_date',
        'status',
        'remarks',
        'marked_by',
        'marked_at'
    ];

    protected $casts = [
        'exam_date' => 'date',
        'marked_at' => 'datetime'
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function allocation()
    {
        return $this->belongsTo(ExamHallAllocation::class, 'allocation_id');
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function hall()
    {
        return $this->belongsTo(Hall::class);
    }

    public function marker()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }
}