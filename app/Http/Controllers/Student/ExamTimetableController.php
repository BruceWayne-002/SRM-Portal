<?php
// app/Http/Controllers/Student/ExamTimetableController.php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Subject;
use App\Models\Exam;
use App\Models\Student;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class ExamTimetableController extends Controller
{
    /**
     * Display student's class timetable with all exams grouped by exam type
     */
    /**
 * Display student's class timetable with all exams grouped by exam type
 */
public function index()
{
    try {
        // Get the logged-in user
        $user = Auth::user();
        
        // Find the student record using register_no
        $student = null;
        if ($user->register_no) {
            $student = Student::where('roll_no', $user->register_no)->first();
        }
        
        // Check if student exists
        if (!$student) {
            return redirect()->back()->with('error', 'Student record not found. Please contact administrator.');
        }
        
        // Check if student has class assigned
        if (!$student->class_id) {
            return redirect()->back()->with('error', 'No class assigned to your account.');
        }
        
        // Get the class details
        $class = ClassModel::find($student->class_id);
        
        if (!$class) {
            return redirect()->back()->with('error', 'Class not found.');
        }
        
        // Get subjects for this class
        $subjects = Subject::where('class_id', $class->id)
            ->orderBy('name', 'asc')
            ->get();
        
        // Get exams for this class
        $exams = collect();
        $groupedByDate = collect();
        $examTypes = collect();
        
        if ($subjects->isNotEmpty()) {
            $subjectIds = $subjects->pluck('id');
            
            $exams = Exam::whereIn('subject_id', $subjectIds)
                ->with('subject')
                ->orderBy('exam_date', 'asc')
                ->orderBy('exam_time', 'asc')
                ->get();
            
            // Get unique exam types
            $examTypes = $exams->pluck('exam_type')->unique()->filter();
            
            // Group exams by date and then by exam type
            $groupedByDate = $exams->groupBy('exam_date')->map(function($dateExams) {
                return $dateExams->groupBy('exam_type');
            })->sortKeys();
            
            // Separate upcoming and completed exams
            $today = now()->toDateString();
            $upcomingDates = collect();
            $completedDates = collect();
            
            foreach ($groupedByDate as $date => $typeGroups) {
                if ($date >= $today) {
                    $upcomingDates->put($date, $typeGroups);
                } else {
                    $completedDates->put($date, $typeGroups);
                }
            }
            
            // Merge with upcoming first, then completed
            $groupedByDate = $upcomingDates->merge($completedDates);
        }
        
        // Calculate statistics
        $stats = [
            'total_exams' => $exams->count(),
            'total_subjects' => $subjects->count(),
            'completed_exams' => $exams->filter(function($exam) {
                return $exam->exam_date < now()->toDateString();
            })->count(),
            'upcoming_exams' => $exams->filter(function($exam) {
                return $exam->exam_date >= now()->toDateString();
            })->count(),
            'exam_types' => $examTypes,
        ];
        
        return view('student.exam-timetable.index', compact(
            'class', 
            'subjects', 
            'exams', 
            'groupedByDate', 
            'stats',
            'student',
            'examTypes'
        ));
        
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error loading timetable: ' . $e->getMessage());
    }
}
    
    /**
     * Show exams for a specific subject
     */
    public function showSubjectExams(Subject $subject)
    {
        try {
            $user = Auth::user();
            $student = Student::where('roll_no', $user->register_no)->first();
            
            if (!$student) {
                return redirect()->route('student.exam-timetable.index')
                    ->with('error', 'Student record not found.');
            }
            
            // Verify subject belongs to student's class
            if ($subject->class_id != $student->class_id) {
                return redirect()->back()->with('error', 'You do not have access to this subject.');
            }
            
            // Get exams for this subject
            $exams = Exam::where('subject_id', $subject->id)
                ->orderBy('exam_date', 'asc')
                ->orderBy('exam_time', 'asc')
                ->get();
            
            // Group exams by date and then by exam type
            $groupedByDate = $exams->groupBy('exam_date')->map(function($dateExams) {
                return $dateExams->groupBy('exam_type');
            })->sortKeys();
            
            return view('student.exam-timetable.subject-exams', compact(
                'subject', 
                'exams', 
                'groupedByDate',
                'student'
            ));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading subject exams: ' . $e->getMessage());
        }
    }
    
    /**
     * Download PDF timetable for student's class with exam type grouping
     */
   /**
 * Download PDF timetable for student's class - ONLY UPCOMING EXAMS
 */
public function downloadTimetable()
{
    try {
        $user = Auth::user();
        $student = Student::where('roll_no', $user->register_no)->first();
        
        if (!$student || !$student->class_id) {
            return redirect()->back()->with('error', 'Student record or class not found.');
        }
        
        $class = ClassModel::find($student->class_id);
        
        // Get subjects for this class
        $subjects = Subject::where('class_id', $class->id)->get();
        
        if ($subjects->isEmpty()) {
            return redirect()->back()->with('error', 'No subjects found for your class.');
        }
        
        // Get exams for this class - ONLY UPCOMING EXAMS (date >= today)
        $subjectIds = $subjects->pluck('id');
        
        $exams = Exam::whereIn('subject_id', $subjectIds)
            ->where('exam_date', '>=', now()->toDateString()) // CRITICAL: Only upcoming exams
            ->with('subject')
            ->orderBy('exam_date', 'asc')
            ->orderBy('exam_time', 'asc')
            ->get();
        
        // Get unique exam types
        $examTypes = $exams->pluck('exam_type')->unique()->filter();
        
        // Group exams by date and then by exam type
        $groupedByDate = $exams->groupBy('exam_date')->map(function($dateExams) {
            return $dateExams->groupBy('exam_type');
        })->sortKeys();
        
        // Prepare data for PDF
        $data = [
            'class' => $class,
            'student' => $student,
            'student_name' => $student->name,
            'student_roll_number' => $student->roll_no,
            'exams_count' => $exams->count(),
            'subjects_count' => $subjects->count(),
            'exams' => $exams,
            'groupedByDate' => $groupedByDate,
            'examTypes' => $examTypes,
            'downloadDate' => now()->format('d M Y, h:i A'),
        ];
        
        // Generate PDF
        $pdf = Pdf::loadView('student.exam-timetable.download', $data);
        
        // Set PDF options
        $pdf->setPaper('A4', 'portrait');
        
        // Generate filename
        $filename = 'upcoming-exams-' . 
                    str_replace(' ', '-', $class->name) . '-' . 
                    now()->format('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
        
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error downloading timetable: ' . $e->getMessage());
    }
}
    
    /**
     * Show only upcoming exams grouped by exam type
     */
    public function upcomingExams()
    {
        try {
            $user = Auth::user();
            $student = Student::where('roll_no', $user->register_no)->first();
            
            if (!$student || !$student->class_id) {
                return redirect()->back()->with('error', 'Student record or class not found.');
            }
            
            $class = ClassModel::find($student->class_id);
            
            // Get subjects for this class
            $subjects = Subject::where('class_id', $class->id)->pluck('id');
            
            // Get upcoming exams
            $upcomingExams = Exam::whereIn('subject_id', $subjects)
                ->where('exam_date', '>=', now()->toDateString())
                ->with('subject')
                ->orderBy('exam_date', 'asc')
                ->orderBy('exam_time', 'asc')
                ->get();
            
            // Get unique exam types
            $examTypes = $upcomingExams->pluck('exam_type')->unique()->filter();
            
            // Group by date and then by exam type
            $groupedByDate = $upcomingExams->groupBy('exam_date')->map(function($dateExams) {
                return $dateExams->groupBy('exam_type');
            })->sortKeys();
            
            return view('student.exam-timetable.upcoming', compact(
                'class', 
                'upcomingExams', 
                'groupedByDate',
                'student',
                'examTypes'
            ));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading upcoming exams: ' . $e->getMessage());
        }
    }
}