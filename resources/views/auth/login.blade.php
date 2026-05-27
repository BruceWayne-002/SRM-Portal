<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Login - SRM EXAMINATION</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/srmlogo1.jpg') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            width: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(145deg, #f8f0ff 0%, #ffffff 50%, #f8f0ff 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
        }

        /* Main Container */
        .login-container {
            width: 100%;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* Login Card */
        .login-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-radius: 30px;
            padding: 2.5rem;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 25px 50px -12px rgba(100, 13, 60, 0.25);
            border: 1px solid rgba(100, 13, 60, 0.1);
            transition: all 0.3s ease;
        }

        /* Logo */
        .logo-wrapper {
            text-align: center;
            margin-bottom: 1rem;
        }

        .brand-logo {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #640d3c;
            padding: 5px;
            background: white;
            box-shadow: 0 10px 25px -5px rgba(100, 13, 60, 0.3);
        }

        /* Brand Title */
        .brand-title {
            font-weight: 700;
            font-size: 24px;
            color: #640d3c;
            text-align: center;
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }

        /* Login Heading */
        .login-heading {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-heading h4 {
            font-weight: 600;
            font-size: 20px;
            color: #4b5563;
            margin: 0;
            position: relative;
            display: inline-block;
        }

        .login-heading h4::after {
            content: '';
            display: block;
            width: 40px;
            height: 3px;
            background: #640d3c;
            border-radius: 2px;
            margin: 8px auto 0;
        }

        /* Form */
        .login-form {
            width: 100%;
        }

        /* Input Groups */
        .input-group-wrapper {
            margin-bottom: 1.5rem;
        }

        .input-wrapper {
            position: relative;
            width: 100%;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #640d3c;
            font-size: 1.1rem;
            z-index: 1;
            opacity: 0.7;
        }

        .form-control {
            width: 100%;
            height: 52px;
            padding: 0 45px;
            border: 1.5px solid #e5e7eb;
            border-radius: 16px;
            font-size: 15px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
            background: white;
        }

        .form-control:focus {
            border-color: #640d3c;
            box-shadow: 0 0 0 4px rgba(100, 13, 60, 0.1);
            outline: none;
        }

        .form-control.is-invalid {
            border-color: #ef4444;
        }

        .toggle-password {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #640d3c;
            cursor: pointer;
            font-size: 1.2rem;
            z-index: 1;
            opacity: 0.7;
            transition: opacity 0.3s;
        }

        .toggle-password:hover {
            opacity: 1;
        }

        /* Error Messages */
        .error-message {
            color: #ef4444;
            font-size: 13px;
            margin-top: 6px;
            padding-left: 16px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .error-message i {
            font-size: 14px;
        }

        /* Alert */
        .alert-custom {
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 14px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 8px;
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .alert-custom i {
            font-size: 18px;
        }

        /* Login Button */
        .btn-login {
            width: 100%;
            height: 52px;
            background: #640d3c;
            color: white;
            border: none;
            border-radius: 16px;
            font-size: 16px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover:not(:disabled) {
            background: #4a0a2d;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(100, 13, 60, 0.4);
        }

        .btn-login:active:not(:disabled) {
            transform: translateY(0);
        }

        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        /* Footer */
        .footer-text {
            text-align: center;
            margin-top: 2rem;
            font-size: 13px;
            color: #6b7280;
        }

        .footer-text a {
            color: #640d3c;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }

        .footer-text a:hover {
            color: #4a0a2d;
            text-decoration: underline;
        }

        /* Loading Spinner */
        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Responsive Breakpoints */
        @media screen and (max-width: 480px) {
            .login-card {
                padding: 2rem 1.5rem;
                max-width: 100%;
                border-radius: 24px;
            }

            .brand-logo {
                width: 85px;
                height: 85px;
            }

            .brand-title {
                font-size: 22px;
            }

            .login-heading h4 {
                font-size: 18px;
            }

            .form-control {
                height: 48px;
                font-size: 14px;
            }

            .btn-login {
                height: 48px;
                font-size: 15px;
            }

            .footer-text {
                font-size: 12px;
                margin-top: 1.5rem;
            }
        }

        @media screen and (min-width: 481px) and (max-width: 768px) {
            .login-card {
                max-width: 420px;
                padding: 2.5rem 2rem;
            }

            .brand-logo {
                width: 95px;
                height: 95px;
            }

            .brand-title {
                font-size: 24px;
            }
        }

        @media screen and (min-width: 769px) and (max-width: 1024px) {
            .login-card {
                max-width: 440px;
            }
        }

        @media screen and (min-width: 1025px) and (max-width: 1400px) {
            .login-card {
                max-width: 440px;
            }
        }

        /* Landscape Mode */
        @media screen and (max-height: 600px) and (orientation: landscape) {
            .login-container {
                min-height: auto;
                padding: 30px 20px;
            }

            .login-card {
                padding: 1.5rem;
            }

            .brand-logo {
                width: 70px;
                height: 70px;
            }

            .brand-title {
                font-size: 20px;
                margin-bottom: 0.25rem;
            }

            .login-heading {
                margin-bottom: 1rem;
            }

            .input-group-wrapper {
                margin-bottom: 1rem;
            }

            .form-control {
                height: 45px;
            }

            .btn-login {
                height: 45px;
            }

            .footer-text {
                margin-top: 1rem;
            }
        }

        /* Extra Small Devices */
        @media screen and (max-width: 360px) {
            .login-card {
                padding: 1.5rem 1rem;
            }

            .brand-logo {
                width: 75px;
                height: 75px;
            }

            .brand-title {
                font-size: 20px;
            }

            .form-control {
                height: 44px;
                font-size: 13px;
                padding: 0 40px;
            }

            .input-icon {
                left: 12px;
                font-size: 1rem;
            }

            .toggle-password {
                right: 12px;
                font-size: 1rem;
            }

            .btn-login {
                height: 44px;
                font-size: 14px;
            }
        }

        /* Large Screens */
        @media screen and (min-width: 1401px) {
            .login-card {
                max-width: 460px;
                padding: 3rem;
            }

            .brand-logo {
                width: 110px;
                height: 110px;
            }

            .brand-title {
                font-size: 28px;
            }

            .login-heading h4 {
                font-size: 22px;
            }

            .form-control {
                height: 56px;
                font-size: 16px;
            }

            .btn-login {
                height: 56px;
                font-size: 18px;
            }
        }

        /* Dark Mode Support */
        @media (prefers-color-scheme: dark) {
            body {
                background: linear-gradient(145deg, #1a1a1a, #2d2d2d);
            }

            .login-card {
                background: rgba(30, 30, 30, 0.98);
                border-color: rgba(255,255,255,0.1);
            }

            .brand-title {
                color: #e5e7eb;
            }

            .login-heading h4 {
                color: #9ca3af;
            }

            .form-control {
                background: #2d2d2d;
                border-color: #404040;
                color: #e5e7eb;
            }

            .form-control:focus {
                border-color: #8b1b5a;
                box-shadow: 0 0 0 4px rgba(139, 27, 90, 0.2);
            }

            .input-icon,
            .toggle-password {
                color: #9ca3af;
            }

            .footer-text {
                color: #9ca3af;
            }

            .footer-text a {
                color: #8b1b5a;
            }
        }

        /* Remove duplicate CSS */
        .brand-logo {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #e5e5e5;
            background-color: #fff;
        }

        .logo-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <!-- Logo -->
            <div class="logo-wrapper">
                <img src="{{ asset('images/srmlogo1.jpg') }}" 
                     alt="SRM College Logo" 
                     class="brand-logo"
                     onerror="this.src='https://via.placeholder.com/100x100/640d3c/ffffff?text=SRM'">
            </div>

            <!-- Brand Title -->
            <div class="brand-title">SRM Examination</div>

            <!-- Login Heading -->
            <div class="login-heading">
                <h4>Welcome Back</h4>
            </div>

            <!-- Error Alert -->
            @if($errors->any())
                <div class="alert-custom">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login.submit') }}" class="login-form" id="loginForm">
                @csrf
                
                <!-- Email/Username Field -->
                <div class="input-group-wrapper">
                    <div class="input-wrapper">
                        <i class="bi bi-person-circle input-icon"></i>
                        <input type="text" 
                               name="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               placeholder="Email or User ID" 
                               value="{{ old('email') }}" 
                               required
                               autofocus>
                    </div>
                    @error('email')
                        <div class="error-message">
                            <i class="bi bi-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="input-group-wrapper">
                    <div class="input-wrapper">
                        <i class="bi bi-lock-fill input-icon"></i>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               placeholder="Password" 
                               required>
                        <i class="bi bi-eye-slash toggle-password" id="togglePassword"></i>
                    </div>
                    @error('password')
                        <div class="error-message">
                            <i class="bi bi-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-login" id="submitBtn">
                    <span id="btnText">Login</span>
                    <span id="btnSpinner" style="display: none;">
                        <i class="bi bi-arrow-repeat spinner"></i>
                    </span>
                </button>
            </form>

            <!-- Footer -->
            <div class="footer-text">
                &copy; {{ date('Y') }} SRM Examination. All rights reserved.<br>
                Powered by <a href="https://asc.srmtrichy.edu.in/" target="_blank" rel="noopener noreferrer">SRM</a>.
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Toggle Password Visibility
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const password = document.getElementById('password');

            if (togglePassword && password) {
                togglePassword.addEventListener('click', function() {
                    // Toggle type
                    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                    password.setAttribute('type', type);
                    
                    // Toggle icon
                    this.classList.toggle('bi-eye');
                    this.classList.toggle('bi-eye-slash');
                });
            }

            // Form Submit Handler
            const loginForm = document.getElementById('loginForm');
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const btnSpinner = document.getElementById('btnSpinner');

            if (loginForm) {
                loginForm.addEventListener('submit', function(e) {
                    if (!this.checkValidity()) {
                        e.preventDefault();
                        return;
                    }

                    // Disable button and show spinner
                    submitBtn.disabled = true;
                    btnText.textContent = 'Logging in...';
                    btnSpinner.style.display = 'inline-block';
                });
            }

            // Prevent double submission on all forms
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    const submitButton = this.querySelector('button[type="submit"]');
                    if (submitButton) {
                        submitButton.disabled = true;
                    }
                });
            });

            // Auto-hide alerts after 5 seconds
            const alert = document.querySelector('.alert-custom');
            if (alert) {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }, 5000);
            }
        });

        // Handle offline status
        window.addEventListener('offline', function() {
            const alert = document.createElement('div');
            alert.className = 'alert-custom';
            alert.innerHTML = '<i class="bi bi-wifi-off"></i> You are offline. Please check your connection.';
            document.querySelector('.login-card').insertBefore(alert, document.querySelector('.login-form'));
        });

        window.addEventListener('online', function() {
            location.reload();
        });

        // Prevent zoom on input focus for iOS
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('focus', function() {
                document.querySelector('meta[name=viewport]').setAttribute('content', 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes');
            });
        });

        // Add touch feedback for mobile
        document.querySelectorAll('.btn-login, .toggle-password').forEach(element => {
            element.addEventListener('touchstart', function() {
                this.style.opacity = '0.8';
            });
            element.addEventListener('touchend', function() {
                this.style.opacity = '1';
            });
        });
    </script>
</body>
</html>