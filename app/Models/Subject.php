<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'total_marks',
        'passing_marks',
        'internal_marks',
        'external_marks',
        'class_id',
        'class_name',
        'section',
        'class_section_json',
        'duration_hours',
        'teacher_id',
        'semester',
        'school_code',
    ];

    protected $casts = [
        'total_marks' => 'integer',
        'passing_marks' => 'integer',
        'internal_marks' => 'integer',
        'external_marks' => 'integer',
        'duration_hours' => 'integer',
        'semester' => 'integer',
        'class_section_json' => 'array',
    ];

    protected $appends = [
        'marks_distribution',
        'passing_percentage',
        'full_class_info',
        'teacher_name',
        'semester_display',
        'academic_year_display'
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function timetables()
    {
        return $this->hasMany(ExamTimetable::class);
    }

    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function marks()
    {
        return $this->hasMany(Mark::class);
    }

    public function getMarksDistributionAttribute()
    {
        $internal = $this->internal_marks ?? 0;
        $external = $this->external_marks ?? 0;
        $total = $this->total_marks ?? ($internal + $external);

        return [
            'internal' => $internal,
            'external' => $external,
            'total' => $total,
            'internal_percent' => $total > 0 ? round(($internal / $total) * 100) : 0,
            'external_percent' => $total > 0 ? round(($external / $total) * 100) : 0,
        ];
    }

    public function getPassingPercentageAttribute()
    {
        if ($this->total_marks > 0) {
            return round(($this->passing_marks / $this->total_marks) * 100);
        }

        return 0;
    }

    public function getFullClassInfoAttribute()
    {
        $classSections = $this->class_section_json ?? [];

        if (!empty($classSections) && is_array($classSections)) {
            return collect($classSections)->map(function ($item) {
                return ($item['class_name'] ?? '') . ' - Section ' . ($item['section'] ?? '');
            })->implode(', ');
        }

        $info = [];

        if ($this->class_name) {
            $info[] = $this->class_name;
        }

        if ($this->section) {
            $info[] = 'Section ' . $this->section;
        }

        return !empty($info) ? implode(' ', $info) : 'Not Assigned';
    }

    public function getTeacherNameAttribute()
    {
        return $this->teacher ? $this->teacher->name : 'Not Assigned';
    }

    public function getSemesterDisplayAttribute()
    {
        if ($this->semester) {
            $semesterNum = $this->semester;
            $suffix = 'th';

            if ($semesterNum == 1) $suffix = 'st';
            elseif ($semesterNum == 2) $suffix = 'nd';
            elseif ($semesterNum == 3) $suffix = 'rd';

            return $semesterNum . $suffix . ' Semester';
        }

        return 'Not Specified';
    }

    public function getAcademicYearDisplayAttribute()
    {
        if ($this->classModel && $this->classModel->academic_year) {
            return $this->classModel->academic_year;
        }

        return 'Not Specified';
    }

    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeBySemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    public function scopeCurrentAcademicYear($query)
    {
        return $query->whereHas('classModel', function ($q) {
            $q->where('academic_year', date('Y'));
        });
    }
}