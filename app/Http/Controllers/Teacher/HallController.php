<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ExamHallAllocation;
use App\Models\ExamHallAllocationStudent;
use App\Models\Teacher;
use App\Models\Exam;
use App\Models\Hall;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class HallController extends Controller
{




private function getTeacher()
{
    $user = Auth::user();

    if (!$user) {
        return null;
    }

    return Teacher::where('user_id', $user->id)
        ->orWhere('employee_id', $user->employee_id ?? null)
        ->orWhere('email', $user->email)
        ->first();
}
    /**
     * Display a listing of halls allocated to the logged-in teacher.
     */
    public function index(Request $request)
    {
        try {
            // Get the logged-in teacher
            $user = Auth::user();
            
            if (!$user) {
                return redirect()->route('login')
                    ->with('error', 'Please login to continue.');
            }
            
            $teacher = $this->getTeacher();
            
            if (!$teacher) {
                return redirect()->route('teacher.dashboard')
                    ->with('error', 'Teacher profile not found!');
            }

            // Get all allocations for this teacher
            $query = ExamHallAllocation::with(['exam', 'hall', 'students'])
                ->where('teacher_id', $teacher->id)
                ->orderBy('exam_date', 'desc');

            // Apply filters if any
            if ($request->filled('exam_id')) {
                $query->where('exam_id', $request->exam_id);
            }

            if ($request->filled('hall_id')) {
                $query->where('hall_id', $request->hall_id);
            }

            if ($request->filled('date_from')) {
                $query->whereDate('exam_date', '>=', Carbon::parse($request->date_from)->format('Y-m-d'));
            }

            if ($request->filled('date_to')) {
                $query->whereDate('exam_date', '<=', Carbon::parse($request->date_to)->format('Y-m-d'));
            }



            $allocations = $query->paginate(10);

            // Get all exams and halls for filter dropdowns
            $exams = Exam::whereHas('allocations', function($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id);
            })->orderBy('subject_name')->get();

            $halls = Hall::whereHas('allocations', function($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id);
            })->orderBy('hall_name')->get();



            return view('teacher.halls.index', compact('allocations', 'exams', 'halls', 'teacher'));

        } catch (\Exception $e) {
            Log::error('Error in HallController@index: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return redirect()->route('teacher.dashboard')
                ->with('error', 'Error loading allocations: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified hall allocation details.
     */
    public function show($id)
    {
        try {
            // Get the logged-in teacher
            $user = Auth::user();
            
            if (!$user) {
                return redirect()->route('login')
                    ->with('error', 'Please login to continue.');
            }
            
            $teacher = $this->getTeacher();
            
            if (!$teacher) {
                return redirect()->route('teacher.dashboard')
                    ->with('error', 'Teacher profile not found!');
            }

            // Get the allocation with all relationships
            $allocation = ExamHallAllocation::with([
                'exam', 
                'hall',
                'students.student'
            ])
            ->where('id', $id)
            ->where('teacher_id', $teacher->id)
            ->first();

            if (!$allocation) {
                return redirect()->route('teacher.halls.index')
                    ->with('error', 'Allocation not found or access denied.');
            }

            // Get student count
            $totalStudents = $allocation->students->count();
            
            return view('teacher.halls.show', compact('allocation', 'totalStudents', 'teacher'));

        } catch (\Exception $e) {
            Log::error('Error in HallController@show: ' . $e->getMessage());
            
            return redirect()->route('teacher.halls.index')
                ->with('error', 'Error loading allocation details: ' . $e->getMessage());
        }
    }

    /**
     * Display the student list for a specific allocation.
     */
    public function studentList($id)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return redirect()->route('login')
                    ->with('error', 'Please login to continue.');
            }
            
            $teacher = $this->getTeacher();
            
            if (!$teacher) {
                return redirect()->route('teacher.dashboard')
                    ->with('error', 'Teacher profile not found!');
            }

            // Get the allocation
            $allocation = ExamHallAllocation::with(['exam', 'hall'])
                ->where('id', $id)
                ->where('teacher_id', $teacher->id)
                ->first();

            if (!$allocation) {
                return redirect()->route('teacher.halls.index')
                    ->with('error', 'Allocation not found!');
            }

            // Get allocated students with their details
            $allocatedStudents = ExamHallAllocationStudent::with('student')
                ->where('allocation_id', $allocation->id)
                ->orderBy('table_number')
                ->orderBy('seat_number')
                ->get();

            return view('teacher.halls.student-list', compact('allocation', 'allocatedStudents', 'teacher'));

        } catch (\Exception $e) {
            Log::error('Error in HallController@studentList: ' . $e->getMessage());
            
            return redirect()->route('teacher.halls.index')
                ->with('error', 'Error loading student list: ' . $e->getMessage());
        }
    }

    /**
     * Download attendance sheet as PDF.
     */
    public function downloadAttendanceSheet($id)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return redirect()->route('login')
                    ->with('error', 'Please login to continue.');
            }
            
            $teacher = $this->getTeacher();
            
            if (!$teacher) {
                return redirect()->route('teacher.dashboard')
                    ->with('error', 'Teacher profile not found!');
            }

            // Get the allocation
            $allocation = ExamHallAllocation::with(['exam', 'hall'])
                ->where('id', $id)
                ->where('teacher_id', $teacher->id)
                ->first();

            if (!$allocation) {
                return redirect()->route('teacher.halls.index')
                    ->with('error', 'Allocation not found!');
            }

            // Get allocated students
            $allocatedStudents = ExamHallAllocationStudent::with('student')
                ->where('allocation_id', $allocation->id)
                ->orderBy('table_number')
                ->orderBy('seat_number')
                ->get();

            // Prepare data for PDF
            $data = [
                'allocation' => $allocation,
                'students' => $allocatedStudents,
                'teacher' => $teacher,
                'date' => Carbon::now()->format('d-m-Y H:i:s'),
                'logo' => $this->getLogoBase64()
            ];

            // Generate PDF
            $pdf = Pdf::loadView('teacher.halls.attendance_sheet', $data);
            $pdf->setPaper('A4', 'portrait');

            // Generate filename
            $subjectName = $allocation->exam->subject_name ?? 'Exam';
            $filename = 'attendance_sheet_' . 
                        str_replace(' ', '_', $subjectName) . '_' . 
                        str_replace(' ', '_', $allocation->hall->hall_name) . '_' . 
                        Carbon::parse($allocation->exam_date)->format('Y-m-d') . '.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('Error in HallController@downloadAttendanceSheet: ' . $e->getMessage());
            
            return redirect()->route('teacher.halls.index')
                ->with('error', 'Error downloading attendance sheet: ' . $e->getMessage());
        }
    }

    /**
     * Get hall layout for a specific allocation (AJAX).
     */
    public function getHallLayout($id)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            
            $teacher = $this->getTeacher();
            
            if (!$teacher) {
                return response()->json(['error' => 'Teacher not found'], 404);
            }

            $allocation = ExamHallAllocation::with(['hall', 'students.student'])
                ->where('id', $id)
                ->where('teacher_id', $teacher->id)
                ->first();

            if (!$allocation) {
                return response()->json(['error' => 'Allocation not found'], 404);
            }

            $hall = $allocation->hall;
            $allocations = [];

            foreach ($allocation->students as $student) {
                $key = $student->table_number . '-' . $student->seat_number;
                $allocations[$key] = [
                    'id' => $student->student->id,
                    'roll_no' => $student->student->roll_no,
                    'name' => $student->student->name,
                    'class' => $student->student->class ?? 'N/A',
                    'section' => $student->student->section ?? 'N/A'
                ];
            }

            return response()->json([
                'success' => true,
                'hall' => [
                    'id' => $hall->id,
                    'hall_name' => $hall->hall_name,
                    'rows' => $hall->rows,
                    'columns' => $hall->columns,
                    'students_per_table' => $hall->students_per_table,
                    'capacity' => $hall->capacity,
                    'allocations' => $allocations
                ],
                'exam' => [
                    'id' => $allocation->exam->id,
                    'name' => $allocation->exam->subject_name ?? 'Exam',
                    'date' => $allocation->exam_date,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in HallController@getHallLayout: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to load hall layout: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API endpoint to get allocations for AJAX requests
     */
    public function getAllocations(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            
            $teacher = $this->getTeacher();
            
            if (!$teacher) {
                return response()->json(['error' => 'Teacher not found'], 404);
            }

            $query = ExamHallAllocation::with(['exam', 'hall'])
                ->where('teacher_id', $teacher->id);

            // Apply filters
            if ($request->filled('exam_id')) {
                $query->where('exam_id', $request->exam_id);
            }

            if ($request->filled('hall_id')) {
                $query->where('hall_id', $request->hall_id);
            }

            if ($request->filled('date')) {
                $query->whereDate('exam_date', Carbon::parse($request->date)->format('Y-m-d'));
            }


            $allocations = $query->orderBy('exam_date', 'desc')->get();

            return response()->json([
                'success' => true,
                'allocations' => $allocations->map(function($allocation) {
                    return [
                        'id' => $allocation->id,
                        'exam_name' => $allocation->exam->subject_name ?? 'Exam',
                        'hall_name' => $allocation->hall->hall_name,
                        'exam_date' => Carbon::parse($allocation->exam_date)->format('Y-m-d'),
                        'student_count' => $allocation->students()->count()
                    ];
                })
            ]);

        } catch (\Exception $e) {
            Log::error('Error in HallController@getAllocations: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to load allocations: ' . $e->getMessage()
            ], 500);
        }
    }

public function saveAttendance(Request $request, $id)
{
    try {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);
        }

        $teacher = $this->getTeacher();
        if (!$teacher) {
            return response()->json(['success' => false, 'error' => 'Teacher not found'], 404);
        }

        $request->validate([
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|integer',
            'attendance.*.status' => 'required|in:present,absent',
        ]);

        $allocation = ExamHallAllocation::where('id', $id)
            ->where('teacher_id', $teacher->id)
            ->first();

        if (!$allocation) {
            return response()->json(['success' => false, 'error' => 'Allocation not found'], 404);
        }

        $validStudentIds = ExamHallAllocationStudent::where('allocation_id', $allocation->id)
            ->pluck('student_id')
            ->toArray();

        DB::beginTransaction();

        foreach ($request->attendance as $item) {
            if (!in_array($item['student_id'], $validStudentIds)) {
                continue;
            }

            Attendance::updateOrCreate(
                [
                    'allocation_id' => $allocation->id,
                    'student_id' => $item['student_id'],
                ],
                [
                    'teacher_id' => $teacher->id,
                    'exam_id' => $allocation->exam_id,
                    'hall_id' => $allocation->hall_id,
                    'exam_date' => $allocation->exam_date,
                    'status' => $item['status'],
                    'marked_by' => $user->id,
                    'marked_at' => now(),
                ]
            );
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Attendance saved successfully'
        ]);

    } catch (\Throwable $e) {
        DB::rollBack();
        Log::error('Save attendance error: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}

public function getAttendance($id)
{
    try {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);
        }

        $teacher = $this->getTeacher();
        if (!$teacher) {
            return response()->json(['success' => false, 'error' => 'Teacher not found'], 404);
        }

        $allocation = ExamHallAllocation::where('id', $id)
            ->where('teacher_id', $teacher->id)
            ->first();

        if (!$allocation) {
            return response()->json(['success' => false, 'error' => 'Allocation not found'], 404);
        }

        $attendance = Attendance::where('allocation_id', $id)
            ->where('teacher_id', $teacher->id)
            ->get(['student_id', 'status']);

        return response()->json([
            'success' => true,
            'attendance' => $attendance,
        ]);

    } catch (\Throwable $e) {
        Log::error('Get attendance error: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
        ], 500);
    }
}

    /**
     * Debug method to check data
     */
    public function debug()
    {
        $user = Auth::user();
        $teacher = $this->getTeacher();
        
        $data = [
            'user' => $user ? $user->email : 'No user',
            'teacher' => $teacher ? $teacher->name : 'No teacher',
            'teacher_id' => $teacher ? $teacher->id : null,
            'allocations_count' => $teacher ? ExamHallAllocation::where('teacher_id', $teacher->id)->count() : 0,
            'exams_count' => Exam::count(),
            'halls_count' => Hall::count(),
            'all_allocations' => ExamHallAllocation::with(['exam', 'hall'])->get()->toArray(),
        ];
        
        return response()->json($data);
    }

    /**
     * Get logo base64 for PDF.
     */
    private function getLogoBase64()
    {
        try {
            $logoPath = public_path('images/srmlogo1.jpg');
            if (file_exists($logoPath)) {
                $logoData = base64_encode(file_get_contents($logoPath));
                $logoMime = mime_content_type($logoPath);
                return "data:{$logoMime};base64,{$logoData}";
            }
        } catch (\Exception $e) {
            Log::warning('Could not load logo: ' . $e->getMessage());
        }
        
        return '';
    }

    /**
 * Debug endpoint to check student allocations
 */
