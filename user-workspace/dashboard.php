<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Retrieve user role from session
$username = $_SESSION['username'];
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Guest'; // Default to 'Guest' if role is not set
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css"> <!-- Your custom styles -->
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Dashboard</h2>
        <p>Welcome, <?php echo htmlspecialchars($username); ?>! You are logged in as a <?php echo htmlspecialchars($role); ?>.</p>
        <ul class="list-group">
            <?php if ($role == 'principal'): ?>
                <li class="list-group-item"><a href="announcements.php">Manage Announcements</a></li>
                <li class="list-group-item"><a href="leave_requests.php">Manage Leave Requests</a></li>
                <li class="list-group-item"><a href="view_attendance.php">Generate Attendance Report</a></li>
                <li class="list-group-item"><a href="appointment_requests.php">View Appointments</a></li>
                <li class="list-group-item"><a href="search_student.php">Search Students</a></li>
                <li class="list-group-item"><a href="view_grades.php">View Student Grades</a></li>
                <li class="list-group-item"><a href="generate_reports.php">Generate Reports</a></li>
                <li class="list-group-item"><a href="manage_users.php">Manage Users</a></li>
            <?php elseif ($role == 'parent'): ?>
                <li class="list-group-item"><a href="view_grades.php">View Student Grades</a></li>
                <li class="list-group-item"><a href="view_attendance.php">View Student Attendance</a></li>
                <li class="list-group-item"><a href="appointment_requests.php">Request Appointment</a></li>
            <?php elseif ($role == 'accountant'): ?>
                <li class="list-group-item"><a href="generate_invoices.php">Generate Invoices</a></li>
                <li class="list-group-item"><a href="confirm_payment.php">Confirm Payment</a></li>
                <li class="list-group-item"><a href="payment_history.php">View Payment History</a></li>
                <li class="list-group-item"><a href="payment_report.php">Generate Payment Report</a></li>
                <li class="list-group-item"><a href="invoice_details.php">Get Invoice Details</a></li>
            <?php elseif ($role == 'staff'): ?>
                <li class="list-group-item"><a href="view_profile.php">View Profile</a></li>
                <li class="list-group-item"><a href="search_student.php">Search Students</a></li>
            <?php else: ?>
                <li class="list-group-item">No specific role assigned.</li>
            <?php endif; ?>
        </ul>
        <a href="logout.php" class="btn btn-danger mt-3">Logout</a>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>