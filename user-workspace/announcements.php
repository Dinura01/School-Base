<?php
session_start();
include 'db.php';

// Check if the user is logged in and is a principal
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'principal') {
    header("Location: login.php");
    exit();
}

// Handle form submission for adding announcements
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['announcement'])) {
    $announcement = $_POST['announcement'];

    // Prepare and execute the SQL statement to insert the announcement
    try {
        $stmt = $pdo->prepare("INSERT INTO Announcements (announcement) VALUES (:announcement)");
        $stmt->bindParam(':announcement', $announcement);
        $stmt->execute();
        $message = "Announcement added successfully!";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Handle update request
if (isset($_GET['update'])) {
    $id = $_GET['update'];
    $stmt = $pdo->prepare("SELECT * FROM Announcements WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $announcementToUpdate = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Handle form submission for updating announcements
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_announcement'])) {
    $id = $_POST['id'];
    $announcement = $_POST['update_announcement'];

    // Prepare and execute the SQL statement to update the announcement
    try {
        $stmt = $pdo->prepare("UPDATE Announcements SET announcement = :announcement WHERE id = :id");
        $stmt->bindParam(':announcement', $announcement);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $message = "Announcement updated successfully!";
        header("Location: announcements.php"); // Redirect to avoid resubmission
        exit();
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Handle delete request
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Prepare and execute the SQL statement to delete the announcement
    try {
        $stmt = $pdo->prepare("DELETE FROM Announcements WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $message = "Announcement deleted successfully!";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Fetch existing announcements
$announcements = $pdo->query("SELECT * FROM Announcements ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Announcements</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Manage Announcements</h2>
        <?php if (isset($message)) echo "<div class='alert alert-success'>$message</div>"; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="announcement">New Announcement:</label>
                <textarea class="form-control" id="announcement" name="announcement" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Add Announcement</button>
        </form>

        <h3 class="mt-5">Existing Announcements</h3>
        <ul class="list-group">
            <?php foreach ($announcements as $ann) : ?>
                <li class="list-group-item">
                    <?php echo htmlspecialchars($ann['announcement']); ?>
                    <a href="announcements.php?update=<?php echo $ann['id']; ?>" class="btn btn-warning btn-sm float-right ml-2">Update</a>
                    <a href="announcements.php?delete=<?php echo $ann['id']; ?>" class="btn btn-danger btn-sm float-right">Delete</a>
                </li>
            <?php endforeach; ?>
        </ul>

        <?php if (isset($announcementToUpdate)): ?>
            <form method="POST" action="">
                <input type="hidden" name="id" value="<?php echo $announcementToUpdate['id']; ?>">
                <div class="form-group">
                    <label for="update_announcement">Update Announcement:</label>
                    <textarea class="form-control" id="update_announcement" name="update_announcement" required><?php echo htmlspecialchars($announcementToUpdate['announcement']); ?></textarea>
                </div>
                <button type="submit" class="btn btn-success">Save Changes</button>
                <a href="announcements.php" class="btn btn-secondary">Cancel</a>
            </form>
        <?php endif; ?>

        <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </div>
</body>
</html>