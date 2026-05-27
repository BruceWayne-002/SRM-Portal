@extends('layouts.student')

@section('page-title', 'Student Help & Support')

@section('main')
<div class="container my-5">

    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-light rounded-3 p-2">
            <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}" class="text-primary-custom">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Help & Support</li>
        </ol>
    </nav>

    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <div class="card-header text-white text-center py-4 bg-primary-gradient">
            <h2 class="fw-bold mb-0">Student Help & Support</h2>
        </div>

        <div class="card-body p-4">

            <h4 class="fw-bold text-primary mb-3"><i class="bi bi-telephone-fill me-2"></i>1. Contact Support</h4>
            <p><strong>Helpline Number:</strong> <a href="tel:+918807501672" class="text-decoration-none text-dark fw-semibold">+91 88075 01672</a></p>
            <p><strong>Email Support:</strong> <a href="mailto:info@freshora.ai" class="text-decoration-none text-dark fw-semibold">info@freshora.ai</a></p>
            <p><strong>WhatsApp Support:</strong> <a href="https://wa.me/918807501672" target="_blank" class="text-decoration-none text-dark fw-semibold">Chat on WhatsApp</a></p>

            <h4 class="fw-bold text-primary mb-3 mt-4"><i class="bi bi-chat-dots-fill me-2"></i>2. In-App Support</h4>
            <ul class="list-group list-group-flush shadow-sm rounded-3 mb-4">
                <li class="list-group-item"><i class="bi bi-person-lines-fill me-2 text-primary"></i>Live Chat with School Admin / Teachers</li>
                <li class="list-group-item"><i class="bi bi-ticket-detailed-fill me-2 text-primary"></i>Raise a Ticket (Fees, Homework, Exams, Transport, General Queries)</li>
                <li class="list-group-item"><i class="bi bi-question-circle-fill me-2 text-primary"></i>FAQ Section (Common Questions & Answers)</li>
            </ul>

            <h4 class="fw-bold text-primary mb-3"><i class="bi bi-exclamation-triangle-fill me-2"></i>3. Emergency Support</h4>
            <ul class="list-group list-group-flush shadow-sm rounded-3 mb-4">
                <li class="list-group-item"><i class="bi bi-person-fill-gear me-2 text-primary"></i>Emergency Contact (Principal / Class Teacher)</li>
                <li class="list-group-item"><i class="bi bi-heart-pulse-fill me-2 text-primary"></i>Medical Help Desk (if available in school)</li>
            </ul>

            <h4 class="fw-bold text-primary mb-3"><i class="bi bi-lightbulb-fill me-2"></i>4. Student Guidance</h4>
            <ul class="list-group list-group-flush shadow-sm rounded-3">
                <li class="list-group-item"><i class="bi bi-journal-text me-2 text-primary"></i>Academic Help (Doubt Clearing Sessions)</li>
                <li class="list-group-item"><i class="bi bi-person-check-fill me-2 text-primary"></i>Counseling Support (Personal & Career Guidance)</li>
                <li class="list-group-item"><i class="bi bi-bell-fill me-2 text-primary"></i>Notifications & Updates for quick information</li>
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
