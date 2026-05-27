@extends('layouts.app')

@section('page-title', 'Teacher Details')

@section('main')

<nav class="navbar navbar-dark bg-primary mb-3">
  <div class="container-fluid d-flex align-items-center">
    <a href="{{ route('admin.teachers.index') }}" class="btn btn-light btn-sm me-2">← Back</a>
    <span class="navbar-brand mb-0">Teacher Details</span>
  </div>
</nav>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb px-3 py-2 rounded" style="background-color:#EEDFFF;">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.teachers.index') }}">Teachers</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $teacher->name }}</li>
  </ol>
</nav>

<div class="container mt-4">
  <div class="card shadow-sm p-3">
    <div class="row g-3">

      <div class="col-12 col-md-4 text-center">
        @php
          $photoUrl = $teacher->photo ? asset($teacher->photo) : 'https://ui-avatars.com/api/?name='.urlencode($teacher->name).'&background=640d3c&color=fff&size=150';
        @endphp
        <img src="{{ $photoUrl }}" 
          alt="{{ $teacher->name }}" 
          class="rounded-circle img-fluid mb-3 border shadow-sm" 
          style="width:150px; height:150px; object-fit:cover;">

        <h4>{{ $teacher->name }}</h4>
        <p class="text-muted mb-1">Emp ID: {{ $teacher->employee_id }}</p>
        <p class="text-muted">Status: 
          <span class="badge {{ $teacher->status == 'Active' ? 'bg-success' : 'bg-danger' }}">
            {{ $teacher->status }}
          </span>
        </p>
      </div>

      <div class="col-12 col-md-8">
        <h5 class="mb-3">Teacher Information</h5>
        <div class="table-responsive">
          <table class="table table-bordered">
            <tr>
              <th style="width: 200px; background-color: #f8f9fc;">Full Name</th>
              <td>{{ $teacher->name }}</td>
            </tr>
            <tr>
  <th style="background-color: #f8f9fc;">Department</th>
  <td>{{ $teacher->department ?? 'N/A' }}</td>
</tr>

<tr>
  <th style="background-color: #f8f9fc;">Designation</th>
  <td>{{ $teacher->designation ?? 'N/A' }}</td>
</tr>
            <tr>
              <th style="background-color: #f8f9fc;">Employee ID</th>
              <td>{{ $teacher->employee_id }}</td>
            </tr>
            <tr>
              <th style="background-color: #f8f9fc;">Date of Birth</th>
              <td>{{ $teacher->dob ? $teacher->dob->format('d M Y') : 'N/A' }}</td>
            </tr>
            <tr>
              <th style="background-color: #f8f9fc;">Gender</th>
              <td>{{ $teacher->gender }}</td>
            </tr>
            <tr>
              <th style="background-color: #f8f9fc;">Email</th>
              <td>{{ $teacher->email }}</td>
            </tr>
            <tr>
              <th style="background-color: #f8f9fc;">Phone</th>
              <td>{{ $teacher->phone }}</td>
            </tr>
            <tr>
              <th style="background-color: #f8f9fc;">Address</th>
              <td>{{ $teacher->address }}</td>
            </tr>
            <tr>
              <th style="background-color: #f8f9fc;">Qualification</th>
              <td>{{ $teacher->qualification ?? 'N/A' }}</td>
            </tr>
            <tr>
              <th style="background-color: #f8f9fc;">Parent/Guardian Name</th>
              <td>{{ $teacher->parent_name ?? 'N/A' }}</td>
            </tr>
            <tr>
              <th style="background-color: #f8f9fc;">Joining Date</th>
              <td>{{ $teacher->joining_date ? $teacher->joining_date->format('d M Y') : 'N/A' }}</td>
            </tr>
            <tr>
              <th style="background-color: #f8f9fc;">Aadhar Number</th>
              <td>{{ $teacher->aadhar_no ?? 'N/A' }}</td>
            </tr>
            <tr>
              <th style="background-color: #f8f9fc;">Blood Group</th>
              <td>{{ $teacher->blood_group ?? 'N/A' }}</td>
            </tr>

            <tr>
              <th style="background-color: #f8f9fc;">Status</th>
              <td>
                <span class="badge {{ $teacher->status == 'Active' ? 'bg-success' : 'bg-danger' }}">
                  {{ $teacher->status }}
                </span>
              </td>
            </tr>
          </table>
        </div>
        
        <div class="mt-3">
          <a href="{{ route('admin.teachers.edit', $teacher->id) }}" class="btn btn-primary">
            <i class="bi bi-pencil-square"></i> Edit Teacher
          </a>
          <a href="{{ route('admin.teachers.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to List
          </a>
        </div>
      </div>

    </div>
  </div>
</div>

@endsection