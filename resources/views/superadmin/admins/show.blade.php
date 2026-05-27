<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Admin - SuperAdmin</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/brand-3.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #E8D8FF;
            font-family: Arial, sans-serif;
        }
        .card {
            background-color: #fff;
            border-radius: 10px;
            padding: 30px;
            max-width: 700px;
            margin: 50px auto;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #640d3c;
            margin-bottom: 25px;
        }
        .detail-label {
            font-weight: bold;
            color: #640d3c;
        }
        .detail-value {
            color: #333;
        }
        .detail-row {
            margin-bottom: 15px;
        }
        .input-password {
            position: relative;
            margin-top: 5px;
        }
        .input-password input {
            width: 100%;
            padding: 10px 40px 10px 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f8f8f8;
            color: #333;
        }
        .input-password i {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #640d3c;
        }
        .btn-back {
            background-color: #640d3c;
            color: #fff;
            border: none;
            width: 100%;
            padding: 12px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .btn-back:hover {
            background-color: #5a3792;
        }
    </style>
</head>
<body>
<div class="card">
    <h2>Admin & School Details</h2>

    <div class="detail-row">
        <span class="detail-label">Admin Name:</span>
        <span class="detail-value">{{ $admin->name }}</span>
    </div>

    <div class="detail-row">
        <span class="detail-label">Admin Email:</span>
        <span class="detail-value">{{ $admin->email }}</span>
    </div>

    

    <div class="detail-row">
        <span class="detail-label">School Code:</span>
        <span class="detail-value">{{ $admin->school->code ?? '-' }}</span>
    </div>

    <div class="detail-row">
        <span class="detail-label">School Name:</span>
        <span class="detail-value">{{ $admin->school->name ?? '-' }}</span>
    </div>

    <div class="detail-row">
        <span class="detail-label">School Address:</span>
        <span class="detail-value">{{ $admin->school->address ?? '-' }}</span>
    </div>

    <div class="detail-row">
        <span class="detail-label">School Email:</span>
        <span class="detail-value">{{ $admin->school->email ?? '-' }}</span>
    </div>

    <div class="detail-row">
        <span class="detail-label">Contact Number:</span>
        <span class="detail-value">{{ $admin->school->contact_number ?? '-' }}</span>
    </div>

    <a href="{{ route('superadmin.admins.index') }}" class="btn btn-back mt-3">Back to List</a>
</div>

<script>
function togglePassword(){
    const passwordInput = document.getElementById('password');
    const icon = document.getElementById('togglePassword');
    if(passwordInput.type === "password"){
        passwordInput.type = "text";
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        passwordInput.type = "password";
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>
</body>
</html>
