<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClassController extends Controller
{
    public function index()
    {
        $classes = ClassModel::withCount('students')
            ->orderBy('name')
            ->orderBy('section_name')
            ->paginate(10);

        return view('admin.classes.index', compact('classes'));
    }

    public function show(ClassModel $class)
    {
        $students = $class->students()->paginate(20);
        return view('admin.classes.show', compact('class', 'students'));
    }

    public function toggleStatus(ClassModel $class)
    {
        $class->update(['is_active' => !$class->is_active]);
        
        $status = $class->is_active ? 'activated' : 'deactivated';
        
        return back()->with('success', "Class {$status} successfully!");
    }

    public function destroy(ClassModel $class)
    {
        if ($class->students()->exists()) {
            return back()->with('error', 'Cannot delete class with existing students!');
        }

        $class->delete();
        return redirect()->route('admin.classes.index')->with('success', 'Class deleted successfully!');
    }

    public function getSections(Request $request)
    {
        $request->validate(['name' => 'required|string']);
        
        $sections = ClassModel::where('name', $request->name)
            ->pluck('section_name')
            ->toArray();
        
        return response()->json($sections);
    }
    
    public function checkSection(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'section_name' => 'required|string|max:10',
        ]);
        
        $query = ClassModel::where('name', $request->name)
            ->where('section_name', $request->section_name);
        
        if ($request->has('exclude_id')) {
            $query->where('id', '!=', $request->exclude_id);
        }
        
        $exists = $query->exists();
        
        return response()->json([
            'available' => !$exists,
            'message' => $exists 
                ? "Section '{$request->section_name}' already exists for {$request->name}"
                : "Section '{$request->section_name}' is available for {$request->name}"
        ]);
    }
    
    public function create()
    {
        $existingClasses = ClassModel::select('name')
            ->distinct()
            ->orderBy('name')
            ->get();
        
        return view('admin.classes.form', compact('existingClasses'));
    }
    
    public function edit(ClassModel $class)
    {
        $existingClasses = ClassModel::select('name')
            ->distinct()
            ->orderBy('name')
            ->get();
        
        return view('admin.classes.form', compact('class', 'existingClasses'));
    }

    public function store(Request $request)
    {
        // Determine class name
        if ($request->class_selector == 'new') {
            // For new class, get name from course selection
            if ($request->course_type == 'other') {
                $name = $request->new_name_manual;
            } else {
                $name = $request->new_name;
            }
        } else {
            // For existing class, use the selected class name
            $name = $request->class_selector;
        }
        
        // Validation rules
        $rules = [
            'class_selector' => 'required',
            'section_name' => [
                'required',
                'string',
                'max:10',
            ],
            'description' => 'nullable|string|max:500'
        ];
        
        // Add validation for new class fields
        if ($request->class_selector == 'new') {
            $rules['course_type'] = 'required|in:arts,science,commerce,other';
            
            if ($request->course_type == 'other') {
                $rules['new_name_manual'] = 'required|string|max:255';
            } else {
                $rules['new_name'] = 'required|string|max:255';
            }
            
            // Check if class name already exists (for new class)
            $rules['new_name_unique'] = 'sometimes|required|string';
        }
        
        // Add unique validation for combination of name and section
        $rules['section_name'][] = Rule::unique('classes')->where(function ($query) use ($name) {
            return $query->where('name', $name);
        });
        
        $messages = [
            'class_selector.required' => 'Please select whether to use existing class or create new.',
            'course_type.required' => 'Please select a course type.',
            'new_name.required' => 'Please select a course.',
            'new_name_manual.required' => 'Please enter a class name.',
            'section_name.required' => 'Section name is required.',
            'section_name.unique' => 'Section :input already exists for ' . $name . '.',
        ];
        
        $validated = $request->validate($rules, $messages);
        
        // Check if class name already exists (for new class)
        if ($request->class_selector == 'new') {
            $existingClass = ClassModel::where('name', $name)->first();
            if ($existingClass) {
                return back()->withInput()->withErrors([
                    'new_name' => 'Class name "' . $name . '" already exists. Please use a different name or select it from the existing classes.'
                ]);
            }
        }
        

        
        $data = [
            'name' => $name,
            'description' => $request->description,
            'section_name' => $request->section_name,
            'is_active' => true
        ];
        
        ClassModel::create($data);
        
        return redirect()->route('admin.classes.index')
            ->with('success', 'Class & Section created successfully!');
    }
    
    public function update(Request $request, ClassModel $class)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'section_name' => [
                'required',
                'string',
                'max:10',
                Rule::unique('classes')->where(function ($query) use ($request) {
                    return $query->where('name', $request->name);
                })->ignore($class->id)
            ],
            'is_active' => 'boolean'
        ];
        
        $messages = [
            'section_name.unique' => 'Section :input already exists for this class.',
        ];
        
        $validated = $request->validate($rules, $messages);
        
        // Check if name or section changed to regenerate codes
        if ($class->name != $validated['name'] || $class->section_name != $validated['section_name']) {

        }
        
        $class->update($validated);
        
        return redirect()->route('admin.classes.index')
            ->with('success', 'Class updated successfully!');
    }
    

    

}