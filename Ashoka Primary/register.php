<?php
// Include the database connection file
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the form fields are set
    if (isset($_POST['name']) && isset($_POST['role']) && isset($_POST['password'])) {
        // Get the form data
        $name = $_POST['name'];
        $role = $_POST['role'];
        $password = $_POST['password'];

        // Prepare and execute the SQL statement
        try {
            $stmt = $pdo->prepare("INSERT INTO Users (name, role, password) VALUES (:name, :role, :password)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':password', $password);
            $stmt->execute();

            // Redirect to login page or show success message
            header("Location: login.php");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <title>Register</title>
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="role">Role:</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="principal">Principal</option>
                    <option value="parent">Parent</option>
                    <option value="accountant">Accountant</option>
                    <option value="staff">Staff</option>
                </select>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
    </div>
</body>
</html>