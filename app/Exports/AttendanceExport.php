<?php

namespace App\Exports;

use App\Models\Mark;
use App\Models\Student;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        // Get students based on filters
        $studentQuery = Student::query();
        
        if ($this->request->filled('class')) {
            $studentQuery->where('class', $this->request->class);
        }
        
        if ($this->request->filled('section')) {
            $studentQuery->where('section', $this->request->section);
        }
        
        $students = $studentQuery->orderBy('roll_no')->get();

        // Get exam attendance
        $examAttendance = Mark::with('student')
            ->where('exam_id', $this->request->exam_id)
            ->get()
            ->keyBy('student_id');

        // Combine data
        $data = collect();
        foreach ($students as $student) {
            $attendance = $examAttendance[$student->id] ?? null;
            $data->push((object)[
                'student' => $student,
                'attendance' => $attendance
            ]);
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'S.No',
            'Roll No',
            'Student Name',
            'Class',
            'Section',
            'Internal',
            'External',
            'Total',
            'Status',
            'Grade',
            'Remarks'
        ];
    }

    public function map($row): array
    {
        static $index = 0;
        $index++;
        
        $student = $row->student;
        $attendance = $row->attendance;
        
        $status = 'Not Marked';
        $internal = '-';
        $external = '-';
        $total = '-';
        $grade = '-';
        $remarks = '-';
        
        if ($attendance) {
            if ($attendance->is_absent) {
                if ($attendance->absent_status == 'both') {
                    $status = 'Absent (Both)';
                } elseif ($attendance->absent_status == 'internal') {
                    $status = 'Absent (Internal)';
                } elseif ($attendance->absent_status == 'external') {
                    $status = 'Absent (External)';
                }
            } else {
                $status = 'Present';
            }
            
            $internal = $attendance->internal_marks ?? '-';
            $external = $attendance->external_marks ?? '-';
            $total = $attendance->total_marks_obtained ?? '-';
            $grade = $attendance->grade ?? '-';
            $remarks = $attendance->remarks ?? '-';
        }
        
        return [
            $index,
            $student->roll_no ?? 'N/A',
            $student->name,
            $student->class ?? '-',
            $student->section ?? '-',
            $internal,
            $external,
            $total,
            $status,
            $grade,
            $remarks
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
            'A1:K1' => ['fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '333333']
            ]],
        ];
    }
}