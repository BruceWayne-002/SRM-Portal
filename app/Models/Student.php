<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $table = 'students';
    
    protected $fillable = [
        'name',
        'email', 
        'roll_no', 
        'class_id',
        'class_name',
        'section', 
        'emis_no', 
        'aadhar_no', 
        'dob', 
        'admission_date', 
        'gender',
        'medium', 
        'blood_group', 
        'parent_name', 
        'mother_name',
        'occupation', 
        'contact', 
        'alt_contact', 
        'address', 
        'image',
        'academic_year',
        'user_id',
        'current_semester',
        'current_year',
        'status'
    ];

    protected $casts = [
        'dob' => 'date',
        'admission_date' => 'date',
        'current_year' => 'integer',
        'current_semester' => 'integer'
    ];

    // Default values for new records
    protected $attributes = [
        'current_year' => 1,
        'current_semester' => 1,
        'status' => 'active'
    ];

    /**
     * Get the class that the student belongs to
     */
    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id', 'id');
    }

    /**
     * Get the user account associated with the student
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the subjects for this student based on class and section
     */
    public function subjects()
    {
        return Subject::where('class_id', $this->class_id)
            ->where('section', $this->section)
            ->where('academic_year', $this->academic_year)
            ->get();
    }

    /**
     * Get start year from academic_year
     */
    public function getStartYearAttribute()
    {
        if ($this->academic_year) {
            $parts = explode('-', $this->academic_year);
            return (int)trim($parts[0] ?? date('Y'));
        }
        return (int)date('Y');
    }

    /**
     * Get end year from academic_year
     */
    public function getEndYearAttribute()
    {
        if ($this->academic_year) {
            $parts = explode('-', $this->academic_year);
            return (int)trim($parts[1] ?? date('Y') + 1);
        }
        return (int)date('Y') + 1;
    }

    /**
     * Get formatted class and section
     */
    public function getFormattedClassAttribute()
    {
        $class = $this->class_name ?? 'N/A';
        $section = $this->section ?? 'N/A';
        return $class . ' - ' . $section;
    }

    /**
     * Get full academic info
     */
    public function getAcademicInfoAttribute()
    {
        return $this->formatted_class . ' | Year: ' . $this->formatted_current_year . ' | Sem: ' . $this->formatted_semester;
    }

    /**
     * Calculate current year of study based on June-June academic cycle
     */
    public function calculateCurrentYear()
    {
        if (!$this->start_year) {
            return 1;
        }

        $currentYear = (int)date('Y');
        $currentMonth = (int)date('n');
        $startYear = (int)$this->start_year;
        $endYear = (int)$this->end_year;
        
        // Total duration in years
        $totalYears = $endYear - $startYear;
        
        // If current year is before start year
        if ($currentYear < $startYear) {
            return 1; // Default to first year
        }
        
        // If current year is after end year
        if ($currentYear > $endYear) {
            return $totalYears;
        }
        
        // Calculate years passed based on June cutoff
        if ($currentMonth >= 6) {
            // June to December: New academic year starts
            return min(($currentYear - $startYear) + 1, $totalYears);
        } else {
            // January to May: Still in previous academic year
            return min($currentYear - $startYear, $totalYears);
        }
    }

    /**
     * Calculate current semester based on June-June academic cycle
     */
    public function calculateCurrentSemester()
    {
        if (!$this->start_year) {
            return 1;
        }

        $currentYear = (int)date('Y');
        $currentMonth = (int)date('n');
        $startYear = (int)$this->start_year;
        $endYear = (int)$this->end_year;
        
        $totalYears = $endYear - $startYear;
        $totalSemesters = $totalYears * 2;
        
        // Calculate years passed
        $yearsPassed = $currentYear - $startYear;
        
        // Determine semester based on month
        if ($currentMonth >= 6 && $currentMonth <= 11) {
            // June to November: First semester (Odd numbers: 1,3,5,7...)
            $semester = ($yearsPassed * 2) + 1;
        } else {
            // December to May: Second semester (Even numbers: 2,4,6,8...)
            if ($currentMonth < 6) {
                // January to May: Second semester of previous academic year
                $semester = ($yearsPassed * 2);
            } else {
                // December: Second semester of current academic year
                $semester = ($yearsPassed * 2) + 2;
            }
        }
        
        // Ensure semester is within valid range
        return min(max($semester, 1), $totalSemesters);
    }

    /**
     * Calculate status based on current date
     */
    public function calculateStatus()
    {
        if (!$this->start_year || !$this->end_year) {
            return 'active';
        }

        $currentYear = (int)date('Y');
        $currentMonth = (int)date('n');
        $startYear = (int)$this->start_year;
        $endYear = (int)$this->end_year;
        
        // If current year is before start year
        if ($currentYear < $startYear) {
            return 'inactive';
        }
        
        // If current year is after end year
        if ($currentYear > $endYear) {
            return 'alumni';
        }
        
        // If in final year and after June, mark as alumni
        if ($currentYear == $endYear && $currentMonth > 5) {
            return 'alumni';
        }
        
        return 'active';
    }

    /**
     * Update current academic status and save
     */
    public function updateCurrentAcademicStatus()
    {
        $this->current_year = $this->calculateCurrentYear();
        $this->current_semester = $this->calculateCurrentSemester();
        $this->status = $this->calculateStatus();
        
        return $this;
    }

    /**
     * Get formatted current year
     */
    public function getFormattedCurrentYearAttribute()
    {
        if (!$this->current_year) {
            return '1st Year';
        }
        
        $suffix = 'th';
        if ($this->current_year % 10 == 1 && $this->current_year % 100 != 11) {
            $suffix = 'st';
        } elseif ($this->current_year % 10 == 2 && $this->current_year % 100 != 12) {
            $suffix = 'nd';
        } elseif ($this->current_year % 10 == 3 && $this->current_year % 100 != 13) {
            $suffix = 'rd';
        }
        
        return $this->current_year . $suffix . ' Year';
    }

    /**
     * Get formatted semester
     */
    public function getFormattedSemesterAttribute()
    {
        if (!$this->current_semester) {
            return '1st Semester';
        }
        
        $suffix = 'th';
        if ($this->current_semester % 10 == 1 && $this->current_semester % 100 != 11) {
            $suffix = 'st';
        } elseif ($this->current_semester % 10 == 2 && $this->current_semester % 100 != 12) {
            $suffix = 'nd';
        } elseif ($this->current_semester % 10 == 3 && $this->current_semester % 100 != 13) {
            $suffix = 'rd';
        }
        
        return $this->current_semester . $suffix . ' Semester';
    }

    /**
     * Get current academic session (e.g., "2025-2026")
     */
    public function getCurrentAcademicSessionAttribute()
    {
        if (!$this->current_year || $this->current_year == 0) {
            return $this->academic_year;
        }
        
        $currentSessionStart = $this->start_year + $this->current_year - 1;
        $currentSessionEnd = $currentSessionStart + 1;
        
        return $currentSessionStart . '-' . $currentSessionEnd;
    }

    // Add these accessor methods to your Student model

