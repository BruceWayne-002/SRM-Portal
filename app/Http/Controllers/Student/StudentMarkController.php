<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Mark;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class StudentMarkController extends Controller
{
    /**
     * Display a listing of the student's marks with semester and exam type selector.
     */
    public function index(Request $request)
    {
        try {
            // Get the logged-in user
            $user = Auth::user();
            
            // Find the student record using register_no from user and roll_no from student
            $student = null;
            
            if ($user && $user->register_no) {
                $student = Student::where('roll_no', $user->register_no)->first();
            }
            
            // If no student record found
            if (!$student) {
                Log::warning('Student record not found for user', [
                    'user_id' => $user ? $user->id : 'unknown',
                    'register_no' => $user ? $user->register_no : 'unknown'
                ]);
                
                return view('student.marks.index', [
                    'student' => null,
                    'error' => 'Your student profile is not set up. Please contact the administrator.'
                ]);
            }
            
            // Get all marks for this student with exam details
            $allMarks = Mark::where('student_id', $student->id)
                ->with(['exam', 'exam.subject'])
                ->where('status', 'published')
                ->get();
            
            // Get unique semesters from exams
            $semesters = [];
            foreach ($allMarks as $mark) {
                if ($mark->exam && $mark->exam->subject && $mark->exam->subject->semester) {
                    $semester = $mark->exam->subject->semester;
                    if (!in_array($semester, $semesters)) {
                        $semesters[] = $semester;
                    }
                }
            }
            sort($semesters); // Sort semesters in ascending order
            
            // Get selected semester from request
            $selectedSemester = $request->get('semester');
            
            // Get exam types based on selected semester
            $examTypes = [];
            if ($selectedSemester) {
                $examTypes = Mark::where('student_id', $student->id)
                    ->where('status', 'published')
                    ->whereHas('exam', function($query) use ($selectedSemester) {
                        $query->whereHas('subject', function($q) use ($selectedSemester) {
                            $q->where('semester', $selectedSemester);
                        });
                    })
                    ->with('exam')
                    ->get()
                    ->pluck('exam.exam_type')
                    ->unique()
                    ->filter()
                    ->values()
                    ->toArray();
            }
            
            // Get selected exam type from request
            $selectedExamType = $request->get('exam_type');
            
            $marks = collect();
            $statistics = null;
            $subjectWiseMarks = collect();
            
            // If both semester and exam type are selected, get marks
            if ($selectedSemester && $selectedExamType) {
                $marks = Mark::with(['exam', 'exam.subject', 'exam.subject.classModel'])
                    ->where('student_id', $student->id)
                    ->where('status', 'published')
                    ->whereHas('exam', function($query) use ($selectedSemester, $selectedExamType) {
                        $query->where('exam_type', $selectedExamType)
                              ->whereHas('subject', function($q) use ($selectedSemester) {
                                  $q->where('semester', $selectedSemester);
                              });
                    })
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                // Calculate statistics for selected filters
                if ($marks->isNotEmpty()) {
                    $totalExams = $marks->count();
                    $totalMarks = $marks->sum('total_marks');
                    $obtainedMarks = $marks->sum('marks_obtained');
                    $averagePercentage = $totalMarks > 0 ? round(($obtainedMarks / $totalMarks) * 100, 2) : 0;
                    
                    // Group marks by subject for subject-wise performance
                    $subjectWiseMarks = $marks->groupBy(function($mark) {
                        return $mark->exam->subject_id;
                    })->map(function($subjectMarks) {
                        $subject = $subjectMarks->first()->exam->subject;
                        $total = $subjectMarks->sum('total_marks');
                        $obtained = $subjectMarks->sum('marks_obtained');
                        $percentage = $total > 0 ? round(($obtained / $total) * 100, 2) : 0;
                        
                        return [
                            'subject_name' => $subject->name ?? 'N/A',
                            'subject_code' => $subject->code ?? 'N/A',
                            'exams_count' => $subjectMarks->count(),
                            'total_marks' => $total,
                            'obtained_marks' => $obtained,
                            'percentage' => $percentage
                        ];
                    })->values();
                    
                    $statistics = [
                        'total_exams' => $totalExams,
                        'total_marks' => $totalMarks,
                        'obtained_marks' => $obtainedMarks,
                        'average_percentage' => $averagePercentage
                    ];
                }
            }
            
            return view('student.marks.index', compact(
                'student', 
                'semesters',
                'selectedSemester',
                'examTypes',
                'selectedExamType',
                'marks',
                'statistics',
                'subjectWiseMarks'
            ));
            
        } catch (\Exception $e) {
            Log::error('Error in StudentMarkController@index: ' . $e->getMessage());
            return view('student.marks.index', [
                'student' => null,
                'error' => 'An error occurred while loading your marks: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get exam types for selected semester (AJAX endpoint)
     */
    public function getExamTypes(Request $request)
    {
        try {
            $semester = $request->get('semester');
            $user = Auth::user();
            
            if (!$semester) {
                return response()->json(['error' => 'Semester is required'], 400);
            }
            
            $student = Student::where('roll_no', $user->register_no)->first();
            
            if (!$student) {
                return response()->json(['error' => 'Student not found'], 404);
            }
            
            $examTypes = Mark::where('student_id', $student->id)
                ->where('status', 'published')
                ->whereHas('exam', function($query) use ($semester) {
                    $query->whereHas('subject', function($q) use ($semester) {
                        $q->where('semester', $semester);
                    });
                })
                ->with('exam')
                ->get()
                ->pluck('exam.exam_type')
                ->unique()
                ->filter()
                ->values()
                ->toArray();
            
            return response()->json(['exam_types' => $examTypes]);
            
        } catch (\Exception $e) {
            Log::error('Error in getExamTypes: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load exam types: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Download marks as PDF
     */
    public function downloadPdf(Request $request)
    {
        try {
            $semester = $request->get('semester');
            $examType = $request->get('exam_type');
            
            if (!$semester || !$examType) {
                return redirect()->back()->with('error', 'Please select both semester and exam type.');
            }
            
            // Get the logged-in user
            $user = Auth::user();
            
            // Find the student record
            $student = null;
            if ($user && $user->register_no) {
                $student = Student::where('roll_no', $user->register_no)->first();
            }
            
            if (!$student) {
                return redirect()->route('student.marks.index')
                    ->with('error', 'Student record not found.');
            }
            
            // Get marks for this semester and exam type
            $marks = Mark::with(['exam', 'exam.subject', 'exam.subject.classModel'])
                ->where('student_id', $student->id)
                ->where('status', 'published')
                ->whereHas('exam', function($query) use ($semester, $examType) {
                    $query->where('exam_type', $examType)
                          ->whereHas('subject', function($q) use ($semester) {
                              $q->where('semester', $semester);
                          });
                })
                ->orderBy('created_at', 'desc')
                ->get();
            
            if ($marks->isEmpty()) {
                return redirect()->route('student.marks.index', ['semester' => $semester, 'exam_type' => $examType])
                    ->with('error', 'No marks found for selected criteria.');
            }
            
            // Calculate statistics
            $totalExams = $marks->count();
            $totalMarks = $marks->sum('total_marks');
            $obtainedMarks = $marks->sum('marks_obtained');
            $averagePercentage = $totalMarks > 0 ? round(($obtainedMarks / $totalMarks) * 100, 2) : 0;
            
            // Group marks by subject
            $subjectWiseMarks = collect();
            
            if ($marks->isNotEmpty()) {
                $subjectWiseMarks = $marks->groupBy(function($mark) {
                    return $mark->exam->subject_id;
                })->map(function($subjectMarks) {
                    $subject = $subjectMarks->first()->exam->subject;
                    $total = $subjectMarks->sum('total_marks');
                    $obtained = $subjectMarks->sum('marks_obtained');
                    $percentage = $total > 0 ? round(($obtained / $total) * 100, 2) : 0;
                    
                    return [
                        'subject_name' => $subject->name ?? 'N/A',
                        'subject_code' => $subject->code ?? 'N/A',
                        'exams_count' => $subjectMarks->count(),
                        'total_marks' => $total,
                        'obtained_marks' => $obtained,
                        'percentage' => $percentage
                    ];
                })->values();
            }
            
            $displayName = ucfirst($examType) . ' Examination - Semester ' . $semester;
            $date = now()->format('d-m-Y H:i:s');
            
            // Get college logo if exists
            $logoBase64 = null;
            $logoPath = public_path('images/srmlogo1.jpg');
            if (file_exists($logoPath)) {
                $logoBase64 = 'data:image/jpg;base64,' . base64_encode(file_get_contents($logoPath));
            }
            
            $pdf = Pdf::loadView('student.marks.pdf', compact(
                'student', 
                'marks', 
                'examType',
                'semester',
                'displayName',
                'totalExams', 
                'totalMarks', 
                'obtainedMarks', 
                'averagePercentage',
                'subjectWiseMarks',
                'date',
                'logoBase64'
            ));
            
            // Set paper size and orientation
            $pdf->setPaper('A4', 'portrait');
            
            // Generate filename
            $filename = 'Semester_' . $semester . '_' . str_replace(' ', '_', $examType) . '_Marks_' . $student->roll_no . '_' . now()->format('d-m-Y') . '.pdf';
            
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            Log::error('Error in StudentMarkController@downloadPdf: ' . $e->getMessage());
            return redirect()->back()->with('error', 
                'An error occurred while downloading the PDF: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified exam marks for the student.
     */
    public function show($examId)
    {
        try {
            // Get the logged-in user
            $user = Auth::user();
            
            // Find the student record using register_no
            $student = null;
            if ($user && $user->register_no) {
                $student = Student::where('roll_no', $user->register_no)->first();
            }
            
            if (!$student) {
                return redirect()->route('student.marks.index')
                    ->with('error', 'Student record not found.');
            }
            
            // Get the exam
            $exam = Exam::with(['subject', 'subject.classModel'])->find($examId);
            
            if (!$exam) {
                return redirect()->route('student.marks.index')
                    ->with('error', 'Exam not found.');
            }
            
            // Get the mark for this specific exam and student
            $mark = Mark::with(['exam', 'exam.subject', 'exam.subject.classModel'])
                ->where('exam_id', $examId)
                ->where('student_id', $student->id)
                ->first();
            
            if (!$mark) {
                return redirect()->route('student.marks.index')
                    ->with('error', 'Mark record not found for this exam.');
            }
            
            // Check if mark is published
            if ($mark->status !== 'published') {
                return redirect()->route('student.marks.index')
                    ->with('error', 'Marks for this exam are not yet published.');
            }
            
            // Get all students' marks for this exam (for comparison/ranking)
            $allMarks = Mark::with('student')
                ->where('exam_id', $examId)
                ->where('status', 'published')
                ->orderBy('marks_obtained', 'desc')
                ->get();
            
            // Find student's rank
            $rank = $allMarks->search(function($item) use ($student) {
                return $item->student_id == $student->id;
            }) + 1;
            
            // Calculate statistics
            $classAverage = $allMarks->avg('marks_obtained');
            $highestMark = $allMarks->max('marks_obtained');
            $lowestMark = $allMarks->min('marks_obtained');
            
            // Get internal and external marks
            $internalMarks = $mark->internal_marks ?? 0;
            $externalMarks = $mark->external_marks ?? 0;
            $totalObtained = $mark->marks_obtained ?? ($internalMarks + $externalMarks);
            $totalMarks = $mark->total_marks ?? ($mark->internal_max_marks + $mark->external_max_marks);
            
            return view('student.marks.show', compact(
                'mark', 
                'exam', 
                'student', 
                'rank', 
                'classAverage', 
                'highestMark', 
                'lowestMark',
                'allMarks',
                'internalMarks',
                'externalMarks',
                'totalObtained',
                'totalMarks'
            ));
            
        } catch (\Exception $e) {
            Log::error('Error in StudentMarkController@show: ' . $e->getMessage());
            return redirect()->back()->with('error', 
                'An error occurred while loading the mark details: ' . $e->getMessage());
        }
    }
}