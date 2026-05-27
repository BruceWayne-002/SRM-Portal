@extends('layouts.app')

@section('page-title', isset($class) ? $class->name : 'Class Details')

@section('main')
<div class="container py-5">
    <div class="row">
        <div class="col-md-12">
            @if(!isset($class))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Class not found. Please go back and try again.
                </div>
                <a href="{{ route('admin.classes.index') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Back to Classes
                </a>
            @else
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold" style="color:#640d3c;">{{ $class->name }}</h2>
                    <p class="text-muted">Class & Section Details</p>
                </div>
                <div>
                    <a href="{{ route('admin.classes.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left"></i> Back to Classes
                    </a>
                    <a href="{{ route('admin.classes.edit', $class->id) }}" class="btn btn-warning me-2">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <!-- Class Information -->
                <div class="col-md-6 mb-4">
                    <div class="card shadow border-0 h-100">
                        <div class="card-header text-white" style="background-color:#640d3c;">
                            <h5 class="mb-0"><i class="fas fa-school me-2"></i>Class Information</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%" class="text-muted">Class Name:</th>
                                    <td><strong>{{ $class->name }}</strong></td>
                                </tr>

                                <tr>
                                    <th class="text-muted">Description:</th>
                                    <td>{{ $class->description ?? 'N/A' }}</td>
                                </tr>

                            </table>
                        </div>
                    </div>
                </div>

                <!-- Section Information -->
                <div class="col-md-6 mb-4">
                    <div class="card shadow border-0 h-100">
                        <div class="card-header text-white" style="background-color:#9d71c9;">
                            <h5 class="mb-0"><i class="fas fa-users me-2"></i>Section Information</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%" class="text-muted">Section Name:</th>
                                    <td><span class="badge bg-primary">{{ $class->section_name }}</span></td>
                                </tr>


                            </table>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="col-md-12 mb-4">
                    <div class="row">                        
                        <div class="col-md-4 mb-3">
                            <div class="card shadow border-0 bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0">Total Subjects</h6>
                                            <h2 class="mb-0">{{ $class->subjects_count ?? 0 }}</h2>
                                        </div>
                                        <i class="fas fa-book fa-3x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.card {
    border-radius: 15px;
    overflow: hidden;
}

.card-header {
    border-bottom: none;
    padding: 1rem 1.5rem;
}

.table-borderless th,
.table-borderless td {
    padding: 0.75rem 0;
}

.table-borderless th {
    font-weight: 500;
}

.opacity-50 {
    opacity: 0.5;
}

.badge {
    font-size: 0.9rem;
    padding: 0.35em 0.65em;
}
</style>
@endpush