<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        // Get statistics
        $totalStudents = Student::count();
        $totalTeachers = Teacher::count();
        
        // Get latest exam for the dashboard card
        $latestExam = Exam::latest()->first();
        

            
        // Get recent exams
        $recentExams = Exam::orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalStudents',
            'totalTeachers',
            'latestExam',
            'recentExams'
        ));
    }
}