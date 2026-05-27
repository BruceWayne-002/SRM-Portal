@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h3>Add New School</h3>

    <form action="{{ route('auth.school-create') }}" method="POST">
        @csrf

        <h5>School Details</h5>
        <div class="mb-3">
            <label>School Code</label>
            <input type="text" name="school_code" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>School Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Address</label>
            <textarea name="address" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label>Contact</label>
            <input type="text" name="contact" class="form-control">
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <h5 class="mt-4">Super Admin Details</h5>
        <div class="mb-3">
            <label>Admin Name</label>
            <input type="text" name="admin_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Admin Email</label>
            <input type="email" name="admin_email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="admin_password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Confirm Password</label>
            <input type="password" name="admin_password_confirmation" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary mt-2">Add School</button>
    </form>
</div>
@endsection
