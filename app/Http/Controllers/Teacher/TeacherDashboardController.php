<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Teacher;

class TeacherDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        if ($user->role !== 'Teacher') {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }

        $teacher = Teacher::where('user_id', $user->id)
            ->orWhere('employee_id', $user->employee_id ?? null)
            ->orWhere('email', $user->email)
            ->first();

        return view('teacher.dashboard', compact('teacher'));
    }
}