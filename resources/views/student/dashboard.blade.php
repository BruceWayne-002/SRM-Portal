@extends('layouts.student')

@section('title', 'Dashboard')

@section('main')
<div class="container py-5">

    <div class="row g-4">

        {{-- Profile --}}
        <div class="col-sm-6 col-lg-3">
            <a href="{{ route('student.profile.show') }}" class="text-decoration-none">
                <div class="card shadow-sm rounded-4 text-center p-4 dashboard-card" style="background: linear-gradient(135deg, #ffc107, #ffecb3); color:#000;">
                    <i class="bi bi-person-circle display-4 mb-3"></i>
                    <h5 class="fw-bold">Profile</h5>
                    <p class="text-dark-50 small">Manage your profile and details.</p>
                </div>
            </a>
        </div>

        {{-- Marks --}}
        <div class="col-sm-6 col-lg-3">
            <a href="{{ route('student.marks.index') }}" class="text-decoration-none">
                <div class="card shadow-sm rounded-4 text-center p-4 dashboard-card" style="background: linear-gradient(135deg, #17a2b8, #81d4fa); color:#fff;">
                    <i class="bi bi-bar-chart-line-fill display-4 mb-3"></i>
                    <h5 class="fw-bold">Marks</h5>
                    <p class="text-white-50 small">Check your exam results and marks.</p>
                </div>
            </a>
        </div>

    </div>
</div>

@push('styles')
<style>
.dashboard-card {
    transition: transform 0.3s, box-shadow 0.3s;
}
.dashboard-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}
.dashboard-card i {
    font-size: 2.5rem;
}
.dashboard-card h5 {
    margin-top: 0.5rem;
}
.dashboard-card p {
    font-size: 0.85rem;
}
</style>
@endpush
@endsection
