@extends('layouts.teacher')

@section('main')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('teacher.subjects.index') }}">My Subjects</a></li>
            <li class="breadcrumb-item active">{{ $subject->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-8">
            <!-- Subject Details Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">{{ $subject->name }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th>Subject Code:</th>
                                    <td>{{ $subject->code ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Class:</th>
                                    <td>{{ $subject->class_name }} - Section {{ $subject->section }}</td>
                                </tr>
                                <tr>
                                    <th>Semester:</th>
                                    <td>{{ $subject->semester_display }}</td>
                                </tr>

                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th>Passing Marks:</th>
                                    <td>{{ $subject->passing_marks ?? 'N/A' }} ({{ $subject->passing_percentage }}%)</td>
                                </tr>
                                <tr>
                                    <th>Internal Marks:</th>
                                    <td>{{ $subject->internal_marks ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <th>External Marks:</th>
                                    <td>{{ $subject->external_marks ?? 0 }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Students Summary Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Students Summary</h5>
                </div>
                <div class="card-body">
                    <h2 class="text-center">{{ $students->count() }}</h2>
                    <p class="text-center text-muted">Total Students</p>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('teacher.subjects.students', $subject->id) }}" 
                           class="btn btn-outline-primary">
                            <i class="bi bi-people"></i> View All Students
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection