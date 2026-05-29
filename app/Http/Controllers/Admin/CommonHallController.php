<?php

namespace App\Http\Controllers\Admin;
use App\Models\ExamClass;
use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamHallAllocation;
use App\Models\ExamHallAllocationStudent;
use App\Models\ExamTimetable;
use App\Models\Hall;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class CommonHallController extends Controller
{
    
    public function index()
    {
        $halls = Hall::latest()->get();
        return view('admin.common-halls.index', compact('halls'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'hall_name' => 'required|string|max:255',
            'building'  => 'required|string|max:255',
            'floor'     => 'nullable|string|max:100',
            'rows'      => 'required|integer|min:1',
            'columns'   => 'required|integer|min:1',
            'students_per_table' => 'required|integer|min:1|max:3',
        ]);

        $totalTables = $request->rows * $request->columns;
        $capacity = $totalTables * $request->students_per_table;


        Hall::create([
            'hall_name' => $request->hall_name,
            'building'  => $request->building,
            'floor'     => $request->floor,
            'rows'      => $request->rows,
            'columns'   => $request->columns,
            'students_per_table' => $request->students_per_table,
            'capacity'  => $capacity,
        ]);

        return redirect()->route('admin.halls.index')
                         ->with('success', 'Hall created successfully');
    }
    
    public function edit($id)
    {
        $hall = Hall::findOrFail($id);
        return view('admin.halls.edit', compact('hall'));
    }
    
   public function update(Request $request, $id)
{
    $hall = Hall::findOrFail($id);

    $request->validate([
        'hall_name' => 'required|string|max:255',
        'building'  => 'required|string|max:255',
        'floor'     => 'nullable|string|max:100',
        'rows'      => 'required|integer|min:1',
        'columns'   => 'required|integer|min:1',
        'students_per_table' => 'required|integer|min:1|max:3',
    ]);

    $totalTables = $request->rows * $request->columns;
    $capacity = $totalTables * $request->students_per_table;

    $hall->update([
        'hall_name' => $request->hall_name,
        'building'  => $request->building,
        'floor'     => $request->floor,
        'rows'      => $request->rows,
        'columns'   => $request->columns,
        'students_per_table' => $request->students_per_table,
        'capacity'  => $capacity,
    ]);

    return redirect()->route('admin.halls.index')
                     ->with('success', 'Hall updated successfully');
}

    // ✅ DELETE
    public function destroy($id)
    {
        $hall = Hall::findOrFail($id);
        $hall->delete();

        return redirect()->route('admin.halls.index')
                         ->with('success', 'Hall deleted successfully');
    }

    public function create(Request $request)
{
    $examDates = Exam::whereNotNull('exam_date')
        ->distinct()
        ->orderBy('exam_date')
        ->pluck('exam_date');

    $selectedDate = $request->get('exam_date');

    $exams = Exam::with('subject')
        ->when($selectedDate, function ($query) use ($selectedDate) {
            $query->whereDate('exam_date', $selectedDate);
        })
        ->orderBy('time_session')
        ->get();

    $halls = Hall::all();
    $teachers = Teacher::all();

    $students = collect();
    $selectedExam = null;
    $selectedHall = null;
    $allocatedStudents = collect();
    $allocatedTeacherId = null;
    $existingAllocations = collect();
    $otherExamAllocations = collect();
    $slotTeacher = null;

if ($request->exam_id) {

    $selectedExam = Exam::with('subject')->findOrFail($request->exam_id);

    $classNames = ExamClass::where('exam_id', $selectedExam->id)
        ->pluck('class_name')
        ->toArray();
    dd([
    'selected_exam' => $selectedExam,
    'class_names' => $classNames,

    'students_count' => Student::whereIn('class', $classNames)->count(),

    'students' => Student::whereIn('class', $classNames)
        ->take(10)
        ->get(),
]);

    $examSemester = $selectedExam->sem
        ?? $selectedExam->semester
        ?? $selectedExam->current_semester
        ?? null;

    $allExamAllocationIds = ExamHallAllocation::where('exam_id', $selectedExam->id)
        ->pluck('id');

        $alreadyAllocatedStudentIds = ExamHallAllocationStudent::whereIn('allocation_id', $allExamAllocationIds)
            ->pluck('student_id')
            ->toArray();

        if ($request->hall_id) {
            $selectedHall = Hall::find($request->hall_id);

            $existingHallSessionAllocation = ExamHallAllocation::where('hall_id', $request->hall_id)
                ->where('exam_date', $selectedExam->exam_date)
                ->where('time_session', $selectedExam->time_session)
                ->first();

            if ($existingHallSessionAllocation) {
                $allocatedTeacherId = $existingHallSessionAllocation->teacher_id;
                $slotTeacher = $existingHallSessionAllocation->teacher_id;
            }

            if ($selectedExam->exam_date) {
                $examsOnSameDateSession = Exam::where('exam_date', $selectedExam->exam_date)
                    ->where('time_session', $selectedExam->time_session)
                    ->pluck('id');

                if ($examsOnSameDateSession->isNotEmpty()) {
                    $otherExamHallAllocations = ExamHallAllocation::whereIn('exam_id', $examsOnSameDateSession)
                        ->where('hall_id', $request->hall_id)
                        ->where('exam_id', '!=', $selectedExam->id)
                        ->get();

                    if ($otherExamHallAllocations->isNotEmpty()) {
                        $otherExamAllocationIds = $otherExamHallAllocations->pluck('id');

                        $otherExamAllocations = ExamHallAllocationStudent::whereIn('allocation_id', $otherExamAllocationIds)
                            ->with('student')
                            ->get()
                            ->map(function ($allocation) {
                                return [
                                    'student_id' => $allocation->student_id,
                                    'student_name' => $allocation->student->name ?? 'Occupied',
                                    'student_roll' => $allocation->student->roll_no ?? '',
                                    'table' => $allocation->table_number,
                                    'seat' => $allocation->seat_number,
                                    'exam_id' => $allocation->allocation->exam_id ?? null,
                                    'is_other_exam' => true,
                                ];
                            });
                    }
                }
            }

            $allocation = ExamHallAllocation::where('exam_id', $selectedExam->id)
                ->where('hall_id', $request->hall_id)
                ->first();

            if ($allocation) {
                $allocatedStudents = ExamHallAllocationStudent::with('student')
                    ->where('allocation_id', $allocation->id)
                    ->get();

                if (!$allocatedTeacherId) {
                    $allocatedTeacherId = $allocation->teacher_id;
                }

                $currentHallStudentIds = $allocatedStudents
                    ->pluck('student_id')
                    ->toArray();

                $otherExamStudentIds = $otherExamAllocations
                    ->pluck('student_id')
                    ->toArray();

                $occupiedStudentIds = array_unique(array_merge(
                    $alreadyAllocatedStudentIds,
                    $otherExamStudentIds,
                    $currentHallStudentIds
                ));

                $students = Student::whereIn('class_name', $classNames)
                    ->when($examSemester, function ($query) use ($examSemester) {
                        $query->where('current_semester', $examSemester);
                    })
                    ->whereNotIn('id', $occupiedStudentIds)
                    ->orderBy('roll_no')
                    ->get();

                $currentExamAllocations = $allocatedStudents->map(function ($allocation) {
                    return [
                        'student_id' => $allocation->student_id,
                        'student_name' => $allocation->student->name ?? '',
                        'student_roll' => $allocation->student->roll_no ?? '',
                        'table' => $allocation->table_number,
                        'seat' => $allocation->seat_number,
                        'is_other_exam' => false,
                    ];
                });

                $existingAllocations = $currentExamAllocations->merge($otherExamAllocations);
            } else {
                $otherExamStudentIds = $otherExamAllocations
                    ->pluck('student_id')
                    ->toArray();

                $occupiedStudentIds = array_unique(array_merge(
                    $alreadyAllocatedStudentIds,
                    $otherExamStudentIds
                ));

                $students = Student::whereIn('class_name', $classNames)
                    ->when($examSemester, function ($query) use ($examSemester) {
                        $query->where('current_semester', $examSemester);
                    })
                    ->whereNotIn('id', $occupiedStudentIds)
                    ->orderBy('roll_no')
                    ->get();

                $existingAllocations = $otherExamAllocations;
            }
        } else {
            $students = Student::whereIn('class_name', $classNames)
                ->when($examSemester, function ($query) use ($examSemester) {
                    $query->where('current_semester', $examSemester);
                })
                ->whereNotIn('id', $alreadyAllocatedStudentIds)
                ->orderBy('roll_no')
                ->get();
        }
    }

    return view('admin.exam_allocation.create', compact(
        'examDates',
        'selectedDate',
        'exams',
        'halls',
        'teachers',
        'students',
        'selectedExam',
        'selectedHall',
        'allocatedStudents',
        'allocatedTeacherId',
        'existingAllocations',
        'slotTeacher'
    ));
}

public function storeStudent(Request $request)
{
    $request->validate([
        'exam_id' => 'required|exists:exams,id',
        'hall_id' => 'required|exists:halls,id',
        'teacher_id' => 'required|exists:teachers,id',
        'assignments' => 'required|array'
    ]);
    
    $exam = Exam::findOrFail($request->exam_id);
    
    // Debug: Log what we're trying to save
    \Log::info('=== STORE STUDENT DEBUG ===');
    \Log::info('Exam ID: ' . $exam->id);
    \Log::info('Exam time_session from database: ' . $exam->time_session);
    \Log::info('Hall ID: ' . $request->hall_id);
    \Log::info('Teacher ID: ' . $request->teacher_id);
    
    // Check if time_session is empty
    if (empty($exam->time_session)) {
        \Log::warning('WARNING: Exam time_session is empty!');
        return redirect()->back()
            ->with('error', 'The selected exam does not have a time session assigned. Please set the time session for this exam first.')
            ->withInput();
    }
    
    $existingHallSessionAllocation = ExamHallAllocation::where('hall_id', $request->hall_id)
        ->where('exam_date', $exam->exam_date)
        ->where('time_session', $exam->time_session)
        ->first();

    if ($existingHallSessionAllocation) {
        \Log::info('Found existing allocation for this slot with teacher: ' . $existingHallSessionAllocation->teacher_id);
        
        if ($existingHallSessionAllocation->teacher_id != $request->teacher_id) {
            $teacher = Teacher::find($existingHallSessionAllocation->teacher_id);
            $teacherName = $teacher ? $teacher->name : 'Unknown';
            
            return redirect()->back()
                ->with('error', 'This hall already has an exam scheduled on ' . 
                       \Carbon\Carbon::parse($exam->exam_date)->format('d M Y') . 
                       ' (' . strtoupper($exam->time_session) . ' session) with teacher: ' . $teacherName . '. ' .
                       'All exams in the same hall on the same date and session must use the same teacher.')
                ->withInput();
        }
    }
    
    $allocation = ExamHallAllocation::where('exam_id', $exam->id)
        ->where('hall_id', $request->hall_id)
        ->first();

    if (!$allocation) {
        \Log::info('Creating new allocation with time_session: ' . $exam->time_session);
        
        $allocation = ExamHallAllocation::create([
            'exam_id' => $exam->id,
            'hall_id' => $request->hall_id,
            'teacher_id' => $request->teacher_id,
            'exam_date' => $exam->exam_date,
            'time_session' => $exam->time_session,
        ]);
        
        // Refresh to get the actual saved data
        $allocation->refresh();
        
        \Log::info('Created allocation ID: ' . $allocation->id);
        \Log::info('Saved time_session in DB: ' . $allocation->time_session);
        
        // Double-check if it saved correctly
        if ($allocation->time_session != $exam->time_session) {
            \Log::error('ERROR: time_session was not saved correctly!');
            \Log::error('Expected: ' . $exam->time_session . ', Got: ' . $allocation->time_session);
            
            // Try to force update it
            $allocation->time_session = $exam->time_session;
            $allocation->save();
            $allocation->refresh();
            \Log::info('After force update: ' . $allocation->time_session);
        }
        
    } else {
        \Log::info('Updating existing allocation ID: ' . $allocation->id);
        \Log::info('Old time_session: ' . $allocation->time_session);
        \Log::info('New time_session: ' . $exam->time_session);
        
        $allocation->update([
            'teacher_id' => $request->teacher_id,
            'time_session' => $exam->time_session,
        ]);
        
        $allocation->refresh();
        \Log::info('Updated time_session in DB: ' . $allocation->time_session);
        
        if ($request->has('delete_allocations')) {
            ExamHallAllocationStudent::where('allocation_id', $allocation->id)
                ->whereIn('student_id', $request->delete_allocations)
                ->delete();
        }
    }
    
    // Process assignments
    foreach ($request->assignments as $assignment) {
        [$studentId, $table, $seat] = explode('|', $assignment);

        $exists = ExamHallAllocationStudent::where('allocation_id', $allocation->id)
            ->where('student_id', $studentId)
            ->exists();

        if (!$exists) {
            ExamHallAllocationStudent::create([
                'allocation_id' => $allocation->id,
                'student_id' => $studentId,
                'table_number' => $table,
                'seat_number' => $seat
            ]);
        } else {
            ExamHallAllocationStudent::where('allocation_id', $allocation->id)
                ->where('student_id', $studentId)
                ->update([
                    'table_number' => $table,
                    'seat_number' => $seat
                ]);
        }
    }
    
    \Log::info('=== END STORE STUDENT DEBUG ===');
    
    return redirect()->route('admin.exam-allocation.create', [
        'exam_id' => $request->exam_id,
        'hall_id' => $request->hall_id
    ])->with('success', 'Seat allocation saved successfully!');
}

public function viewAllocationPage($hall_id)
{
    $hall = Hall::findOrFail($hall_id);
    
    // ❌ OLD (wrong)
    // $exams = Exam::with('subject')->get();

    // ✅ NEW (correct)
    $allocatedExamIds = ExamHallAllocation::where('hall_id', $hall_id)
        ->pluck('exam_id')
        ->unique();

    $exams = Exam::with('subject')
        ->whereIn('id', $allocatedExamIds)
        ->orderBy('exam_date', 'desc')
        ->orderBy('time_session')
        ->get();

    return view('admin.exam_allocation.view', compact('hall', 'exams'));
}

public function getAllocationLayout($hallId, $examId)
{
    $hall = Hall::find($hallId);
    $allocation = ExamHallAllocation::where('hall_id', $hallId)
                    ->where('exam_id', $examId)
                    ->first();

    $allocations = [];
    if($allocation){
        foreach($allocation->students as $s){
            $allocations[$s->table_number.'-'.$s->seat_number] = [
                'id' => $s->student->id,
                'roll_no' => $s->student->roll_no,
                'name' => $s->student->name
            ];
        }
    }

    return response()->json([
        'hall' => $hall ? [
            'id' => $hall->id,
            'hall_name' => $hall->hall_name,
            'rows' => $hall->rows,
            'columns' => $hall->columns,
            'students_per_table' => $hall->students_per_table,
            'allocations' => $allocations
        ] : null
    ]);
}
}