/**
 * Get year suffix (st, nd, rd, th)
 */
public function getCurrentYearSuffixAttribute()
{
    $year = $this->current_year ?? 1;
    
    if ($year % 10 == 1 && $year % 100 != 11) {
        return 'st';
    } elseif ($year % 10 == 2 && $year % 100 != 12) {
        return 'nd';
    } elseif ($year % 10 == 3 && $year % 100 != 13) {
        return 'rd';
    }
    return 'th';
}

/**
 * Get semester suffix (st, nd, rd, th)
 */
public function getSemesterSuffixAttribute()
{
    $semester = $this->current_semester ?? 1;
    
    if ($semester % 10 == 1 && $semester % 100 != 11) {
        return 'st';
    } elseif ($semester % 10 == 2 && $semester % 100 != 12) {
        return 'nd';
    } elseif ($semester % 10 == 3 && $semester % 100 != 13) {
        return 'rd';
    }
    return 'th';
}

    public function hallAllocations()
    {
        return $this->hasMany(ExamHallAllocationStudent::class, 'student_id');
    }

    /**
     * Get the exam allocations through hall allocations.
     */
    public function examAllocations()
    {
        return $this->belongsToMany(ExamHallAllocation::class, 'exam_hall_allocation_students', 'student_id', 'allocation_id')
                    ->withPivot('table_number', 'seat_number')
                    ->withTimestamps();
    }
}