@extends('layouts.app')

@section('title', 'Edit Subject')

@section('main')
@php
    $classSectionData = json_decode($subject->class_section_json ?? '[]', true);

    $selectedClassIds = collect($classSectionData)
        ->pluck('class_id')
        ->toArray();

    $selectedClassIds = old('class_ids', $selectedClassIds);

    if (empty($selectedClassIds) && $subject->class_id) {
        $selectedClassIds = [$subject->class_id];
    }
@endphp

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-warning text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-pencil-square me-2"></i>Edit Subject: {{ $subject->name }}
                    </h4>
                </div>

                <div class="card-body">
                    <form action="{{ route('admin.subjects.update', $subject) }}" method="POST">
                        @csrf
                        @method('PUT')

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <strong>Please fix the following errors:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Basic Information</h5>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Subject Name <span class="text-danger">*</span></label>
                                        <input type="text"
                                               name="name"
                                               class="form-control @error('name') is-invalid @enderror"
                                               value="{{ old('name', $subject->name) }}"
                                               required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Subject Code <span class="text-danger">*</span></label>
                                        <input type="text"
                                               name="code"
                                               class="form-control @error('code') is-invalid @enderror"
                                               value="{{ old('code', $subject->code) }}"
                                               required>
                                        @error('code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Semester</label>
                                        <select name="semester" class="form-select @error('semester') is-invalid @enderror">
                                            <option value="">Select Semester</option>
                                            @for($i = 1; $i <= 8; $i++)
                                                <option value="{{ $i }}" {{ old('semester', $subject->semester) == $i ? 'selected' : '' }}>
                                                    {{ $i }}
                                                </option>
                                            @endfor
                                        </select>
                                        @error('semester')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Class & Academic Information</h5>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    @foreach($classes as $class)
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check">
                                                <input type="checkbox"
                                                       name="class_ids[]"
                                                       value="{{ $class->id }}"
                                                       id="class_{{ $class->id }}"
                                                       class="form-check-input"
                                                       {{ in_array($class->id, $selectedClassIds) ? 'checked' : '' }}>

                                                <label class="form-check-label" for="class_{{ $class->id }}">
                                                    {{ $class->name }} - Section {{ $class->section_name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                @error('class_ids')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror

                                @error('class_ids.*')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Teacher Assignment</h5>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Assign Teacher</label>
                                        <select name="teacher_id" class="form-select @error('teacher_id') is-invalid @enderror">
                                            <option value="">-- Select Teacher (Optional) --</option>
                                            @foreach($teachers as $teacher)
                                                <option value="{{ $teacher->id }}" {{ old('teacher_id', $subject->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                                    {{ $teacher->name }}
                                                </option>
                                            @endforeach
                                        </select>

                                        @error('teacher_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Marks Configuration</h5>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Total Marks <span class="text-danger">*</span></label>
                                        <input type="number"
                                               name="total_marks"
                                               id="total_marks"
                                               class="form-control @error('total_marks') is-invalid @enderror"
                                               value="{{ old('total_marks', $subject->total_marks) }}"
                                               min="1"
                                               max="500"
                                               required>
                                        @error('total_marks')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Passing Marks <span class="text-danger">*</span></label>
                                        <input type="number"
                                               name="passing_marks"
                                               id="passing_marks"
                                               class="form-control @error('passing_marks') is-invalid @enderror"
                                               value="{{ old('passing_marks', $subject->passing_marks) }}"
                                               min="0"
                                               required>
                                        @error('passing_marks')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Internal Marks</label>
                                        <input type="number"
                                               name="internal_marks"
                                               id="internal_marks"
                                               class="form-control @error('internal_marks') is-invalid @enderror"
                                               value="{{ old('internal_marks', $subject->internal_marks) }}"
                                               min="0">
                                        @error('internal_marks')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">External Marks</label>
                                        <input type="number"
                                               name="external_marks"
                                               id="external_marks"
                                               class="form-control @error('external_marks') is-invalid @enderror"
                                               value="{{ old('external_marks', $subject->external_marks) }}"
                                               min="0">
                                        @error('external_marks')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="button" class="btn btn-outline-primary w-100" onclick="calculateMarks()">
                                            <i class="bi bi-calculator"></i> Auto Calculate
                                        </button>
                                    </div>
                                </div>

                                <div id="marksPreview" class="mt-2"></div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Cancel
                            </a>

                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-save"></i> Update Subject
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function calculateMarks() {
        const total = parseInt(document.getElementById('total_marks').value) || 0;

        if (total <= 0) {
            alert('Please enter total marks first');
            return;
        }

        const internal = Math.floor(total * 0.3);
        const external = total - internal;

        document.getElementById('internal_marks').value = internal;
        document.getElementById('external_marks').value = external;

        updateMarksPreview();
    }

    function updateMarksPreview() {
        const total = parseInt(document.getElementById('total_marks').value) || 0;
        const passing = parseInt(document.getElementById('passing_marks').value) || 0;
        const internal = parseInt(document.getElementById('internal_marks').value) || 0;
        const external = parseInt(document.getElementById('external_marks').value) || 0;

        if (total <= 0) {
            document.getElementById('marksPreview').innerHTML = '';
            return;
        }

        const internalPercent = Math.round((internal / total) * 100);
        const externalPercent = Math.round((external / total) * 100);
        const passingPercent = Math.round((passing / total) * 100);

        let warning = '';

        if (internal + external !== total) {
            warning = `
                <div class="alert alert-warning mt-2">
                    <i class="bi bi-exclamation-triangle"></i>
                    Internal + External (${internal + external}) does not equal Total (${total})
                </div>
            `;
        }

        document.getElementById('marksPreview').innerHTML = `
            <div class="progress mb-2" style="height: 20px;">
                <div class="progress-bar bg-info" style="width: ${internalPercent}%">
                    Internal ${internalPercent}%
                </div>
                <div class="progress-bar bg-warning" style="width: ${externalPercent}%">
                    External ${externalPercent}%
                </div>
            </div>
            <div class="progress" style="height: 20px;">
                <div class="progress-bar bg-success" style="width: ${passingPercent}%">
                    Pass ${passingPercent}%
                </div>
            </div>
            ${warning}
        `;
    }

    document.getElementById('total_marks').addEventListener('input', updateMarksPreview);
    document.getElementById('passing_marks').addEventListener('input', updateMarksPreview);
    document.getElementById('internal_marks').addEventListener('input', updateMarksPreview);
    document.getElementById('external_marks').addEventListener('input', updateMarksPreview);

    document.querySelector('input[name="code"]').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });

    updateMarksPreview();
</script>
@endpush