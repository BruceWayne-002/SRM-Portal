<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\ClassModel;
use App\Models\Student;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ExamTimetableController extends Controller
{
    public function index(Request $request)
    {
        $classes = ClassModel::active()
            ->orderBy('name', 'asc')
            ->orderBy('section_name', 'asc')
            ->get();
        
        foreach ($classes as $class) {
            $class->subjects_count = Subject::where('class_id', $class->id)->count();
            
            if ($class->subjects_count > 0) {
                $subjectIds = Subject::where('class_id', $class->id)->pluck('id');
                $class->exams_count = Exam::whereIn('subject_id', $subjectIds)->count();
            } else {
                $class->exams_count = 0;
            }
            
            // Get current year from students
            $class->current_year = Student::where('class_id', $class->id)
                ->whereNotNull('current_year')
                ->value('current_year');
        }
        
        return view('admin.exam-timetable.index', compact('classes'));
    }

    public function showClassExams(ClassModel $class, Request $request)
    {
        $subjects = Subject::where('class_id', $class->id)
            ->with('teacher')
            ->get();
        
        // Get current year from students
        $currentYear = Student::where('class_id', $class->id)
            ->whereNotNull('current_year')
            ->value('current_year');
        
        // Get unique semesters from subjects for filtering
        $semesters = $subjects->pluck('semester')
            ->unique()
            ->filter()
            ->sort()
            ->values();
        
        // Build exam query
        $examQuery = Exam::with(['subject', 'subject.teacher', 'subject.classModel']);
        
        if ($subjects->isNotEmpty()) {
            $subjectIds = $subjects->pluck('id');
            $examQuery->whereIn('subject_id', $subjectIds);
        } else {
            $examQuery->whereRaw('1 = 0'); // No results if no subjects
        }
        
        // Apply semester filter if provided
        if ($request->has('semester') && $request->semester) {
            $examQuery->whereHas('subject', function($q) use ($request) {
                $q->where('semester', $request->semester);
            });
        }
        
        $exams = $examQuery->orderBy('exam_date', 'asc')
            ->orderBy('exam_time', 'asc')
            ->get();
        
        // Group exams by date
        $groupedExams = $exams->groupBy(function($exam) {
            return $exam->exam_date ? Carbon::parse($exam->exam_date)->format('Y-m-d') : 'No Date';
        })->sortKeys();
        
        // Get stats
        $totalExams = $exams->count();
        $totalSubjects = $subjects->count();
        $totalDays = $groupedExams->count();
        
        return view('admin.exam-timetable.class-exams', compact(
            'class', 
            'exams', 
            'groupedExams',
            'subjects',
            'semesters',
            'totalExams',
            'totalSubjects',
            'totalDays',
            'currentYear'
        ));
    }

    public function downloadTimetable(ClassModel $class, Request $request)
    {
        $subjects = Subject::where('class_id', $class->id)->get();
        
        if ($subjects->isEmpty()) {
            return redirect()->back()
                ->with('error', 'No subjects found for this class.');
        }
        
        // Get current year from students
        $currentYear = Student::where('class_id', $class->id)
            ->whereNotNull('current_year')
            ->value('current_year');
        
        $subjectIds = $subjects->pluck('id');
        
        $examQuery = Exam::whereIn('subject_id', $subjectIds)
            ->with(['subject', 'subject.classModel', 'subject.teacher']);
        
        // Apply semester filter if provided
        if ($request->has('semester') && $request->semester) {
            $examQuery->whereHas('subject', function($q) use ($request) {
                $q->where('semester', $request->semester);
            });
        }
        
        $exams = $examQuery->orderBy('exam_date', 'asc')
            ->orderBy('exam_time', 'asc')
            ->get();
        
        // Group exams by date
        $groupedExams = $exams->groupBy(function($exam) {
            return $exam->exam_date ? Carbon::parse($exam->exam_date)->format('Y-m-d') : 'No Date';
        })->sortKeys();
        
        // Get logo path
        $logo = $this->getLogoPath();
        
        $data = [
            'class' => $class,
            'exams_count' => $exams->count(),
            'subjects_count' => $subjects->count(),
            'exams' => $exams,
            'groupedExams' => $groupedExams,
            'downloadDate' => now()->format('d M Y, h:i A'),
            'selectedSemester' => $request->semester,
            'currentYear' => $currentYear,
            'logo' => $logo,
            'institution_name' => config('app.name', 'Examination System'),
        ];
        
        $pdf = Pdf::loadView('admin.exam-timetable.download', $data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true
        ]);
        
        $filename = 'exam-timetable-' . str_replace(' ', '-', $class->name) . '-' . now()->format('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }

    public function downloadAllClassesTimetable(Request $request)
    {
        $classes = ClassModel::active()
            ->orderBy('name', 'asc')
            ->orderBy('section_name', 'asc')
            ->get();
        
        $examQuery = Exam::with(['subject', 'subject.classModel', 'subject.teacher'])
            ->whereHas('subject.classModel');
        
        // Apply semester filter if provided
        if ($request->has('semester') && $request->semester) {
            $examQuery->whereHas('subject', function($q) use ($request) {
                $q->where('semester', $request->semester);
            });
        }
        
        $allExams = $examQuery->orderBy('exam_date', 'asc')
            ->orderBy('exam_time', 'asc')
            ->get();
        
        // Group exams by date
        $allGroupedExams = $allExams->groupBy(function($exam) {
            return $exam->exam_date ? Carbon::parse($exam->exam_date)->format('Y-m-d') : 'No Date';
        })->sortKeys();
        
        $totalExams = $allExams->count();
        $totalClasses = $classes->count();
        $totalSubjects = Subject::count();
        $examDays = $allGroupedExams->count();
        
        // Get unique semesters present in exams
        $semesters = $allExams->pluck('subject.semester')
            ->unique()
            ->filter()
            ->sort()
            ->values();
        
        // Group exams by class for summary
        $examsByClass = [];
        foreach ($classes as $class) {
            $classSubjects = Subject::where('class_id', $class->id)->pluck('id');
            $classExams = $allExams->whereIn('subject_id', $classSubjects);
            
            // Get current year from students
            $currentYear = Student::where('class_id', $class->id)
                ->whereNotNull('current_year')
                ->value('current_year');
            
            if ($classExams->count() > 0) {
                $examsByClass[] = [
                    'class' => $class,
                    'exams_count' => $classExams->count(),
                    'subjects_count' => $classSubjects->count(),
                    'semesters' => $classExams->pluck('subject.semester')->unique()->filter()->values(),
                    'current_year' => $currentYear
                ];
            }
        }
        
        // Get logo path
        $logo = $this->getLogoPath();
        
        $data = [
            'classes' => $classes,
            'allExams' => $allExams,
            'allGroupedExams' => $allGroupedExams,
            'examsByClass' => $examsByClass,
            'totalExams' => $totalExams,
            'totalClasses' => $totalClasses,
            'totalSubjects' => $totalSubjects,
            'examDays' => $examDays,
            'semesters' => $semesters,
            'downloadDate' => now()->format('d M Y, h:i A'),
            'selectedSemester' => $request->semester,
            'logo' => $logo,
            'institution_name' => config('app.name', 'Examination System'),
        ];
        
        $pdf = Pdf::loadView('admin.exam-timetable.download-all', $data);
        $pdf->setPaper('A4', 'landscape');
        $pdf->setOptions([
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true
        ]);
        
        $filename = 'all-classes-exam-timetable-' . now()->format('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Get logo path
     */
    private function getLogoPath()
    {
        // Check multiple possible logo locations
        $possiblePaths = [
            public_path('images/logo.png'),
            public_path('assets/images/logo.png'),
            public_path('img/logo.png'),
            public_path('logo.png'),
            public_path('images/logo.jpg'),
            public_path('assets/images/logo.jpg'),
            public_path('images/logo.jpeg'),
            public_path('assets/images/logo.jpeg'),
        ];
        
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }
        
        // Return null if no logo found
        return null;
    }
}