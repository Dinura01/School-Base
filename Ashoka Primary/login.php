<?php
// Include the database connection file
include 'db.php';
session_start(); // Start the session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the form fields are set
    if (isset($_POST['username']) && isset($_POST['password'])) {
        // Get the form data
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Prepare and execute the SQL statement
        try {
            $stmt = $pdo->prepare("SELECT * FROM Users WHERE name = :username AND password = :password");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            $stmt->execute();

            // Check if a user was found
            if ($stmt->rowCount() > 0) {
                // User found, start session and redirect to dashboard
                $user = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch user data
                $_SESSION['username'] = $user['name']; // Store username in session
                $_SESSION['role'] = $user['role']; // Store user role in session
                header("Location: dashboard.php"); // Redirect to dashboard
                exit(); // Ensure no further code is executed
            } else {
                echo "Invalid username or password.";
            }
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
    <title>Login</title>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
</body>
</html>