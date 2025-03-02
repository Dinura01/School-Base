<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'parent') {
    header("Location: login.php");
    exit();
}

$parent_id = $_SESSION['user_id'];
$students = $conn->query("SELECT * FROM Students WHERE parent_id = $parent_id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Attendance</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h2 class="mt-5">View Attendance</h2>
        <h4>Select a Student</h4>
        <form method="GET" action="attendance_details.php">
            <select name="student_id" class="form-control" required>
                <option value="">Select Student</option>
                <?php while ($student = $students->fetch_assoc()): ?>
                    <option value="<?php echo $student['id']; ?>"><?php echo $student['name']; ?></option>
                <?php endwhile; ?>
            </select>
            <button type="submit" class="btn btn-primary mt-3">View Attendance</button>
        </form>
        <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </div>
</body>
</html>