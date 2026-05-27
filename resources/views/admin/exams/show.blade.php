@extends('layouts.app')

@section('title', 'Exam Details - ' . $exam->subject_code)

@section('main')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">
            <i class="fas fa-file-alt"></i> Exam Details
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.exams.index') }}">Exams</a></li>
                <li class="breadcrumb-item active">Exam #{{ $exam->id }}</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="{{ route('admin.exams.edit', $exam) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit Exam
        </a>
        <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<div class="row">
    <!-- Exam Information Card -->
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle"></i> Exam Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th style="width: 120px;">Exam ID:</th>
                                <td><span class="badge bg-secondary">#{{ $exam->id }}</span></td>
                            </tr>
                            <tr>
                                <th>Subject:</th>
                                <td>
                                    <span class="fw-bold">{{ $exam->subject_code }}</span>
                                    <br>
                                    <span class="text-muted">{{ $exam->subject_name }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th>Exam Type:</th>
                                <td>
                                    @php
                                        $typeColors = [
                                            'midterm' => 'info',
                                            'final' => 'danger',
                                            'quiz' => 'success',
                                            'assignment' => 'secondary'
                                        ];
                                        $typeColor = $typeColors[$exam->exam_type] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $typeColor }}">
                                        {{ ucfirst($exam->exam_type) }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th style="width: 120px;">Date:</th>
                                <td>
                                    <i class="fas fa-calendar"></i> 
                                    {{ $exam->exam_date->format('l, d F Y') }}
                                </td>
                            </tr>
                            <tr>
                                <th>Time:</th>
                                <td>
                                    <i class="fas fa-clock"></i> 
                                    {{ \Carbon\Carbon::parse($exam->exam_time)->format('h:i A') }}
                                </td>
                            </tr>
                            <tr>
                                <th>Session:</th>
                                <td>
                                    <span class="badge bg-{{ $exam->time_session == 'FN' ? 'warning' : 'dark' }}">
                                        {{ $exam->time_session }} ({{ $exam->session_text }})
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Duration:</th>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $exam->formatted_duration }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instructions Card -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-clipboard-list"></i> Instructions
                </h5>
            </div>
            <div class="card-body">
                @if($exam->instructions)
                    <p class="mb-0">{{ $exam->instructions }}</p>
                @else
                    <p class="text-muted fst-italic mb-0">No instructions provided for this exam.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar Information -->
    <div class="col-md-4">
        <!-- Subject Details Card -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-book"></i> Subject Details
                </h5>
            </div>
            <div class="card-body">
                @if($exam->subject)
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th>Code:</th>
                            <td>{{ $exam->subject->code }}</td>
                        </tr>
                        <tr>
                            <th>Name:</th>
                            <td>{{ $exam->subject->name }}</td>
                        </tr>
                        <tr>
                            <th>Class:</th>
                            <td>{{ $exam->subject->class_name }}</td>
                        </tr>
                        <tr>
                            <th>Duration:</th>
                            <td>{{ $exam->subject->duration_hours }} hour(s)</td>
                        </tr>
                    </table>
                    <a href="{{ route('admin.subjects.show', $exam->subject) }}" class="btn btn-sm btn-outline-primary w-100">
                        <i class="fas fa-eye"></i> View Subject Details
                    </a>
                @else
                    <p class="text-muted mb-0">Subject information not available.</p>
                @endif
            </div>
        </div>

        <!-- Status Card -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-clock"></i> Status
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    @if($exam->isToday())
                        <span class="badge bg-warning text-dark p-2">
                            <i class="fas fa-calendar-day"></i> Today
                        </span>
                    @elseif($exam->isUpcoming())
                        <span class="badge bg-success p-2">
                            <i class="fas fa-calendar-alt"></i> Upcoming
                        </span>
                    @else
                        <span class="badge bg-secondary p-2">
                            <i class="fas fa-calendar-check"></i> Past
                        </span>
                    @endif
                    <span class="ms-2">
                        {{ $exam->exam_date->diffForHumans() }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection