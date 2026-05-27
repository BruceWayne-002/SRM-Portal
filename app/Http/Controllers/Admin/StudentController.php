<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use App\Models\ClassModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;

class StudentController extends Controller
{
    /**
     * Generate years from 2000 to current year + 20
     */
    private function getYears()
    {
        $currentYear = date('Y');
        $startYear = 2020;
        $endYear = $currentYear + 20;
        $years = [];
        
        for ($year = $startYear; $year <= $endYear; $year++) {
            $years[] = $year;
        }
        
        return $years;
    }

    // Show class-section grid
    public function index()
    {
        $classes = ClassModel::orderBy('name')
            ->get()
            ->groupBy('name');
        
        return view('admin.students.index', compact('classes'));
    }

    // Create method with class ID
    public function create($classId = null)
    {
        if (!$classId) {
            return redirect()->route('admin.students.index')
                ->with('error', 'Please select a class and section first.');
        }
        
        $classDetails = ClassModel::find($classId);
        
        if (!$classDetails) {
            return redirect()->route('admin.students.index')
                ->with('error', 'Class not found!');
        }
        
        $years = $this->getYears();
        $class = $classDetails->name;
        $section = $classDetails->section_name;
        
        return view('admin.students.add', compact('class', 'section', 'classDetails', 'years', 'classId'));
    }
    
