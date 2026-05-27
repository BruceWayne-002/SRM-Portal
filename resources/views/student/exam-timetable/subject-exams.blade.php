@extends('layouts.student')

@section('title', 'Subject Exams - ' . $subject->name)

@section('main')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">Exams for {{ $subject->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('student.exam-timetable.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Timetable
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    
                    <div class="alert alert-info">
                        <strong>Subject:</strong> {{ $subject->name }} | 
                        <strong>Code:</strong> {{ $subject->code ?? 'N/A' }} |
                        <strong>Class:</strong> {{ $subject->classModel->name ?? 'N/A' }}
                    </div>

                    @if($exams->isEmpty())
                        <div class="alert alert-warning">
                            No exams found for this subject.
                        </div>
                    @else
                        @foreach($groupedByDate as $date => $typeGroups)
                            <div class="card mb-3">
                                <div class="card-header bg-{{ $date >= now()->toDateString() ? 'success' : 'secondary' }} text-white">
                                    <h5 class="card-title text-white mb-0">
                                        {{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}
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
                                                        <th>Duration</th>
                                                        <th>Total Marks</th>
                                                        <th>Passing Marks</th>
                                                        <th>Room</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($typeExams as $exam)
                                                        <tr>
                                                            <td>{{ $exam->exam_time ? \Carbon\Carbon::parse($exam->exam_time)->format('h:i A') : 'N/A' }}</td>
                                                            <td>{{ $exam->duration ?? '3 Hours' }}</td>
                                                            <td>{{ $exam->total_marks ?? 100 }}</td>
                                                            <td>{{ $exam->passing_marks ?? 33 }}</td>
                                                            <td>{{ $exam->room_number ?? 'TBA' }}</td>
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