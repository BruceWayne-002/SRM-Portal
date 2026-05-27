@extends('layouts.student')

@section('page-title', 'Student Timetable')

@section('main')
<div class="container py-5">
    <h3 class="mb-4 text-center text-primary-custom fw-bold">Class Timetable</h3>

     <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-secondary-custom rounded-3 p-2">
            <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}" class="text-primary-custom">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Timetable</li>
        </ol>
    </nav>

    @php
        $studentClass = $student->class;
        $studentSection = $student->section;
        $studentGroup = $student->group;
        $days = ['Monday','Tuesday','Wednesday','Thursday','Friday'];
        $periods = ['Morning', 1, 2, 3, 4, 5, 6, 7, 8, 'Evening'];

        $colors = [
            '#FDE2FF', '#E6F0FF', '#FFF3E6', '#E6FFE6', '#FFF0F0', 
            '#FFF8E6', '#E6F9FF', '#F9E6FF', '#FFE6F2', '#E6FFF5'
        ];
    @endphp

    <h5 class="mb-4 text-center text-secondary-custom fw-semibold">
        Class {{ $studentClass }} - Section {{ $studentSection }}
        @if($studentGroup) ({{ $studentGroup }}) @endif
    </h5>

    @if($timetables->isNotEmpty())
        <div class="table-responsive">
            <table class="table timetable-table text-center align-middle mb-0">
                <thead>
                    <tr>
                        <th style="background: #640d3c; color: #fff;">Day</th>
                        @foreach($periods as $idx => $p)
                            <th style="background: {{ $colors[$idx % count($colors)] }};">{{ $p == 'Morning' || $p == 'Evening' ? $p : 'Period '.$p }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($days as $day)
                        <tr>
                            <td class="fw-bold text-white" style="background: #640d3c;">{{ $day }}</td>
                            @foreach($periods as $idx => $p)
                                @php
                                    $subject = $timetables->firstWhere(function($tt) use ($day, $p, $studentGroup) {
                                        if($studentGroup) {
                                            return $tt->day == $day && $tt->period == $p && $tt->group == $studentGroup;
                                        }
                                        return $tt->day == $day && $tt->period == $p;
                                    });
                                @endphp
                                <td style="background: {{ $colors[$idx % count($colors)] }}; font-weight: 500; color: #333;">
                                    {{ $subject->subject ?? '-' }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-info text-center">
            No timetable found for your class and section.
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.text-primary-custom { color: #640d3c !important; }
.text-secondary-custom { color: #9281a5ff !important; }

.timetable-table {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-collapse: separate;
    border-spacing: 0;
}
.timetable-table th,
.timetable-table td {
    padding: 0.6rem 0.5rem;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.timetable-table tbody tr:hover td {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-radius: 8px;
}
.timetable-table tbody tr td:first-child {
    color: #fff;
    font-weight: bold;
}
@media (max-width: 768px) {
    .timetable-table th, .timetable-table td {
        font-size: 0.75rem;
        padding: 0.35rem 0.2rem;
    }
    h3 {
        font-size: 1.6rem;
    }
    h5 {
        font-size: 1.1rem;
    }
}
</style>
@endpush
