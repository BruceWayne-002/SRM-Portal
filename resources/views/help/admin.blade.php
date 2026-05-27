@extends('layouts.app')

@section('main')
<div class="container py-5">
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        {{-- Card Header --}}
        <div class="card-header text-white text-center py-4" style="background-color: #640d3c;">
            <h2 class="fw-bold mb-0">Teacher Help & Support</h2>
        </div>
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb bg-light rounded-3 p-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}" class="text-primary-custom">Dashboard</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Help and support</li>
        </ol>
    </nav>

        <div class="card-body p-4">
            {{-- General Guidance --}}
            <h4 class="fw-bold text-primary mb-3">1. General Guidance</h4>
            <ul class="list-group mb-4">
                <li class="list-group-item">Step-by-step guide on marking attendance</li>
                <li class="list-group-item">Assigning homework easily</li>
                <li class="list-group-item">Uploading marks securely</li>
                <li class="list-group-item">Communicating with students & parents</li>
            </ul>

            {{-- FAQs --}}
            <h4 class="fw-bold text-primary mb-3">2. FAQs</h4>
            <ul class="list-group mb-4">
                <li class="list-group-item">How do I reset my password?</li>
                <li class="list-group-item">How do I update my profile?</li>
                <li class="list-group-item">How can I send a message to parents?</li>
                <li class="list-group-item">How do I check student progress?</li>
            </ul>

            {{-- Technical Support --}}
            <h4 class="fw-bold text-primary mb-3">3. Technical Support</h4>
            <ul class="list-group mb-4">
                <li class="list-group-item">Login errors</li>
                <li class="list-group-item">Data not syncing</li>
                <li class="list-group-item">Notifications not showing</li>
            </ul>

            {{-- Contact Us --}}
            <h4 class="fw-bold text-primary mb-3">4. Contact Us</h4>
            <p><strong>Email:</strong> support@teacherapp.com</p>
            <p><strong>Helpline:</strong> +91-98765-43210</p>
            <p><strong>Support Hours:</strong> Mon–Sat, 9 AM – 6 PM</p>

            {{-- Feedback --}}
            <h4 class="fw-bold text-primary mb-3 mt-4">5. Feedback</h4>
            <p>We value your feedback! Share your suggestions to help us improve the Teacher App.</p>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .list-group-item {
        border: none;
        border-bottom: 1px solid #eee;
        padding: 10px 15px;
    }
</style>
@endpush
