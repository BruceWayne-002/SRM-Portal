@extends('layouts.app')

@section('main')
<div class="container py-5">
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <div class="card-header text-white text-center py-4" style="background-color: #640d3c;">
            <h2 class="fw-bold mb-0">Privacy & Security</h2>
        </div>

        {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb bg-light rounded-3 p-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}" class="text-primary-custom">Dashboard</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Privacy and security</li>
        </ol>
    </nav>

        <div class="card-body p-4">
            {{-- Data Protection --}}
            <h4 class="fw-bold text-primary mb-3">1. Data Protection</h4>
            <ul class="list-group mb-4">
                <li class="list-group-item">All teacher, student, and school data is stored securely with advanced encryption.</li>
                <li class="list-group-item">Personal details are never shared with unauthorized third parties.</li>
            </ul>

            {{-- Secure Login --}}
            <h4 class="fw-bold text-primary mb-3">2. Secure Login</h4>
            <ul class="list-group mb-4">
                <li class="list-group-item">Each teacher is provided with a unique ID and password.</li>
                <li class="list-group-item">Optional OTP verification ensures only authorized users can access the app.</li>
                <li class="list-group-item">Teachers can access only their own classes and assigned student information.</li>
                <li class="list-group-item">Sensitive data is restricted to authorized staff only.</li>
            </ul>

            {{-- Safe Communication --}}
            <h4 class="fw-bold text-primary mb-3">3. Safe Communication</h4>
            <ul class="list-group mb-4">
                <li class="list-group-item">All messages between teachers, students, and parents are protected within the app.</li>
                <li class="list-group-item">No external sharing of communication is allowed.</li>
            </ul>

            {{-- Privacy Rights --}}
            <h4 class="fw-bold text-primary mb-3">4. Privacy Rights</h4>
            <ul class="list-group mb-4">
                <li class="list-group-item">Teachers have the right to update their profile and manage their information.</li>
                <li class="list-group-item">Teachers can request support for any data concerns.</li>
            </ul>

            {{-- Regular Updates --}}
            <h4 class="fw-bold text-primary mb-3">5. Regular Updates</h4>
            <ul class="list-group">
                <li class="list-group-item">The app is regularly updated with the latest security features.</li>
                <li class="list-group-item">Security updates prevent data leaks, hacking, or misuse.</li>
            </ul>
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
