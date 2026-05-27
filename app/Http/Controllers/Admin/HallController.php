<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamHall;
use App\Models\Exam;
use App\Models\Teacher;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HallController extends Controller
{
    public function index()
    {
        $classes = ClassModel::withCount('students')
            ->ordered()
            ->paginate(10);
        return view('admin.halls.index', compact('classes'));
    }

    public function create(Request $request)
{
    // Check if there are any classes first
    $class = ClassModel::first();
    
    if (!$class) {
        return redirect()->route('admin.classes.index')
            ->with('error', 'Please create a class first before creating exam halls.');
    }
    
    // Get all active teachers
    $teachers = Teacher::where('status', true)
        ->orderBy('name')
        ->get();
    
    // Get all available exam dates for filter
    $availableDates = Exam::where('exam_date', '>=', Carbon::today())
        ->select('exam_date')
        ->distinct()
        ->orderBy('exam_date')
        ->get()
        ->map(function($item) {
            return [
                'date' => $item->exam_date->format('Y-m-d'),
                'formatted' => $item->exam_date->format('d M Y')
            ];
        });
    
    // Get selected date and session from request
    $selectedDate = $request->get('exam_date');
    $selectedSession = $request->get('session');
    
    // Build the exams query
    $examsQuery = Exam::where('exam_date', '>=', Carbon::today())
        ->where(function($query) {
            $query->whereNull('hall_id')
                ->orWhere('hall_id', 0)
                ->orWhere(function($q) {
                    $q->whereNotNull('hall_id')
                      ->where('remaining_students', '>', 0);
                });
        })
        ->with(['subject.classModel.students'])
        ->orderBy('exam_date')
        ->orderBy('exam_time');
    
    // Apply date filter if selected
    if ($selectedDate) {
        $examsQuery->whereDate('exam_date', $selectedDate);
    }
    
    $exams = $examsQuery->get();
    
    // Group exams by date and session
    $groupedExams = $exams->groupBy(function($exam) {
        $time = Carbon::parse($exam->exam_time);
        $hour = (int)$time->format('H');
        
        // Determine session based on hour
        if ($hour >= 6 && $hour < 12) {
            $session = 'FN';
        } elseif ($hour >= 12 && $hour < 18) {
            $session = 'AN';
        } else {
            $session = 'EV';
        }
        
        return $exam->exam_date->format('Y-m-d') . '_' . $session;
    });
    
    // Filter by selected session if provided
    if ($selectedSession) {
        $filteredGroups = [];
        foreach ($groupedExams as $key => $group) {
            if (str_contains($key, '_' . $selectedSession)) {
                $filteredGroups[$key] = $group;
            }
        }
        $groupedExams = collect($filteredGroups);
    }
    
    // Filter groups with 1-6 exams
    $availableGroups = $groupedExams->filter(function($group) {
        return $group->count() >= 1 && $group->count() <= 6;
    });
    
    return view('admin.halls.create', compact(
        'class', 
        'teachers', 
        'availableGroups', 
        'availableDates',
        'selectedDate',
        'selectedSession'
    ));
}

    public function store(Request $request)
{
    $validated = $request->validate([
        'room_number' => 'required|string|max:255',
        'capacity' => 'required|integer|min:1',
        'rows' => 'required|integer|min:1|max:50',
        'columns' => 'required|integer|min:1|max:20',
        'building' => 'required|string|max:100',
        'floor' => 'required|string|max:50',
        'teacher_id' => 'required|exists:teachers,id',
        'exam_ids' => 'required|array|min:1|max:6',
        'exam_ids.*' => 'exists:exams,id',
    ]);

    // Validate capacity vs rows*columns
    if ($request->capacity > ($request->rows * $request->columns)) {
        return back()->withErrors([
            'capacity' => 'Capacity cannot exceed total seats (' . ($request->rows * $request->columns) . ')'
        ])->withInput();
    }

    // Get all selected exams with their current allocation status
    $exams = Exam::whereIn('id', $request->exam_ids)
        ->with(['subject.classModel.students'])
        ->get();
    
    if ($exams->isEmpty()) {
        return back()->withErrors(['exam_ids' => 'No exams selected.'])->withInput();
    }

    // Check if we have manual allocations from the form
    $hasManualAllocations = $request->has('allocations') && is_array($request->allocations);
    
    if ($hasManualAllocations) {
        // Validate manual allocations
        $totalAllocatedStudents = 0;
        foreach ($request->allocations as $examId => $allocatedCount) {
            $exam = $exams->firstWhere('id', $examId);
            if ($exam) {
                $remainingStudents = $exam->remaining_students ?? $exam->student_count ?? 0;
                if ($allocatedCount > $remainingStudents) {
                    return back()->withErrors([
                        'allocations' => "Cannot allocate {$allocatedCount} students from {$exam->subject->name}. Only {$remainingStudents} students available."
                    ])->withInput();
                }
                $totalAllocatedStudents += $allocatedCount;
            }
        }
        
        $allocatedStudents = $totalAllocatedStudents;
    } else {
        // Calculate remaining students from selected exams
        $totalRemainingStudents = $exams->sum(function($exam) {
            return $exam->remaining_students ?? $exam->student_count ?? 0;
        });

        if ($totalRemainingStudents <= 0) {
            return back()->withErrors([
                'exam_ids' => 'Selected exams have no remaining students to allocate.'
            ])->withInput();
        }

        // Calculate allocated students (min of capacity and total remaining students)
        $allocatedStudents = min($request->capacity, $totalRemainingStudents);
    }

    // Validate that all selected exams have same date and time
    $firstExam = $exams->first();
    $examDate = $firstExam->exam_date;
    $examTime = $firstExam->exam_time;
    
    foreach ($exams as $exam) {
        if ($exam->exam_date->format('Y-m-d') != $examDate->format('Y-m-d')) {
            return back()->withErrors(['exam_ids' => 'All selected exams must be on the same date.'])->withInput();
        }
        
        if ($exam->exam_time != $examTime) {
            return back()->withErrors(['exam_ids' => 'All selected exams must be at the same time.'])->withInput();
        }
        
        // Check if exam is already assigned to another hall and has no remaining students
        if ($exam->hall_id && $exam->hall_id != 0 && $exam->remaining_students <= 0) {
            return back()->withErrors(['exam_ids' => "Exam for {$exam->subject->name} is already fully allocated to another hall."])->withInput();
        }
    }

    // Calculate total remaining students and remaining after allocation
    $totalRemainingStudents = $exams->sum(function($exam) {
        return $exam->remaining_students ?? $exam->student_count ?? 0;
    });
    
    $remainingAfterAllocation = $totalRemainingStudents - $allocatedStudents;
    
    // Determine allocation status for this hall
    $allocationStatus = 'complete';
    if ($remainingAfterAllocation > 0) {
        $allocationStatus = 'partial';
    }

    // Check teacher availability
    $availabilityCheck = $this->checkTeacherAvailability(
        $request->teacher_id, 
        $examDate, 
        $examTime,
        null
    );
    
    if (!$availabilityCheck['available']) {
        return back()->withErrors([
            'teacher_id' => $availabilityCheck['message']
        ])->withInput();
    }

    // Generate hall code
    $hallCode = 'HALL_' . strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $request->room_number)) . '_' . time();
    
    // Create exam hall
    $examHall = ExamHall::create([
        'hall_name' => $request->room_number . ' Exam Hall',
        'hall_code' => $hallCode,
        'capacity' => $request->capacity,
        'rows' => $request->rows,
        'columns' => $request->columns,
        'building' => $request->building,
        'floor' => $request->floor,
        'teacher_id' => $request->teacher_id,
        'exam_ids' => json_encode($request->exam_ids),
        'total_students' => $totalRemainingStudents,
        'allocated_students' => $allocatedStudents,
        'remaining_students' => $remainingAfterAllocation,
        'allocation_status' => $allocationStatus,
        'exam_date' => $examDate,
        'start_time' => $examTime,
        'end_time' => Carbon::parse($examTime)->addHours(2)->format('H:i:s'),
        'exam_type' => $firstExam->exam_type,
        'school_code' => auth()->user()->school_code ?? 'SCHOOL',
    ]);

    // Update exams with hall_id and track allocated/remaining students
    DB::transaction(function() use ($request, $examHall, $exams, $allocatedStudents, $hasManualAllocations) {
        if ($hasManualAllocations) {
            // Manual allocation mode
            foreach ($exams as $exam) {
                $examId = $exam->id;
                $allocateForThisExam = $request->allocations[$examId] ?? 0;
                
                if ($allocateForThisExam > 0) {
                    $examRemaining = $exam->remaining_students ?? $exam->student_count ?? 0;
                    $newRemaining = $examRemaining - $allocateForThisExam;
                    $allocationStatus = $newRemaining > 0 ? 'partial' : 'complete';
                    
                    // Update exam with allocation details
                    $exam->update([
                        'hall_id' => $examHall->id,
                        'allocated_students' => ($exam->allocated_students ?? 0) + $allocateForThisExam,
                        'remaining_students' => $newRemaining,
                        'allocation_status' => $allocationStatus
                    ]);
                }
            }
        } else {
            // Auto allocation mode
            $remainingToAllocate = $allocatedStudents;
            
            foreach ($exams as $exam) {
                if ($remainingToAllocate <= 0) break;
                
                $examRemaining = $exam->remaining_students ?? $exam->student_count ?? 0;
                $allocateForThisExam = min($examRemaining, $remainingToAllocate);
                
                $newRemaining = $examRemaining - $allocateForThisExam;
                $allocationStatus = $newRemaining > 0 ? 'partial' : 'complete';
                
                // Update exam with allocation details
                $exam->update([
                    'hall_id' => $examHall->id,
                    'allocated_students' => ($exam->allocated_students ?? 0) + $allocateForThisExam,
                    'remaining_students' => $newRemaining,
                    'allocation_status' => $allocationStatus
                ]);
                
                $remainingToAllocate -= $allocateForThisExam;
            }
        }
    });

    return redirect()->route('admin.halls.seating', ['hall' => $examHall->id])
        ->with('success', 'Exam hall created successfully! Allocated ' . $allocatedStudents . ' out of ' . $totalRemainingStudents . ' students. Please assign seats.');
}

    public function show(ExamHall $hall)
    {
        // Load relationships
        $hall->load(['teacher']);
        
        // Get assigned exams
        $examIds = json_decode($hall->exam_ids, true) ?? [];
        $assignedExams = Exam::whereIn('id', $examIds)
            ->with(['subject', 'class' => function($query) {
                $query->withCount('students');
            }])
            ->get();
        
        // Calculate total students from this hall's allocation
        $totalStudents = $hall->allocated_students ?? 0;
        
        // Generate seating data for view
        $seatingArrangement = $this->generateSeatingArrangement($hall);
        
        return view('admin.halls.show', compact('hall', 'assignedExams', 'seatingArrangement', 'totalStudents'));
    }

    public function edit(ExamHall $hall)
    {
        // Get all active teachers
        $teachers = Teacher::where('status', true)
            ->orderBy('name')
            ->get();
        
        // Get assigned exam IDs
        $assignedExamIds = json_decode($hall->exam_ids, true) ?? [];
        
        // Get currently assigned exams
        $assignedExams = Exam::whereIn('id', $assignedExamIds)
            ->with(['subject', 'class'])
            ->get();
        
        // Get all available exams (including partially allocated ones)
        $availableExams = Exam::where('exam_date', $hall->exam_date)
            ->where('exam_time', $hall->start_time)
            ->where(function($query) use ($hall, $assignedExamIds) {
                $query->whereNull('hall_id')
                    ->orWhere('hall_id', 0)
                    ->orWhere('hall_id', $hall->id)
                    ->orWhereIn('id', $assignedExamIds)
                    ->orWhere(function($q) {
                        $q->whereNotNull('hall_id')
                          ->where('remaining_students', '>', 0); // Partially allocated exams
                    });
            })
            ->with(['subject', 'class'])
            ->orderBy('exam_date')
            ->orderBy('exam_time')
            ->get()
            ->unique('id'); // Remove duplicates
        
        // Group by date/time
        $groupedExams = $availableExams->groupBy(function($exam) {
            return $exam->exam_date->format('Y-m-d') . '_' . $exam->exam_time;
        });
        
        // Filter groups with 1-6 exams
        $availableGroups = $groupedExams->filter(function($group) use ($assignedExamIds) {
            $groupIds = $group->pluck('id')->toArray();
            $assignedInGroup = array_intersect($groupIds, $assignedExamIds);
            $totalCount = count($group);
            
            return $totalCount >= 1 && $totalCount <= 6;
        });

        return view('admin.halls.edit', compact(
            'hall', 
            'teachers', 
            'assignedExams', 
            'availableGroups', 
            'assignedExamIds'
        ));
    }

    public function update(Request $request, ExamHall $hall)
    {
        $validated = $request->validate([
            'room_number' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'rows' => 'required|integer|min:1|max:50',
            'columns' => 'required|integer|min:1|max:20',
            'building' => 'required|string|max:100',
            'floor' => 'required|string|max:50',
            'teacher_id' => 'required|exists:teachers,id',
            'exam_ids' => 'required|array|min:1|max:6',
            'exam_ids.*' => 'exists:exams,id',
        ]);

        // Validate capacity vs rows*columns
        if ($request->capacity > ($request->rows * $request->columns)) {
            return back()->withErrors([
                'capacity' => 'Capacity cannot exceed total seats (' . ($request->rows * $request->columns) . ')'
            ])->withInput();
        }

        // Get all selected exams
        $exams = Exam::whereIn('id', $request->exam_ids)
            ->with(['class'])
            ->get();
        
        if ($exams->isEmpty()) {
            return back()->withErrors(['exam_ids' => 'No exams selected.'])->withInput();
        }

        // Calculate remaining students from selected exams
        $totalRemainingStudents = $exams->sum(function($exam) use ($hall) {
            // If exam is already assigned to this hall, use its remaining students
            if ($exam->hall_id == $hall->id) {
                return $exam->remaining_students ?? 0;
            }
            // Otherwise, use the full remaining count
            return $exam->remaining_students ?? $exam->student_count ?? 0;
        });

        // Validate total students against hall capacity
        if ($totalRemainingStudents > $request->capacity) {
            return back()->withErrors([
                'capacity' => 'Total remaining students from selected exams (' . $totalRemainingStudents . ') exceeds hall capacity (' . $request->capacity . '). Please adjust exam selection or increase hall capacity.'
            ])->withInput();
        }

        // Validate that all selected exams have same date and time
        $firstExam = $exams->first();
        $examDate = $firstExam->exam_date;
        $examTime = $firstExam->exam_time;
        
        foreach ($exams as $exam) {
            if ($exam->exam_date->format('Y-m-d') != $examDate->format('Y-m-d')) {
                return back()->withErrors(['exam_ids' => 'All selected exams must be on the same date.'])->withInput();
            }
            
            if ($exam->exam_time != $examTime) {
                return back()->withErrors(['exam_ids' => 'All selected exams must be at the same time.'])->withInput();
            }
            
            // Check if exam is already assigned to another hall and has no remaining students
            if ($exam->hall_id && $exam->hall_id != $hall->id && $exam->hall_id != 0 && $exam->remaining_students <= 0) {
                return back()->withErrors(['exam_ids' => "Exam for {$exam->subject->name} is already fully allocated to another hall."])->withInput();
            }
        }

        // Check teacher availability
        $availabilityCheck = $this->checkTeacherAvailability(
            $request->teacher_id, 
            $examDate, 
            $examTime,
            $hall->id
        );
        
        if (!$availabilityCheck['available']) {
            return back()->withErrors([
                'teacher_id' => $availabilityCheck['message']
            ])->withInput();
        }

        // Get previous exam IDs
        $previousExamIds = json_decode($hall->exam_ids, true) ?? [];
        
        // Update exam hall
        $hall->update([
            'hall_name' => $request->room_number . ' Exam Hall',
            'capacity' => $request->capacity,
            'rows' => $request->rows,
            'columns' => $request->columns,
            'building' => $request->building,
            'floor' => $request->floor,
            'teacher_id' => $request->teacher_id,
            'exam_ids' => json_encode($request->exam_ids),
            'total_students' => $totalRemainingStudents,
            'allocated_students' => $totalRemainingStudents, // All remaining students fit in capacity
            'remaining_students' => 0,
            'allocation_status' => 'complete',
            'exam_date' => $examDate,
            'start_time' => $examTime,
            'end_time' => Carbon::parse($examTime)->addHours(2)->format('H:i:s'),
            'exam_type' => $firstExam->exam_type,
        ]);

// Handle exam allocations in transaction
DB::transaction(function() use ($previousExamIds, $request, $hall) {
    // Get current exam IDs from request
    $newExamIds = $request->exam_ids;
    
    // 1. Remove exams that are no longer selected
    $examsToRemove = array_diff($previousExamIds, $newExamIds);
    if (!empty($examsToRemove)) {
        $exams = Exam::whereIn('id', $examsToRemove)
            ->where('hall_id', $hall->id)
            ->get();
            
        foreach ($exams as $exam) {
            $studentCount = $exam->student_count ?? 0;
            $exam->update([
                'hall_id' => null,
                'allocated_students' => 0,
                'remaining_students' => $studentCount,
                'allocation_status' => 'pending'
            ]);
        }
    }

    // 2. Add newly selected exams
    $examsToAdd = array_diff($newExamIds, $previousExamIds);
    if (!empty($examsToAdd)) {
        $exams = Exam::whereIn('id', $examsToAdd)->get();
        foreach ($exams as $exam) {
            $studentCount = $exam->student_count ?? 0;
            $remainingStudents = $exam->remaining_students ?? $studentCount;
            
            // Check if this hall has enough capacity
            $allocatedStudents = min($remainingStudents, $hall->capacity);
            $newRemaining = $remainingStudents - $allocatedStudents;
            $allocationStatus = $newRemaining > 0 ? 'partial' : 'complete';
            
            $exam->update([
                'hall_id' => $hall->id,
                'allocated_students' => ($exam->allocated_students ?? 0) + $allocatedStudents,
                'remaining_students' => $newRemaining,
                'allocation_status' => $allocationStatus
            ]);
        }
    }
    
    // 3. Update exams that remain in the hall (if capacity changed)
    $examsToKeep = array_intersect($previousExamIds, $newExamIds);
    if (!empty($examsToKeep) && $hall->wasChanged('capacity')) {
        $exams = Exam::whereIn('id', $examsToKeep)
            ->where('hall_id', $hall->id)
            ->get();
            
        foreach ($exams as $exam) {
            $studentCount = $exam->student_count ?? 0;
            $currentAllocated = $exam->allocated_students ?? 0;
            
            // If capacity decreased, we need to reduce allocation
            if ($hall->capacity < $currentAllocated) {
                $exam->update([
                    'allocated_students' => $hall->capacity,
                    'remaining_students' => $studentCount - $hall->capacity,
                    'allocation_status' => 'partial'
                ]);
            }
        }
    }
});

        return redirect()->route('admin.halls.storedhalls')
            ->with('success', 'Exam hall updated successfully! Allocated ' . $totalRemainingStudents . ' students.');
    }

    public function destroy(ExamHall $hall)
{
    // Get assigned exam IDs
    $examIds = json_decode($hall->exam_ids, true) ?? [];

    // Clear hall_id from exams and reset allocation
    if (!empty($examIds)) {
        $exams = Exam::whereIn('id', $examIds)->get();
        foreach ($exams as $exam) {
            $studentCount = $exam->student_count ?? 0;
            $exam->update([
                'hall_id' => null,
                'allocated_students' => 0,
                'remaining_students' => $studentCount,
                'allocation_status' => 'pending'
            ]);
        }
    }

    // Delete hall
    $hall->delete();

    return redirect()->route('admin.halls.storedhalls')
        ->with('success', 'Exam hall deleted successfully and exams unassigned!');
}
    public function unavailableTeachers(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'time' => 'required'
        ]);
        
        $date = Carbon::parse($request->date);
        $time = $request->time;
        $excludeHallId = $request->exclude ?? null;
        
        $availabilityCheck = $this->checkTeacherAvailability(null, $date, $time, $excludeHallId);
        
        return response()->json([
            'unavailableTeachers' => $availabilityCheck['unavailable_teachers'] ?? [],
            'timeWindow' => $availabilityCheck['time_window'] ?? null,
            'message' => $availabilityCheck['message'] ?? null
        ]);
    }
    
    public function hallindex()
    {
        $halls = ExamHall::with('teacher')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('admin.halls.storedhalls', compact('halls'));
    }

    /**
     * Check teacher availability for a given date and time
     */
    private function checkTeacherAvailability($teacherId, $examDate, $examTime, $excludeHallId = null)
    {
        $selectedTime = Carbon::parse($examTime);
        $fourHoursBefore = $selectedTime->copy()->subHours(4)->format('H:i:s');
        $fourHoursAfter = $selectedTime->copy()->addHours(4)->format('H:i:s');
        
        // Get all conflicting halls within the 4-hour window
        $query = ExamHall::where('exam_date', $examDate->format('Y-m-d'))
            ->where(function($query) use ($fourHoursBefore, $fourHoursAfter) {
                $query->whereBetween('start_time', [$fourHoursBefore, $fourHoursAfter])
                    ->orWhereBetween('end_time', [$fourHoursBefore, $fourHoursAfter])
                    ->orWhere(function($q) use ($fourHoursBefore, $fourHoursAfter) {
                        $q->where('start_time', '<=', $fourHoursBefore)
                          ->where('end_time', '>=', $fourHoursAfter);
                    });
            });
        
        if ($excludeHallId) {
            $query->where('id', '!=', $excludeHallId);
        }
        
        $conflictingHalls = $query->with('teacher')->get();
        
        // Get unavailable teacher IDs
        $unavailableTeacherIds = $conflictingHalls->pluck('teacher_id')->unique()->values()->toArray();
        
        // If checking specific teacher
        if ($teacherId) {
            $teacherConflict = $conflictingHalls->where('teacher_id', $teacherId)->first();
            
            if ($teacherConflict) {
                return [
                    'available' => false,
                    'message' => 'This teacher is already assigned to an exam hall (' . 
                               $teacherConflict->hall_name . ') on ' . 
                               $examDate->format('d M Y') . ' at ' . 
                               Carbon::parse($teacherConflict->start_time)->format('h:i A') . 
                               ' (within 4-hour window)',
                    'conflicting_hall' => $teacherConflict
                ];
            }
            
            return ['available' => true];
        }
        
        return [
            'available' => true,
            'unavailable_teachers' => $unavailableTeacherIds,
            'time_window' => [
                'start' => $fourHoursBefore,
                'end' => $fourHoursAfter,
                'selected' => $examTime
            ],
            'conflicting_halls' => $conflictingHalls
        ];
    }
    
    public function seatingArrangement(ExamHall $hall)
    {
        $seatingArrangement = $this->generateSeatingArrangement($hall);
        
        return view('admin.halls.seating', compact('hall', 'seatingArrangement'));
    }

    private function generateSeatingArrangement(ExamHall $hall)
    {
        $seats = [];
        $alphabet = range('A', 'Z');
        
        for ($row = 0; $row < $hall->rows; $row++) {
            for ($col = 1; $col <= $hall->columns; $col++) {
                $seatNumber = $row * $hall->columns + $col;
                $seatPosition = $alphabet[$row] . $col;
                $seats[] = [
                    'seat_number' => $seatNumber,
                    'seat_position' => $seatPosition,
                    'row' => $alphabet[$row],
                    'column' => $col,
                    'row_index' => $row,
                    'col_index' => $col
                ];
            }
        }
        
        return $seats;
    }

    /**
 * API endpoint to check remaining students for selected exams
 */
