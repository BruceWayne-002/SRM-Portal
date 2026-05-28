<?php

use App\Http\Controllers\SuperAdmin\SuperAdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\TimeTableController;
use App\Http\Controllers\Admin\StudentTimetableController;
use App\Http\Controllers\Admin\TeacherTimetableController;
use App\Http\Controllers\Admin\ExamTimetableController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\AdminStudentAttendanceController;
use App\Http\Controllers\Teacher\TeacherDashboardController;
use App\Http\Controllers\Teacher\TeacherStudentController;
use App\Http\Controllers\Admin\MarkController;
use App\Http\Controllers\Teacher\StudentAttendanceController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Student\StudentMarkController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\Admin\CommonHallController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\SectionController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// SuperAdmin Routes
Route::prefix('superadmin')->name('superadmin.')->middleware(['auth', 'isSuperAdmin'])->group(function() {
    Route::resource('admins', SuperAdminController::class);
});

// Admin Routes
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    
        // Classes
        Route::resource('classes', ClassController::class);
        Route::patch('classes/{class}/toggle-status', [ClassController::class, 'toggleStatus'])->name('classes.toggle-status');
        // Add these three routes for AJAX functionality
        Route::get('classes/sections', [ClassController::class, 'getSections'])->name('classes.sections');
        Route::post('classes/check-section', [ClassController::class, 'checkSection'])->name('classes.check-section');
        Route::post('classes/check-room', [ClassController::class, 'checkRoomNumber'])->name('classes.check-room'); // Add this line
        Route::post('classes/check-class', [ClassController::class, 'checkClass'])->name('classes.check-class'); // Add this line

     // Student Management Routes
    Route::prefix('students')->name('students.')->controller(StudentController::class)->group(function () {
        // These should come FIRST (specific routes)
        Route::get('/', 'index')->name('index');
        Route::get('/create/{classId?}', 'create')->name('create');
        Route::post('/store/{classId}', 'store')->name('store');
        Route::get('/class/{classId}', 'classList')->name('class_list');
        
        // These should come LAST (parameterized routes)
        Route::get('/{student}', 'show')->name('show');
        Route::get('/{student}/edit', 'edit')->name('edit');
        Route::put('/{student}', 'update')->name('update');
        Route::delete('/{student}', 'destroy')->name('destroy');
    });

       // Marks Routes - FULL ACCESS
    Route::prefix('marks')->name('marks.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\MarkController::class, 'index'])->name('index');
        Route::get('/exam/{exam}/create', [App\Http\Controllers\Admin\MarkController::class, 'create'])->name('create');
        Route::post('/exam/{exam}', [App\Http\Controllers\Admin\MarkController::class, 'store'])->name('store');
        Route::get('/exam/{exam}', [App\Http\Controllers\Admin\MarkController::class, 'show'])->name('show');
        Route::get('/exam/{exam}/edit', [App\Http\Controllers\Admin\MarkController::class, 'edit'])->name('edit');
        Route::put('/exam/{exam}', [App\Http\Controllers\Admin\MarkController::class, 'update'])->name('update');
        Route::get('/exam/{exam}/download', [App\Http\Controllers\Admin\MarkController::class, 'download'])->name('download');

        Route::post('/exam/{exam}/publish', [App\Http\Controllers\Admin\MarkController::class, 'publish'])->name('publish');
         // Bulk publish routes
        Route::post('/bulk-publish', [App\Http\Controllers\Admin\MarkController::class, 'bulkPublish'])->name('bulk-publish');
        Route::get('/publish-all', [App\Http\Controllers\Admin\MarkController::class, 'publishAll'])->name('publish-all');
        Route::get('/draft-exams', [App\Http\Controllers\Admin\MarkController::class, 'getDraftExams'])->name('draft-exams');
        Route::post('/exam/{exam}/publish-all', [App\Http\Controllers\Admin\MarkController::class, 'publishExam'])->name('publish-exam');
    });



    //hall creation
    Route::get('/halls', [CommonHallController::class, 'index'])->name('halls.index');
    Route::post('/halls', [CommonHallController::class, 'store'])->name('halls.store');
    Route::put('/halls/{id}', [CommonHallController::class, 'update'])->name('halls.update');
    Route::delete('/halls/{id}', [CommonHallController::class, 'destroy'])->name('halls.destroy');
    Route::get('/exam-allocation/create', [CommonHallController::class, 'create'])->name('exam-allocation.create');
    Route::post('/exam-allocation/store', [CommonHallController::class, 'storeStudent'])->name('exam-allocation.store');
    Route::get('/exam-allocation/view/{hall_id}', [CommonHallController::class, 'viewAllocationPage'])->name('exam-allocation.view');
    Route::get('/exam-allocation/view-layout/{hall}/{exam}', [CommonHallController::class, 'getAllocationLayout']);


   // Attendance Routes
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/clear-filters', [AttendanceController::class, 'clearFilters'])->name('attendance.clear-filters');
    Route::get('/attendance/export-pdf', [AttendanceController::class, 'exportPdf'])->name('attendance.export-pdf');
    Route::get('/attendance/all', [AttendanceController::class, 'allAttendance'])->name('attendance.all');
    Route::get('/attendance/exam/{examId}', [AttendanceController::class, 'examAttendance'])->name('attendance.exam');
    Route::get('/attendance/exam/{examId}/export-pdf', [AttendanceController::class, 'exportExamPdf'])->name('attendance.exam.export-pdf');

      // Common Pages
    Route::get('/about', [CommonController::class, 'index'])->name('about');
    Route::get('/help', [CommonController::class, 'helpIndex'])->name('help');
    Route::get('/privacy', [CommonController::class, 'privacy'])->name('privacy.index');
    
     // Exams
    Route::resource('exams', \App\Http\Controllers\Admin\ExamController::class);
    
     // Subjects
    Route::resource('subjects', \App\Http\Controllers\Admin\SubjectController::class);
    // Subjects
