@extends('layouts.student')

@section('page-title', 'Student Profile')

@section('main')
<style>
:root {
    --primary: #640d3c;
    --primary-light: #8b1b5a;
    --primary-soft: rgba(100, 13, 60, 0.1);
    --primary-gradient: linear-gradient(135deg, #640d3c 0%, #8b1b5a 100%);
    --gray-50: #f8fafc;
    --gray-100: #f1f5f9;
    --gray-200: #e2e8f0;
    --gray-300: #cbd5e1;
    --gray-600: #475569;
    --gray-700: #334155;
    --gray-800: #1e293b;
    --shadow-sm: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
    --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
    --shadow-xl: 0 20px 25px -5px rgba(100, 13, 60, 0.1), 0 10px 10px -5px rgba(100, 13, 60, 0.04);
    --radius-sm: 8px;
    --radius-md: 12px;
    --radius-lg: 16px;
    --radius-xl: 24px;
}

/* Breadcrumb */
.breadcrumb-nav {
    margin-bottom: 2rem;
}

.breadcrumb-custom {
    background: white;
    padding: 0.875rem 2rem;
    border-radius: 100px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--gray-200);
    display: inline-flex;
}

.breadcrumb-custom .breadcrumb-item {
    font-size: 0.95rem;
}

.breadcrumb-custom .breadcrumb-item a {
    color: var(--primary);
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s ease;
}

.breadcrumb-custom .breadcrumb-item a:hover {
    color: var(--primary-light);
}

.breadcrumb-custom .breadcrumb-item.active {
    color: var(--gray-600);
}

.breadcrumb-custom .breadcrumb-item + .breadcrumb-item::before {
    color: var(--gray-400);
    content: "•";
}

/* Profile Container */
.profile-container {
    max-width: 1200px;
    margin: 0 auto;
}

/* Profile Card */
.profile-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-xl);
    overflow: hidden;
    border: none;
    transition: transform 0.3s ease;
}

/* Profile Header */
.profile-header {
    background: var(--primary-gradient);
    padding: 2.5rem 3rem;
    color: white;
    display: flex;
    align-items: center;
    gap: 2.5rem;
    position: relative;
    overflow: hidden;
}

.profile-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 300px;
    height: 300px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    transform: rotate(15deg);
}

.profile-header::after {
    content: '';
    position: absolute;
    bottom: -50%;
    left: -10%;
    width: 250px;
    height: 250px;
    background: rgba(255, 255, 255, 0.08);
    border-radius: 50%;
}

.profile-avatar-wrapper {
    position: relative;
    z-index: 2;
}

.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 4px solid rgba(255, 255, 255, 0.3);
    object-fit: cover;
    background: white;
    box-shadow: var(--shadow-lg);
    transition: transform 0.3s ease;
}

.profile-avatar:hover {
    transform: scale(1.05);
}

.profile-title {
    flex: 1;
    position: relative;
    z-index: 2;
}

.profile-title h2 {
    margin: 0 0 0.5rem 0;
    font-size: 2.5rem;
    font-weight: 700;
    letter-spacing: -0.5px;
    line-height: 1.2;
}

.profile-title .class-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
}

.profile-title .class-info i {
    font-size: 1.25rem;
    opacity: 0.9;
}

.profile-title .class-info span {
    font-size: 1.1rem;
    opacity: 0.95;
    background: rgba(255, 255, 255, 0.2);
    padding: 0.4rem 1.2rem;
    border-radius: 100px;
    display: inline-block;
}

.profile-badge {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    padding: 0.6rem 1.5rem;
    border-radius: 100px;
    font-size: 0.95rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    border: 1px solid rgba(255, 255, 255, 0.3);
    z-index: 2;
    position: relative;
}

.profile-badge i {
    color: #4ade80;
}

/* Profile Body */
.profile-body {
    padding: 2.5rem 3rem;
}

/* Quick Stats */
.quick-stats {
    display: flex;
    gap: 2rem;
    margin-bottom: 2.5rem;
    padding: 1.5rem 2rem;
    background: var(--gray-50);
    border-radius: var(--radius-lg);
    border: 1px solid var(--gray-200);
}

.stat-item {
    flex: 1;
    text-align: center;
    position: relative;
}

.stat-item:not(:last-child)::after {
    content: '';
    position: absolute;
    right: -1rem;
    top: 50%;
    transform: translateY(-50%);
    width: 1px;
    height: 40px;
    background: var(--gray-300);
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: var(--primary);
    line-height: 1.2;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.9rem;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 500;
}

/* Section Title */
.section-title {
    color: var(--gray-800);
    font-size: 1.3rem;
    font-weight: 600;
    margin: 2.5rem 0 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid var(--primary-soft);
    display: flex;
    align-items: center;
}

