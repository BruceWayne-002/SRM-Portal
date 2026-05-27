<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamHallAllocation;
use App\Models\Teacher;
use App\Models\Hall;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TimetableController extends Controller
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

    public function teacherTimetable(Request $request)
    {
        try {
            $teacher = $this->getTeacher();

            if (!$teacher) {
                return redirect()->route('teacher.dashboard')
                    ->with('error', 'Teacher profile not found!');
            }

            $filter = $request->get('filter', 'upcoming');
            $hallId = $request->get('hall_id');
            $date = $request->get('date');

            $query = ExamHallAllocation::with(['exam', 'hall', 'exam.subject'])
                ->where('teacher_id', $teacher->id);

            switch ($filter) {
                case 'today':
                    $query->whereDate('exam_date', Carbon::today());
                    break;

                case 'week':
                    $query->whereBetween('exam_date', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ]);
                    break;

                case 'month':
                    $query->whereMonth('exam_date', Carbon::now()->month)
                        ->whereYear('exam_date', Carbon::now()->year);
                    break;

                case 'upcoming':
                    $query->where('exam_date', '>=', Carbon::today());
                    break;

                case 'completed':
                    $query->where('exam_date', '<', Carbon::today());
                    break;

                case 'all':
                    break;
            }

            if ($hallId) {
                $query->where('hall_id', $hallId);
            }

            if ($date) {
                $query->whereDate('exam_date', Carbon::parse($date)->format('Y-m-d'));
            }

            $allocations = $query->orderBy('exam_date', 'asc')
                ->orderBy('exam_time', 'asc')
                ->get();

            $halls = Hall::whereHas('allocations', function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id);
            })->orderBy('hall_name')->get();

            $stats = [
                'total' => ExamHallAllocation::where('teacher_id', $teacher->id)->count(),
                'today' => ExamHallAllocation::where('teacher_id', $teacher->id)
                    ->whereDate('exam_date', Carbon::today())
                    ->count(),
                'upcoming' => ExamHallAllocation::where('teacher_id', $teacher->id)
                    ->where('exam_date', '>=', Carbon::today())
                    ->count(),
                'completed' => ExamHallAllocation::where('teacher_id', $teacher->id)
                    ->where('exam_date', '<', Carbon::today())
                    ->count(),
                'this_week' => ExamHallAllocation::where('teacher_id', $teacher->id)
                    ->whereBetween('exam_date', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ])
                    ->count(),
                'this_month' => ExamHallAllocation::where('teacher_id', $teacher->id)
                    ->whereMonth('exam_date', Carbon::now()->month)
                    ->whereYear('exam_date', Carbon::now()->year)
                    ->count(),
            ];

            return view('teacher.timetable.index', compact(
                'allocations',
                'halls',
                'stats',
                'teacher',
                'filter',
                'hallId',
                'date'
            ));

        } catch (\Exception $e) {
            Log::error('Error in teacherTimetable: ' . $e->getMessage());

            return redirect()->route('teacher.dashboard')
                ->with('error', 'Error loading timetable: ' . $e->getMessage());
        }
    }

    public function allocationDetails($id)
    {
        try {
            $teacher = $this->getTeacher();

            if (!$teacher) {
                return response()->json([
                    'success' => false,
                    'error' => 'Teacher not found'
                ], 404);
            }

            $allocation = ExamHallAllocation::with(['exam', 'hall', 'exam.subject', 'students.student'])
                ->where('id', $id)
                ->where('teacher_id', $teacher->id)
                ->first();

            if (!$allocation) {
                return response()->json([
                    'success' => false,
                    'error' => 'Allocation not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'allocation' => $allocation
            ]);

        } catch (\Exception $e) {
            Log::error('Error in allocationDetails: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function examDetails($id)
    {
        try {
            $teacher = $this->getTeacher();

            if (!$teacher) {
                return response()->json([
                    'success' => false,
                    'error' => 'Teacher not found'
                ], 404);
            }

            $exam = Exam::with(['subject', 'subject.classModel'])
                ->where('id', $id)
                ->first();

            if (!$exam) {
                return response()->json([
                    'success' => false,
                    'error' => 'Exam not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'exam' => $exam
            ]);

        } catch (\Exception $e) {
            Log::error('Error in examDetails: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}