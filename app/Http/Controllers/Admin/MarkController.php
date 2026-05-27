<?php
// app/Http/Controllers/Admin/MarkController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Mark;
use App\Models\Student;
use App\Models\ClassModel;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MarkController extends Controller
{
    /**
     * Display list of completed exams for mark entry
     */
    public function index(Request $request)
    {
        $query = Exam::with(['subject', 'subject.classModel'])
            ->completed()
            ->orderBy('exam_date', 'desc')
            ->orderBy('exam_time', 'desc');
        
        // Filter by class
        if ($request->filled('class_id')) {
            $query->whereHas('subject', function($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }
        
        // Filter by subject
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        
        // Filter by date range
        if ($request->filled('from_date')) {
            $query->where('exam_date', '>=', $request->from_date);
        }
        
        if ($request->filled('to_date')) {
            $query->where('exam_date', '<=', $request->to_date);
        }
        
        // Filter by marks status
        if ($request->filled('marks_status')) {
            if ($request->marks_status == 'completed') {
                $query->whereHas('marks');
            } elseif ($request->marks_status == 'pending') {
                $query->whereDoesntHave('marks');
            } elseif ($request->marks_status == 'absent') {
                $query->whereHas('marks', function($q) {
                    $q->where('is_absent', true);
                });
            }
        }

        // Filter by draft only
        if ($request->filled('draft_only')) {
            $query->whereHas('marks', function($q) {
                $q->where('status', 'draft');
            });
        }
        
        $exams = $query->paginate(15)->withQueryString();
        
        // Get classes and subjects for filters
        $classes = ClassModel::all();
        $subjects = Subject::all();
        
        return view('admin.marks.index', compact('exams', 'classes', 'subjects'));
    }

    /**
     * Show form to enter marks for an exam
     */
    public function create(Exam $exam)
    {
        // Check if exam is completed
        if (!$this->isExamCompleted($exam)) {
            return redirect()->route('admin.marks.index')
                ->with('error', 'Cannot enter marks for upcoming or ongoing exams.');
        }
        
        // Get students for this exam
        $students = $this->getExamStudents($exam);
        
        if ($students->isEmpty()) {
            // Get class info for debugging
            $classInfo = 'N/A';
            if ($exam->subject && $exam->subject->classModel) {
                $classInfo = $exam->subject->classModel->name . ' - ' . ($exam->subject->classModel->section_name ?? 'No Section');
            } elseif ($exam->subject && $exam->subject->class_id) {
                $classInfo = 'Class ID: ' . $exam->subject->class_id;
            }
            
            return redirect()->route('admin.marks.index')
                ->with('error', 'No students found for this exam. Class: ' . $classInfo . '. Please check if students are assigned to the class.');
        }
        
        // Get existing marks for this exam
        $existingMarks = Mark::where('exam_id', $exam->id)
            ->get()
            ->keyBy('student_id');
        
        // Get subject details for marks distribution
        $subject = $exam->subject;
        
        return view('admin.marks.create', compact('exam', 'students', 'existingMarks', 'subject'));
    }

    /**
     * Store marks for an exam
     */
    public function store(Request $request, Exam $exam)
    {
        $request->validate([
            'internal_marks' => 'nullable|array',
            'internal_marks.*' => 'nullable|numeric|min:0',
            'external_marks' => 'nullable|array',
            'external_marks.*' => 'nullable|numeric|min:0',
            'absent' => 'nullable|array',
            'absent.*' => 'nullable|in:internal,external,both',
            'status' => 'required|in:draft,published'
        ]);

        DB::beginTransaction();
        
        try {
            $students = $this->getExamStudents($exam);
            $subject = $exam->subject;
            $classId = $subject->class_id;
            $internalMax = $subject->internal_marks ?? 0;
            $externalMax = $subject->external_marks ?? 0;
            $totalMax = $internalMax + $externalMax;
            
            // Passing marks criteria
            $internalPassing = 10; // Minimum 10 marks in internal
            $externalPassing = 20; // Minimum 20 marks in external
            $totalPassing = 35;     // Minimum 35 marks total
            
            $marksSaved = 0;
            
            foreach ($students as $student) {
                // Get absent status
                $absentStatus = $request->absent[$student->id] ?? null;
                
                // Get marks
                $internalMarks = isset($request->internal_marks[$student->id]) && $request->internal_marks[$student->id] !== '' 
                    ? (float) $request->internal_marks[$student->id] 
                    : null;
                    
                $externalMarks = isset($request->external_marks[$student->id]) && $request->external_marks[$student->id] !== '' 
                    ? (float) $request->external_marks[$student->id] 
                    : null;
                
                // Only create/update if at least one mark is entered or student has absent status
                if ($internalMarks !== null || $externalMarks !== null || $absentStatus) {
                    
                    // Initialize variables
                    $isAbsent = !empty($absentStatus);
                    $totalObtained = 0;
                    $grade = null;
                    $remarks = $request->remarks[$student->id] ?? null;
                    
                    // CRITICAL RULE: If student is absent in either internal OR external, they FAIL the entire exam
                    if ($absentStatus == 'internal' || $absentStatus == 'external' || $absentStatus == 'both') {
                        $isAbsent = true;
                        
                        if ($absentStatus == 'both') {
                            // Both components absent
                            $totalObtained = 0;
                            $grade = 'AB';
                            $remarks = $remarks ?: 'Absent for both components';
                        } else {
                            // Absent in either internal OR external - FAIL
                            $totalObtained = ($internalMarks ?? 0) + ($externalMarks ?? 0);
                            $grade = 'F';
                            
                            if ($absentStatus == 'internal') {
                                $remarks = $remarks ?: 'Failed - Absent for internal examination';
                            } else {
                                $remarks = $remarks ?: 'Failed - Absent for external examination';
                            }
                        }
                    } else {
                        // No absence - both components present
                        $totalObtained = ($internalMarks ?? 0) + ($externalMarks ?? 0);
                        $isAbsent = false;
                        
                        // Check passing criteria
                        $internalPass = true;
                        $externalPass = true;
                        
                        if ($internalMarks !== null) {
                            $internalPass = $internalMarks >= $internalPassing;
                        }
                        
                        if ($externalMarks !== null) {
                            $externalPass = $externalMarks >= $externalPassing;
                        }
                        
                        $totalPass = $totalObtained >= $totalPassing;
                        
                        if ($internalPass && $externalPass && $totalPass) {
                            $percentage = ($totalObtained / $totalMax) * 100;
                            $grade = $this->calculateGrade($percentage);
                        } else {
                            $grade = 'F';
                            $failReasons = [];
                            if (!$internalPass) $failReasons[] = 'Internal below ' . $internalPassing . ' (' . ($internalMarks ?? 0) . ')';
                            if (!$externalPass) $failReasons[] = 'External below ' . $externalPassing . ' (' . ($externalMarks ?? 0) . ')';
                            if (!$totalPass) $failReasons[] = 'Total below ' . $totalPassing . ' (' . $totalObtained . ')';
                            $remarks = $remarks ?: 'Failed - ' . implode(', ', $failReasons);
                        }
                    }
                    
                    // Calculate percentages
                    $internalPercentage = ($internalMarks !== null && $internalMax > 0) 
                        ? ($internalMarks / $internalMax) * 100 
                        : 0;
                    
                    $externalPercentage = ($externalMarks !== null && $externalMax > 0) 
                        ? ($externalMarks / $externalMax) * 100 
                        : 0;
                    
                    $overallPercentage = ($totalMax > 0 && $totalObtained > 0 && $grade != 'AB' && $grade != 'F' && !$isAbsent) 
                        ? ($totalObtained / $totalMax) * 100 
                        : 0;
                    
                    $markData = [
                        'exam_id' => $exam->id,
                        'student_id' => $student->id,
                        'subject_id' => $exam->subject_id,
                        'class_id' => $classId,
                        'internal_marks' => $internalMarks,
                        'external_marks' => $externalMarks,
                        'internal_percentage' => $internalPercentage,
                        'external_percentage' => $externalPercentage,
                        'is_absent' => $isAbsent,
                        'absent_status' => $absentStatus,
                        'total_marks_obtained' => $totalObtained,
                        'total_marks' => $totalMax,
                        'percentage' => $overallPercentage,
                        'grade' => $grade,
                        'remarks' => $remarks,
                        'status' => $request->status,
                        'entered_by' => Auth::id()
                    ];
                    
                    // Update or create mark
                    $mark = Mark::updateOrCreate(
                        [
                            'exam_id' => $exam->id,
                            'student_id' => $student->id
                        ],
                        $markData
                    );
                    
                    if ($mark->wasRecentlyCreated || $mark->wasChanged()) {
                        $marksSaved++;
                    }
                }
            }
            
            DB::commit();
            
            $message = $request->status == 'published' 
                ? "{$marksSaved} marks have been published successfully." 
                : "{$marksSaved} marks have been saved as draft.";
            
            return redirect()->route('admin.marks.index')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Mark Store Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Error saving marks: ' . $e->getMessage());
        }
    }

    /**
     * Display marks for an exam
     */
    public function show(Exam $exam)
    {
        $marks = Mark::with(['student', 'subject'])
            ->where('exam_id', $exam->id)
            ->orderBy('student_id')
            ->get();
        
        $students = $this->getExamStudents($exam);
        
        // Calculate statistics
        $totalStudents = $students->count();
        $marksEntered = $marks->count();
        $present = $marks->where('is_absent', false)->count();
        $absent = $marks->where('is_absent', true)->count();
        $passed = $marks->whereNotIn('grade', ['F', 'AB'])->count();
        $failed = $marks->whereIn('grade', ['F', 'AB'])->count();
        $average = $marks->where('is_absent', false)->avg('percentage') ?? 0;
        
        $statistics = [
            'total_students' => $totalStudents,
            'marks_entered' => $marksEntered,
            'present' => $present,
            'absent' => $absent,
            'passed' => $passed,
            'failed' => $failed,
            'average' => round($average, 2),
            'pass_percentage' => $present > 0 ? round(($passed / $present) * 100, 2) : 0
        ];
        
        return view('admin.marks.show', compact('exam', 'marks', 'students', 'statistics'));
    }

    /**
     * Edit marks for an exam
     */
    public function edit(Exam $exam)
    {
        $students = $this->getExamStudents($exam);
        $existingMarks = Mark::where('exam_id', $exam->id)
            ->get()
            ->keyBy('student_id');
        
        $subject = $exam->subject;
        
        return view('admin.marks.edit', compact('exam', 'students', 'existingMarks', 'subject'));
    }

    /**
     * Update marks for an exam
     */
    public function update(Request $request, Exam $exam)
    {
        return $this->store($request, $exam);
    }

    /**
     * Publish marks for an exam
     */
    public function publish(Exam $exam)
    {
        Mark::where('exam_id', $exam->id)
            ->update(['status' => 'published']);
        
        return redirect()->back()
            ->with('success', 'Marks published successfully.');
    }

    /**
     * Download marks as PDF
     */
    public function download(Exam $exam)
    {
        $marks = Mark::with(['student', 'subject'])
            ->where('exam_id', $exam->id)
            ->orderBy('student_id')
            ->get();
        
        $subject = $exam->subject;
        $students = $this->getExamStudents($exam);
        
        // Calculate statistics
        $totalStudents = $students->count();
        $marksEntered = $marks->count();
        $present = $marks->where('is_absent', false)->count();
        $absent = $marks->where('is_absent', true)->count();
        $passed = $marks->whereNotIn('grade', ['F', 'AB'])->count();
        $failed = $marks->whereIn('grade', ['F', 'AB'])->count();
        $average = $marks->where('is_absent', false)->avg('percentage') ?? 0;
        
        $statistics = [
            'total_students' => $totalStudents,
            'marks_entered' => $marksEntered,
            'present' => $present,
            'absent' => $absent,
            'passed' => $passed,
            'failed' => $failed,
            'average' => round($average, 2),
            'pass_percentage' => $present > 0 ? round(($passed / $present) * 100, 2) : 0
        ];
        
        // Get logo base64
        $logoPath = public_path('images/srmlogo1.jpg');
        if (file_exists($logoPath)) {
            $logoData = base64_encode(file_get_contents($logoPath));
            $logoMime = mime_content_type($logoPath);
            $logoBase64 = "data:{$logoMime};base64,{$logoData}";
        } else {
            $logoBase64 = '';
        }
        
        // Generate filename
        $filename = $exam->subject_name . '_' . $exam->exam_date->format('Y-m-d') . '_marks.pdf';
        
        // Load the view and generate PDF
        $pdf = Pdf::loadView('admin.marks.pdf', compact('exam', 'marks', 'subject', 'statistics', 'logoBase64'));
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'isPhpEnabled' => true
        ]);
        
        return $pdf->download($filename);
    }

    /**
     * Get students for an exam
     */
    private function getExamStudents(Exam $exam)
    {
        try {
            \Log::info('Getting students for exam ID: ' . $exam->id);
            
            // Method 1: Try to get students from exam timetables
            if (method_exists($exam, 'examTimetables') && $exam->examTimetables()->exists()) {
                $studentIds = $exam->examTimetables()->pluck('student_id')->toArray();
                
                if (!empty($studentIds)) {
                    $students = Student::whereIn('id', $studentIds)
                        ->orderBy('roll_no')
                        ->get();
                    
                    if ($students->isNotEmpty()) {
                        \Log::info('Found ' . $students->count() . ' students from exam timetables');
                        return $students;
                    }
                }
            }
            
            // Method 2: Get class_id from subject
            if ($exam->subject && $exam->subject->class_id) {
                $classId = $exam->subject->class_id;
                \Log::info('Found class_id from subject: ' . $classId);
                
                $students = Student::where('class_id', $classId)
                    ->orderBy('roll_no')
                    ->get();
                
                if ($students->isNotEmpty()) {
                    \Log::info('Found ' . $students->count() . ' students from class_id: ' . $classId);
                    return $students;
                }
            }
            
            // Method 3: Get class from subject's classModel
            if ($exam->subject && $exam->subject->classModel) {
                $classModel = $exam->subject->classModel;
                \Log::info('Found classModel: ' . $classModel->name);
                
                $students = $classModel->students()
                    ->orderBy('roll_no')
                    ->get();
                
                if ($students->isNotEmpty()) {
                    \Log::info('Found ' . $students->count() . ' students from classModel relationship');
                    return $students;
                }
            }
            
            // Method 4: Try to get class by name from exam's class_name attribute
            if ($exam->class_name) {
                \Log::info('Trying to find class by name: ' . $exam->class_name);
                
                $class = ClassModel::where('name', 'like', '%' . $exam->class_name . '%')->first();
                
                if ($class) {
                    $students = Student::where('class_id', $class->id)
                        ->orderBy('roll_no')
                        ->get();
                    
                    if ($students->isNotEmpty()) {
                        \Log::info('Found ' . $students->count() . ' students from class name match');
                        return $students;
                    }
                }
            }
            
            \Log::warning('No students found through specific methods. Total students in DB: ' . Student::count());
            
            return collect();
            
        } catch (\Exception $e) {
            \Log::error('Error getting exam students: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return collect();
        }
    }

    /**
     * Check if exam is completed
     */
    private function isExamCompleted(Exam $exam)
    {
        $examDateTime = Carbon::parse($exam->exam_date->format('Y-m-d') . ' ' . $exam->exam_time);
        return $examDateTime->isPast();
    }

    /**
     * Calculate grade based on percentage
     */
    private function calculateGrade($percentage)
    {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C+';
        if ($percentage >= 40) return 'C';
        if ($percentage >= 35) return 'D';
        return 'F';
    }

    /**
     * Bulk publish all draft marks
     */
    public function bulkPublish(Request $request)
    {
        $request->validate([
            'exam_ids' => 'required|array',
            'exam_ids.*' => 'exists:exams,id'
        ]);

        DB::beginTransaction();
        
        try {
            $updatedCount = 0;
            $examIds = $request->exam_ids;
            
            foreach ($examIds as $examId) {
                // Update marks status to published for this exam
                $count = Mark::where('exam_id', $examId)
                    ->where('status', 'draft')
                    ->update(['status' => 'published']);
                
                if ($count > 0) {
                    $updatedCount++;
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "{$updatedCount} exams have been published successfully."
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Bulk Publish Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error publishing exams: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Publish all draft marks
     */
    public function publishAll()
    {
        DB::beginTransaction();
        
        try {
            // Get all draft marks
            $draftMarks = Mark::where('status', 'draft')->get();
            
            if ($draftMarks->isEmpty()) {
                return redirect()->back()
                    ->with('info', 'No draft marks found to publish.');
            }
            
            // Group by exam to count unique exams
            $examIds = $draftMarks->pluck('exam_id')->unique();
            $examCount = $examIds->count();
            $markCount = $draftMarks->count();
            
            // Update all draft marks to published
            Mark::where('status', 'draft')->update(['status' => 'published']);
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', "Successfully published {$markCount} marks from {$examCount} exams.");
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Publish All Error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error publishing marks: ' . $e->getMessage());
        }
    }

    /**
     * Get draft exams for bulk publish
     */
    public function getDraftExams()
    {
        // Get all exams that have draft marks
        $draftExams = Exam::whereHas('marks', function($q) {
            $q->where('status', 'draft');
        })->with(['subject', 'subject.classModel'])->get();
        
        $draftExams->each(function($exam) {
            $exam->draft_marks_count = $exam->marks()->where('status', 'draft')->count();
            $exam->total_marks_count = $exam->marks()->count();
        });
        
        return response()->json($draftExams);
    }

    /**
     * Publish marks for a specific exam
     */
    public function publishExam(Exam $exam)
    {
        $count = Mark::where('exam_id', $exam->id)
            ->where('status', 'draft')
            ->update(['status' => 'published']);
        
        return redirect()->back()
            ->with('success', "Published {$count} marks for {$exam->subject_name}");
    }
}