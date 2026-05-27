<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ExamHallAllocationStudent;
use App\Models\Student;
use App\Models\StudentHallAllocation;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;


class HallTicketController extends Controller
{


public function index()
{
    try {
        // Get logged in user
        $user = Auth::user();

        // Find student using register_no → roll_no
        $student = null;

        if ($user->register_no) {
            $student = Student::where('roll_no', $user->register_no)->first();
        }

        if (!$student) {
            return redirect()->back()->with('error', 'Student record not found.');
        }

        $startDate = Carbon::yesterday()->toDateString();
        $endDate   = Carbon::tomorrow()->toDateString();

        $hallTickets = ExamHallAllocationStudent::with([
                'allocation.exam',
                'allocation.hall'
            ])
            ->where('student_id', $student->id)
            ->whereHas('allocation', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('exam_date', [$startDate, $endDate]);
            })
            ->get();

        return view('student.hall_tickets.index', compact('hallTickets', 'student'));

    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error loading hall ticket: ' . $e->getMessage());
    }
}

    public function show($ticketNumber)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student record not found');
        }

        $allocation = StudentHallAllocation::where('hall_ticket_number', $ticketNumber)
            ->where('student_id', $student->id)
            ->with(['hallAllocation.examTimetable.exam', 'hallAllocation.examTimetable.subject', 
                   'hallAllocation.examHall', 'hallAllocation.teacher'])
            ->firstOrFail();
        
        // Generate QR code if not exists
        if (empty($allocation->qr_code_path)) {
            $qrContent = json_encode([
                'ticket_number' => $allocation->hall_ticket_number,
                'student_id' => $student->id,
                'exam' => $allocation->hallAllocation->examTimetable->exam->name,
                'date' => $allocation->hallAllocation->examTimetable->exam_date,
                'seat' => $allocation->seat_position
            ]);
            
            $qrCode = QrCode::format('png')->size(200)->generate($qrContent);
            $fileName = 'qr_codes/' . $allocation->hall_ticket_number . '.png';
            
            Storage::disk('public')->put($fileName, $qrCode);
            
            $allocation->update(['qr_code_path' => $fileName]);
        }
        
        return view('student.hall-tickets.show', compact('allocation'));
    }

public function download($id)
{
    try {

        $user = Auth::user();

        // Find student
        $student = null;
        if ($user->register_no) {
            $student = Student::where('roll_no', $user->register_no)->first();
        }

        if (!$student) {
            return back()->with('error', 'Student record not found.');
        }

        // Fetch allocation record (same logic as index)
        $ticket = ExamHallAllocationStudent::with([
                'allocation.exam.subject',
                'allocation.hall'
            ])
            ->where('student_id', $student->id)
            ->where('id', $id)
            ->firstOrFail();

        $data = [
            'ticket' => $ticket,
            'student' => $student,
            'downloadDate' => now()->format('d M Y'),
        ];

        $pdf = Pdf::loadView('student.hall_tickets.pdf', $data);

        return $pdf->download(
            'Hall_Ticket_'.$student->roll_no.'_'.Carbon::parse($ticket->allocation->exam_date)->format('d-m-Y').'.pdf'
        );

    } catch (\Exception $e) {
        return back()->with('error', 'Error downloading hall ticket: '.$e->getMessage());
    }
}
}