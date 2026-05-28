<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::with(['teacher', 'classModel'])
            ->latest()
            ->get();

        $teachers = Teacher::orderBy('name')->get();
        $classes = ClassModel::orderBy('name')->orderBy('section_name')->get();

        return view('admin.subjects.index', compact('subjects', 'teachers', 'classes'));
    }

    public function create()
    {
        $teachers = Teacher::orderBy('name')->get();
        $classes = ClassModel::orderBy('name')->orderBy('section_name')->get();

        return view('admin.subjects.create', compact('teachers', 'classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:subjects,code',
            'total_marks' => 'required|integer|min:1|max:500',
            'passing_marks' => 'required|integer|min:0|lt:total_marks',
            'internal_marks' => 'nullable|integer|min:0|lte:total_marks',
            'external_marks' => 'nullable|integer|min:0|lte:total_marks',
            'class_ids' => 'required|array|min:1',
            'class_ids.*' => 'exists:classes,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'semester' => 'nullable|integer|min:1|max:8'
        ]);

        if (!$request->filled('semester')) {
            $request->merge(['semester' => 1]);
        }

        $classIds = $request->class_ids ?? [];

        $selectedClasses = ClassModel::whereIn('id', $classIds)->get();

        $classSectionArray = $selectedClasses->map(function ($class) {
            return [
                'class_id' => $class->id,
                'class_name' => $class->name,
                'section' => $class->section_name,
            ];
        })->values()->toArray();

        $firstClass = $selectedClasses->first();

        if ($firstClass) {
            $request->merge([
                'class_id' => $firstClass->id,
                'class_name' => $firstClass->name,
                'section' => $firstClass->section_name,
            ]);
        }

        if ($request->filled('internal_marks') && $request->filled('external_marks')) {
            $sum = $request->internal_marks + $request->external_marks;

            if ($sum != $request->total_marks) {
                return back()->withInput()->withErrors([
                    'internal_marks' => 'Internal + External must equal Total marks',
                    'external_marks' => 'Internal + External must equal Total marks'
                ]);
            }
        } elseif ($request->filled('internal_marks') && !$request->filled('external_marks')) {
            $request->merge([
                'external_marks' => $request->total_marks - $request->internal_marks
            ]);
        } elseif (!$request->filled('internal_marks') && $request->filled('external_marks')) {
            $request->merge([
                'internal_marks' => $request->total_marks - $request->external_marks
            ]);
        } else {
            $internal = floor($request->total_marks * 0.3);

            $request->merge([
                'internal_marks' => $internal,
                'external_marks' => $request->total_marks - $internal
            ]);
        }

        $schoolCode = Auth::user()->school_code ?? null;

        Subject::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'total_marks' => $request->total_marks,
            'passing_marks' => $request->passing_marks,
            'internal_marks' => $request->internal_marks,
            'external_marks' => $request->external_marks,
            'class_id' => $request->class_id,
            'class_name' => $request->class_name,
            'section' => $request->section,
            'class_section_json' => json_encode($classSectionArray),
            'teacher_id' => $request->teacher_id,
            'semester' => $request->semester ?? 1,
            'school_code' => $schoolCode
        ]);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject created successfully!');
    }

    public function edit(Subject $subject)
    {
        $teachers = Teacher::orderBy('name')->get();
        $classes = ClassModel::orderBy('name')->orderBy('section_name')->get();

        return view('admin.subjects.edit', compact('subject', 'teachers', 'classes'));
    }

    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:subjects,code,' . $subject->id,
            'total_marks' => 'required|integer|min:1|max:500',
            'passing_marks' => 'required|integer|min:0|lt:total_marks',
            'internal_marks' => 'nullable|integer|min:0|lte:total_marks',
            'external_marks' => 'nullable|integer|min:0|lte:total_marks',
            'class_ids' => 'required|array|min:1',
            'class_ids.*' => 'exists:classes,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'semester' => 'nullable|integer|min:1|max:8'
        ]);

        $classIds = $request->class_ids ?? [];

        $selectedClasses = ClassModel::whereIn('id', $classIds)->get();

        $classSectionArray = $selectedClasses->map(function ($class) {
            return [
                'class_id' => $class->id,
                'class_name' => $class->name,
                'section' => $class->section_name,
            ];
        })->values()->toArray();

        $firstClass = $selectedClasses->first();

        if ($firstClass) {
            $request->merge([
                'class_id' => $firstClass->id,
                'class_name' => $firstClass->name,
                'section' => $firstClass->section_name,
            ]);
        }

        if ($request->filled('internal_marks') && $request->filled('external_marks')) {
            $sum = $request->internal_marks + $request->external_marks;

            if ($sum != $request->total_marks) {
                return back()->withInput()->withErrors([
                    'internal_marks' => 'Internal + External must equal Total marks',
                    'external_marks' => 'Internal + External must equal Total marks'
                ]);
            }
        } elseif ($request->filled('internal_marks') && !$request->filled('external_marks')) {
            $request->merge([
                'external_marks' => $request->total_marks - $request->internal_marks
            ]);
        } elseif (!$request->filled('internal_marks') && $request->filled('external_marks')) {
            $request->merge([
                'internal_marks' => $request->total_marks - $request->external_marks
            ]);
        }

        $subject->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'total_marks' => $request->total_marks,
            'passing_marks' => $request->passing_marks,
            'internal_marks' => $request->internal_marks,
            'external_marks' => $request->external_marks,
            'class_id' => $request->class_id,
            'class_name' => $request->class_name,
            'section' => $request->section,
            'class_section_json' => json_encode($classSectionArray),
            'teacher_id' => $request->teacher_id,
            'semester' => $request->semester ?? 1
        ]);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject updated successfully!');
    }

    public function destroy(Subject $subject)
    {
        if ($subject->timetables()->count() > 0) {
            return redirect()->route('admin.subjects.index')
                ->with('error', 'Cannot delete subject. It is being used in exam timetables.');
        }

        $subject->delete();

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject deleted successfully!');
    }

    public function show($id)
{
    $subject = \App\Models\Subject::findOrFail($id);

    return view('admin.subjects.show', compact('subject'));
}
}