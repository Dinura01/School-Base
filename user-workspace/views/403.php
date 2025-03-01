<?php
$pageTitle = '403 - Forbidden';
$errorCode = 403;
$errorMessage = 'Access Denied';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - School Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
        }
        .error-container {
            text-align: center;
            padding: 2rem;
            max-width: 600px;
        }
        .error-code {
            font-size: 8rem;
            font-weight: bold;
            color: #dc3545;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        .error-message {
            font-size: 2rem;
            color: #343a40;
            margin-bottom: 2rem;
        }
        .error-details {
            color: #6c757d;
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }
        .error-icon {
            font-size: 5rem;
            color: #dc3545;
            margin-bottom: 2rem;
        }
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }
        .action-button {
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .helpful-links {
            margin-top: 2rem;
            padding: 1rem;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .helpful-links h5 {
            color: #343a40;
            margin-bottom: 1rem;
        }
        .helpful-links ul {
            list-style: none;
            padding: 0;
            margin: 0;
            text-align: left;
        }
        .helpful-links li {
            margin-bottom: 0.5rem;
        }
        .helpful-links a {
            color: #007bff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .helpful-links a:hover {
            color: #0056b3;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-lock"></i>
        </div>
        
        <div class="error-code">403</div>
        <div class="error-message">Access Denied</div>
        
        <div class="error-details">
            <p>Sorry, you don't have permission to access this page.</p>
            <p>If you believe this is a mistake, please contact your system administrator.</p>
        </div>

        <div class="action-buttons">
            <button onclick="history.back()" class="btn btn-outline-secondary action-button">
                <i class="fas fa-arrow-left"></i>
                Go Back
            </button>
            <a href="/" class="btn btn-primary action-button">
                <i class="fas fa-home"></i>
                Return Home
            </a>
        </div>

        <div class="helpful-links">
            <h5>You might want to:</h5>
            <ul>
                <li>
                    <a href="/dashboard">
                        <i class="fas fa-tachometer-alt"></i>
                        Return to your dashboard
                    </a>
                </li>
                <li>
                    <a href="/profile">
                        <i class="fas fa-user"></i>
                        Check your profile settings
                    </a>
                </li>
                <li>
                    <a href="/contact">
                        <i class="fas fa-envelope"></i>
                        Contact support
                    </a>
                </li>
            </ul>
        </div>

        <?php if (ENVIRONMENT === 'development'): ?>
            <div class="mt-4 text-start">
                <div class="alert alert-info">
                    <h6 class="alert-heading">Debug Information:</h6>
                    <hr>
                    <p class="mb-0"><strong>Requested URL:</strong> <?php echo $_SERVER['REQUEST_URI']; ?></p>
                    <p class="mb-0"><strong>User Role:</strong> <?php echo Session::getUserRole() ?? 'Not logged in'; ?></p>
                    <p class="mb-0"><strong>Required Permission:</strong> <?php echo $_GET['permission'] ?? 'Unknown'; ?></p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Track 403 errors for analytics
        if (typeof gtag !== 'undefined') {
            gtag('event', '403_error', {
                'event_category': 'error',
                'event_label': window.location.pathname
            });
        }

        // Add to browser history so back button works
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>
</html>
