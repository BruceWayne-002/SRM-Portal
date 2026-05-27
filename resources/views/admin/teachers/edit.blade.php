@extends('layouts.app')

@section('main')

<nav class="navbar navbar-dark bg-primary mb-3">
  <div class="container-fluid d-flex align-items-center">
    <a href="{{ route('admin.teachers.index') }}" class="btn btn-light btn-sm me-2">← Back</a>
    <span class="navbar-brand mb-0">Edit Teacher</span>
  </div>
</nav>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb px-3 py-2 rounded" style="background-color:#EEDFFF;">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.teachers.index') }}">Teachers</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit - {{ $teacher->name }}</li>
  </ol>
</nav>

<div class="container mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-body">
            
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.teachers.update', $teacher->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-secondary">Full Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $teacher->name) }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-secondary">Employee ID</label>
                        <input type="text" name="employee_id" class="form-control" value="{{ old('employee_id', $teacher->employee_id) }}" required>
                    </div>
                </div>

                <div class="row">
  <div class="col-md-6 mb-3">
    <label class="form-label fw-bold text-secondary">Department</label>
    <input type="text" name="department" class="form-control"
           value="{{ old('department', $teacher->department) }}" required>
  </div>

  <div class="col-md-6 mb-3">
  <label class="form-label fw-bold text-secondary">Designation</label>
  <select name="designation" class="form-select" required>
    <option value="">-- Select Designation --</option>

    @foreach([
      'Principal',
      'Vice Principal',
      'Dean',
      'Head of Department',
      'Professor',
      'Associate Professor',
      'Assistant Professor',
      'Lecturer',
      'Teaching Assistant',
      'Lab Assistant',
      'Librarian',
      'Library Assistant',
      'Physical Director',
      'Office Superintendent',
      'Administrative Officer',
      'Secretary',
      'Accountant',
      'Clerk',
      'Receptionist',
      'Exam Coordinator',
      'Placement Officer',
      'Counsellor',
      'Hostel Warden',
      'Transport Coordinator',
      'Security Officer'
    ] as $designation)
      <option value="{{ $designation }}"
        {{ old('designation', $teacher->designation ?? '') == $designation ? 'selected' : '' }}>
        {{ $designation }}
      </option>
    @endforeach
  </select>
</div>
</div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-secondary">Date of Birth</label>
                        <input type="date" name="dob" class="form-control" value="{{ old('dob', $teacher->dob ? $teacher->dob->format('Y-m-d') : '') }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-secondary">Gender</label>
                        <select name="gender" class="form-select" required>
                            <option value="">Select</option>
                            <option value="Male" {{ old('gender', $teacher->gender)=='Male'?'selected':'' }}>Male</option>
                            <option value="Female" {{ old('gender', $teacher->gender)=='Female'?'selected':'' }}>Female</option>
                            <option value="Other" {{ old('gender', $teacher->gender)=='Other'?'selected':'' }}>Other</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-secondary">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $teacher->email) }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-secondary">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $teacher->phone) }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">Address</label>
                    <textarea name="address" class="form-control" required>{{ old('address', $teacher->address) }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-secondary">Qualification</label>
                        <input type="text" name="qualification" class="form-control" value="{{ old('qualification', $teacher->qualification) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-secondary">Parent/Guardian Name</label>
                        <input type="text" name="parent_name" class="form-control" value="{{ old('parent_name', $teacher->parent_name) }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-secondary">Joining Date</label>
                        <input type="date" name="joining_date" class="form-control" value="{{ old('joining_date', $teacher->joining_date ? $teacher->joining_date->format('Y-m-d') : '') }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-secondary">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="Active" {{ old('status', $teacher->status)=='Active'?'selected':'' }}>Active</option>
                            <option value="Inactive" {{ old('status', $teacher->status)=='Inactive'?'selected':'' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-secondary">Aadhar Number</label>
                        <input type="text" name="aadhar_no" class="form-control" value="{{ old('aadhar_no', $teacher->aadhar_no) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-secondary">Blood Group</label>
                        <select name="blood_group" class="form-select">
                            <option value="">-- Select Blood Group --</option>
                            @foreach(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $bg)
                                <option value="{{ $bg }}" {{ old('blood_group', $teacher->blood_group) == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

               <div class="row">                
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold text-secondary">Photo</label>
        <input type="file" name="photo" class="form-control" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
        <small class="text-muted">Allowed formats: JPG, JPEG, PNG, GIF, WEBP (Max: 2MB)</small>
        @if($teacher->photo)
            <div class="mt-2">
                <img src="{{ asset($teacher->photo) }}" class="rounded shadow-sm" style="max-width:120px; height:auto;">
                <p class="text-muted small mt-1">Current photo</p>
            </div>
        @endif
    </div>
</div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-success px-4">Update</button>
                    <a href="{{ route('admin.teachers.index') }}" class="btn btn-secondary px-4">Cancel</a>
                </div>

            </form>
        </div>
    </div>
</div>

@endsection