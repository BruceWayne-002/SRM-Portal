@extends('layouts.teacher')

@section('main')
<div class="container py-3">

<div class="mb-4 text-center">
        <h2 class="fw-bold">Welcome, {{ auth()->user()->name }}!</h2>
        <!-- <p class="text-muted">Here's a quick overview of your classroom and school activities</p> -->
    </div>


    <div class="row g-4">

    <!-- Add this to your teacher dashboard cards -->
<div class="col-sm-6 col-lg-3">
    <a href="{{ route('teacher.subjects.index') }}" class="text-decoration-none">
        <div class="card shadow-sm rounded-4 text-center p-4 dashboard-card">
            <i class="bi bi-book-fill display-4 mb-3"></i>
            <h5 class="fw-bold">My Subjects</h5>
            <p class="text-muted small">View your assigned subjects</p>
        </div>
    </a>
</div>

        <div class="col-sm-6 col-lg-3">
            <a href="{{ route('teacher.halls.index') }}" class="text-decoration-none">
                <div class="card shadow-sm rounded-4 text-center p-4 dashboard-card">
                    <i class="bi bi-calendar-check-fill display-4 mb-3"></i>
                    <h5 class="fw-bold">My Exams</h5>
                    <p class="text-muted small">Exams & view student attendance</p>
                </div>
            </a>
        </div>

<!-- 
        <div class="col-sm-6 col-lg-3">
            <a href="{{ route('teacher.timetable.index') }}" class="text-decoration-none">
                <div class="card shadow-sm rounded-4 text-center p-4 dashboard-card">
                    <i class="bi bi-clock-fill display-4 mb-3"></i>
                    <h5 class="fw-bold">Timetable</h5>
                    <p class="text-muted small">View class schedules</p>
                </div>
            </a>
        </div> -->


        <div class="col-sm-6 col-lg-3">
            <a href="{{ route('teacher.profile.show') }}" class="text-decoration-none">
                <div class="card shadow-sm rounded-4 text-center p-4 dashboard-card">
                    <i class="bi bi-person-circle display-4 mb-3"></i>
                    <h5 class="fw-bold">Profile</h5>
                    <p class="text-muted small">View & edit your profile</p>
                </div>
            </a>
        </div>


    </div>
</div>
@endsection

@push('styles')
<style>
.dashboard-card {
    background: linear-gradient(135deg, #640d3c, #EEDFFF);
    color: #fff;
    transition: transform 0.3s, box-shadow 0.3s;
    min-height: 200px; /* Ensure uniform height */
    display: flex;
    align-items: stretch;
}
.dashboard-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    background: linear-gradient(135deg, #EEDFFF, #640d3c);
    color: #000;
}
.dashboard-card .card-body {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.dashboard-card i {
    font-size: 2.5rem;
}
.dashboard-card h5 {
    margin-top: 0.5rem;
    flex-grow: 0;
}
.dashboard-card p {
    font-size: 0.85rem;
    margin: 0;
}
</style>
@endpush