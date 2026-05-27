<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Student;
use Illuminate\Http\Request;

class TeacherSubjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:Teacher');
    }

    private function getTeacher()
    {
        $user = auth()->user();

        if (!$user) {
            return null;
        }

        return Teacher::where('user_id', $user->id)
            ->orWhere('employee_id', $user->employee_id ?? null)
            ->orWhere('email', $user->email)
            ->first();
    }

    public function index()
    {
        try {
            $teacher = $this->getTeacher();

            if (!$teacher) {
                return redirect()->route('teacher.dashboard')
                    ->with('error', 'Teacher profile not found!');
            }

            $subjects = Subject::where('teacher_id', $teacher->id)
                ->with(['classModel'])
                ->orderBy('class_name')
                ->orderBy('section')
                ->orderBy('semester')
                ->orderBy('name')
                ->get();

            $groupedSubjects = $subjects->groupBy(function ($subject) {
                return $subject->class_name . ' - ' . ($subject->section ?? 'No Section');
            });

            return view('teacher.subjects.index', compact('subjects', 'groupedSubjects', 'teacher'));

        } catch (\Exception $e) {
            return redirect()->route('teacher.dashboard')
                ->with('error', 'Error loading subjects: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $teacher = $this->getTeacher();

            if (!$teacher) {
                return redirect()->route('teacher.dashboard')
                    ->with('error', 'Teacher profile not found!');
            }

            $subject = Subject::where('id', $id)
                ->where('teacher_id', $teacher->id)
                ->with(['classModel', 'teacher'])
                ->first();

            if (!$subject) {
                return redirect()->route('teacher.subjects.index')
                    ->with('error', 'Subject not found or not assigned to you!');
            }

            $students = Student::where('class_id', $subject->class_id)
                ->where('section', $subject->section)
                ->where('status', 'active')
                ->when($subject->academic_year, function ($query) use ($subject) {
                    $query->where('academic_year', $subject->academic_year);
                })
                ->orderBy('roll_no')
                ->get();

            $timetables = $subject->timetables()
                ->with(['exam', 'hall'])
                ->orderBy('exam_date')
                ->get();

            return view('teacher.subjects.show', compact('subject', 'students', 'timetables', 'teacher'));

        } catch (\Exception $e) {
            return redirect()->route('teacher.subjects.index')
                ->with('error', 'Error loading subject details: ' . $e->getMessage());
        }
    }

    public function students($id)
    {
        try {
            $teacher = $this->getTeacher();

            if (!$teacher) {
                return redirect()->route('teacher.dashboard')
                    ->with('error', 'Teacher profile not found!');
            }

            $subject = Subject::where('id', $id)
                ->where('teacher_id', $teacher->id)
                ->firstOrFail();

            $students = Student::where('class_id', $subject->class_id)
                ->where('section', $subject->section)
                ->where('status', 'active')
                ->when($subject->academic_year, function ($query) use ($subject) {
                    $query->where('academic_year', $subject->academic_year);
                })
                ->orderBy('roll_no')
                ->orderBy('name')
                ->get();

            foreach ($students as $student) {
                $student->academic_info = $student->academic_info;
                $student->current_year_formatted = $student->formatted_current_year;
                $student->current_semester_formatted = $student->formatted_semester;
            }

            return view('teacher.subjects.students', compact('subject', 'students', 'teacher'));

        } catch (\Exception $e) {
            return redirect()->route('teacher.subjects.index')
                ->with('error', 'Error loading students: ' . $e->getMessage());
        }
    }

    public function marks($id)
    {
        try {
            $teacher = $this->getTeacher();

            if (!$teacher) {
                return redirect()->route('teacher.dashboard')
                    ->with('error', 'Teacher profile not found!');
            }

            $subject = Subject::where('id', $id)
                ->where('teacher_id', $teacher->id)
                ->firstOrFail();

            $students = Student::where('class_id', $subject->class_id)
                ->where('section', $subject->section)
                ->where('status', 'active')
                ->when($subject->academic_year, function ($query) use ($subject) {
                    $query->where('academic_year', $subject->academic_year);
                })
                ->orderBy('roll_no')
                ->get();

            return view('teacher.subjects.marks', compact('subject', 'students', 'teacher'));

        } catch (\Exception $e) {
            return redirect()->route('teacher.subjects.index')
                ->with('error', 'Error loading marks page: ' . $e->getMessage());
        }
    }

    public function timetable($id)
    {
        try {
            $teacher = $this->getTeacher();

            if (!$teacher) {
                return redirect()->route('teacher.dashboard')
                    ->with('error', 'Teacher profile not found!');
            }

            $subject = Subject::where('id', $id)
                ->where('teacher_id', $teacher->id)
                ->firstOrFail();

            $timetables = $subject->timetables()
                ->with(['exam', 'hall'])
                ->orderBy('exam_date')
                ->get();

            return view('teacher.subjects.timetable', compact('subject', 'timetables', 'teacher'));

        } catch (\Exception $e) {
            return redirect()->route('teacher.subjects.index')
                ->with('error', 'Error loading timetable: ' . $e->getMessage());
        }
    }
}