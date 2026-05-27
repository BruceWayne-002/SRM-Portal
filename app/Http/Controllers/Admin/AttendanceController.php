<?php
// app/Http/Controllers/Admin/AttendanceController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Exam;
use App\Models\Hall;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display attendance records with filters
     */
    public function index(Request $request)
    {
        $query = Attendance::with(['student', 'teacher', 'exam', 'hall']);

        // Apply filters
        if ($request->filled('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }

        if ($request->filled('hall_id')) {
            $query->where('hall_id', $request->hall_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('exam_date', Carbon::parse($request->date));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('exam_date', '>=', Carbon::parse($request->date_from));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('exam_date', '<=', Carbon::parse($request->date_to));
        }



        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('teacher_id')) {
            $query->where('marked_by', $request->teacher_id);
        }

        if ($request->filled('roll_no')) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('roll_no', 'LIKE', '%' . $request->roll_no . '%');
            });
        }

        if ($request->filled('student_name')) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->student_name . '%');
            });
        }

        // Get statistics
        $statistics = $this->getStatistics($request);

        $attendances = $query->orderBy('exam_date', 'desc')
                            ->paginate(20)
                            ->withQueryString();

        $exams = Exam::orderBy('exam_date', 'desc')->get();
        $halls = Hall::orderBy('hall_name')->get();
        $teachers = User::where('role', 'teacher')->orderBy('name')->get();

        return view('admin.attendance.index', compact(
            'attendances', 
            'exams', 
            'halls', 
            'teachers', 
            'statistics'
        ));
    }

    /**
     * Get attendance statistics based on filters
     */
    private function getStatistics($request)
    {
        $query = Attendance::query();

        if ($request->filled('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('exam_date', Carbon::parse($request->date));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('exam_date', '>=', Carbon::parse($request->date_from));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('exam_date', '<=', Carbon::parse($request->date_to));
        }

        return [
            'total' => $query->count(),
            'present' => (clone $query)->where('status', 'present')->count(),
            'absent' => (clone $query)->where('status', 'absent')->count(),
            'late' => (clone $query)->where('status', 'late')->count(),
            'present_percentage' => $query->count() > 0 
                ? round(((clone $query)->where('status', 'present')->count() / $query->count()) * 100, 2)
                : 0
        ];
    }

    /**
     * Export attendance to PDF
     */
    public function exportPdf(Request $request)
    {
        $query = Attendance::with(['student', 'teacher', 'exam', 'hall']);

        // Apply same filters as index
        if ($request->filled('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }

        if ($request->filled('hall_id')) {
            $query->where('hall_id', $request->hall_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('exam_date', Carbon::parse($request->date));
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('exam_date', [
                Carbon::parse($request->date_from),
                Carbon::parse($request->date_to)
            ]);
        }


        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->orderBy('exam_date', 'desc')
                            ->get();

        $statistics = $this->getStatistics($request);
        
        $filters = $request->all();
        
        $pdf = Pdf::loadView('admin.attendance.pdf', compact('attendances', 'statistics', 'filters'));
        
        return $pdf->download('attendance_report_' . Carbon::now()->format('Y-m-d_H-i-s') . '.pdf');
    }

    /**
     * Show attendance for a specific exam
     */
    public function examAttendance($examId)
    {
        $exam = Exam::with(['hall', 'students'])->findOrFail($examId);
        
        $attendances = Attendance::with(['student', 'teacher'])
            ->where('exam_id', $examId)
            ->get();

        $statistics = [
            'total' => $attendances->count(),
            'present' => $attendances->where('status', 'present')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
            'late' => $attendances->where('status', 'late')->count(),
        ];

        return view('admin.attendance.exam', compact('exam', 'attendances', 'statistics'));
    }

    /**
     * Export exam attendance to PDF
     */
    public function exportExamPdf($examId)
    {
        $exam = Exam::with(['hall', 'students'])->findOrFail($examId);
        
        $attendances = Attendance::with(['student', 'teacher'])
            ->where('exam_id', $examId)
            ->get();

        $statistics = [
            'total' => $attendances->count(),
            'present' => $attendances->where('status', 'present')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
            'late' => $attendances->where('status', 'late')->count(),
        ];

        $pdf = Pdf::loadView('admin.attendance.exam-pdf', compact('exam', 'attendances', 'statistics'));
        
        return $pdf->download('exam_attendance_' . $exam->subject_name . '_' . Carbon::now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Show all attendance records (alternative view)
     */
    public function allAttendance(Request $request)
    {
        $query = Attendance::with(['student', 'teacher', 'exam', 'hall']);

        // Apply date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('exam_date', '>=', Carbon::parse($request->date_from));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('exam_date', '<=', Carbon::parse($request->date_to));
        }

        $attendances = $query->orderBy('exam_date', 'desc')
                            ->paginate(50)
                            ->withQueryString();

        return view('admin.attendance.all', compact('attendances'));
    }

    /**
     * Clear all filters
     */
    public function clearFilters()
    {
        return redirect()->route('admin.attendance.index');
    }
}