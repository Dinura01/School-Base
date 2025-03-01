<?php
// Define environment
define('ENVIRONMENT', 'development'); // Options: development, production

// Error reporting based on environment
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'school_management');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Session configuration
define('SESSION_NAME', 'school_session');
define('SESSION_LIFETIME', 7200); // 2 hours
define('SESSION_PATH', '/');
define('SESSION_DOMAIN', '');
define('SESSION_SECURE', ENVIRONMENT === 'production');
define('SESSION_HTTPONLY', true);

// Application paths
define('BASE_PATH', dirname(__DIR__));
define('INCLUDE_PATH', BASE_PATH . '/includes');
define('CONTROLLER_PATH', BASE_PATH . '/controllers');
define('VIEW_PATH', BASE_PATH . '/views');
define('UPLOAD_PATH', BASE_PATH . '/uploads');

// File upload settings
define('MAX_UPLOAD_SIZE', 2 * 1024 * 1024); // 2MB
define('ALLOWED_IMAGE_TYPES', [
    'image/jpeg',
    'image/png',
    'image/gif'
]);
define('ALLOWED_DOC_TYPES', [
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
]);

// Email configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
define('SMTP_AUTH', true);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-specific-password');
define('MAIL_FROM_ADDRESS', 'noreply@school.com');
define('MAIL_FROM_NAME', 'School Management System');

// Security settings
define('PASSWORD_MIN_LENGTH', 8);
define('PASSWORD_HASH_ALGO', PASSWORD_DEFAULT);
define('TOKEN_EXPIRY', 3600); // 1 hour
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes

// Pagination settings
define('ITEMS_PER_PAGE', 10);
define('MAX_PAGE_LINKS', 5);

// Date and time settings
define('DEFAULT_TIMEZONE', 'UTC');
define('DATE_FORMAT', 'Y-m-d');
define('TIME_FORMAT', 'H:i:s');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');

// Cache settings
define('CACHE_ENABLED', true);
define('CACHE_LIFETIME', 3600); // 1 hour
define('CACHE_PATH', BASE_PATH . '/cache');

// API settings
define('API_VERSION', 'v1');
define('API_KEY_HEADER', 'X-API-Key');
define('API_RATE_LIMIT', 100); // requests per hour
define('API_RESPONSE_FORMAT', 'json');

// School settings
define('SCHOOL_NAME', 'Demo School');
define('SCHOOL_ADDRESS', '123 Education Street');
define('SCHOOL_PHONE', '+1234567890');
define('SCHOOL_EMAIL', 'info@school.com');
define('SCHOOL_WEBSITE', 'https://school.com');

// Academic settings
define('ACADEMIC_YEAR_START', '08-01'); // August 1st
define('ACADEMIC_YEAR_END', '05-31');   // May 31st
define('GRADING_SCALE', [
    'A' => ['min' => 90, 'max' => 100],
    'B' => ['min' => 80, 'max' => 89],
    'C' => ['min' => 70, 'max' => 79],
    'D' => ['min' => 60, 'max' => 69],
    'F' => ['min' => 0,  'max' => 59]
]);

// Role permissions
define('ROLE_PERMISSIONS', [
    'principal' => [
        'manage_staff',
        'manage_students',
        'manage_parents',
        'manage_accounts',
        'view_reports',
        'manage_settings'
    ],
    'teacher' => [
        'view_students',
        'manage_attendance',
        'manage_grades',
        'view_reports'
    ],
    'parent' => [
        'view_grades',
        'view_attendance',
        'view_fees',
        'send_messages'
    ],
    'accountant' => [
        'manage_fees',
        'manage_expenses',
        'view_reports',
        'send_reminders'
    ]
]);

// System maintenance
define('MAINTENANCE_MODE', false);
define('MAINTENANCE_MESSAGE', 'System is under maintenance. Please try again later.');
define('BACKUP_PATH', BASE_PATH . '/backups');
define('LOG_PATH', BASE_PATH . '/logs');

// Initialize required settings
date_default_timezone_set(DEFAULT_TIMEZONE);
mb_internal_encoding('UTF-8');

// Auto-loader function
spl_autoload_register(function ($class) {
    $file = INCLUDE_PATH . '/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Error handler function
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        return false;
    }

    $errorType = match($errno) {
        E_ERROR, E_USER_ERROR => 'Fatal Error',
        E_WARNING, E_USER_WARNING => 'Warning',
        E_NOTICE, E_USER_NOTICE => 'Notice',
        default => 'Unknown Error'
    };

    $message = "$errorType: $errstr in $errfile on line $errline";
    
    if (ENVIRONMENT === 'development') {
        error_log($message);
        if ($errno == E_ERROR || $errno == E_USER_ERROR) {
            die($message);
        }
    } else {
        error_log($message);
        if ($errno == E_ERROR || $errno == E_USER_ERROR) {
            die('An error occurred. Please try again later.');
        }
    }

    return true;
}

// Set custom error handler
set_error_handler('customErrorHandler');

// Helper functions
function redirect($url) {
    header("Location: $url");
    exit();
}

function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function generateToken() {
    return bin2hex(random_bytes(32));
}

function validateToken($token) {
    return isset($_SESSION['token']) && hash_equals($_SESSION['token'], $token);
}

function isAjax() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

function formatDate($date, $format = null) {
    return date($format ?? DATE_FORMAT, strtotime($date));
}

function formatMoney($amount) {
    return number_format($amount, 2);
}

function getGradeLabel($score) {
    foreach (GRADING_SCALE as $grade => $range) {
        if ($score >= $range['min'] && $score <= $range['max']) {
            return $grade;
        }
    }
    return 'N/A';
}

function hasPermission($permission) {
    $userRole = $_SESSION['user_role'] ?? '';
    return in_array($permission, ROLE_PERMISSIONS[$userRole] ?? []);
}
