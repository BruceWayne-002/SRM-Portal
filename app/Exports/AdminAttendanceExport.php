<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class AdminAttendanceExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Attendance::with(['student', 'exam', 'hall', 'teacher']);

        if ($this->request->filled('exam_id')) {
            $query->where('exam_id', $this->request->exam_id);
        }

        if ($this->request->filled('hall_id')) {
            $query->where('hall_id', $this->request->hall_id);
        }

        if ($this->request->filled('status')) {
            $query->where('status', $this->request->status);
        }

        if ($this->request->filled('date_from')) {
            $query->whereDate('exam_date', '>=', Carbon::parse($this->request->date_from));
        }

        if ($this->request->filled('date_to')) {
            $query->whereDate('exam_date', '<=', Carbon::parse($this->request->date_to));
        }

        $attendances = $query->orderBy('exam_date', 'desc')->get();

        // Apply class/section filter
        if ($this->request->filled('class') || $this->request->filled('section')) {
            $attendances = $attendances->filter(function ($attendance) {
                if (!$attendance->student) return false;
                
                if ($this->request->filled('class') && $attendance->student->class_name != $this->request->class) {
                    return false;
                }
                if ($this->request->filled('section') && $attendance->student->section != $this->request->section) {
                    return false;
                }
                return true;
            });
        }

        return $attendances;
    }

    public function headings(): array
    {
        return [
            'S.No',
            'Date',
            'Student Roll No',
            'Student Name',
            'Class',
            'Section',
            'Exam',
            'Hall',
            'Teacher',
            'Session',
            'Status',
            'Marked At'
        ];
    }

    public function map($attendance): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        return [
            $rowNumber,
            $attendance->exam_date ? $attendance->exam_date->format('d-m-Y') : 'N/A',
            $attendance->student->roll_no ?? 'N/A',
            $attendance->student->name ?? 'N/A',
            $attendance->student->class_name ?? 'N/A',
            $attendance->student->section ?? 'N/A',
            $attendance->exam->subject_name ?? 'N/A',
            $attendance->hall->hall_name ?? 'N/A',
            $attendance->teacher->name ?? 'N/A',
            $attendance->session == 'MORNING' ? 'Morning' : ($attendance->session == 'AFTERNOON' ? 'Afternoon' : 'N/A'),
            ucfirst($attendance->status),
            $attendance->marked_at ? $attendance->marked_at->format('d-m-Y H:i:s') : 'N/A'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 
                  'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']]],
            'A1:L1' => ['alignment' => ['horizontal' => 'center']],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 15,
            'C' => 15,
            'D' => 25,
            'E' => 12,
            'F' => 10,
            'G' => 25,
            'H' => 15,
            'I' => 20,
            'J' => 12,
            'K' => 12,
            'L' => 20,
        ];
    }
}