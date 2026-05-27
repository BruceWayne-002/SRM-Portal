<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'classes';
    
    protected $fillable = [
        'name',
        'code',
        'academic_year',
        'description',
        'section_name',
        'section_code',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'academic_year' => 'string',
    ];

    protected $appends = ['full_name', 'display_name'];

    // ========== RELATIONSHIPS ==========
    
    public function subjects()
    {
        return $this->hasMany(Subject::class, 'class_id');
    }

    public function examTimetables()
    {
        return $this->hasMany(ExamTimetable::class, 'class_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id', 'id');
    }

    // ========== ACCESSORS ==========

    public function getFullNameAttribute()
    {
        return $this->name . ($this->section_name ? ' - Section ' . $this->section_name : '');
    }

    public function getDisplayNameAttribute()
    {
        return $this->name . ' - Section ' . $this->section_name . ' (' . $this->academic_year . ')';
    }

    public function getStudentsCountAttribute()
    {
        return $this->students()->count();
    }
    
    public function getSubjectsCountAttribute()
    {
        return $this->subjects()->count();
    }

    // ========== SCOPES ==========

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('name')->orderBy('section_name');
    }

    public function scopeByAcademicYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    // ========== CODE GENERATION METHODS ==========

    public static function generateClassCode($name, $academicYear)
    {
        // Create a code from the class name (first 3 letters uppercase)
        $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $name), 0, 3));
        
        if (empty($prefix)) {
            $prefix = 'CLS';
        }
        
        // Add academic year to code
        $yearSuffix = str_replace('-', '', $academicYear);
        
        // Count existing classes with this prefix and year
        $count = self::where('code', 'LIKE', $prefix . '-' . $yearSuffix . '%')->count();
        
        if ($count == 0) {
            $code = $prefix . '-' . $yearSuffix . '-001';
        } else {
            $nextNumber = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
            $code = $prefix . '-' . $yearSuffix . '-' . $nextNumber;
        }
        
        // Ensure uniqueness
        $suffix = 1;
        $originalCode = $code;
        
        while (self::where('code', $code)->exists()) {
            $code = $originalCode . '-' . $suffix;
            $suffix++;
        }
        
        return $code;
    }

    public static function generateSectionCode($classCode, $sectionName)
    {
        $baseCode = $classCode . '-SEC-' . strtoupper($sectionName);
        
        $code = $baseCode;
        $suffix = 1;
        
        while (self::where('section_code', $code)->exists()) {
            $code = $baseCode . '-' . $suffix;
            $suffix++;
        }
        
        return $code;
    }
}