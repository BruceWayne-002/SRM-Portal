@extends('layouts.teacher')

@section('page-title', 'Teacher Profile')

@section('main')
<nav aria-label="breadcrumb" class="mt-5">
    <ol class="breadcrumb" style="background-color:#E8D8FF; padding:10px; border-radius:8px;">
        <li class="breadcrumb-item">
        <a href="{{ route('teacher.dashboard') }}" style="color:#640d3c; text-decoration:none;">Dashboard</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page" style="color:#333;">My Profile</li>
    </ol>
    </nav>

<div class="container-fluid  d-flex align-items-center justify-content-center bg-light py-2">
    
    <div class="row w-100 justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="row g-0 flex-column flex-md-row">

                    <!-- Profile Sidebar -->
                    <div class="col-md-4 bg-gradient text-white d-flex flex-column align-items-center justify-content-center py-4">
                        <img src="{{ asset($teacher->photo) }}" 
                             class="rounded-circle border border-white shadow-sm mb-3 profile-photo" 
                             alt="{{ $teacher->name }}">
                       <h3 class="fw-bold mb-1 text-center" style="color: #000;">{{ $teacher->name }}</h3>
                       <p class="mb-0 fs-6 text-center" style="color: #000;">{{ $teacher->desigination }}</p>

                    </div>

                    <!-- Profile Details -->
                    <div class="col-md-8 p-4 p-md-5 bg-white profile-details">
                        <h4 class="fw-bold mb-4 text-primary">Personal Information</h4>
                        <div class="row mb-3">
                            <div class="col-12 col-sm-6 mb-2 mb-sm-0"><strong>Employee ID:</strong> {{ $teacher->employee_id }}</div>
                            <div class="col-12 col-sm-6"><strong>Joining Date:</strong> {{ \Carbon\Carbon::parse($teacher->joining_date)->format('d M Y') }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12 col-sm-6 mb-2 mb-sm-0"><strong>DOB:</strong> {{ \Carbon\Carbon::parse($teacher->dob)->format('d M Y') }}</div>
                            <div class="col-12 col-sm-6"><strong>Gender:</strong> {{ $teacher->gender }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12 col-sm-6 mb-2 mb-sm-0"><strong>Email:</strong> {{ $teacher->email }}</div>
                            <div class="col-12 col-sm-6"><strong>Phone:</strong> {{ $teacher->phone }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12 col-sm-6 mb-2 mb-sm-0"><strong>Blood Group:</strong> {{ $teacher->blood_group }}</div>
                            <div class="col-12 col-sm-6"><strong>Aadhar No:</strong> {{ $teacher->aadhar_no }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12 col-sm-6 mb-2 mb-sm-0"><strong>Parent Name:</strong> {{ $teacher->parent_name }}</div>
                            <div class="col-12 col-sm-6"><strong>Address:</strong> {{ $teacher->address }}</div>
                        </div>


                        <h4 class="fw-bold mt-3 mb-4 text-primary">Professional Details</h4>
                        <div class="row mb-3">
                            <div class="col-12 col-sm-6 mb-2 mb-sm-0"><strong>Qualification:</strong> {{ $teacher->qualification }}</div>
                            <div class="col-12 col-sm-6"><strong>Assigned Class:</strong> {{ $teacher->assigned_class }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12 col-sm-6 mb-2 mb-sm-0"><strong>Assigned Section:</strong> {{ $teacher->assigned_section }}</div>
                            <div class="col-12 col-sm-6"><strong>Status:</strong> 
                                <span class="badge {{ $teacher->status == 'Active' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $teacher->status }}
                                </span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Gradient Sidebar */
.bg-gradient {
    background: linear-gradient(135deg, #640d3c, #5a3a87);
}

/* Headings & Text */
h4 {
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 5px;
}
.fs-6 {
    font-size: 0.95rem !important;
}
p, strong {
    font-size: 0.95rem;
}

/* Card Shadow & Hover */
.card {
    transition: all 0.3s ease;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 25px rgba(121, 82, 179, 0.2);
}

/* Profile photo */
.profile-photo {
    width: 160px;
    height: 160px;
    object-fit: cover;
    margin-bottom: 1rem;
}

/* Responsive adjustments */
@media (max-width: 767px) {
    .row.g-0.flex-column.flex-md-row {
        flex-direction: column !important;
    }

    .col-md-4, .col-md-8 {
        width: 100%;
        text-align: center;
        padding: 1.5rem 1rem !important;
    }

    .profile-photo {
        width: 140px !important;
        height: 140px !important;
    }

    h3 {
        font-size: 1.5rem;
    }

    .profile-details .row > div {
        text-align: left !important; /* keep labels readable */
    }

    .profile-details h4 {
        font-size: 1.2rem;
    }
}
</style>
@endpush
