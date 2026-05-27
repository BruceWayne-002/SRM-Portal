@extends('layouts.app')

@section('page-title', 'Student List')

@section('main')
<div class="container py-2">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h3 class="fw-bold mb-2" style="color:#640d3c;">
                {{ $class }} - Section {{ $section }}
            </h3>
            <p class="text-muted mb-0">
                <strong>Academic Year:</strong> 
                @if($academicYear)
                    {{ $academicYear }}
                @else
                    <span class="badge bg-info">All Academic Years</span>
                @endif
            </p>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <!-- Academic Year Filter -->
            <form method="GET" action="{{ route('admin.students.class_list', ['classId' => $classId]) }}" class="d-flex gap-2">
                <select name="academic_year" class="form-select" style="width: auto;" onchange="this.form.submit()">
                    <option value="">All Academic Years</option>
                    @foreach($academicYears as $year)
                        <option value="{{ $year }}" {{ $academicYear == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
                
                <!-- Current Year Filter (1st, 2nd, 3rd, etc.) -->
                <select name="current_year" class="form-select" style="width: auto;" onchange="this.form.submit()">
                    <option value="">All Years</option>
                    <option value="1" {{ request('current_year') == '1' ? 'selected' : '' }}>1st Year</option>
                    <option value="2" {{ request('current_year') == '2' ? 'selected' : '' }}>2nd Year</option>
                    <option value="3" {{ request('current_year') == '3' ? 'selected' : '' }}>3rd Year</option>
                    <option value="4" {{ request('current_year') == '4' ? 'selected' : '' }}>4th Year</option>
                    <option value="5" {{ request('current_year') == '5' ? 'selected' : '' }}>5th Year</option>
                </select>
            </form>
            
            <a href="{{ route('admin.students.create', ['classId' => $classId]) }}" 
               class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Add Student
            </a>
            <a href="{{ route('admin.students.index') }}" 
               class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Classes
            </a>
        </div>
    </div>

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb bg-light rounded-3 p-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.students.index') }}">All Classes</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                {{ $class }} - Section {{ $section }}
            </li>
        </ol>
    </nav>

    {{-- Class Info Card --}}
    @if(isset($classDetails) && $classDetails)
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body p-3">
            <div class="row">
                <div class="col-md-3">
                    <strong>Class:</strong> {{ $classDetails->name }}
                </div>
                <div class="col-md-3">
                    <strong>Section:</strong> {{ $classDetails->section_name }}
                </div>
                <div class="col-md-3">
                    <strong>Academic Year:</strong> 
                    @if($academicYear)
                        {{ $academicYear }}
                    @else
                        <span class="text-muted">All Years</span>
                    @endif
                </div>
                <div class="col-md-3">
                    <strong>Total Students:</strong> {{ $students->total() }}
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Students Table --}}
    <div class="card shadow-lg border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0" style="color:#640d3c;">
                Students List 
                @if(request('current_year'))
                    ({{ request('current_year') == '1' ? '1st' : (request('current_year') == '2' ? '2nd' : (request('current_year') == '3' ? '3rd' : request('current_year') . 'th')) }} Year)
                @endif
                @if(!$academicYear)
                    (All Academic Years)
                @endif
                - {{ $students->total() }} students
            </h5>
            <div>
                @if(request('current_year'))
                <a href="{{ route('admin.students.class_list', ['classId' => $classId, 'academic_year' => $academicYear]) }}" 
                   class="btn btn-sm btn-outline-secondary me-1">
                    <i class="bi bi-x-circle"></i> Clear Year Filter
                </a>
                @endif
                @if($academicYear)
                <a href="{{ route('admin.students.class_list', ['classId' => $classId, 'current_year' => request('current_year')]) }}" 
                   class="btn btn-sm btn-outline-info">
                    <i class="bi bi-calendar-x"></i> Clear Academic Year
                </a>
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                @if($students->hasPages())
    <div class="p-3 d-flex justify-content-center">
        {{ $students->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
@endif
                <table class="table table-hover align-middle text-center mb-0">
                    <thead style="background-color:#640d3c; color:white;">
                        <tr>
                            <th>S.No</th>
                            <th>Image</th>
                            <th>Student Name</th>
                            <th>Roll No</th>
                            <th>Academic Year</th>
                            <th>Current Year</th>
                            <th>Semester</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $index => $student)
                        <tr>
                            <td class="fw-bold">
    {{ $students->firstItem() + $index }}
</td>
                            <td>
                                @if($student->image && file_exists(public_path($student->image)))
                                <img src="{{ asset($student->image) }}" 
                                     alt="{{ $student->name }}" 
                                     class="rounded-circle border" 
                                     style="width:50px; height:50px; object-fit:cover;">
                                @else
                                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center mx-auto"
                                     style="width:50px; height:50px; background-color: #640d3c;">
                                    {{ strtoupper(substr($student->name, 0, 1)) }}
                                </div>
                                @endif
                            </td>
                            <td class="text-start">
                                <div class="fw-medium">{{ $student->name }}</div>
                                <small class="text-muted">{{ $student->email }}</small>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $student->roll_no }}</span>
                            </td>
                            <td>
                                <span class="badge bg-dark">{{ $student->academic_year }}</span>
                            </td>
                            <td>
                                <span class="badge bg-info text-dark">{{ $student->formatted_current_year }}</span>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $student->formatted_semester }}</span>
                            </td>
                            <td>
                                @php
                                    $statusClass = $student->status == 'active' ? 'success' : ($student->status == 'suspended' ? 'warning' : 'secondary');
                                @endphp
                                <span class="badge bg-{{ $statusClass }}">
                                    {{ ucfirst($student->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.students.show', $student->id) }}" 
                                       class="btn btn-sm btn-info text-white" 
                                       title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.students.edit', $student->id) }}?classId={{ $classId }}&academic_year={{ $student->academic_year }}" 
                                       class="btn btn-sm btn-warning text-white" 
                                       title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-danger" 
                                            onclick="confirmDelete({{ $student->id }})" 
                                            title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <form id="delete-form-{{ $student->id }}" 
                                          action="{{ route('admin.students.destroy', $student->id) }}" 
                                          method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="bi bi-people display-4 text-muted"></i><br>
                                No students found 
                                @if(request('current_year') && $academicYear)
                                    for {{ request('current_year') == '1' ? '1st' : (request('current_year') == '2' ? '2nd' : (request('current_year') == '3' ? '3rd' : request('current_year') . 'th')) }} year in academic year {{ $academicYear }}.
                                @elseif(request('current_year'))
                                    for {{ request('current_year') == '1' ? '1st' : (request('current_year') == '2' ? '2nd' : (request('current_year') == '3' ? '3rd' : request('current_year') . 'th')) }} year.
                                @elseif($academicYear)
                                    in academic year {{ $academicYear }}.
                                @else
                                    in this class.
                                @endif
                                <br>
                                <a href="{{ route('admin.students.create', ['classId' => $classId]) }}" 
                                   class="btn btn-primary btn-sm mt-2">
                                    <i class="bi bi-plus-lg"></i> Add Student
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete(studentId) {
    if (confirm('Are you sure you want to delete this student? This action cannot be undone.')) {
        document.getElementById('delete-form-' + studentId).submit();
    }
}
</script>
@endpush

@push('styles')
<style>
.btn-group .btn {
    margin: 0 2px;
    border-radius: 4px !important;
}
.badge {
    font-size: 0.85rem;
    padding: 0.35rem 0.65rem;
}
.form-select {
    min-width: 120px;
}
</style>
@endpush
@endsection