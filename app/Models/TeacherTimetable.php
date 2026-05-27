<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherTimetable extends Model
{
    use HasFactory;

    protected $table = 'teacher_timetables';

    protected $fillable = [
        'teacher_id',
        'class',
        'section',
        'group',
        'day',
        'period',
        'subject',
        'school_code',
    ];

    // Constants for ordering days and periods
    public const DAYS = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
    public const PERIODS = ['Morning','1','2','3','4','5','6','7','8','Evening'];

    /**
     * Relationship: A timetable entry belongs to a teacher.
     */
    public function teacher()
    {
        // Ensure the foreign key matches your Teacher model
        return $this->belongsTo(Teacher::class, 'teacher_id', 'employee_id');
    }

    /**
     * Relationship: A timetable entry belongs to a school.
     */
    public function school()
    {
        return $this->belongsTo(School::class, 'school_code', 'code');
    }

    /**
     * Get timetable for a teacher with optional filters.
     *
     * @param int $teacherId
     * @param string|null $class
     * @param string|null $section
     * @param string|null $group
     * @param string|null $schoolCode
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getTimetable($teacherId, $class = null, $section = null, $group = null, $schoolCode = null)
    {
        $query = self::where('teacher_id', $teacherId);

        if ($class) $query->where('class', $class);
        if ($section) $query->where('section', $section);
        if ($group) $query->where('group', $group);
        if ($schoolCode) $query->where('school_code', $schoolCode);

        return $query->orderByRaw("FIELD(day, " . implode(',', array_map(fn($d) => "'$d'", self::DAYS)) . ")")
                     ->orderByRaw("FIELD(period, " . implode(',', array_map(fn($p) => "'$p'", self::PERIODS)) . ")")
                     ->get();
    }
}
