@extends('layouts.app')

@section('page-title', 'Add Student')

@section('main')
<div class="container mt-4">
    <div class="bg-primary text-white p-3 rounded shadow-sm mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0">
                <i class="bi bi-person-plus-fill me-2"></i>Add Student to {{ $class }} - Section {{ $section }}
            </h4>
            <a href="{{ route('admin.students.class_list', ['classId' => $classId]) }}?academic_year={{ old('start_year') . ' - ' . old('end_year') }}" 
   class="btn btn-light btn-sm">
    ← Back to Students
</a>
        </div>
    </div>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.students.index') }}">All Classes</a>
            </li>
           <li class="breadcrumb-item">
    <a href="{{ route('admin.students.class_list', ['classId' => $classId]) }}">
        {{ $class }} - {{ $section }}
    </a>
</li>
            <li class="breadcrumb-item active" aria-current="page">Add Student</li>
        </ol>
    </nav>

    <div class="card shadow-lg border-0">
        <div class="card-body p-4">
            <div class="alert alert-info mb-4">
                <i class="bi bi-info-circle"></i> 
                Adding student to <strong>{{ $class }} - Section {{ $section }}</strong>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <strong>Success:</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

<form action="{{ route('admin.students.store', ['classId' => $classId]) }}" method="POST" enctype="multipart/form-data">
                    @csrf

      <fieldset class="mb-4">
    <legend class="fw-semibold text-primary">Academic Information</legend>
    <div class="row">
        <div class="col-md-3 mb-3">
            <label class="form-label">Start Year <span class="text-danger">*</span></label>
            <select name="start_year" class="form-select @error('start_year') is-invalid @enderror" required>
                <option value="">Select Start Year</option>
                @php
                    $currentYear = date('Y');
                    $startRange = range($currentYear - 5, $currentYear);
                @endphp
                @foreach($startRange as $year)
                    <option value="{{ $year }}" {{ old('start_year') == $year ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                @endforeach
            </select>
            @error('start_year')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="col-md-3 mb-3">
            <label class="form-label">End Year <span class="text-danger">*</span></label>
            <select name="end_year" class="form-select @error('end_year') is-invalid @enderror" required>
                <option value="">Select End Year</option>
                @php
                    $currentYear = date('Y');
                    $endRange = range($currentYear, $currentYear + 5);
                @endphp
                @foreach($endRange as $year)
                    <option value="{{ $year }}" {{ old('end_year') == $year ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                @endforeach
            </select>
            @error('end_year')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">End year must be greater than start year</small>
        </div>
        
        <div class="col-md-6 mb-3">
            <div class="alert alert-secondary">
                <strong>Class:</strong> {{ $class }}<br>
                <strong>Section:</strong> {{ $section }}
            </div>
        </div>
    </div>
</fieldset>

                <fieldset class="mb-4">
                    <legend class="fw-semibold text-primary">Student Information</legend>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" 
                                   class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}" 
                                   class="form-control @error('email') is-invalid @enderror" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Roll No <span class="text-danger">*</span></label>
                            <input type="text" name="roll_no" value="{{ old('roll_no') }}" 
                                   class="form-control @error('roll_no') is-invalid @enderror" required>
                            @error('roll_no')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">EMIS Number</label>
                            <input type="text" name="emis_no" value="{{ old('emis_no') }}" 
                                   class="form-control @error('emis_no') is-invalid @enderror">
                            @error('emis_no')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Aadhar Number</label>
                            <input type="text" name="aadhar_no" value="{{ old('aadhar_no') }}" 
                                   class="form-control @error('aadhar_no') is-invalid @enderror" 
                                   maxlength="12" pattern="\d{12}" placeholder="12 digits">
                            @error('aadhar_no')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                <option value="">-- Select --</option>
                                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" name="dob" value="{{ old('dob') }}" 
                                   class="form-control @error('dob') is-invalid @enderror" 
                                   max="{{ date('Y-m-d') }}" required>
                            @error('dob')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Admission Date <span class="text-danger">*</span></label>
                            <input type="date" name="admission_date" value="{{ old('admission_date') }}" 
                                   class="form-control @error('admission_date') is-invalid @enderror" 
                                   max="{{ date('Y-m-d') }}" required>
                            @error('admission_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Medium <span class="text-danger">*</span></label>
                            <select name="medium" class="form-select @error('medium') is-invalid @enderror" required>
                                <option value="">-- Select Medium --</option>
                                @foreach(['English','Tamil','Hindi','Telugu','Malayalam'] as $med)
                                    <option value="{{ $med }}" {{ old('medium') == $med ? 'selected' : '' }}>
                                        {{ $med }}
                                    </option>
                                @endforeach
                            </select>
                            @error('medium')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Blood Group</label>
                            <select name="blood_group" class="form-select @error('blood_group') is-invalid @enderror">
                                <option value="">-- Select Blood Group --</option>
                                @foreach(['A+','A-','B+','B-','O+','O-','AB+','AB-'] as $bg)
                                    <option value="{{ $bg }}" {{ old('blood_group') == $bg ? 'selected' : '' }}>
                                        {{ $bg }}
                                    </option>
                                @endforeach
                            </select>
                            @error('blood_group')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Upload Photo</label>
                            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" 
                                   accept="image/jpeg,image/png,image/jpg">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </fieldset>

                <fieldset class="mb-4">
                    <legend class="fw-semibold text-primary">Parent / Guardian Information</legend>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Father/Guardian Name <span class="text-danger">*</span></label>
                            <input type="text" name="parent_name" value="{{ old('parent_name') }}" 
                                   class="form-control @error('parent_name') is-invalid @enderror" required>
                            @error('parent_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mother Name</label>
                            <input type="text" name="mother_name" value="{{ old('mother_name') }}" 
                                   class="form-control @error('mother_name') is-invalid @enderror">
                            @error('mother_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Occupation</label>
                            <input type="text" name="occupation" value="{{ old('occupation') }}" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Primary Contact <span class="text-danger">*</span></label>
                            <input type="tel" name="contact" value="{{ old('contact') }}" 
                                   class="form-control @error('contact') is-invalid @enderror" 
                                   pattern="[0-9]{10,15}" maxlength="15" required>
                            @error('contact')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Alternate Contact</label>
                            <input type="tel" name="alt_contact" value="{{ old('alt_contact') }}" 
                                   class="form-control @error('alt_contact') is-invalid @enderror" 
                                   pattern="[0-9]{10,15}" maxlength="15">
                            @error('alt_contact')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </fieldset>

                <div class="mb-4">
                    <label class="form-label">Address <span class="text-danger">*</span></label>
                    <textarea name="address" rows="3" class="form-control @error('address') is-invalid @enderror" required>{{ old('address') }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('admin.students.class_list', ['classId' => $classId]) }}?academic_year={{ old('start_year') . ' - ' . old('end_year') }}" 
   class="btn btn-outline-secondary">
    Cancel
</a>
                    <div>
                        <button type="reset" class="btn btn-outline-danger me-2">Reset</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-person-plus"></i> Add Student
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const startYearSelect = document.querySelector('select[name="start_year"]');
    const endYearSelect = document.querySelector('select[name="end_year"]');
    
    function validateYears() {
        const startYear = parseInt(startYearSelect.value);
        const endYear = parseInt(endYearSelect.value);
        
        if (startYear && endYear && endYear <= startYear) {
            endYearSelect.setCustomValidity('End year must be greater than start year');
        } else {
            endYearSelect.setCustomValidity('');
        }
    }
    
    if (startYearSelect && endYearSelect) {
        startYearSelect.addEventListener('change', validateYears);
        endYearSelect.addEventListener('change', validateYears);
    }
});
</script>
@endpush

@push('styles')
<style>
.alert-info {
    background-color: #e7f3ff;
    border-color: #b3d7ff;
    color: #004085;
}

.alert-secondary {
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

.form-label {
    font-weight: 500;
    margin-bottom: 0.3rem;
}

.card {
    border-radius: 10px;
}

fieldset {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

legend {
    width: auto;
    padding: 0 10px;
    margin-bottom: 0;
    font-size: 1.1rem;
}
</style>
@endpush
@endsection