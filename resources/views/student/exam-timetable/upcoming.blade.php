@extends('layouts.student')

@section('title', 'Upcoming Exams')

@section('main')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">Upcoming Exams - {{ $class->name ?? 'N/A' }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('student.exam-timetable.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Timetable
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    
                    @if($upcomingExams->isEmpty())
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>No upcoming exams. Enjoy your break!
                        </div>
                    @else
                        @foreach($groupedByDate as $date => $typeGroups)
                            <div class="card mb-3">
                                <div class="card-header bg-success text-white">
                                    <h5 class="card-title text-white mb-0">
                                        {{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}
                                        @if($date == now()->toDateString())
                                            <span class="badge bg-warning text-dark ms-2">Today</span>
                                        @endif
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @foreach($typeGroups as $examType => $typeExams)
                                        <h6 class="text-primary">{{ ucfirst($examType) }} Session</h6>
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Time</th>
                                                        <th>Subject</th>
                                                        <th>Subject Code</th>
                                                        <th>Duration</th>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($typeExams as $exam)
                                                        <tr>
                                                            <td>{{ $exam->exam_time ? \Carbon\Carbon::parse($exam->exam_time)->format('h:i A') : 'N/A' }}</td>
                                                            <td><strong>{{ $exam->subject->name ?? 'N/A' }}</strong></td>
                                                            <td>{{ $exam->subject->code ?? 'N/A' }}</td>
                                                            <td>{{ $exam->duration ?? '3 Hours' }}</td>
    
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection