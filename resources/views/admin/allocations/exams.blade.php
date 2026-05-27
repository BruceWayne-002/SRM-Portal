@extends('layouts.app')

@section('title', 'Exams')

@section('main')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Exams Schedule</h5>
        <a href="{{ route('admin.exams.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Schedule New Exam
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Subject</th>
                        <th>Subject Code</th>
                        <th>Exam Type</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Session</th>
                        <th>Duration</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $exam)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            @if($exam->subject)
                                {{ $exam->subject->name }}
                            @else
                                <span class="text-danger">Subject not found</span>
                            @endif
                        </td>
                        <td>
                            @if($exam->subject)
                                <strong>{{ $exam->subject->code }}</strong>
                            @else
                                <span class="text-danger">N/A</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $exam->exam_type == 'final' ? 'danger' : ($exam->exam_type == 'midterm' ? 'warning' : 'info') }}">
                                {{ ucfirst($exam->exam_type) }}
                            </span>
                        </td>
                        <td>
                            @if($exam->exam_date)
                                {{ \Carbon\Carbon::parse($exam->exam_date)->format('d/m/Y') }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            @if($exam->exam_time)
                                {{ \Carbon\Carbon::parse($exam->exam_time)->format('h:i A') }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $exam->time_session == 'FN' ? 'primary' : 'secondary' }}">
                                {{ $exam->time_session }}
                            </span>
                        </td>
                        <td>{{ $exam->duration_minutes ?? 0 }} mins</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.exams.show', $exam) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.exams.edit', $exam) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.exams.destroy', $exam) }}" method="POST" 
                                      class="d-inline" onsubmit="return confirm('Delete this exam?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center">No exams scheduled.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center">
            {{ $exams->links() }}
        </div>
    </div>
</div>
@endsection