Route::resource('subjects', \App\Http\Controllers\Admin\SubjectController::class);
    // Exam Timetable Routes
    Route::get('/exam-timetable', [ExamTimetableController::class, 'index'])->name('exam-timetable.index');
    Route::get('/exam-timetable/class/{class}', [ExamTimetableController::class, 'showClassExams'])->name('exam-timetable.class-exams');
    Route::get('/exam-timetable/class/{class}/download', [ExamTimetableController::class, 'downloadTimetable'])->name('exam-timetable.download');
    Route::get('/exam-timetable/download-all', [ExamTimetableController::class, 'downloadAllClassesTimetable'])->name('exam-timetable.download-all');
    
    // Allocations Routes - FIXED
   Route::prefix('allocations')->group(function () {
    // Fix the route parameter name - should be 'exam' not 'exams'
    Route::get('/exams/{exam}', [\App\Http\Controllers\Admin\AllocationController::class, 'examAllocations'])->name('allocations.exams');
    
    // Allocate hall to timetable
    Route::post('/timetable/{timetable}/allocate-hall', [\App\Http\Controllers\Admin\AllocationController::class, 'allocateHall'])->name('allocate.hall');
    
    // Auto-allocate students
    Route::post('/timetable/{timetable}/auto-allocate', [\App\Http\Controllers\Admin\AllocationController::class, 'autoAllocateStudents'])->name('auto.allocate');
    
    // View hall allocation details
    Route::get('/{allocation}/view', [\App\Http\Controllers\Admin\AllocationController::class, 'viewHallAllocation'])->name('allocation.view');
    
    // Remove allocation
    Route::delete('/{allocation}', [\App\Http\Controllers\Admin\AllocationController::class, 'removeAllocation'])->name('allocation.remove');
   });
    
    // Teachers Routes
    Route::prefix('teachers')->group(function () {
        Route::get('/', [TeacherController::class, 'index'])->name('teachers.index');
        Route::get('/create', [TeacherController::class, 'create'])->name('teachers.create');
        Route::post('/', [TeacherController::class, 'store'])->name('teachers.store');
        Route::get('/{teacher}', [TeacherController::class, 'show'])->name('teachers.show');
        Route::get('/{teacher}/edit', [TeacherController::class, 'edit'])->name('teachers.edit');
        Route::put('/{teacher}', [TeacherController::class, 'update'])->name('teachers.update');
        Route::delete('/{teacher}', [TeacherController::class, 'destroy'])->name('teachers.destroy');
        
        // Teacher Timetable
        Route::get('/timetable', [TeacherTimetableController::class, 'index'])->name('teachers.timetable.index');
        Route::resource('timetable', TeacherTimetableController::class)->except(['index']);
    });
    
});

