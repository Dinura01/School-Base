<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'principal') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $teacher_name = $_POST['teacher_name'];
    $leave_date = $_POST['leave_date'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO LeaveRequests (teacher_name, leave_date, status) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $teacher_name, $leave_date, $status);
    $stmt->execute();
}

$leave_requests = $conn->query("SELECT * FROM LeaveRequests");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leave Requests</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Leave Requests</h2>
        <form method="POST" class="mb-4">
            <div class="form-group">
                <label for="teacher_name">Teacher Name:</label>
                <input type="text" class="form-control" name="teacher_name" required>
            </div>
            <div class="form-group">
                <label for="leave_date">Leave Date:</label>
                <input type="date" class="form-control" name="leave_date" required>
            </div>
            <div class="form-group">
                <label for="status">Status:</label>
                <select class="form-control" name="status" required>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add Leave Request</button>
        </form>

        <h3>Current Leave Requests</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Teacher Name</th>
                    <th>Leave Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $leave_requests->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['teacher_name']; ?></td>
                        <td><?php echo $row['leave_date']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </div>
</body>
</html>