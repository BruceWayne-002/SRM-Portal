@extends('layouts.app')

@section('page-title', 'Student Details')

@section('main')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold" style="color:#640d3c;">Student Details</h3>
        <a href="{{ route('admin.students.class_list', ['classId' => $student->class_id]) }}?academic_year={{ $student->academic_year }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="card shadow-lg border-0">
        <div class="card-body p-4">
            <div class="row">
                <div class="col-md-3 text-center mb-4">
                    @if(!empty($student->image) && file_exists(public_path($student->image)))
                        <img src="{{ asset($student->image) }}"
                             alt="{{ $student->name }}"
                             class="img-fluid rounded-circle border"
                             style="width:150px; height:150px; object-fit:cover;">
                    @else
                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center mx-auto"
                             style="width:150px; height:150px; background-color: #640d3c !important; font-size: 3rem;">
                            {{ strtoupper(substr($student->name ?? 'S', 0, 1)) }}
                        </div>
                    @endif
                </div>

                <div class="col-md-9">
                    <h4 class="mb-3">{{ $student->name ?? 'N/A' }}</h4>

                    @php
                        $statusClass = ($student->status ?? '') == 'active'
                            ? 'success'
                            : (($student->status ?? '') == 'suspended' ? 'warning' : 'secondary');
                    @endphp

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <strong>Email:</strong> {{ $student->email ?? 'N/A' }}
                        </div>

                        <div class="col-md-6 mb-2">
                            <strong>Roll No:</strong> {{ $student->roll_no ?? 'N/A' }}
                        </div>

                        <div class="col-md-6 mb-2">
                            <strong>Class:</strong>
                            {{ $student->class_name ?? 'N/A' }}
                            @if(!empty($student->section))
                                - Section {{ $student->section }}
                            @endif
                        </div>

                        <div class="col-md-6 mb-2">
                            <strong>Academic Year:</strong> {{ $student->academic_year ?? 'N/A' }}
                        </div>

                        <div class="col-md-6 mb-2">
                            <strong>Current Year:</strong>
                            <span class="badge bg-info">
                                {{ $student->formatted_current_year ?? ($student->current_year ?? 'N/A') }}
                            </span>
                        </div>

                        <div class="col-md-6 mb-2">
                            <strong>Current Semester:</strong>
                            <span class="badge bg-primary">
                                {{ $student->formatted_semester ?? ($student->current_semester ?? 'N/A') }}
                            </span>
                        </div>

                        <div class="col-md-6 mb-2">
                            <strong>Status:</strong>
                            <span class="badge bg-{{ $statusClass }}">
                                {{ ucfirst($student->status ?? 'N/A') }}
                            </span>
                        </div>

                        <div class="col-md-6 mb-2">
                            <strong>Gender:</strong> {{ $student->gender ?? 'N/A' }}
                        </div>

                        <div class="col-md-6 mb-2">
                            <strong>Date of Birth:</strong>
                            @if(!empty($student->dob))
                                {{ \Carbon\Carbon::parse($student->dob)->format('d-m-Y') }}
                            @else
                                N/A
                            @endif
                        </div>

                        <div class="col-md-6 mb-2">
                            <strong>Admission Date:</strong>
                            @if(!empty($student->admission_date))
                                {{ \Carbon\Carbon::parse($student->admission_date)->format('d-m-Y') }}
                            @else
                                N/A
                            @endif
                        </div>

                        <div class="col-md-6 mb-2">
                            <strong>Medium:</strong> {{ $student->medium ?? 'N/A' }}
                        </div>

                        <div class="col-md-6 mb-2">
                            <strong>Blood Group:</strong> {{ $student->blood_group ?? 'N/A' }}
                        </div>

                        <div class="col-md-6 mb-2">
                            <strong>EMIS No:</strong> {{ $student->emis_no ?? 'N/A' }}
                        </div>

                        <div class="col-md-6 mb-2">
                            <strong>Aadhar No:</strong> {{ $student->aadhar_no ?? 'N/A' }}
                        </div>
                    </div>

                    <hr>

                    <h5 class="mb-3">Parent Information</h5>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <strong>Father/Guardian:</strong> {{ $student->parent_name ?? 'N/A' }}
                        </div>

                        <div class="col-md-6 mb-2">
                            <strong>Mother:</strong> {{ $student->mother_name ?? 'N/A' }}
                        </div>

                        <div class="col-md-6 mb-2">
                            <strong>Occupation:</strong> {{ $student->occupation ?? 'N/A' }}
                        </div>

                        <div class="col-md-6 mb-2">
                            <strong>Contact:</strong> {{ $student->contact ?? 'N/A' }}
                        </div>

                        <div class="col-md-6 mb-2">
                            <strong>Alternate Contact:</strong> {{ $student->alt_contact ?? 'N/A' }}
                        </div>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <strong>Address:</strong><br>
                        {{ $student->address ?? 'N/A' }}
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.students.edit', $student->id) }}?classId={{ $student->class_id }}&academic_year={{ $student->academic_year }}"
                           class="btn btn-warning">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>

                        <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $student->id }})">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete(studentId) {
    if (confirm('Are you sure you want to delete this student?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ url("admin/students") }}/' + studentId;
        form.innerHTML = `@csrf @method("DELETE")`;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
@endsection