// Teacher Routes
Route::middleware(['auth', 'role:Teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    
    
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Teacher\TeacherDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');

    
    // Subjects
    Route::prefix('subjects')->name('subjects.')->group(function () {
        Route::get('/', [App\Http\Controllers\Teacher\TeacherSubjectController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\Teacher\TeacherSubjectController::class, 'show'])->name('show');
        Route::get('/{id}/students', [App\Http\Controllers\Teacher\TeacherSubjectController::class, 'students'])->name('students');
        Route::get('/{id}/marks', [App\Http\Controllers\Teacher\TeacherSubjectController::class, 'marks'])->name('marks');
        Route::get('/{id}/timetable', [App\Http\Controllers\Teacher\TeacherSubjectController::class, 'timetable'])->name('timetable');
    });
    
    // Halls - FIXED: Remove duplicate teacher prefix
    Route::prefix('halls')->name('halls.')->group(function () {
        Route::get('/', [App\Http\Controllers\Teacher\HallController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\Teacher\HallController::class, 'show'])->name('show');
        Route::get('/{id}/students', [App\Http\Controllers\Teacher\HallController::class, 'studentList'])->name('student-list');
        Route::get('/{id}/attendance-sheet', [App\Http\Controllers\Teacher\HallController::class, 'downloadAttendanceSheet'])->name('attendance-sheet');
        Route::get('/{id}/layout', [App\Http\Controllers\Teacher\HallController::class, 'getHallLayout'])->name('layout');
        Route::get('/api/allocations', [App\Http\Controllers\Teacher\HallController::class, 'getAllocations'])->name('api.allocations');
        Route::post('/{id}/attendance/save', [App\Http\Controllers\Teacher\HallController::class, 'saveAttendance'])->name('attendance.save');
        Route::get('/{id}/attendance/get', [App\Http\Controllers\Teacher\HallController::class, 'getAttendance'])->name('attendance.get');
        Route::get('/halls/{id}/debug-allocations', [App\Http\Controllers\Teacher\HallController::class, 'debugAllocation'])->name('debug');
    });
    
    // Student Attendance
    Route::prefix('student-attendance')->name('studentAttendance.')->group(function () {
        Route::get('/', [App\Http\Controllers\Teacher\StudentAttendanceController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\Teacher\StudentAttendanceController::class, 'store'])->name('store');
        Route::get('/records', [App\Http\Controllers\Teacher\StudentAttendanceController::class, 'records'])->name('records');
    });
        
    // Timetable - FIXED: Proper route naming
    Route::prefix('timetable')->name('timetable.')->group(function () {
        Route::get('/', [App\Http\Controllers\Teacher\TimetableController::class, 'teacherTimetable'])->name('index');
        Route::get('/exam/{id}/details', [App\Http\Controllers\Teacher\TimetableController::class, 'examDetails'])->name('exam.details');
    });
});

// Student Routes
Route::middleware(['auth', 'role:Student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    
    // Student Marks Routes
  Route::prefix('marks')->name('marks.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Student\StudentMarkController::class, 'index'])->name('index');
    Route::get('/get-exam-types', [\App\Http\Controllers\Student\StudentMarkController::class, 'getExamTypes'])->name('get-exam-types');
    Route::get('/download-pdf', [\App\Http\Controllers\Student\StudentMarkController::class, 'downloadPdf'])->name('download-pdf');
    Route::get('/{examId}', [\App\Http\Controllers\Student\StudentMarkController::class, 'show'])->name('show');
   });
    
    // Exam Timetable Routes - FIXED
    Route::prefix('exam-timetable')->name('exam-timetable.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Student\ExamTimetableController::class, 'index'])->name('index');
        Route::get('/subject/{subject}', [\App\Http\Controllers\Student\ExamTimetableController::class, 'showSubjectExams'])->name('subject');
        Route::get('/download', [\App\Http\Controllers\Student\ExamTimetableController::class, 'downloadTimetable'])->name('download');
        Route::get('/upcoming', [\App\Http\Controllers\Student\ExamTimetableController::class, 'upcomingExams'])->name('upcoming');
    });
    
    // Hall Tickets
    Route::get('/hall-tickets', [\App\Http\Controllers\Student\HallTicketController::class, 'index'])->name('hall-tickets.index');
    Route::get('/hall-tickets/{ticketNumber}', [\App\Http\Controllers\Student\HallTicketController::class, 'show'])->name('hall-tickets.show');
    Route::get('/hall-tickets/{ticketNumber}/download', [\App\Http\Controllers\Student\HallTicketController::class, 'download'])->name('hall-tickets.download');
    
    
    // Common Pages
    Route::get('/about', [CommonController::class, 'index'])->name('about');
    Route::get('/help', [CommonController::class, 'helpIndex'])->name('help');
    Route::get('/privacy', [CommonController::class, 'privacy'])->name('privacy.index');
});