    // Store a new student
    public function store(Request $request, $classId)
    {
        $classDetails = ClassModel::find($classId);
        
        if (!$classDetails) {
            return redirect()->back()->with('error', 'Class not found!');
        }

        $class = $classDetails->name;
        $section = $classDetails->section_name;

        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:students,email|unique:users,email',
            'roll_no'        => [
                'required',
                'string',
                Rule::unique('students')->where(function($query) use ($classDetails, $section, $request) {
                    return $query->where('class_id', $classDetails->id)
                                 ->where('section', $section)
                                 ->where('academic_year', $request->start_year . ' - ' . $request->end_year);
                })
            ],
            'emis_no'        => 'nullable|string|max:50',
            'aadhar_no'      => 'nullable|digits:12',
            'dob'            => 'required|date|before:today',
            'admission_date' => 'required|date|before_or_equal:today',
            'gender'         => 'required|in:Male,Female,Other',
            'medium'         => 'required|string|max:50',
            'blood_group'    => 'nullable|string|max:3',
            'parent_name'    => 'required|string|max:255',
            'mother_name'    => 'nullable|string|max:255',
            'occupation'     => 'nullable|string|max:255',
            'contact'        => 'required|string|regex:/^[0-9]{10,15}$/',
            'alt_contact'    => 'nullable|string|regex:/^[0-9]{10,15}$/',
            'address'        => 'required|string',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'start_year'     => 'required|integer|min:2000|max:' . (date('Y') + 20),
            'end_year'       => 'required|integer|min:2000|max:' . (date('Y') + 20) . '|gt:start_year'
        ]);

        // Combine start and end year into academic_year
        $validated['academic_year'] = $request->start_year . ' - ' . $request->end_year;
        $validated['class_id'] = $classDetails->id;
        $validated['class_name'] = $class;
        $validated['section'] = $section;

        // Remove individual year fields
        unset($validated['start_year'], $validated['end_year']);

        // Handle image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/students'), $filename);
            $validated['image'] = 'uploads/students/' . $filename;
        }

        // Create user account with DOB as password (format: DDMMYYYY)
        $password = date('dmY', strtotime($validated['dob']));

        // Prepare user data
        $userData = [
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'password'    => Hash::make($password),
            'role'        => 'Student',
            'register_no' => $validated['roll_no'],
            'dob'         => $validated['dob'],
            'is_active'   => true,
        ];

        // Create user
        $user = User::create($userData);

        // Store user_id in student record
        $validated['user_id'] = $user->id;

        // Set default values for academic status
        $validated['current_year'] = 1;
        $validated['current_semester'] = 1;
        $validated['status'] = 'active';

        // Create student
        $student = Student::create($validated);
        
        // Update with correct academic status based on current date
        $student->updateCurrentAcademicStatus()->save();

        // Optional: Store plain password in session to show to admin once
        session()->flash('student_password', $password);

        return redirect()->route('admin.students.class_list', [
            'classId' => $classId,
            'academic_year' => $validated['academic_year']
        ])->with('success', 'Student added successfully. Login password is date of birth (DDMMYYYY): ' . $password);
    }

    // Show students by class ID
    public function classList($classId)
    {
        // Find the class details
        $classDetails = ClassModel::find($classId);

        if (!$classDetails) {
            return redirect()->route('admin.students.index')
                ->with('error', 'Class not found!');
        }

        $class = $classDetails->name;
        $section = $classDetails->section_name;
        
        // Get filters from request
        $academicYear = request()->get('academic_year');
        $currentYearFilter = request()->get('current_year');
        
        // Build query for students - always filter by class_id
        $query = Student::where('class_id', $classId)
            ->with('user');
        
        // Apply academic year filter ONLY if a specific year is selected (not empty)
        if (!empty($academicYear)) {
            $query->where('academic_year', $academicYear);
        }
        
        // Apply current year filter if provided
        if (!empty($currentYearFilter)) {
            $query->where('current_year', $currentYearFilter);
        }
        
        $students = $query->orderBy('roll_no')->paginate(10);

$students->getCollection()->transform(function ($student) {
    $student->updateCurrentAcademicStatus()->save();
    return $student;
});

        // Get unique academic years for this class for the filter dropdown
        $academicYears = Student::where('class_id', $classId)
            ->distinct()
            ->pluck('academic_year')
            ->filter()
            ->values()
            ->toArray();

        // If no academic years found, provide empty array
        if (empty($academicYears)) {
            $academicYears = [];
        }

        // If no academic year is selected, set it to null for display
        if (empty($academicYear)) {
            $academicYear = null;
        }

        return view('admin.students.list', compact(
            'students', 
            'class', 
            'section', 
            'classDetails', 
            'academicYears', 
            'academicYear',
            'classId',
            'currentYearFilter'
        ));
    }

    // Show a single student
    public function show(Student $student)
    {
        // Update current academic status
        $student->updateCurrentAcademicStatus()->save();
        
        // Load relationships
        $student->load(['classModel', 'user']);
        
        return view('admin.students.show', compact('student'));
    }

    // Edit student form
    public function edit(Student $student)
    {
        // Get years for dropdown
        $years = $this->getYears();
        
        // Get class ID from request or from student
        $classId = request()->get('classId', $student->class_id);
        $classDetails = ClassModel::find($classId);
        
        $class = $classDetails->name ?? $student->class_name;
        $section = $classDetails->section_name ?? $student->section;
        
        // Parse academic year to get start and end year
        $startYear = '';
        $endYear = '';
        
        if (!empty($student->academic_year)) {
            $yearsParts = explode('-', $student->academic_year);
            
            if (isset($yearsParts[0])) {
                $startYear = trim($yearsParts[0]);
            }
            
            if (isset($yearsParts[1])) {
                $endYear = trim($yearsParts[1]);
            }
        }
        
        // If parsing failed, set defaults
        if (empty($startYear)) {
            $startYear = date('Y') - 1;
        }
        if (empty($endYear)) {
            $endYear = date('Y');
        }
        
        return view('admin.students.edit', compact(
            'student', 
            'years', 
            'class', 
            'section', 
            'classDetails', 
            'startYear', 
            'endYear', 
            'classId'
        ));
    }

    // Update student
    public function update(Request $request, Student $student)
    {
        $classId = $request->classId ?? $student->class_id;
        $classDetails = ClassModel::find($classId);

        if (!$classDetails) {
            return redirect()->back()->with('error', 'Class not found!');
        }

        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => [
                'required',
                'email',
                Rule::unique('students')->ignore($student->id),
                Rule::unique('users')->ignore($student->user_id)
            ],
            'roll_no'        => [
                'required',
                'string',
                Rule::unique('students')->where(function($query) use ($classDetails, $request) {
                    return $query->where('class_id', $classDetails->id)
                                 ->where('academic_year', $request->start_year . ' - ' . $request->end_year);
                })->ignore($student->id),
            ],
            'emis_no'        => 'nullable|string|max:50',
            'aadhar_no'      => 'nullable|digits:12',
            'dob'            => 'required|date|before:today',
            'admission_date' => 'required|date|before_or_equal:today',
            'gender'         => 'required|in:Male,Female,Other',
            'medium'         => 'required|string|max:50',
            'blood_group'    => 'nullable|string|max:3',
            'parent_name'    => 'required|string|max:255',
            'mother_name'    => 'nullable|string|max:255',
            'occupation'     => 'nullable|string|max:255',
            'contact'        => 'required|string|regex:/^[0-9]{10,15}$/',
            'alt_contact'    => 'nullable|string|regex:/^[0-9]{10,15}$/',
            'address'        => 'required|string',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'start_year'     => 'required|integer|min:2000|max:' . (date('Y') + 20),
            'end_year'       => 'required|integer|min:2000|max:' . (date('Y') + 20) . '|gt:start_year'
        ]);

        // Combine start and end year into academic_year
        $validated['academic_year'] = $request->start_year . ' - ' . $request->end_year;
        $validated['class_id'] = $classDetails->id;
        $validated['class_name'] = $classDetails->name;
        $validated['section'] = $classDetails->section_name;

        // Remove individual year fields
        unset($validated['start_year'], $validated['end_year']);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($student->image && file_exists(public_path($student->image))) {
                unlink(public_path($student->image));
            }

            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/students'), $filename);
            $validated['image'] = 'uploads/students/' . $filename;
        }

        // Update student
        $student->update($validated);
        
        // Update current academic status
        $student->updateCurrentAcademicStatus()->save();

        // Update user account
        if ($student->user_id) {
            $user = User::find($student->user_id);
            if ($user) {
                // Check if password needs to be updated (if DOB changed)
                $password = date('dmY', strtotime($student->dob));
                
                $userData = [
                    'name'  => $student->name,
                    'email' => $student->email,
                    'dob'   => $student->dob,
                    'register_no' => $student->roll_no,
                ];
                
                // Only update password if DOB changed
                if ($user->dob != $student->dob) {
                    $userData['password'] = Hash::make($password);
                }
                
                $user->update($userData);
            }
        }

        return redirect()->route('admin.students.class_list', [
            'classId' => $classId,
            'academic_year' => $validated['academic_year']
        ])->with('success', 'Student updated successfully!');
    }

    // Delete student
    public function destroy(Student $student)
    {
        // Delete image if exists
        if ($student->image && file_exists(public_path($student->image))) {
            File::delete(public_path($student->image));
        }

        // Delete associated user
        if ($student->user_id) {
            User::where('id', $student->user_id)->delete();
        }

        $student->delete();

        return redirect()->back()->with('success', 'Student deleted successfully!');
    }

    // Optional: Reset password for a student
    public function resetPassword(Student $student)
    {
        if ($student->user_id) {
            $user = User::find($student->user_id);
            if ($user) {
                // Reset password to DOB
                $password = date('dmY', strtotime($student->dob));
                $user->password = Hash::make($password);
                $user->save();
                
                return redirect()->back()->with('success', 'Password reset successfully to DOB: ' . $password);
            }
        }
        
        return redirect()->back()->with('error', 'User not found!');
    }
}