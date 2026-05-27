<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user(); // Logged in user

        // Fetch student info using the user’s register_no and school_code
        $student = Student::where('roll_no', $user->register_no)
                          ->where('school_code', $user->school_code) // ensure correct school
                          ->first();

        if (!$student) {
            return redirect()->back()->with('error', 'Student details not found.');
        }

        return view('student.dashboard', compact('student'));
    }
}
