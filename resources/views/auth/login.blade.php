<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Neshat us Sania</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4e73df;
            --primary-darker: #224abe;
            --background-color: #f0f2f5;
            --danger-color: #e74a3b;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--background-color);
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .login-wrapper {
            width: 100%;
            max-width: 1000px;
            background-color: white;
            border-radius: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        .login-branding {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-darker));
            color: white;
            padding: 4rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .logo-container {
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            /* Base padding */
        }

        .login-branding .logo-container {
            width: 120px;
            height: 120px;
            margin: 0 auto 1.5rem auto;
            /* Center it and add margin bottom */
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.2);
        }

        .login-branding .logo-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .login-branding h2 {
            font-weight: 700;
        }

        .login-form-container {
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* Mobile logo specific styles */
        .mobile-logo-wrapper {
            margin-bottom: 1.5rem;
        }

        .mobile-logo-wrapper .logo-container {
            width: 100px;
            height: 100px;
            margin: 0 auto;
            padding: 0.75rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .mobile-logo-wrapper .logo-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .form-control {
            border-radius: 0.75rem;
            padding: 1rem 1.25rem;
            border: 1px solid #e0e0e0;
            transition: all 0.2s ease-in-out;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(78, 115, 223, 0.2);
        }

        .form-control.is-invalid {
            border-color: var(--danger-color);
        }

        .form-control.is-invalid:focus {
            box-shadow: 0 0 0 4px rgba(231, 74, 59, 0.2);
        }

        .form-floating>label {
            padding: 1rem 1.25rem;
            color: #6c757d;
        }

        .btn-primary {
            background: linear-gradient(90deg, var(--primary-color), var(--primary-darker));
            border: none;
            border-radius: 0.75rem;
            padding: 1rem;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(78, 115, 223, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(78, 115, 223, 0.4);
        }

        .alert-danger {
            border-radius: 0.75rem;
            background-color: #fbebee;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .password-toggle-icon {
            position: absolute;
            top: 50%;
            right: 1.25rem;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            z-index: 100;
        }

        @media (max-width: 991.98px) {
            .login-branding {
                display: none;
            }

            .login-form-container {
                padding: 2.5rem;
            }

            .login-wrapper {
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-wrapper row g-0">
            <!-- Branding Section (Visible on Large Screens) -->
            <div class="col-lg-5 login-branding">
                <div>
                    <div class="logo-container">
                        <img src="{{ asset('images/logo.png') }}" alt="School Logo">
                    </div>
                    <h2 class="mb-3">Neshat us Sania</h2>
                    <p class="lead">Learning Management System</p>
                </div>
            </div>

            <!-- Form Section -->
            <div class="col-lg-7 login-form-container">
                <div class="mobile-logo-wrapper d-lg-none">
                    <div class="logo-container">
                        <img src="{{ asset('images/logo.png') }}" alt="School Logo">
                    </div>
                </div>
                <h3 class="fw-bold text-center mb-2">Welcome Back!</h3>
                <p class="text-muted text-center mb-4">Sign in to continue to your dashboard.</p>

                @if ($errors->any())
                <div class="alert alert-danger d-flex align-items-center p-2 mb-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div class="small">
                        @foreach ($errors->all() as $error)
                        {{ $error }}
                        @endforeach
                    </div>
                </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-floating mb-3">
                        <input class="form-control @error('email') is-invalid @enderror" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="name@example.com">
                        <label for="email"><i class="bi bi-envelope-fill me-2"></i>Email address</label>
                    </div>
                    <div class="form-floating mb-4 position-relative">
                        <input class="form-control pe-5 @error('email') is-invalid @enderror" id="password" type="password" name="password" required placeholder="Password">
                        <label for="password"><i class="bi bi-lock-fill me-2"></i>Password</label>
                        <i class="bi bi-eye-slash password-toggle-icon" id="togglePassword"></i>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" id="remember_me" type="checkbox" name="remember">
                            <label class="form-check-label" for="remember_me">Remember Me</label>
                        </div>
                        <a href="#" class="small text-decoration-none">Forgot Password?</a>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Login
                        </button>
                    </div>
                </form>
                <div class="text-center py-3 mt-4">
                    <div class="small text-muted">© {{ date('Y') }} Neshat us Sania. All Rights Reserved.</div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        if (togglePassword && password) {
            togglePassword.addEventListener('click', function(e) {
                // toggle the type attribute
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);

                // toggle the eye / eye slash icon
                this.classList.toggle('bi-eye');
                this.classList.toggle('bi-eye-slash');
            });
        }
    </script>
</body>

</html>