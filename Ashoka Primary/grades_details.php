<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'parent') {
    header("Location: login.php");
    exit();
}

$student_id = $_GET['student_id'];
$grades_records = $conn->query("SELECT * FROM Grades WHERE student_id = $student_id");
$student_info = $conn->query("SELECT name FROM Students WHERE id = $student_id")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Grades Details for <?php echo $student_info['name']; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Grades Details for <?php echo $student_info['name']; ?></h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Grade</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $grades_records->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['subject']; ?></td>
                        <td><?php echo $row['grade']; ?></td </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="view_grades.php" class="btn btn-secondary mt-3">Back to View Grades</a>
    </div>
</body>
</html>