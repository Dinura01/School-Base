<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'accountant') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];
    $amount = $_POST['amount'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO Invoices (student_id, amount, status) VALUES (?, ?, ?)");
    $stmt->bind_param("ids", $student_id, $amount, $status);
    $stmt->execute();
}

$students = $conn->query("SELECT * FROM Students");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Generate Invoices</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Generate Invoices</h2>
        <form method="POST" class="mb-4">
            <div class="form-group">
                <label for="student_id">Select Student:</label>
                <select name="student_id" class="form-control" required>
                    <option value="">Select Student</option>
                    <?php while ($student = $students->fetch_assoc()): ?>
                        <option value="<?php echo $student['id']; ?>"><?php echo $student['name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="amount">Amount:</label>
                <input type="number" class="form-control" name="amount" required>
            </div>
            <div class="form-group">
                <label for="status">Status:</label>
                <select class="form-control" name="status" required>
                    <option value="paid">Paid</option>
                    <option value="unpaid">Unpaid</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Generate Invoice</button>
        </form>

        <h3>Current Invoices</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student ID</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $invoices = $conn->query("SELECT * FROM Invoices");
                while ($row = $invoices->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['student_id']; ?></td>
                        <td><?php echo $row['amount']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </div>
</body>
</html>