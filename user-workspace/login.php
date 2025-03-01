<?php
session_start();
require_once 'config/config.php';
require_once 'includes/Auth.php';
require_once 'includes/Session.php';

// If user is already logged in, redirect to appropriate dashboard
if (isset($_SESSION['user_id'])) {
    $role = Session::getUserRole();
    header("Location: /{$role}/dashboard");
    exit();
}

$error = '';
$success = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    try {
        $auth = new Auth();
        if ($auth->login($email, $password, $remember)) {
            $role = Session::getUserRole();
            header("Location: /{$role}/dashboard");
            exit();
        } else {
            $error = 'Invalid email or password';
        }
    } catch (Exception $e) {
        if (ENVIRONMENT === 'development') {
            $error = $e->getMessage();
        } else {
            $error = 'An error occurred. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - School Management System</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            max-width: 400px;
            width: 100%;
            padding: 20px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #eee;
            padding: 20px;
            text-align: center;
        }
        .school-logo {
            max-width: 150px;
            height: auto;
            margin-bottom: 20px;
        }
        .card-body {
            padding: 30px;
        }
        .form-floating {
            margin-bottom: 20px;
        }
        .btn-login {
            padding: 12px;
            font-weight: 500;
        }
        .forgot-password {
            font-size: 14px;
        }
        .register-link {
            font-size: 14px;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Login Form -->
        <div class="card">
            <div class="card-header">
                <img src="/assets/img/logo.png" alt="School Logo" class="school-logo">
                <h4 class="mb-0">School Management System</h4>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" class="needs-validation" novalidate>
                    <div class="form-floating">
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder="name@example.com" required>
                        <label for="email">Email address</label>
                        <div class="invalid-feedback">
                            Please enter a valid email address.
                        </div>
                    </div>

                    <div class="form-floating">
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Password" required>
                        <label for="password">Password</label>
                        <div class="invalid-feedback">
                            Please enter your password.
                        </div>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            Remember me
                        </label>
                    </div>

                    <button class="btn btn-primary w-100 btn-login mb-3" type="submit">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </button>

                    <div class="text-center">
                        <a href="/forgot-password.php" class="forgot-password text-decoration-none">
                            Forgot your password?
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Register Link -->
        <div class="register-link text-center mt-3">
            Don't have an account? <a href="/register.php" class="text-decoration-none">Register here</a>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        (function() {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();

        // Password visibility toggle
        document.addEventListener('DOMContentLoaded', function() {
            const password = document.getElementById('password');
            const togglePassword = document.createElement('button');
            togglePassword.type = 'button';
            togglePassword.className = 'btn btn-outline-secondary position-absolute end-0 top-50 translate-middle-y';
            togglePassword.style.zIndex = '10';
            togglePassword.innerHTML = '<i class="fas fa-eye"></i>';
            togglePassword.style.marginTop = '3px';
            togglePassword.style.marginRight = '10px';
            
            password.parentElement.style.position = 'relative';
            password.parentElement.appendChild(togglePassword);

            togglePassword.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                this.querySelector('i').className = `fas fa-eye${type === 'password' ? '' : '-slash'}`;
            });
        });

        // Auto-hide alerts
        window.setTimeout(function() {
            document.querySelectorAll('.alert').forEach(function(alert) {
                bootstrap.Alert.getOrCreateInstance(alert).close();
            });
        }, 5000);
    </script>
</body>
</html>
