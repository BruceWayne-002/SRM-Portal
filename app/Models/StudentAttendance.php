<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Student;

class StudentAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_roll_no',
        'date',
        'shift',
        'status',
        'morning',
        'afternoon',
        'school_code', // for multi-school
    ];

    // Boot method to auto-set school_code and apply global scope
    protected static function boot()
    {
        parent::boot();

        // Always set school_code when creating
        static::creating(function ($attendance) {
            if (Auth::check() && empty($attendance->school_code)) {
                $attendance->school_code = Auth::user()->school_code;
            }
        });

        // Global Scope → auto-filter by school_code
        static::addGlobalScope('school', function (Builder $builder) {
            if (Auth::check()) {
                $builder->where('school_code', Auth::user()->school_code);
            }
        });
    }

    // Relation to Student
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_roll_no', 'roll_no');
    }

    // Relation to School
    public function school()
    {
        return $this->belongsTo(School::class, 'school_code', 'code');
    }
}
