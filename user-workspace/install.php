<?php
session_start();
require_once 'config/config.php';

$step = $_GET['step'] ?? 1;
$error = '';
$success = '';

function checkRequirements() {
    $requirements = [
        'PHP Version >= 8.0' => version_compare(PHP_VERSION, '8.0.0', '>='),
        'PDO Extension' => extension_loaded('pdo'),
        'MySQL Extension' => extension_loaded('mysqli'),
        'MBString Extension' => extension_loaded('mbstring'),
        'XML Extension' => extension_loaded('xml'),
        'CURL Extension' => extension_loaded('curl'),
        'uploads/ Directory Writable' => is_writable('uploads'),
        'logs/ Directory Writable' => is_writable('logs'),
        'cache/ Directory Writable' => is_writable('cache'),
        'backups/ Directory Writable' => is_writable('backups')
    ];

    return $requirements;
}

function testDatabaseConnection($host, $dbname, $username, $password) {
    try {
        $dsn = "mysql:host=$host;charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step == 2) {
        $db_host = $_POST['db_host'];
        $db_name = $_POST['db_name'];
        $db_user = $_POST['db_user'];
        $db_pass = $_POST['db_pass'];

        if (testDatabaseConnection($db_host, $db_name, $db_user, $db_pass)) {
            // Save database configuration
            $config_content = file_get_contents('config/config.php');
            $config_content = preg_replace(
                "/define\('DB_HOST',.*?\);/",
                "define('DB_HOST', '$db_host');",
                $config_content
            );
            $config_content = preg_replace(
                "/define\('DB_NAME',.*?\);/",
                "define('DB_NAME', '$db_name');",
                $config_content
            );
            $config_content = preg_replace(
                "/define\('DB_USER',.*?\);/",
                "define('DB_USER', '$db_user');",
                $config_content
            );
            $config_content = preg_replace(
                "/define\('DB_PASS',.*?\);/",
                "define('DB_PASS', '$db_pass');",
                $config_content
            );

            file_put_contents('config/config.php', $config_content);
            
            // Import database schema
            try {
                $pdo = new PDO("mysql:host=$db_host", $db_user, $db_pass);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // Create database if not exists
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name`");
                $pdo->exec("USE `$db_name`");
                
                // Import schema
                $sql = file_get_contents('school_management.sql');
                $pdo->exec($sql);
                
                $success = 'Database configuration successful!';
                header('Location: install.php?step=3');
                exit;
            } catch (PDOException $e) {
                $error = 'Error importing database schema: ' . $e->getMessage();
            }
        } else {
            $error = 'Could not connect to database. Please check your credentials.';
        }
    }
    
    if ($step == 3) {
        $admin_name = $_POST['admin_name'];
        $admin_email = $_POST['admin_email'];
        $admin_password = $_POST['admin_password'];
        
        if (strlen($admin_password) < 8) {
            $error = 'Password must be at least 8 characters long.';
        } else {
            try {
                $pdo = new PDO(
                    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
                    DB_USER,
                    DB_PASS
                );
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                $hash = password_hash($admin_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("
                    INSERT INTO Users (name, email, password, role) 
                    VALUES (?, ?, ?, 'principal')
                ");
                $stmt->execute([$admin_name, $admin_email, $hash]);
                
                $success = 'Installation complete! You can now log in.';
                header('Location: install.php?step=4');
                exit;
            } catch (PDOException $e) {
                $error = 'Error creating admin account: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install - School Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Installation - Step <?php echo $step; ?></h4>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>

                        <?php if ($step == 1): ?>
                            <h5 class="mb-4">System Requirements Check</h5>
                            <?php
                            $requirements = checkRequirements();
                            $canProceed = true;
                            foreach ($requirements as $requirement => $satisfied):
                                $canProceed = $canProceed && $satisfied;
                            ?>
                                <div class="mb-3">
                                    <span class="me-2"><?php echo $requirement; ?></span>
                                    <?php if ($satisfied): ?>
                                        <span class="badge bg-success">✓</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">✗</span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>

                            <?php if ($canProceed): ?>
                                <a href="?step=2" class="btn btn-primary">Continue</a>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    Please fix the requirements before continuing.
                                </div>
                            <?php endif; ?>

                        <?php elseif ($step == 2): ?>
                            <h5 class="mb-4">Database Configuration</h5>
                            <form method="post">
                                <div class="mb-3">
                                    <label for="db_host" class="form-label">Database Host</label>
                                    <input type="text" class="form-control" id="db_host" name="db_host" 
                                           value="localhost" required>
                                </div>
                                <div class="mb-3">
                                    <label for="db_name" class="form-label">Database Name</label>
                                    <input type="text" class="form-control" id="db_name" name="db_name" 
                                           value="school_management" required>
                                </div>
                                <div class="mb-3">
                                    <label for="db_user" class="form-label">Database Username</label>
                                    <input type="text" class="form-control" id="db_user" name="db_user" required>
                                </div>
                                <div class="mb-3">
                                    <label for="db_pass" class="form-label">Database Password</label>
                                    <input type="password" class="form-control" id="db_pass" name="db_pass">
                                </div>
                                <button type="submit" class="btn btn-primary">Continue</button>
                            </form>

                        <?php elseif ($step == 3): ?>
                            <h5 class="mb-4">Create Admin Account</h5>
                            <form method="post">
                                <div class="mb-3">
                                    <label for="admin_name" class="form-label">Admin Name</label>
                                    <input type="text" class="form-control" id="admin_name" name="admin_name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="admin_email" class="form-label">Admin Email</label>
                                    <input type="email" class="form-control" id="admin_email" name="admin_email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="admin_password" class="form-label">Admin Password</label>
                                    <input type="password" class="form-control" id="admin_password" 
                                           name="admin_password" required>
                                    <div class="form-text">
                                        Password must be at least 8 characters long.
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Complete Installation</button>
                            </form>

                        <?php elseif ($step == 4): ?>
                            <div class="text-center">
                                <h5 class="mb-4">Installation Complete!</h5>
                                <p>You can now log in to your School Management System.</p>
                                <div class="alert alert-warning">
                                    For security reasons, please delete the install.php file.
                                </div>
                                <a href="login.php" class="btn btn-primary">Go to Login</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
