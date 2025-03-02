<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'parent') {
   header("Location: login.php");
   exit();
}

$student_id = $_GET['student_id'];
$attendance_records = $conn->query("SELECT * FROM Attendance WHERE student_id = $student_id");
$student_info = $conn->query("SELECT name FROM Students WHERE id = $student_id")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <title>Attendance Details for <?php echo $student_info['name']; ?></title>
   <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
   <link rel="stylesheet" href="css/styles.css">
</head>
<body>
   <div class="container">
       <h2 class="mt-5">Attendance Details for <?php echo $student_info['name']; ?></h2>
       <table class="table">
           <thead>
               <tr>
                   <th>Date</th>
                   <th>Status</th>
               </tr>
           </thead>
           <tbody>
               <?php while ($row = $attendance_records->fetch_assoc()): ?>
                   <tr>
                       <td><?php echo $row['date']; ?></td>
                       <td><?php echo $row['status']; ?></td>
                   </tr>
               <?php endwhile; ?>
           </tbody>
       </table>
       <a href="view_attendance.php" class="btn btn-secondary mt-3">Back to View Attendance</a>
   </div>
</body>
</html>