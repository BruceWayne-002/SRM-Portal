<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mark extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'exam_id',
        'student_id',
        'subject_id',
        'class_id',
        'internal_marks',
        'external_marks',
        'is_absent',
        'absent_status',
        'total_marks_obtained',
        'total_marks',
        'internal_percentage',
        'external_percentage',
        'percentage',
        'grade',
        'remarks',
        'status',
        'entered_by'
    ];

    protected $casts = [
        'internal_marks' => 'decimal:2',
        'external_marks' => 'decimal:2',
        'is_absent' => 'boolean',
        'total_marks_obtained' => 'decimal:2',
        'total_marks' => 'decimal:2',
        'internal_percentage' => 'decimal:2',
        'external_percentage' => 'decimal:2',
        'percentage' => 'decimal:2'
    ];

    protected $appends = [
        'formatted_internal',
        'formatted_external',
        'formatted_total',
        'formatted_percentage',
        'absent_display',
        'result_status',
        'formatted_result',
        'result_class',
        'marks_breakdown'
    ];

    // Relationships
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function enteredBy()
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    // Accessors
    public function getFormattedInternalAttribute()
    {
        if ($this->absent_status == 'internal' || $this->absent_status == 'both') {
            return 'ABSENT';
        }
        if ($this->internal_marks === null) {
            return 'Not Entered';
        }
        return $this->internal_marks . ' / ' . ($this->subject->internal_marks ?? 'N/A');
    }

    public function getFormattedExternalAttribute()
    {
        if ($this->absent_status == 'external' || $this->absent_status == 'both') {
            return 'ABSENT';
        }
        if ($this->external_marks === null) {
            return 'Not Entered';
        }
        return $this->external_marks . ' / ' . ($this->subject->external_marks ?? 'N/A');
    }

    public function getFormattedTotalAttribute()
    {
        if ($this->is_absent) {
            return 'ABSENT';
        }
        if ($this->total_marks_obtained === null) {
            return 'Not Entered';
        }
        return $this->total_marks_obtained . ' / ' . $this->total_marks;
    }

    public function getFormattedPercentageAttribute()
    {
        if ($this->is_absent) {
            return 'ABSENT';
        }
        if ($this->percentage === null) {
            return 'N/A';
        }
        return number_format($this->percentage, 2) . '%';
    }

    public function getAbsentDisplayAttribute()
    {
        if (!$this->is_absent) return null;
        
        return match($this->absent_status) {
            'internal' => 'Absent (Internal)',
            'external' => 'Absent (External)',
            'both' => 'Absent',
            default => 'Absent'
        };
    }

    public function getMarksBreakdownAttribute()
    {
        if ($this->is_absent) {
            return 'ABSENT';
        }
        if ($this->internal_marks === null || $this->external_marks === null) {
            return 'Incomplete';
        }
        return "I: {$this->internal_marks} ({$this->internal_percentage}%), E: {$this->external_marks} ({$this->external_percentage}%)";
    }

    public function getResultStatusAttribute()
    {
        if ($this->is_absent) {
            return 'absent';
        }
        
        if ($this->percentage === null) {
            return 'pending';
        }
        
        return $this->percentage >= 35 ? 'pass' : 'fail';
    }

    public function getFormattedResultAttribute()
    {
        return match($this->result_status) {
            'pass' => 'PASS',
            'fail' => 'FAIL',
            'absent' => 'ABSENT',
            'pending' => 'PENDING',
            default => 'PENDING'
        };
    }

    public function getResultClassAttribute()
    {
        return match($this->result_status) {
            'pass' => 'success',
            'fail' => 'danger',
            'absent' => 'secondary',
            'pending' => 'warning',
            default => 'secondary'
        };
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopePresent($query)
    {
        return $query->where('is_absent', false);
    }

    public function scopeAbsent($query)
    {
        return $query->where('is_absent', true);
    }

    public function scopeForExam($query, $examId)
    {
        return $query->where('exam_id', $examId);
    }

    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeForSubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function scopePassed($query)
    {
        return $query->where('percentage', '>=', 35)->where('is_absent', false);
    }

    public function scopeFailed($query)
    {
        return $query->where('percentage', '<', 35)->where('is_absent', false);
    }
}