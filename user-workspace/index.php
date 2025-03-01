<?php
// Initialize session
session_start();

// Load configuration
require_once 'config/config.php';

// Check if system is in maintenance mode
if (MAINTENANCE_MODE && !isset($_SESSION['admin'])) {
    die(MAINTENANCE_MESSAGE);
}

// Auto-load classes
spl_autoload_register(function ($class) {
    $file = INCLUDE_PATH . '/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Initialize database connection
try {
    $db = new Database();
    $db->connect();
} catch (Exception $e) {
    if (ENVIRONMENT === 'development') {
        die('Database connection failed: ' . $e->getMessage());
    } else {
        die('An error occurred. Please try again later.');
    }
}

// Initialize authentication
$auth = new Auth();

// Route the request
$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$urlParts = explode('/', $url);

// Default controller and action
$controllerName = !empty($urlParts[0]) ? ucfirst($urlParts[0]) . 'Controller' : 'HomeController';
$actionName = $urlParts[1] ?? 'index';

// Check if user is logged in
if (!$auth->isLoggedIn() && $controllerName !== 'AuthController') {
    header('Location: /login.php');
    exit();
}

// Validate user role access
if ($auth->isLoggedIn()) {
    $userRole = Session::getUserRole();
    $allowedControllers = [
        'principal' => ['PrincipalController', 'ProfileController', 'SettingsController'],
        'teacher' => ['TeacherController', 'ProfileController', 'SettingsController'],
        'parent' => ['ParentController', 'ProfileController', 'SettingsController'],
        'accountant' => ['AccountantController', 'ProfileController', 'SettingsController']
    ];

    if (!in_array($controllerName, $allowedControllers[$userRole])) {
        header('Location: /403.php');
        exit();
    }
}

// Load and initialize controller
$controllerFile = CONTROLLER_PATH . '/' . $controllerName . '.php';
if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controller = new $controllerName();
    
    // Check if action exists
    if (method_exists($controller, $actionName)) {
        // Get parameters
        $params = array_slice($urlParts, 2);
        
        try {
            // Call the action with parameters
            call_user_func_array([$controller, $actionName], $params);
        } catch (Exception $e) {
            if (ENVIRONMENT === 'development') {
                die('Error: ' . $e->getMessage());
            } else {
                error_log($e->getMessage());
                header('Location: /500.php');
                exit();
            }
        }
    } else {
        header('Location: /404.php');
        exit();
    }
} else {
    header('Location: /404.php');
    exit();
}

// Close database connection
$db->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>School Management System</title>
    
    <!-- Meta tags -->
    <meta name="description" content="School Management System">
    <meta name="author" content="Your Name">
    <meta name="csrf-token" content="<?php echo Session::getCsrfToken(); ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/assets/img/favicon.ico">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
    
    <!-- Custom page CSS -->
    <?php if (isset($pageStyles)): ?>
        <?php foreach ($pageStyles as $style): ?>
            <link href="<?php echo $style; ?>" rel="stylesheet">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Loading Spinner -->
    <div class="spinner-overlay" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <?php if ($auth->isLoggedIn()): ?>
        <!-- Sidebar -->
        <?php include 'views/layouts/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Topbar -->
            <?php include 'views/layouts/topbar.php'; ?>

            <!-- Page Content -->
            <div class="container-fluid py-4">
                <?php
                // Display flash messages
                $flashMessages = Session::getFlashMessages();
                foreach ($flashMessages as $type => $message):
                ?>
                    <div class="alert alert-<?php echo $type; ?> alert-dismissible fade show">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endforeach; ?>

                <!-- Main content will be injected here by the controller -->
            </div>

            <!-- Footer -->
            <?php include 'views/layouts/footer.php'; ?>
        </div>
    <?php endif; ?>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="/assets/js/scripts.js"></script>

    <!-- Custom page scripts -->
    <?php if (isset($pageScripts)): ?>
        <?php foreach ($pageScripts as $script): ?>
            <script src="<?php echo $script; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Initialize page-specific JavaScript -->
    <?php if (isset($pageInitScript)): ?>
        <script><?php echo $pageInitScript; ?></script>
    <?php endif; ?>
</body>
</html>
