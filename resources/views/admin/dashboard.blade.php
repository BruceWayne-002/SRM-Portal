@extends('layouts.app')

@section('page-title', 'Admin Dashboard')

@section('main')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-primary">Admin Dashboard</h1>
        <div class="text-muted">
            <i class="bi bi-calendar3 me-2"></i>
            {{ now()->format('l, d F Y') }}
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="font-weight-bold">Total Students</h6>
                            <h3>{{ $totalStudents ?? 0 }}</h3>
                        </div>
                        <i class="bi bi-people-fill fa-2x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('admin.students.index') }}">
                        View Details
                    </a>
                    <div class="small text-white"><i class="bi bi-chevron-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="font-weight-bold">Total Teachers</h6>
                            <h3>{{ $totalTeachers ?? 0 }}</h3>
                        </div>
                        <i class="bi bi-person-badge-fill fa-2x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('admin.teachers.index') }}">
                        View Details
                    </a>
                    <div class="small text-white"><i class="bi bi-chevron-right"></i></div>
                </div>
            </div>
        </div>
        <!--  -->

    </div>

    <!-- Quick Access Cards -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h4 class="mb-3">Quick Access</h4>
        </div>

        <div class="col-sm-6 col-lg-3 d-flex mb-3">
            <a href="{{ route('admin.students.index') }}" class="text-decoration-none w-100">
                <div class="card shadow-sm rounded-4 text-center p-4 dashboard-card h-100" style="background: linear-gradient(135deg, #640d3c, #EEDFFF); color:#fff;">
                    <i class="bi bi-people-fill display-4 mb-3"></i>
                    <h5 class="fw-bold">Students</h5>
                    <p class="text-white-50 small">Manage all students</p>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-lg-3 d-flex mb-3">
            <a href="{{ route('admin.teachers.index') }}" class="text-decoration-none w-100">
                <div class="card shadow-sm rounded-4 text-center p-4 dashboard-card h-100" style="background: linear-gradient(135deg, #17a2b8, #81d4fa); color:#fff;">
                    <i class="bi bi-person-badge-fill display-4 mb-3"></i>
                    <h5 class="fw-bold">Teachers</h5>
                    <p class="text-white-50 small">Manage all teachers</p>
                </div>
            </a>
        </div>
    {{-- Add this after the Timetable card in your dashboard --}}
<div class="col-sm-6 col-lg-3 d-flex mb-3">
    <a href="{{ route('admin.classes.index') }}" class="text-decoration-none w-100">
        <div class="card shadow-sm rounded-4 text-center p-4 dashboard-card h-100" style="background: linear-gradient(135deg, #ff6b6b, #ff9e7d); color:#fff;">
            <i class="bi bi-building display-4 mb-3"></i>
            <h5 class="fw-bold">Classes</h5>
            <p class="text-white-50 small">Manage classes & sections</p>
        </div>
    </a>
</div>

    <!-- Exam Hall Management Cards -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h4 class="mb-3">Exam Hall Management</h4>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-clipboard-check fa-2x me-3"></i>
                        <h5 class="card-title mb-0">Exam Management</h5>
                    </div>
                    <p class="card-text flex-grow-1">Create and manage exams</p>
                    <a href="{{ route('admin.exams.index') }}" class="btn btn-light mt-auto">Go to Exams</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-door-closed fa-2x me-3"></i>
                        <h5 class="card-title mb-0">Exam Halls</h5>
                    </div>
                    <p class="card-text flex-grow-1">Manage exam halls and capacity</p>
                    <a href="{{ route('admin.halls.index') }}" class="btn btn-light mt-auto">Go to Halls</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-calendar-week fa-2x me-3"></i>
                        <h5 class="card-title mb-0">Exam Timetable</h5>
                    </div>
                    <p class="card-text flex-grow-1">Create exam schedule</p>
                    <a href="{{ route('admin.exam-timetable.index') }}" class="btn btn-light mt-auto">Go to Timetable</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.dashboard-card {
    transition: transform 0.3s, box-shadow 0.3s;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%;
}
.dashboard-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.15);
}
.dashboard-card i {
    font-size: 2.5rem;
}
.dashboard-card h5 {
    margin-top: 0.3rem;
}
.dashboard-card p {
    font-size: 0.85rem;
}
.card {
    transition: transform 0.2s;
}
.card:hover {
    transform: translateY(-2px);
}
</style>
@endpush