public function debugAllocation($id)
{
    try {
        $user = Auth::user();
        $teacher = $this->getTeacher();
        
        if (!$teacher) {
            return response()->json(['error' => 'Teacher not found'], 404);
        }

        // Get allocation
        $allocation = ExamHallAllocation::with(['exam', 'hall'])
            ->where('id', $id)
            ->where('teacher_id', $teacher->id)
            ->first();

        if (!$allocation) {
            return response()->json(['error' => 'Allocation not found'], 404);
        }

        // Get allocated students
        $allocatedStudents = ExamHallAllocationStudent::with('student')
            ->where('allocation_id', $allocation->id)
            ->get();

        // Get saved attendance
        $savedAttendance = Attendance::where('allocation_id', $id)
            ->get(['student_id', 'status']);

        return response()->json([
            'allocation' => [
                'id' => $allocation->id,
                'teacher_id' => $allocation->teacher_id,
                'exam_id' => $allocation->exam_id,
                'hall_id' => $allocation->hall_id
            ],
            'allocated_students' => $allocatedStudents->map(function($item) {
                return [
                    'allocation_student_id' => $item->id,
                    'student_id' => $item->student_id,
                    'student_roll_no' => $item->student->roll_no ?? 'N/A',
                    'student_name' => $item->student->name ?? 'N/A',
                    'table_number' => $item->table_number,
                    'seat_number' => $item->seat_number
                ];
            }),
            'saved_attendance' => $savedAttendance,
            'counts' => [
                'allocated' => $allocatedStudents->count(),
                'attendance_saved' => $savedAttendance->count()
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
}

}