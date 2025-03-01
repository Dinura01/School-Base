<?php
session_start();
include 'db.php';

// Check if the user is logged in and is a principal
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'principal') {
    header("Location: login.php");
    exit();
}

echo "<h1>Search Students</h1>";
// Add your student search code here
?>