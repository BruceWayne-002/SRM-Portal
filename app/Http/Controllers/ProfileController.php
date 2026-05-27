<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Teacher;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        if (!$user) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'error' => 'Session expired. Please log in again.',
            ]);
        }

        $schoolCode = $user->school_code;

        if ($user->role === 'Student') {
            $student = Student::where('roll_no', $user->register_no)
                              ->where('school_code', $schoolCode) // Filter by school
                              ->firstOrFail();
            return view('student.profile.index', compact('student'));
        } 
        
        if ($user->role === 'Teacher') {
            $teacher = Teacher::where('employee_id', $user->employee_id)
                              ->where('school_code', $schoolCode) // Filter by school
                              ->firstOrFail();
            return view('teacher.profile.index', compact('teacher'));
        }

        Auth::logout();
        return redirect()->route('login')->withErrors([
            'error' => 'Unauthorized access. Please log in again.',
        ]);
    }
}