.section-title:first-of-type {
    margin-top: 0;
}

.section-title i {
    margin-right: 1rem;
    color: var(--primary);
    font-size: 1.4rem;
    width: 28px;
    text-align: center;
}

/* Info Grid */
.info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.info-row {
    display: flex;
    align-items: center;
    padding: 1rem 1.25rem;
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-md);
    transition: all 0.3s ease;
}

.info-row:hover {
    border-color: var(--primary);
    box-shadow: 0 4px 12px rgba(100, 13, 60, 0.1);
    transform: translateY(-2px);
}

.info-label {
    width: 140px;
    font-size: 0.9rem;
    color: var(--gray-600);
    font-weight: 500;
    display: flex;
    align-items: center;
}

.info-label i {
    width: 24px;
    color: var(--primary);
    margin-right: 0.75rem;
    font-size: 1rem;
}

.info-value {
    flex: 1;
    font-weight: 600;
    color: var(--gray-800);
    font-size: 1rem;
    padding-left: 1rem;
    border-left: 2px solid var(--primary-soft);
}

/* Status Badge */
.status-active {
    background: linear-gradient(135deg, #22c55e, #16a34a);
    color: white;
    padding: 0.5rem 1.25rem;
    border-radius: 100px;
    font-size: 0.85rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    box-shadow: 0 2px 8px rgba(34, 197, 94, 0.3);
}

.status-active i {
    font-size: 0.9rem;
}

/* Avatar Error Fallback */
.avatar-fallback {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: var(--primary-gradient);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 3rem;
    font-weight: 600;
    border: 4px solid rgba(255, 255, 255, 0.3);
}

/* Responsive */
@media (max-width: 992px) {
    .info-grid {
        grid-template-columns: 1fr;
    }

    .profile-header {
        padding: 2rem;
        gap: 1.5rem;
    }

    .profile-body {
        padding: 2rem;
    }

    .quick-stats {
        flex-direction: column;
        gap: 1rem;
    }

    .stat-item:not(:last-child)::after {
        display: none;
    }
}

@media (max-width: 768px) {
    .profile-header {
        flex-direction: column;
        text-align: center;
        padding: 2rem 1.5rem;
    }

    .profile-avatar {
        width: 100px;
        height: 100px;
    }

    .profile-title h2 {
        font-size: 1.8rem;
    }

    .profile-title .class-info {
        justify-content: center;
    }

    .info-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .info-label {
        width: 100%;
    }

    .info-value {
        padding-left: 0;
        border-left: none;
        padding-top: 0.5rem;
        border-top: 2px solid var(--primary-soft);
        width: 100%;
    }

    .breadcrumb-custom {
        padding: 0.75rem 1.5rem;
    }
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.profile-card {
    animation: fadeIn 0.5s ease-out;
}

.info-row {
    animation: fadeIn 0.5s ease-out;
    animation-fill-mode: both;
}
</style>

<div class="container my-4">
    <nav class="breadcrumb-nav" aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-custom">
            <li class="breadcrumb-item">
                <a href="{{ route('student.dashboard') }}">
                    <i class="fas fa-home me-2"></i>Dashboard
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <i class="fas fa-user-circle me-2"></i>My Profile
            </li>
        </ol>
    </nav>

    <div class="profile-card">
        <div class="profile-header">
            <div class="profile-avatar-wrapper">
                @if(isset($student->image) && $student->image)
                    <img src="{{ asset($student->image) }}"
                         class="profile-avatar"
                         alt="{{ $student->name ?? 'Student' }}">
                @else
                    <div class="avatar-fallback">
                        {{ strtoupper(substr($student->name ?? 'S', 0, 1)) }}
                    </div>
                @endif
            </div>

            <div class="profile-title">
                <h2>{{ $student->name ?? 'Student Name' }}</h2>
                <div class="class-info">
                    <i class="fas fa-graduation-cap"></i>
                    <span>
                        @if(isset($student->classModel) && $student->classModel)
                            {{ $student->classModel->full_name ?? $student->classModel->name }}
                            @if(!empty($student->section_name))
                                - Section {{ $student->section_name }}
                            @endif
                        @else
                            Class Not Assigned
                        @endif
                    </span>
                </div>

                @if(isset($student->admission_no) && $student->admission_no)
                    <div style="font-size: 0.95rem; opacity: 0.9;">
                        <i class="fas fa-id-card me-2"></i>Admission No: {{ $student->admission_no }}
                    </div>
                @endif
            </div>
        </div>

        <div class="profile-body">
            <div class="quick-stats">
                <div class="stat-item">
                    <div class="stat-value">{{ $student->roll_no ?? '--' }}</div>
                    <div class="stat-label">Roll Number</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $student->emis_no ?? '--' }}</div>
                    <div class="stat-label">EMIS Number</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $student->academic_year ?? date('Y') }}</div>
                    <div class="stat-label">Academic Year</div>
                </div>
            </div>

            <h5 class="section-title">
                <i class="fas fa-user-circle"></i>Personal Information
            </h5>

            <div class="info-grid">
                <div class="info-row">
                    <span class="info-label">
                        <i class="fas fa-calendar-alt"></i>Date of Birth
                    </span>
                    <span class="info-value">
                        @if(!empty($student->dob))
                            {{ \Carbon\Carbon::parse($student->dob)->format('d M, Y') }}
                            <small class="text-muted d-block" style="font-size: 0.8rem;">
                                ({{ \Carbon\Carbon::parse($student->dob)->age }} years)
                            </small>
                        @else
                            Not Provided
                        @endif
                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">
                        <i class="fas fa-venus-mars"></i>Gender
                    </span>
                    <span class="info-value">{{ $student->gender ?? 'Not Provided' }}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">
                        <i class="fas fa-tint"></i>Blood Group
                    </span>
                    <span class="info-value">
                        @if(!empty($student->blood_group))
                            <span class="badge bg-danger">{{ $student->blood_group }}</span>
                        @else
                            Not Provided
                        @endif
                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">
                        <i class="fas fa-language"></i>Medium
                    </span>
                    <span class="info-value">{{ $student->medium ?? 'Not Provided' }}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">
                        <i class="fas fa-flag"></i>Nationality
                    </span>
                    <span class="info-value">{{ $student->nationality ?? 'Indian' }}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">
                        <i class="fas fa-id-card"></i>Aadhar Number
                    </span>
                    <span class="info-value">
                        @if(!empty($student->aadhar_no))
                            {{ substr($student->aadhar_no, 0, 4) }} **** **** {{ substr($student->aadhar_no, -4) }}
                        @else
                            Not Provided
                        @endif
                    </span>
                </div>
            </div>

            <h5 class="section-title">
                <i class="fas fa-users"></i>Parent / Guardian Information
            </h5>

            <div class="info-grid">
                <div class="info-row">
                    <span class="info-label">
                        <i class="fas fa-user-tie"></i>Father's Name
                    </span>
                    <span class="info-value">{{ $student->parent_name ?? 'Not Provided' }}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">
                        <i class="fas fa-user"></i>Mother's Name
                    </span>
                    <span class="info-value">{{ $student->mother_name ?? 'Not Provided' }}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">
                        <i class="fas fa-briefcase"></i>Occupation
                    </span>
                    <span class="info-value">{{ $student->occupation ?? 'Not Provided' }}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">
                        <i class="fas fa-phone"></i>Contact Number
                    </span>
                    <span class="info-value">
                        @if(!empty($student->contact))
                            <a href="tel:{{ $student->contact }}" style="color: inherit; text-decoration: none;">
                                {{ $student->contact }}
                            </a>
                        @else
                            Not Provided
                        @endif
                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">
                        <i class="fas fa-phone"></i>Alternative Contact
                    </span>
                    <span class="info-value">
                        @if(!empty($student->alt_contact))
                            <a href="tel:{{ $student->alt_contact }}" style="color: inherit; text-decoration: none;">
                                {{ $student->alt_contact }}
                            </a>
                        @else
                            Not Provided
                        @endif
                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">
                        <i class="fas fa-envelope"></i>Email
                    </span>
                    <span class="info-value">
                        @if(!empty($student->email))
                            <a href="mailto:{{ $student->email }}" style="color: inherit; text-decoration: none;">
                                <i class="fas fa-envelope me-1" style="font-size: 0.8rem;"></i>{{ $student->email }}
                            </a>
                        @else
                            Not Provided
                        @endif
                    </span>
                </div>

                <div class="info-row" style="grid-column: span 2;">
                    <span class="info-label">
                        <i class="fas fa-map-marker-alt"></i>Address
                    </span>
                    <span class="info-value">{{ $student->address ?? 'Not Provided' }}</span>
                </div>
            </div>

            <h5 class="section-title">
                <i class="fas fa-graduation-cap"></i>Academic Information
            </h5>

            <div class="info-grid">
                <div class="info-row">
                    <span class="info-label">
                        <i class="fas fa-calendar-check"></i>Admission Date
                    </span>
                    <span class="info-value">
                        @if(!empty($student->admission_date))
                            {{ \Carbon\Carbon::parse($student->admission_date)->format('d M, Y') }}
                        @else
                            Not Provided
                        @endif
                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">
                        <i class="fas fa-sort-numeric-up-alt"></i>Current Year
                    </span>
                    <span class="info-value">{{ $student->current_year ?? 'Not Provided' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush
@endsection