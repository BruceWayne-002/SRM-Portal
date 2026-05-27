@extends('layouts.student')

@section('page-title', 'Privacy & Security')

@section('main')
<div class="container my-5">


<nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-light rounded-3 p-2">
            <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}" class="text-primary-custom">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Privacy & Security</li>
        </ol>
    </nav>

    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">

    <div class="card-header text-white text-center py-4 bg-primary-gradient">
            <h2 class="fw-bold mb-0">Privacy & Security</h2>
        </div>

        <div class="card-body p-4">

            <h4 class="fw-bold text-primary mb-3"><i class="bi bi-shield-lock-fill me-2"></i>1. Data Protection</h4>
            <ul class="list-group list-group-flush shadow-sm rounded-3 mb-4">
                <li class="list-group-item"><i class="bi bi-person-fill-check me-2 text-primary"></i>Student information (name, class, contact details, academic records) is securely stored and not shared with third parties.</li>
                <li class="list-group-item"><i class="bi bi-lock-fill me-2 text-primary"></i>All personal data is encrypted for safety.</li>
            </ul>

            <h4 class="fw-bold text-primary mb-3"><i class="bi bi-key-fill me-2"></i>2. Login Security</h4>
            <ul class="list-group list-group-flush shadow-sm rounded-3 mb-4">
                <li class="list-group-item"><i class="bi bi-person-badge-fill me-2 text-primary"></i>Each student/parent is provided with a unique ID and password.</li>
                <li class="list-group-item"><i class="bi bi-shield-fill-check me-2 text-primary"></i>Option for OTP verification during login for added security.</li>
            </ul>

            <h4 class="fw-bold text-primary mb-3"><i class="bi bi-people-fill me-2"></i>3. Controlled Access</h4>
            <ul class="list-group list-group-flush shadow-sm rounded-3 mb-4">
                <li class="list-group-item"><i class="bi bi-person-lines-fill me-2 text-primary"></i>Only authorized teachers and school administrators can view student details.</li>
                <li class="list-group-item"><i class="bi bi-person-check-fill me-2 text-primary"></i>Parents and students can access only their own information.</li>
            </ul>

            <h4 class="fw-bold text-primary mb-3"><i class="bi bi-chat-dots-fill me-2"></i>4. Safe Communication</h4>
            <ul class="list-group list-group-flush shadow-sm rounded-3 mb-4">
                <li class="list-group-item"><i class="bi bi-shield-fill me-2 text-primary"></i>In-app communication is monitored to prevent misuse.</li>
                <li class="list-group-item"><i class="bi bi-slash-circle-fill me-2 text-primary"></i>No external advertisements or unsafe links are displayed.</li>
            </ul>

            <h4 class="fw-bold text-primary mb-3"><i class="bi bi-file-earmark-lock-fill me-2"></i>5. Privacy Rights</h4>
            <ul class="list-group list-group-flush shadow-sm rounded-3">
                <li class="list-group-item"><i class="bi bi-pencil-square me-2 text-primary"></i>Parents can request correction or deletion of student data.</li>
                <li class="list-group-item"><i class="bi bi-check2-square me-2 text-primary"></i>School ensures compliance with child data privacy norms.</li>
            </ul>

        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.text-primary-custom {
    color: #640d3c !important;
}
.bg-primary-gradient {
    background: linear-gradient(135deg, #640d3c, #5a3a87);
}
.list-group-item {
    border: none;
    border-bottom: 1px solid #eee;
    padding: 12px 15px;
    font-size: 1rem;
    transition: all 0.3s ease;
}
.list-group-item:last-child {
    border-bottom: none;
}
.list-group-item:hover {
    background-color: #f8f0ff;
    transform: translateX(5px);
}
.card-header h2 {
    letter-spacing: 1px;
}
</style>
@endpush
