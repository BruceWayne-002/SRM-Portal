<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Exam extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'subject_id',
        'exam_type',
        'exam_date',
        'exam_time',
        'time_session',
        'duration_minutes',
        'instructions',
        'hall_id',
        'allocated_students',
        'remaining_students',
        'allocation_status',
        'subject_name',        // ADD THIS for auto-fill
        'subject_code'          // ADD THIS for auto-fill
        // 'class_name' removed - not needed
    ];

    protected $casts = [
        'exam_date' => 'date',
        'exam_time' => 'string',
    ];

    protected $appends = [
        'formatted_duration',
        'formatted_exam_datetime',
        'subject_name',
        'subject_code',
        'student_count',
        'is_fully_allocated',
        'can_be_allocated'
    ];

    /**
     * Boot method to auto-fill subject details
     */
    protected static function boot()
    {
        parent::boot();

        // When creating a new exam
        static::creating(function ($exam) {
            if ($exam->subject_id) {
                $subject = Subject::find($exam->subject_id);
                if ($subject) {
                    $exam->subject_name = $subject->name;
                    $exam->subject_code = $subject->code;
                    // class_name removed - not storing
                }
            }
        });

        // When updating an exam
        static::updating(function ($exam) {
            if ($exam->isDirty('subject_id')) {
                $subject = Subject::find($exam->subject_id);
                if ($subject) {
                    $exam->subject_name = $subject->name;
                    $exam->subject_code = $subject->code;
                    // class_name removed - not storing
                }
            }
        });
    }

    /**
     * Check if exam can be allocated to a new hall
     */
    public function getCanBeAllocatedAttribute()
    {
        if (!$this->hall_id || $this->hall_id == 0) {
            return true;
        }
        
        if ($this->remaining_students > 0 && $this->allocation_status == 'partial') {
            return true;
        }
        
        return false;
    }

    /**
     * Check if exam is fully allocated
     */
    public function getIsFullyAllocatedAttribute()
    {
        return $this->remaining_students == 0;
    }

    /**
     * Scope for exams available for allocation
     */
    public function scopeAvailableForAllocation($query, $excludeHallId = null)
    {
        return $query->where(function($q) use ($excludeHallId) {
            $q->whereNull('hall_id')
              ->orWhere('hall_id', 0)
              ->orWhere(function($sub) use ($excludeHallId) {
                  $sub->whereNotNull('hall_id')
                      ->where('remaining_students', '>', 0)
                      ->where('allocation_status', 'partial');
                  
                  if ($excludeHallId) {
                      $sub->orWhere('hall_id', $excludeHallId);
                  }
              });
        });
    }

    /**
     * Get student count from class through subject
     */
    public function getStudentCountAttribute()
    {
        if ($this->subject && $this->subject->classModel) {
            return $this->subject->classModel->students()->count();
        }
        return 0;
    }

    public function hall()
    {
        return $this->belongsTo(ExamHall::class, 'hall_id');
    }

    public function getAllocationColorAttribute()
    {
        if ($this->allocation_status == 'complete') {
            return 'success';
        } elseif ($this->allocation_status == 'partial') {
            return 'warning';
        } else {
            return 'danger';
        }
    }

    /**
     * Check if exam is completely unassigned (not allocated to any hall)
     */
    public function scopeCompletelyUnassigned($query)
    {
        $assignedIds = self::whereNotNull('hall_id')
            ->where('hall_id', '!=', 0)
            ->where('hall_id', '!=', '')
            ->pluck('id')
            ->toArray();
        
        return $query->whereNotIn('id', $assignedIds)
            ->where(function($q) {
                $q->whereNull('hall_id')
                  ->orWhere('hall_id', 0)
                  ->orWhere('hall_id', '');
            })
            ->where(function($q) {
                $q->whereNull('allocated_students')
                  ->orWhere('allocated_students', 0);
            })
            ->where(function($q) {
                $q->whereNull('allocation_status')
                  ->orWhere('allocation_status', 'pending')
                  ->orWhere('allocation_status', '');
            });
    }

    /**
     * Scope to get only exams that are NOT assigned to ANY hall
     */
    public function scopeNotAssignedToAnyHall($query)
    {
        $assignedIds = self::whereNotNull('hall_id')
            ->where('hall_id', '!=', 0)
            ->where('hall_id', '!=', '')
            ->pluck('id')
            ->toArray();
        
        return $query->whereNotIn('id', $assignedIds)
            ->where(function($q) {
                $q->whereNull('hall_id')
                  ->orWhere('hall_id', 0)
                  ->orWhere('hall_id', '');
            });
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the exam timetables for the exam
     */
    public function examTimetables()
    {
        return $this->hasMany(ExamTimetable::class);
    }

    /**
     * Get the class through subject
     */
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    /**
     * Get the hall allocation for the exam
     */
    public function hallAllocation()
    {
        return $this->belongsTo(ExamHall::class, 'hall_allocation_id');
    }

    /**
     * Get students through exam timetables
     */
    public function students()
    {
        return $this->hasManyThrough(
            Student::class,
            ExamTimetable::class,
            'exam_id',
            'id',
            'id',
            'student_id'
        );
    }

    /**
     * Accessor for formatted duration
     */
    public function getFormattedDurationAttribute()
    {
        if (!$this->duration_minutes) {
            return 'Not specified';
        }
        
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;
        
        if ($hours > 0 && $minutes > 0) {
            return "{$hours} hr {$minutes} min";
        } elseif ($hours > 0) {
            return "{$hours} hr";
        } else {
            return "{$minutes} min";
        }
    }

    /**
     * Accessor for formatted exam datetime
     */
    public function getFormattedExamDatetimeAttribute()
    {
        if (!$this->exam_date || !$this->exam_time) {
            return 'Not scheduled';
        }
        
        return \Carbon\Carbon::parse($this->exam_date->format('Y-m-d') . ' ' . $this->exam_time)
            ->format('d M Y, h:i A');
    }

    /**
     * Accessor for subject name (from relationship if not stored)
     */
    public function getSubjectNameAttribute()
    {
        // First check if it's stored directly
        if (isset($this->attributes['subject_name']) && $this->attributes['subject_name']) {
            return $this->attributes['subject_name'];
        }
        // Fallback to relationship
        return $this->subject ? $this->subject->name : 'N/A';
    }

    /**
     * Accessor for subject code (from relationship if not stored)
     */
    public function getSubjectCodeAttribute()
    {
        // First check if it's stored directly
        if (isset($this->attributes['subject_code']) && $this->attributes['subject_code']) {
            return $this->attributes['subject_code'];
        }
        // Fallback to relationship
        return $this->subject ? $this->subject->code : 'N/A';
    }

    /**
     * Accessor for formatted time
     */
    public function getFormattedTimeAttribute()
    {
        return $this->exam_time ? \Carbon\Carbon::parse($this->exam_time)->format('h:i A') : 'N/A';
    }

    /**
     * Accessor for session full text
     */
    public function getSessionTextAttribute()
    {
        return $this->time_session == 'FN' ? 'Forenoon' : 'Afternoon';
    }

    /**
     * Scope for upcoming exams
     */
    public function scopeUpcoming($query)
    {
        return $query->where('exam_date', '>=', now()->format('Y-m-d'))
            ->orWhere(function($q) {
                $q->where('exam_date', now()->format('Y-m-d'))
                  ->where('exam_time', '>=', now()->format('H:i:s'));
            });
    }

    /**
     * Scope for past exams
     */
    public function scopePast($query)
    {
        return $query->where('exam_date', '<', now()->format('Y-m-d'))
            ->orWhere(function($q) {
                $q->where('exam_date', now()->format('Y-m-d'))
                  ->where('exam_time', '<', now()->format('H:i:s'));
            });
    }

    /**
     * Scope for today's exams
     */
    public function scopeToday($query)
    {
        return $query->where('exam_date', now()->format('Y-m-d'));
    }

    /**
     * Scope for this week's exams
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('exam_date', [
            now()->startOfWeek()->format('Y-m-d'),
            now()->endOfWeek()->format('Y-m-d')
        ]);
    }

    /**
     * Scope for FN session exams
     */
    public function scopeForenoon($query)
    {
        return $query->where('time_session', 'FN');
    }

    /**
     * Scope for AN session exams
     */
    public function scopeAfternoon($query)
    {
        return $query->where('time_session', 'AN');
    }

    /**
     * Scope for completed exams (past exams)
     */
    public function scopeCompleted($query)
    {
        return $query->where('exam_date', '<', now()->format('Y-m-d'))
            ->orWhere(function($q) {
                $q->where('exam_date', now()->format('Y-m-d'))
                  ->where('exam_time', '<', now()->format('H:i:s'));
            });
    }

    /**
     * Check if exam is today
     */
    public function isToday()
    {
        return $this->exam_date && $this->exam_date->isToday();
    }

    /**
     * Check if exam is upcoming
     */
    public function isUpcoming()
    {
        if (!$this->exam_date) {
            return false;
        }
        
        if ($this->exam_date->isFuture()) {
            return true;
        }
        
        return $this->exam_date->isToday() && 
               $this->exam_time && 
               \Carbon\Carbon::parse($this->exam_time)->isFuture();
    }

    /**
     * Check if exam can be allocated to a hall
     */
    public function canBeAllocated(): bool
    {
        $remaining = $this->remaining_students ?? $this->student_count ?? 0;
        
        if ($remaining <= 0) {
            return false;
        }
        
        if (!$this->hall_id || $this->hall_id == 0 || $this->hall_id == '') {
            return true;
        }
        
        if ($this->allocation_status == 'partial' && $remaining > 0) {
            return true;
        }
        
        return false;
    }

    /**
     * Scope for exams that can be allocated
     */
    public function scopeAllocatable($query)
    {
        return $query->where('exam_date', '>=', Carbon::today())
            ->where(function($q) {
                $q->whereNull('hall_id')
                  ->orWhere('hall_id', 0)
                  ->orWhere('hall_id', '')
                  ->orWhere(function($sub) {
                      $sub->whereNotNull('hall_id')
                          ->where('remaining_students', '>', 0)
                          ->where('allocation_status', 'partial');
                  });
            })
            ->where('remaining_students', '>', 0);
    }

    /**
     * Get marks for this exam
     */
    public function marks()
    {
        return $this->hasMany(Mark::class);
    }

    /**
     * Check if marks are entered for this exam
     */
    public function getMarksEnteredAttribute()
    {
        return $this->marks()->exists();
    }

    /**
     * Get marks entry status
     */
    public function getMarksStatusAttribute()
    {
        if (!$this->marks()->exists()) {
            return 'pending';
        }
        
        $totalStudents = $this->student_count;
        $enteredMarks = $this->marks()->count();
        
        if ($enteredMarks == 0) {
            return 'pending';
        } elseif ($enteredMarks < $totalStudents) {
            return 'partial';
        } else {
            return 'completed';
        }
    }

    /**
     * Get marks entry status color
     */
    public function getMarksStatusColorAttribute()
    {
        return match($this->marks_status) {
            'completed' => 'success',
            'partial' => 'warning',
            'pending' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get semester attribute from subject
     */
    public function getSemesterAttribute()
    {
        return $this->subject ? $this->subject->semester_display : 'N/A';
    }

    /**
     * Get semester number attribute
     */
    public function getSemesterNumberAttribute()
    {
        return $this->subject ? $this->subject->semester : null;
    }

    /**
     * Get exam date time attribute
     */
    public function getExamDateTimeAttribute()
    {
        if ($this->exam_date && $this->exam_time) {
            return Carbon::parse($this->exam_date->format('Y-m-d') . ' ' . $this->exam_time)->format('d M Y, h:i A');
        }
        return 'Not Scheduled';
    }

    /**
     * Get exam date formatted attribute
     */
    public function getExamDateFormattedAttribute()
    {
        return $this->exam_date ? Carbon::parse($this->exam_date)->format('d M Y') : 'N/A';
    }

    /**
     * Get exam time formatted attribute
     */
    public function getExamTimeFormattedAttribute()
    {
        return $this->exam_time ? Carbon::parse($this->exam_time)->format('h:i A') : 'N/A';
    }

    /**
     * Scope by semester
     */
    public function scopeBySemester($query, $semester)
    {
        return $query->whereHas('subject', function($q) use ($semester) {
            $q->where('semester', $semester);
        });
    }

    /**
     * Get class model attribute
     */
    public function getClassModelAttribute()
    {
        if ($this->subject && $this->subject->classModel) {
            return $this->subject->classModel;
        }
        return null;
    }

    public function allocations()
    {
        return $this->hasMany(ExamHallAllocation::class, 'exam_id');
    }

    /**
     * Get the halls through allocations.
     */
    public function halls()
    {
        return $this->belongsToMany(Hall::class, 'exam_hall_allocations', 'exam_id', 'hall_id')
                    ->withPivot('teacher_id', 'exam_date', 'session')
                    ->withTimestamps();
    }

    /**
     * Get the teachers through allocations.
     */
    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'exam_hall_allocations', 'exam_id', 'teacher_id')
                    ->withPivot('hall_id', 'exam_date', 'session')
                    ->withTimestamps();
    }

public function classes()
{
    return $this->hasMany(\App\Models\ExamClass::class);
}

}