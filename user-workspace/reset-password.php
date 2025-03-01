<?php
session_start();
require_once 'config/config.php';
require_once 'includes/Auth.php';
require_once 'includes/Session.php';

// Get token from URL
$token = $_GET['token'] ?? '';

if (empty($token)) {
    header('Location: /login.php');
    exit();
}

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($password) || empty($confirm_password)) {
        $error = 'All fields are required';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        try {
            $auth = new Auth();
            if ($auth->resetPassword($token, $password)) {
                $success = 'Password has been reset successfully. You can now login with your new password.';
                // Redirect to login page after 3 seconds
                header("refresh:3;url=/login.php");
            } else {
                $error = 'Invalid or expired reset token.';
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
    <title>Reset Password - School Management System</title>
    
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
        .reset-password-container {
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
        .btn-reset {
            padding: 12px;
            font-weight: 500;
        }
        .password-requirements {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }
        .password-strength {
            height: 5px;
            margin-top: 10px;
            transition: all 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="reset-password-container">
        <!-- Reset Password Form -->
        <div class="card">
            <div class="card-header">
                <img src="/assets/img/logo.png" alt="School Logo" class="school-logo">
                <h4 class="mb-0">Reset Password</h4>
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
                <?php else: ?>
                    <form method="POST" action="" class="needs-validation" novalidate>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="New Password" required minlength="8">
                            <label for="password">New Password</label>
                            <div class="invalid-feedback">
                                Password must be at least 8 characters long.
                            </div>
                            <div class="password-requirements">
                                Password must:
                                <ul class="mb-0">
                                    <li id="length-check">Be at least 8 characters long</li>
                                    <li id="uppercase-check">Contain at least one uppercase letter</li>
                                    <li id="lowercase-check">Contain at least one lowercase letter</li>
                                    <li id="number-check">Contain at least one number</li>
                                    <li id="special-check">Contain at least one special character</li>
                                </ul>
                            </div>
                            <div class="progress password-strength">
                                <div class="progress-bar" role="progressbar" style="width: 0%"></div>
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

                        <button class="btn btn-primary w-100 btn-reset" type="submit" id="submitBtn" disabled>
                            <i class="fas fa-key me-2"></i>Reset Password
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password strength checker
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        const submitBtn = document.getElementById('submitBtn');
        const progressBar = document.querySelector('.progress-bar');

        const checks = {
            length: { element: document.getElementById('length-check'), regex: /.{8,}/ },
            uppercase: { element: document.getElementById('uppercase-check'), regex: /[A-Z]/ },
            lowercase: { element: document.getElementById('lowercase-check'), regex: /[a-z]/ },
            number: { element: document.getElementById('number-check'), regex: /[0-9]/ },
            special: { element: document.getElementById('special-check'), regex: /[!@#$%^&*]/ }
        };

        function updatePasswordStrength() {
            let strength = 0;
            let passedChecks = 0;

            Object.entries(checks).forEach(([key, check]) => {
                if (check.regex.test(password.value)) {
                    check.element.classList.add('text-success');
                    check.element.classList.remove('text-muted');
                    passedChecks++;
                } else {
                    check.element.classList.remove('text-success');
                    check.element.classList.add('text-muted');
                }
            });

            strength = (passedChecks / Object.keys(checks).length) * 100;
            progressBar.style.width = strength + '%';

            if (strength < 40) {
                progressBar.className = 'progress-bar bg-danger';
            } else if (strength < 80) {
                progressBar.className = 'progress-bar bg-warning';
            } else {
                progressBar.className = 'progress-bar bg-success';
            }

            // Enable submit button only if all checks pass and passwords match
            submitBtn.disabled = !(strength === 100 && password.value === confirmPassword.value);
        }

        password.addEventListener('input', updatePasswordStrength);
        confirmPassword.addEventListener('input', updatePasswordStrength);

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

        // Auto-hide alerts
        window.setTimeout(function() {
            document.querySelectorAll('.alert').forEach(function(alert) {
                bootstrap.Alert.getOrCreateInstance(alert).close();
            });
        }, 5000);

        // Prevent form resubmission
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>
</html>
