<?php
$errorCode = $errorCode ?? 500;
$errorMessage = $errorMessage ?? 'An unexpected error occurred.';
$pageTitle = "Error $errorCode";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - School Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            max-width: 500px;
        }
        .error-code {
            font-size: 6rem;
            font-weight: bold;
            color: #dc3545;
            margin-bottom: 1rem;
        }
        .error-message {
            font-size: 1.5rem;
            color: #6c757d;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code"><?php echo $errorCode; ?></div>
        <div class="error-message"><?php echo htmlspecialchars($errorMessage); ?></div>
        
        <?php if ($errorCode == 404): ?>
            <p class="text-muted mb-4">The page you're looking for doesn't exist or has been moved.</p>
        <?php elseif ($errorCode == 403): ?>
            <p class="text-muted mb-4">You don't have permission to access this resource.</p>
        <?php elseif ($errorCode == 500): ?>
            <p class="text-muted mb-4">Something went wrong on our end. Please try again later.</p>
        <?php endif; ?>

        <div class="d-flex justify-content-center gap-3">
            <button onclick="history.back()" class="btn btn-outline-secondary">
                Go Back
            </button>
            <a href="/" class="btn btn-primary">
                Home
            </a>
        </div>

        <?php if (defined('ENVIRONMENT') && ENVIRONMENT === 'development' && isset($exception)): ?>
            <div class="mt-4">
                <div class="alert alert-danger">
                    <h5>Debug Information:</h5>
                    <p><strong>Type:</strong> <?php echo get_class($exception); ?></p>
                    <p><strong>Message:</strong> <?php echo $exception->getMessage(); ?></p>
                    <p><strong>File:</strong> <?php echo $exception->getFile(); ?></p>
                    <p><strong>Line:</strong> <?php echo $exception->getLine(); ?></p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