public function checkRemainingStudents(Request $request)
{
    $request->validate([
        'exam_ids' => 'required|array',
        'exam_ids.*' => 'exists:exams,id',
        'hall_id' => 'nullable|exists:exam_halls,id'
    ]);

    $exams = Exam::whereIn('id', $request->exam_ids)
        ->with(['subject.classModel'])
        ->get();

    $totalRemainingStudents = $exams->sum(function($exam) use ($request) {
        // If exam is already assigned to this hall (for edit), use its remaining students
        if ($request->hall_id && $exam->hall_id == $request->hall_id) {
            return $exam->remaining_students ?? 0;
        }
        // Otherwise, if exam is assigned to another hall, only count remaining
        if ($exam->hall_id && $exam->hall_id != $request->hall_id) {
            return $exam->remaining_students ?? 0;
        }
        // New exam - use full student count
        return $exam->student_count ?? 0;
    });

    $examDetails = $exams->map(function($exam) use ($request) {
        $remainingStudents = 0;
        $allocatedStudents = $exam->allocated_students ?? 0;
        $totalStudents = $exam->student_count ?? 0;
        
        if ($request->hall_id && $exam->hall_id == $request->hall_id) {
            $remainingStudents = $exam->remaining_students ?? 0;
        } elseif ($exam->hall_id && $exam->hall_id != $request->hall_id) {
            $remainingStudents = $exam->remaining_students ?? 0;
        } else {
            $remainingStudents = $totalStudents;
        }
        
        return [
            'id' => $exam->id,
            'subject' => $exam->subject->name ?? 'N/A',
            'class' => $exam->subject->classModel->full_name ?? $exam->subject->classModel->name ?? 'N/A',
            'total_students' => $totalStudents,
            'already_allocated' => $allocatedStudents,
            'remaining_students' => $remainingStudents,
            'hall_id' => $exam->hall_id,
            'allocation_status' => $exam->allocation_status ?? 'pending'
        ];
    });

    return response()->json([
        'total_remaining_students' => $totalRemainingStudents,
        'exam_count' => $exams->count(),
        'exams' => $examDetails
    ]);
}
}