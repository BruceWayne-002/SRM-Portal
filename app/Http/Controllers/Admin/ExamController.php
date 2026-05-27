<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Subject;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $query = Exam::with('subject');
        
        // Apply filters
        if ($request->has('filter')) {
            switch($request->filter) {
                case 'upcoming':
                    $query->upcoming();
                    break;
                case 'today':
                    $query->today();
                    break;
                case 'this-week':
                    $query->thisWeek();
                    break;
                case 'past':
                    $query->past();
                    break;
            }
        }
        
        $exams = $query->orderBy('exam_date', 'desc')
                      ->orderBy('exam_time', 'desc')
                      ->paginate(10);
            
        return view('admin.exams.index', compact('exams'));
    }

    public function create()
    {
        // Get all subjects with necessary fields
        $subjects = Subject::orderBy('class_name')
            ->orderBy('section')
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'class_name', 'section', 'duration_hours', 'semester']);
            
        return view('admin.exams.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'exam_type' => 'required|in:midterm,final,quiz,assignment',
            'exam_date' => 'required|date|after_or_equal:today',
            'exam_time' => 'required|date_format:H:i',
            'time_session' => 'required|in:FN,AN',
            'duration_minutes' => 'required|integer|min:1|max:200',
            'instructions' => 'nullable|string'
        ]);

        // Check for schedule conflict
        $conflict = Exam::where('subject_id', $request->subject_id)
            ->where('exam_date', $request->exam_date)
            ->where('exam_time', $request->exam_time)
            ->exists();
            
        if ($conflict) {
            return redirect()->back()
                ->withErrors(['exam_time' => 'An exam for this subject is already scheduled at this time.'])
                ->withInput();
        }

        // Get subject details for auto-fill (will be used by boot method)
        $subject = Subject::find($request->subject_id);

        // Create exam with auto-filled fields (boot method will add subject_name and subject_code)
        $exam = Exam::create([
            'subject_id' => $request->subject_id,
            'exam_type' => $request->exam_type,
            'exam_date' => $request->exam_date,
            'exam_time' => $request->exam_time,
            'time_session' => $request->time_session,
            'duration_minutes' => $request->duration_minutes,
            'instructions' => $request->instructions,
            'allocated_students' => 0,
            'remaining_students' => 0,
            'allocation_status' => 'pending'
            // subject_name and subject_code will be auto-filled by boot method
        ]);

        return redirect()->route('admin.exams.index')
            ->with('success', 'Exam scheduled successfully!');
    }

    public function show(Exam $exam)
    {
        $exam->load('subject');
        return view('admin.exams.show', compact('exam'));
    }

    public function edit(Exam $exam)
    {
        $subjects = Subject::orderBy('class_name')
            ->orderBy('section')
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'class_name', 'section', 'duration_hours', 'semester']);
            
        return view('admin.exams.edit', compact('exam', 'subjects'));
    }

    public function update(Request $request, Exam $exam)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'exam_type' => 'required|in:midterm,final,quiz,assignment',
            'exam_date' => 'required|date',
            'exam_time' => 'required|date_format:H:i',
            'time_session' => 'required|in:FN,AN',
            'duration_minutes' => 'required|integer|min:1|max:200',
            'instructions' => 'nullable|string'
        ]);

        // Check for schedule conflict (excluding current exam)
        $conflict = Exam::where('subject_id', $request->subject_id)
            ->where('exam_date', $request->exam_date)
            ->where('exam_time', $request->exam_time)
            ->where('id', '!=', $exam->id)
            ->exists();
            
        if ($conflict) {
            return redirect()->back()
                ->withErrors(['exam_time' => 'An exam for this subject is already scheduled at this time.'])
                ->withInput();
        }

        // Get subject details for auto-fill if subject changed
        $subject = Subject::find($request->subject_id);

        // Update exam (boot method will handle subject_name and subject_code if subject_id changed)
        $exam->update([
            'subject_id' => $request->subject_id,
            'exam_type' => $request->exam_type,
            'exam_date' => $request->exam_date,
            'exam_time' => $request->exam_time,
            'time_session' => $request->time_session,
            'duration_minutes' => $request->duration_minutes,
            'instructions' => $request->instructions
            // subject_name and subject_code will be auto-filled by boot method if subject_id changed
        ]);

        return redirect()->route('admin.exams.index')
            ->with('success', 'Exam updated successfully!');
    }

    public function destroy(Exam $exam)
    {
        $exam->delete();
        
        return redirect()->route('admin.exams.index')
            ->with('success', 'Exam deleted successfully!');
    }
} 