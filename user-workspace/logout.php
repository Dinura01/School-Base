<?php
require_once 'includes/Session.php';
require_once 'includes/Auth.php';

// Initialize session
Session::init();

// Create auth instance
$auth = new Auth();

// Perform logout
$result = $auth->logout();

// Clear all session data
Session::destroy();

// Redirect to login page
header('Location: /login.php');
exit();
