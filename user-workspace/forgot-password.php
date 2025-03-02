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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';

    if (empty($email)) {
        $error = 'Please enter your email address';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } else {
        try {
            $auth = new Auth();
            if ($auth->sendPasswordResetLink($email)) {
                $success = 'Password reset instructions have been sent to your email.';
            } else {
                $error = 'Email address not found in our records.';
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
    <title>Forgot Password - School Management System</title>
    
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
        .forgot-password-container {
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
        .back-to-login {
            font-size: 14px;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="forgot-password-container">
        <!-- Forgot Password Form -->
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
                    <p class="text-muted mb-4">
                        Enter your email address and we'll send you instructions to reset your password.
                    </p>

                    <form method="POST" action="" class="needs-validation" novalidate>
                        <div class="form-floating">
                            <input type="email" class="form-control" id="email" name="email" 
                                   placeholder="name@example.com" required>
                            <label for="email">Email address</label>
                            <div class="invalid-feedback">
                                Please enter a valid email address.
                            </div>
                        </div>

                        <button class="btn btn-primary w-100 btn-reset mb-3" type="submit">
                            <i class="fas fa-paper-plane me-2"></i>Send Reset Link
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Back to Login Link -->
        <div class="back-to-login text-center mt-3">
            <a href="/login.php" class="text-decoration-none">
                <i class="fas fa-arrow-left me-2"></i>Back to Login
            </a>
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

        // Auto-hide alerts
        window.setTimeout(function() {
            document.querySelectorAll('.alert').forEach(function(alert) {
                bootstrap.Alert.getOrCreateInstance(alert).close();
            });
        }, 5000);

        // Disable form resubmission
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>
</html>
