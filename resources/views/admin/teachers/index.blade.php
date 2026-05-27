@extends('layouts.app')

@section('page-title', 'Teacher List')

@section('main')
<div class="container mt-4">
    <!-- Page Title & Action -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold text-primary">
            <i class="bi bi-people-fill me-2"></i> Teachers
        </h3>
        <a href="{{ route('admin.teachers.create') }}" class="btn btn-success shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Add Teacher
        </a>
    </div>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb px-3 py-2 rounded" style="background-color:#EEDFFF;">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Teacher Management</li>
        </ol>
    </nav>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.teachers.index') }}" class="row g-2">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control"
                           placeholder="Search name, emp id..."
                           value="{{ request('search') }}">
                </div>

                <div class="col-md-3">
                    <select name="department" class="form-select">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department }}" {{ request('department') == $department ? 'selected' : '' }}>
                                {{ $department }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="designation" class="form-select">
                        <option value="">All Designations</option>
                        @foreach($designations as $designation)
                            <option value="{{ $designation }}" {{ request('designation') == $designation ? 'selected' : '' }}>
                                {{ $designation }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    <a href="{{ route('admin.teachers.index') }}" class="btn btn-secondary w-100">
                        Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Teacher Table -->
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="text-center">
                        <tr>
                            <th class="rounded-start">Photo</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Designation</th>
                            <th class="rounded-end">Action</th>
                        </tr>
                    </thead>

                    <tbody class="text-center">
                        @forelse($teachers as $teacher)
                            <tr>
                                <td>
                                    @php
                                        $photoUrl = $teacher->photo
                                            ? asset($teacher->photo)
                                            : 'https://ui-avatars.com/api/?name='.urlencode($teacher->name).'&background=640d3c&color=fff&size=70';
                                    @endphp

                                    <img src="{{ $photoUrl }}"
                                         alt="{{ $teacher->name }}"
                                         class="rounded-circle border shadow-sm"
                                         style="width:70px; height:70px; object-fit:cover;"
                                         loading="lazy">
                                </td>

                                <td class="text-start fw-semibold">
                                    {{ $teacher->name }}
                                </td>

                                <td>
                                    {{ $teacher->department ?? 'N/A' }}
                                </td>

                                <td>
                                    <span class="badge bg-light text-dark px-3 py-2">
                                        {{ $teacher->designation ?? 'N/A' }}
                                    </span>
                                </td>

                                <td>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="{{ route('admin.teachers.show', $teacher->id) }}"
                                           class="btn btn-sm btn-info action-btn"
                                           title="View Teacher Details"
                                           data-bs-toggle="tooltip">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <a href="{{ route('admin.teachers.edit', $teacher->id) }}"
                                           class="btn btn-sm btn-dark action-btn"
                                           title="Edit Teacher"
                                           data-bs-toggle="tooltip">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <form action="{{ route('admin.teachers.destroy', $teacher->id) }}"
                                              method="POST"
                                              class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-danger action-btn"
                                                    title="Delete Teacher"
                                                    data-bs-toggle="tooltip"
                                                    onclick="return confirm('Are you sure you want to delete this teacher? This action cannot be undone.');">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-exclamation-circle display-6 d-block mb-3"></i>
                                        <h5>No Teachers Found</h5>
                                        <p class="mb-3">Get started by adding your first teacher.</p>
                                        <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary">
                                            <i class="bi bi-plus-circle me-1"></i> Add Teacher
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($teachers->count() > 0)
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-3 gap-2">
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        Showing {{ $teachers->firstItem() }} to {{ $teachers->lastItem() }}
                        of {{ $teachers->total() }} teachers
                    </small>

                    <div>
                        {{ $teachers->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    table thead tr {
        background-color: #640d3c !important;
        color: #fff !important;
    }

    table thead th {
        background-color: #640d3c !important;
        color: #fff !important;
        text-align: center;
        vertical-align: middle;
        border-bottom: none !important;
        font-weight: 600;
        font-size: 0.95rem;
        letter-spacing: 0.5px;
    }

    table thead th.rounded-start {
        border-top-left-radius: 8px;
    }

    table thead th.rounded-end {
        border-top-right-radius: 8px;
    }

    .action-btn {
        width: 35px;
        height: 35px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        transition: all 0.2s ease;
        border-radius: 6px;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    .btn-info.action-btn {
        background-color: #17a2b8;
        border-color: #17a2b8;
        color: white;
    }

    .btn-info.action-btn:hover {
        background-color: #138496;
        border-color: #117a8b;
    }

    .btn-dark.action-btn {
        background-color: #343a40;
        border-color: #343a40;
        color: white;
    }

    .btn-dark.action-btn:hover {
        background-color: #23272b;
        border-color: #1d2124;
    }

    .btn-danger.action-btn {
        background-color: #dc3545;
        border-color: #dc3545;
        color: white;
    }

    .btn-danger.action-btn:hover {
        background-color: #c82333;
        border-color: #bd2130;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(100, 13, 60, 0.03);
        transition: background-color 0.2s ease;
    }

    .card {
        border-radius: 12px;
        overflow: hidden;
    }

    .card-body {
        padding: 1.5rem;
    }

    .badge {
        font-weight: 500;
        font-size: 0.85rem;
    }

    .badge.bg-light {
        background-color: #f8f9fa !important;
        border: 1px solid #dee2e6;
    }

    .breadcrumb {
        margin-bottom: 0;
        background-color: #EEDFFF !important;
    }

    .breadcrumb a {
        color: #640d3c;
        text-decoration: none;
        font-weight: 500;
    }

    .breadcrumb a:hover {
        text-decoration: underline;
    }

    .pagination {
        margin-bottom: 0;
    }

    @media (max-width: 768px) {
        .table-responsive {
            border-radius: 8px;
        }

        .action-btn {
            width: 32px;
            height: 32px;
        }

        .badge.px-3 {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }
    }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush

@endsection