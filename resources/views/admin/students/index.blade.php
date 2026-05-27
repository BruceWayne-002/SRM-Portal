@extends('layouts.app')

@section('page-title', 'Student Management')

@section('main')
<div class="container py-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold" style="color:#640d3c;">Class & Section Management</h2>
        <p class="text-muted">Select a class and section to view students</p>
    </div>

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb bg-light rounded-3 p-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}" class="text-primary-custom">Dashboard</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Class List</li>
        </ol>
    </nav>

    {{-- Grid of Classes from Database --}}
    @if($classes->count() > 0)
        <div class="row g-4">
            @foreach($classes as $className => $classGroup)
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header text-white text-center fw-bold" style="background-color:#640d3c;">
                            {{ $className }}
                        </div>
                        <div class="card-body d-flex flex-wrap gap-2 justify-content-center">
                            @foreach($classGroup as $class)
                                <a href="{{ route('admin.students.class_list', ['classId' => $class->id]) }}" 
                                   class="btn btn-outline-primary" style="border-color: #640d3c; color: #640d3c;">
                                   Section {{ $class->section_name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-warning text-center">
            <i class="fas fa-exclamation-triangle"></i> No classes found. Please create classes first.
        </div>
    @endif
</div>
@endsection