@extends('layouts.app')

@section('title', 'Subject Details')

@section('main')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="fas fa-book"></i> Subject Details
        </h4>

        <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <h3 class="fw-bold mb-4">
                {{ $subject->name }}
            </h3>

            <div class="row">

                <div class="col-md-6 mb-3">
                    <strong>Subject Code:</strong>
                    <div>{{ $subject->code }}</div>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>Class:</strong>
                    <div>{{ $subject->class_name }}</div>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>Section:</strong>
                    <div>{{ $subject->section }}</div>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>Semester:</strong>
                    <div>{{ $subject->semester }}</div>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>Duration:</strong>
                    <div>{{ $subject->duration_hours }} hour(s)</div>
                </div>

            </div>

        </div>
    </div>

</div>

@endsection