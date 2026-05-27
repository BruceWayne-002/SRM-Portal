<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SuperAdmin - Create Admin</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/brand-3.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #E8D8FF;
        }
        .container {
            width: 95%;
            max-width: 700px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        h2, h3 {
            text-align: center;
            color: #640d3c;
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            color: #640d3c;
        }
        .btn-submit {
            background-color: #640d3c;
            color: #fff;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
            width: 100%;
        }
        .btn-submit:hover {
            background-color: #5a3792;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Create Admin & School</h2>

    @if ($errors->any())
        <div class="error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="adminForm" method="POST" action="{{ route('superadmin.admins.store') }}">
        @csrf

        <h3>School Details</h3>

        <label for="school_code">School Code</label>
        <input type="text" name="school_code" id="school_code" class="form-control mb-3" value="{{ old('school_code') }}" required>

        <label for="school_name">School Name</label>
        <input type="text" name="school_name" id="school_name" class="form-control mb-3" value="{{ old('school_name') }}" required>

        <label for="school_address">Address</label>
        <input type="text" name="school_address" id="school_address" class="form-control mb-3" value="{{ old('school_address') }}" required>

        <label for="school_email">Email</label>
        <input type="email" name="school_email" id="school_email" class="form-control mb-3" value="{{ old('school_email') }}" required>

        <label for="school_contact">Contact Number</label>
        <input type="text" name="school_contact" id="school_contact" class="form-control mb-3" value="{{ old('school_contact') }}">

        <h3>Admin Details</h3>

        <label for="name">Admin Name</label>
        <input type="text" name="name" id="name" class="form-control mb-3" value="{{ old('name') }}" required>

        <label for="email">Admin Email</label>
        <input type="email" name="email" id="email" class="form-control mb-3" value="{{ old('email') }}" required>

        <label for="password">Password</label>
        <div class="input-group mb-3">
            <input type="password" name="password" id="password" class="form-control" required>
            <span class="input-group-text" onclick="togglePassword('password')" style="cursor:pointer;">
                <i class="bi bi-eye-fill" id="togglePasswordIcon1"></i>
            </span>
        </div>

        <label for="password_confirmation">Confirm Password</label>
        <div class="input-group mb-3">
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
            <span class="input-group-text" onclick="togglePassword('password_confirmation')" style="cursor:pointer;">
                <i class="bi bi-eye-fill" id="togglePasswordIcon2"></i>
            </span>
        </div>

        <button type="submit" class="btn-submit">Create</button>
    </form>
</div>

<script>
function togglePassword(id){
    const input = document.getElementById(id);
    const icon = id === 'password' ? document.getElementById('togglePasswordIcon1') : document.getElementById('togglePasswordIcon2');
    if(input.type === 'password'){
        input.type = 'text';
        icon.classList.remove('bi-eye-fill');
        icon.classList.add('bi-eye-slash-fill');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash-fill');
        icon.classList.add('bi-eye-fill');
    }
}

// Client-side validation for matching passwords
document.getElementById('adminForm').addEventListener('submit', function(e){
    const pwd = document.getElementById('password').value;
    const confirmPwd = document.getElementById('password_confirmation').value;
    if(pwd !== confirmPwd){
        e.preventDefault();
        alert("Passwords do not match!");
    }
});

$(document).ready(function() {
    // Disable multiple form submissions
    $('form').on('submit', function() {
        var $form = $(this);
        var $btn = $form.find('button[type="submit"]');
        $btn.prop('disabled', true);
        $btn.text('Submitting...'); // Optional: show feedback
    });
});
</script>

</body>
</html>
