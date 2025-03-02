<?php
$pageTitle = 'System Maintenance';
$maintenanceMessage = MAINTENANCE_MESSAGE ?? 'System is under maintenance. Please try again later.';
$estimatedTime = $_ENV['MAINTENANCE_END_TIME'] ?? '+1 hour';
$endTime = strtotime($estimatedTime);
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .maintenance-container {
            text-align: center;
            padding: 2rem;
            max-width: 600px;
        }
        .maintenance-icon {
            font-size: 5rem;
            color: #ffc107;
            margin-bottom: 2rem;
            animation: wrench 2.5s ease infinite;
        }
        @keyframes wrench {
            0% { transform: rotate(-12deg); }
            8% { transform: rotate(12deg); }
            10% { transform: rotate(24deg); }
            18% { transform: rotate(-24deg); }
            20% { transform: rotate(-24deg); }
            28% { transform: rotate(24deg); }
            30% { transform: rotate(24deg); }
            38% { transform: rotate(-24deg); }
            40% { transform: rotate(-24deg); }
            48% { transform: rotate(24deg); }
            50% { transform: rotate(24deg); }
            58% { transform: rotate(-24deg); }
            60% { transform: rotate(-24deg); }
            68% { transform: rotate(24deg); }
            75% { transform: rotate(0deg); }
        }
        .maintenance-title {
            font-size: 2.5rem;
            color: #343a40;
            margin-bottom: 1rem;
        }
        .maintenance-message {
            color: #6c757d;
            margin-bottom: 2rem;
            font-size: 1.1rem;
            line-height: 1.6;
        }
        .countdown-container {
            background-color: #fff;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .countdown-title {
            color: #343a40;
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }
        .countdown {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }
        .countdown-item {
            text-align: center;
            min-width: 80px;
        }
        .countdown-number {
            font-size: 2rem;
            font-weight: bold;
            color: #007bff;
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 0.5rem;
            margin-bottom: 0.5rem;
        }
        .countdown-label {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
        }
        .notification-form {
            background-color: #fff;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .progress-container {
            margin-top: 2rem;
        }
        .progress {
            height: 10px;
            margin-bottom: 1rem;
        }
        .maintenance-footer {
            color: #6c757d;
            font-size: 0.9rem;
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <div class="maintenance-icon">
            <i class="fas fa-wrench"></i>
        </div>
        
        <h1 class="maintenance-title">System Maintenance</h1>
        
        <div class="maintenance-message">
            <p><?php echo htmlspecialchars($maintenanceMessage); ?></p>
            <p>We're working hard to improve your experience and will be back shortly.</p>
        </div>

        <div class="countdown-container">
            <h2 class="countdown-title">Estimated Time Remaining</h2>
            <div class="countdown" id="countdown">
                <div class="countdown-item">
                    <div class="countdown-number" id="hours">00</div>
                    <div class="countdown-label">Hours</div>
                </div>
                <div class="countdown-item">
                    <div class="countdown-number" id="minutes">00</div>
                    <div class="countdown-label">Minutes</div>
                </div>
                <div class="countdown-item">
                    <div class="countdown-number" id="seconds">00</div>
                    <div class="countdown-label">Seconds</div>
                </div>
            </div>
        </div>

        <div class="notification-form">
            <h3 class="h5 mb-3">Get Notified When We're Back</h3>
            <form id="notificationForm" class="needs-validation" novalidate>
                <div class="input-group mb-3">
                    <input type="email" class="form-control" placeholder="Enter your email" 
                           required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-bell me-2"></i>Notify Me
                    </button>
                </div>
                <div class="form-text">
                    We'll email you once the system is back online.
                </div>
            </form>
        </div>

        <div class="progress-container">
            <div class="progress">
                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                     role="progressbar" style="width: 75%"></div>
            </div>
            <small class="text-muted">Maintenance Progress: 75% Complete</small>
        </div>

        <div class="maintenance-footer">
            <p>
                For urgent matters, please contact support:<br>
                <a href="mailto:support@school.com">support@school.com</a>
            </p>
            <p class="mb-0">
                <small>Maintenance ID: <?php echo uniqid('maint_'); ?></small>
            </p>
        </div>
    </div>

    <script>
        // Countdown Timer
        function updateCountdown() {
            const now = new Date().getTime();
            const endTime = <?php echo $endTime * 1000; ?>;
            const timeLeft = endTime - now;

            if (timeLeft <= 0) {
                document.getElementById('countdown').innerHTML = `
                    <div class="alert alert-info mb-0">
                        Maintenance should be complete. Please try refreshing the page.
                    </div>
                `;
                return;
            }

            const hours = Math.floor(timeLeft / (1000 * 60 * 60));
            const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

            document.getElementById('hours').textContent = String(hours).padStart(2, '0');
            document.getElementById('minutes').textContent = String(minutes).padStart(2, '0');
            document.getElementById('seconds').textContent = String(seconds).padStart(2, '0');
        }

        setInterval(updateCountdown, 1000);
        updateCountdown();

        // Form Validation
        const form = document.getElementById('notificationForm');
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            if (!form.checkValidity()) {
                event.stopPropagation();
            } else {
                const email = form.querySelector('input[type="email"]').value;
                // Here you would typically send this to your server
                alert(`Thank you! We'll notify ${email} when the system is back online.`);
                form.reset();
            }
            form.classList.add('was-validated');
        });

        // Auto-refresh check
        function checkStatus() {
            fetch(window.location.href, { method: 'HEAD' })
                .then(response => {
                    if (!response.url.includes('maintenance.php')) {
                        window.location.reload();
                    }
                })
                .catch(() => {
                    // If fetch fails, do nothing and try again later
                });
        }

        // Check status every minute
        setInterval(checkStatus, 60000);
    </script>
</body>
</html>
