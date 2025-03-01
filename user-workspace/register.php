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

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? '';
    $phone = $_POST['phone'] ?? '';

    // Basic validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password) || empty($role)) {
        $error = 'All fields are required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        try {
            $auth = new Auth();
            if ($auth->register($name, $email, $password, $role, $phone)) {
                $success = 'Registration successful! You can now login.';
                // Redirect to login page after 3 seconds
                header("refresh:3;url=/login.php");
            } else {
                $error = 'Registration failed. Email might already be in use.';
            }
        } catch (Exception $e) {
            if (ENVIRONMENT === 'development') {
                $error = $e->getMessage();
            } else {
                $error = 'An error occurred. Please try again later.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - School Management System</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
        }
        .register-container {
            max-width: 500px;
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
        .btn-register {
            padding: 12px;
            font-weight: 500;
        }
        .login-link {
            font-size: 14px;
            text-align: center;
            margin-top: 20px;
        }
        .password-requirements {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <!-- Registration Form -->
        <div class="card">
            <div class="card-header">
                <img src="/assets/img/logo.png" alt="School Logo" class="school-logo">
                <h4 class="mb-0">Create an Account</h4>
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
                        <input type="text" class="form-control" id="name" name="name" 
                               placeholder="Full Name" required>
                        <label for="name">Full Name</label>
                        <div class="invalid-feedback">
                            Please enter your full name.
                        </div>
                    </div>

                    <div class="form-floating">
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder="name@example.com" required>
                        <label for="email">Email address</label>
                        <div class="invalid-feedback">
                            Please enter a valid email address.
                        </div>
                    </div>

                    <div class="form-floating">
                        <input type="tel" class="form-control" id="phone" name="phone" 
                               placeholder="Phone Number">
                        <label for="phone">Phone Number (Optional)</label>
                    </div>

                    <div class="form-floating">
                        <select class="form-select" id="role" name="role" required>
                            <option value="">Select Role</option>
                            <option value="teacher">Teacher</option>
                            <option value="parent">Parent</option>
                        </select>
                        <label for="role">Role</label>
                        <div class="invalid-feedback">
                            Please select your role.
                        </div>
                    </div>

                    <div class="form-floating">
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Password" required minlength="8">
                        <label for="password">Password</label>
                        <div class="invalid-feedback">
                            Password must be at least 8 characters long.
                        </div>
                        <div class="password-requirements">
                            Password must be at least 8 characters long and contain letters and numbers.
                        </div>
                    </div>

                    <div class="form-floating">
                        <input type="password" class="form-control" id="confirm_password" 
                               name="confirm_password" placeholder="Confirm Password" required>
                        <label for="confirm_password">Confirm Password</label>
                        <div class="invalid-feedback">
                            Passwords do not match.
                        </div>
                    </div>

                    <button class="btn btn-primary w-100 btn-register mb-3" type="submit">
                        <i class="fas fa-user-plus me-2"></i>Register
                    </button>
                </form>
            </div>
        </div>

        <!-- Login Link -->
        <div class="login-link text-center mt-3">
            Already have an account? <a href="/login.php" class="text-decoration-none">Login here</a>
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

        // Password confirmation validation
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');

        function validatePassword() {
            if (password.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity('Passwords do not match');
            } else {
                confirmPassword.setCustomValidity('');
            }
        }

        password.addEventListener('change', validatePassword);
        confirmPassword.addEventListener('keyup', validatePassword);

        // Password visibility toggle
        function addPasswordToggle(inputId) {
            const input = document.getElementById(inputId);
            const togglePassword = document.createElement('button');
            togglePassword.type = 'button';
            togglePassword.className = 'btn btn-outline-secondary position-absolute end-0 top-50 translate-middle-y';
            togglePassword.style.zIndex = '10';
            togglePassword.innerHTML = '<i class="fas fa-eye"></i>';
            togglePassword.style.marginTop = '3px';
            togglePassword.style.marginRight = '10px';
            
            input.parentElement.style.position = 'relative';
            input.parentElement.appendChild(togglePassword);

            togglePassword.addEventListener('click', function() {
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                this.querySelector('i').className = `fas fa-eye${type === 'password' ? '' : '-slash'}`;
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            addPasswordToggle('password');
            addPasswordToggle('confirm_password');
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
