<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TeacherController extends Controller
{
    // ===============================
    // INDEX
    // ===============================
    public function index(Request $request)
{
    $query = Teacher::with('user');

    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('employee_id', 'like', "%{$search}%")
              ->orWhere('department', 'like', "%{$search}%")
              ->orWhere('designation', 'like', "%{$search}%");
        });
    }

    if ($request->filled('department')) {
        $query->where('department', $request->department);
    }

    if ($request->filled('designation')) {
        $query->where('designation', $request->designation);
    }

    $teachers = $query->orderBy('name')->paginate(10);

    $departments = Teacher::whereNotNull('department')
        ->where('department', '!=', '')
        ->distinct()
        ->orderBy('department')
        ->pluck('department');

    $designations = Teacher::whereNotNull('designation')
        ->where('designation', '!=', '')
        ->distinct()
        ->orderBy('designation')
        ->pluck('designation');

    return view('admin.teachers.index', compact(
        'teachers',
        'departments',
        'designations'
    ));
}

    // ===============================
    // CREATE
    // ===============================
    public function create()
    {
        return view('admin.teachers.create');
    }

    // ===============================
    // STORE
    // ===============================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'employee_id'       => 'required|string|unique:teachers,employee_id',
            'dob'               => 'required|date|before:today',
            'gender'            => 'required|in:Male,Female,Other',
            'email'             => 'required|email|unique:teachers,email|unique:users,email',
            'phone'             => 'required|string|max:15',
            'address'           => 'required|string',
            'qualification'     => 'nullable|string|max:255',
            'parent_name'       => 'nullable|string|max:255',
            'joining_date'      => 'required|date|before_or_equal:today',
            'status'            => 'required|in:Active,Inactive',
            'aadhar_no'         => 'nullable|string|size:12',
            'blood_group'       => 'nullable|string|in:A+,A-,B+,B-,O+,O-,AB+,AB-',
            'photo'             => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'department'  => 'required|string|max:255',
            'designation' => 'required|string|max:255',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/teachers'), $filename);
            $validated['photo'] = 'uploads/teachers/' . $filename;
        }

        // Generate password from DOB (format: DDMMYYYY)
        $password = date('dmY', strtotime($validated['dob']));

        // Create User account first
        $userData = [
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'password'    => Hash::make($password),
            'role'        => 'teacher',
            'employee_id' => $validated['employee_id'], // for teachers
            'dob'         => $validated['dob'],
            'is_active'   => true,
        ];

        $user = User::create($userData);

        // Add user_id to validated data
        $validated['user_id'] = $user->id;

        // Create teacher with user_id
        $teacher = Teacher::create($validated);

        // Store password in session to show once (optional)
        session()->flash('teacher_password', $password);

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Teacher added successfully. Login password is date of birth (DDMMYYYY): ' . $password);
    }

    // ===============================
    // SHOW
    // ===============================
    public function show(Teacher $teacher)
    {
        $teacher->load('user');
        return view('admin.teachers.show', compact('teacher'));
    }

    // ===============================
    // EDIT
    // ===============================
    public function edit(Teacher $teacher)
    {
        $teacher->load('user');
        return view('admin.teachers.edit', compact('teacher'));
    }

    // ===============================
    // UPDATE
    // ===============================
    public function update(Request $request, Teacher $teacher)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'email'             => [
                'required',
                'email',
                Rule::unique('teachers')->ignore($teacher->id),
                Rule::unique('users')->ignore($teacher->user_id)
            ],
            'employee_id'       => [
                'required',
                'string',
                Rule::unique('teachers')->ignore($teacher->id)
            ],
            'dob'               => 'required|date|before:today',
            'gender'            => 'required|in:Male,Female,Other',
            'phone'             => 'required|string|max:15',
            'address'           => 'required|string',
            'qualification'     => 'nullable|string|max:255',
            'parent_name'       => 'nullable|string|max:255',
            'joining_date'      => 'required|date|before_or_equal:today',
            'status'            => 'required|in:Active,Inactive',
            'aadhar_no'         => 'nullable|string|size:12',
            'blood_group'       => 'nullable|string|in:A+,A-,B+,B-,O+,O-,AB+,AB-',
            'photo'             => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'department'  => 'required|string|max:255',
            'designation' => 'required|string|max:255',
        ]);

        // Handle photo update
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($teacher->photo && file_exists(public_path($teacher->photo))) {
                unlink(public_path($teacher->photo));
            }

            $file = $request->file('photo');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/teachers'), $filename);
            $validated['photo'] = 'uploads/teachers/' . $filename;
        }

        // Store old DOB to check if it changed
        $oldDob = $teacher->dob;

        // Update teacher
        $teacher->update($validated);

        // Update linked User account
        if ($teacher->user_id) {
            $user = User::find($teacher->user_id);
            if ($user) {
                $userData = [
                    'name'        => $teacher->name,
                    'email'       => $teacher->email,
                    'employee_id' => $teacher->employee_id,
                    'dob'         => $teacher->dob,
                ];
                
                // If DOB changed, update password as well
                if ($oldDob != $teacher->dob) {
                    $newPassword = date('dmY', strtotime($teacher->dob));
                    $userData['password'] = Hash::make($newPassword);
                    
                    // Optional: Notify about password change
                    session()->flash('password_changed', 'Password has been updated to new DOB format: ' . $newPassword);
                }
                
                $user->update($userData);
            }
        }

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Teacher updated successfully.');
    }

    // ===============================
    // DESTROY
    // ===============================
    public function destroy(Teacher $teacher)
    {
        // Delete photo if exists
        if ($teacher->photo && file_exists(public_path($teacher->photo))) {
            unlink(public_path($teacher->photo));
        }

        // Delete linked user using user_id
        if ($teacher->user_id) {
            User::where('id', $teacher->user_id)->delete();
        }

        $teacher->delete();

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Teacher deleted successfully.');
    }

    // ===============================
    // RESET PASSWORD
    // ===============================
    public function resetPassword(Teacher $teacher)
    {
        if ($teacher->user_id) {
            $user = User::find($teacher->user_id);
            if ($user) {
                // Reset password to DOB
                $password = date('dmY', strtotime($teacher->dob));
                $user->password = Hash::make($password);
                $user->save();
                
                return redirect()->back()
                    ->with('success', 'Password reset successfully to DOB: ' . $password);
            }
        }
        
        return redirect()->back()
            ->with('error', 'User account not found for this teacher!');
    }
}