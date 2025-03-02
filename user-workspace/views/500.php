<?php
$pageTitle = '500 - Internal Server Error';
$errorCode = 500;
$errorMessage = 'Internal Server Error';

// Get error details if in development mode
$errorDetails = '';
if (ENVIRONMENT === 'development' && isset($exception)) {
    $errorDetails = [
        'type' => get_class($exception),
        'message' => $exception->getMessage(),
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'trace' => $exception->getTraceAsString()
    ];
}
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
            max-width: 800px;
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
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .action-button {
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .support-info {
            background-color: #fff;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .support-info h5 {
            color: #343a40;
            margin-bottom: 1rem;
        }
        .support-info p {
            margin-bottom: 0.5rem;
        }
        .error-trace {
            text-align: left;
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 4px;
            margin-top: 1rem;
            overflow-x: auto;
        }
        .error-trace pre {
            margin: 0;
            font-size: 0.875rem;
        }
        .refresh-button {
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        
        <div class="error-code">500</div>
        <div class="error-message">Internal Server Error</div>
        
        <div class="error-details">
            <p>Sorry, something went wrong on our end.</p>
            <p>Our team has been notified and we're working to fix the issue.</p>
        </div>

        <div class="action-buttons">
            <button onclick="location.reload()" class="btn btn-outline-primary action-button">
                <i class="fas fa-sync-alt refresh-button"></i>
                Try Again
            </button>
            <button onclick="history.back()" class="btn btn-outline-secondary action-button">
                <i class="fas fa-arrow-left"></i>
                Go Back
            </button>
            <a href="/" class="btn btn-primary action-button">
                <i class="fas fa-home"></i>
                Return Home
            </a>
        </div>

        <div class="support-info">
            <h5>Need Immediate Assistance?</h5>
            <p>Contact our support team:</p>
            <p>
                <i class="fas fa-envelope me-2"></i>
                <a href="mailto:support@school.com">support@school.com</a>
            </p>
            <p>
                <i class="fas fa-phone me-2"></i>
                <a href="tel:+1234567890">+1 (234) 567-890</a>
            </p>
            <p class="mb-0">
                <small>Please include the error ID: <?php echo uniqid(); ?></small>
            </p>
        </div>

        <?php if (ENVIRONMENT === 'development' && $errorDetails): ?>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Debug Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Error Type:</strong> <?php echo htmlspecialchars($errorDetails['type']); ?>
                    </div>
                    <div class="mb-3">
                        <strong>Message:</strong> <?php echo htmlspecialchars($errorDetails['message']); ?>
                    </div>
                    <div class="mb-3">
                        <strong>File:</strong> <?php echo htmlspecialchars($errorDetails['file']); ?>
                    </div>
                    <div class="mb-3">
                        <strong>Line:</strong> <?php echo htmlspecialchars($errorDetails['line']); ?>
                    </div>
                    <div>
                        <strong>Stack Trace:</strong>
                        <div class="error-trace">
                            <pre><?php echo htmlspecialchars($errorDetails['trace']); ?></pre>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Track 500 errors for analytics
        if (typeof gtag !== 'undefined') {
            gtag('event', '500_error', {
                'event_category': 'error',
                'event_label': window.location.pathname,
                'error_id': '<?php echo uniqid(); ?>'
            });
        }

        // Auto-refresh functionality (optional)
        let refreshAttempts = 0;
        const maxRefreshAttempts = 3;
        const refreshInterval = 10000; // 10 seconds

        function autoRefresh() {
            if (refreshAttempts < maxRefreshAttempts) {
                setTimeout(() => {
                    refreshAttempts++;
                    location.reload();
                }, refreshInterval);
            }
        }

        // Uncomment to enable auto-refresh
        // autoRefresh();

        // Add to browser history so back button works
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>
</html>
