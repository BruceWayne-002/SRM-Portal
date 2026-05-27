<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admins - SuperAdmin</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/brand-3.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #E8D8FF; }
        .btn-primary-custom { background-color: #640d3c; border: none; color: #fff; }
        .btn-primary-custom:hover { background-color: #623b99; }
        .btn-view { background-color: #640d3c; color: #fff; border-radius: 4px; padding: 5px 10px; text-decoration: none; }
        .btn-edit { background-color: #4B0082; color: #fff; border-radius: 4px; padding: 5px 10px; text-decoration: none; }
        .btn-delete { background-color: #FF4C4C; color: #fff; border-radius: 4px; padding: 5px 10px; border: none; }
        table { background-color: #fff; border-radius: 8px; }
        th, td { text-align: center; vertical-align: middle; }
    </style>
</head>
<body>
<div class="container mt-5">

    <!-- Header with Logout -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="color:#640d3c;">All Admins</h2>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-danger">Logout</button>
        </form>
    </div>

    <a href="{{ route('superadmin.admins.create') }}" class="btn btn-primary-custom mb-3">+ Add Admin</a>

    <table class="table table-bordered table-hover">
        <thead class="table-secondary">
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>School Code</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($admins as $admin)
            <tr>
                <td>{{ $admin->name }}</td>
                <td>{{ $admin->email }}</td>
                <td>{{ $admin->school_code }}</td>
                <td>
                    <a href="{{ route('superadmin.admins.show', $admin->id) }}" class="btn-view">View</a>
                    <a href="{{ route('superadmin.admins.edit', $admin->id) }}" class="btn-edit">Edit</a>
                    <form action="{{ route('superadmin.admins.destroy', $admin->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-delete" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
</body>
</html>
