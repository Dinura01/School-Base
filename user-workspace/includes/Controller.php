<?php
abstract class Controller {
    protected $db;
    protected $auth;
    protected $data = [];
    protected $pageTitle = '';
    protected $pageStyles = [];
    protected $pageScripts = [];
    protected $breadcrumbs = [];

    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance();
        $this->auth = Auth::getInstance();
        
        // Check for session timeout
        if (Session::checkTimeout()) {
            $this->redirect('/login.php');
        }

        // Update last activity
        Session::updateActivity();

        // Set default data
        $this->data['currentUser'] = $this->auth->getCurrentUser();
        $this->data['userRole'] = Session::getUserRole();
    }

    /**
     * Render a view
     */
    protected function render(string $view, array $data = []): void {
        // Merge controller data with view data
        $data = array_merge($this->data, $data);
        
        // Extract variables for use in view
        extract($data);
        
        // Set page title
        $pageTitle = $this->pageTitle;
        
        // Set styles and scripts
        $pageStyles = $this->pageStyles;
        $pageScripts = $this->pageScripts;
        
        // Set breadcrumbs
        $breadcrumbs = $this->breadcrumbs;

        // Start output buffering
        ob_start();
        
        // Include view file
        $viewFile = VIEW_PATH . '/' . $view . '.php';
        if (!file_exists($viewFile)) {
            throw new Exception("View file not found: {$view}");
        }
        
        include $viewFile;
        
        // Get content and clean buffer
        $content = ob_get_clean();
        
        // Include layout
        include VIEW_PATH . '/layouts/main.php';
    }

    /**
     * Redirect to another URL
     */
    protected function redirect(string $url, array $flashMessage = null): void {
        if ($flashMessage) {
            Session::setFlash($flashMessage['type'], $flashMessage['message']);
        }
        header("Location: {$url}");
        exit();
    }

    /**
     * Send JSON response
     */
    protected function json($data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    /**
     * Check if request is AJAX
     */
    protected function isAjax(): bool {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Validate CSRF token
     */
    protected function validateCsrf(): void {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        
        if (!$token || !Session::validateCsrfToken($token)) {
            if ($this->isAjax()) {
                $this->json(['error' => 'Invalid CSRF token'], 403);
            } else {
                $this->redirect('/403.php');
            }
        }
    }

    /**
     * Check if user has permission
     */
    protected function checkPermission(string $permission): void {
        if (!$this->auth->hasPermission($permission)) {
            if ($this->isAjax()) {
                $this->json(['error' => 'Permission denied'], 403);
            } else {
                $this->redirect('/403.php');
            }
        }
    }

    /**
     * Get POST data
     */
    protected function getPost(string $key = null, $default = null) {
        if ($key === null) {
            return $_POST;
        }
        return $_POST[$key] ?? $default;
    }

    /**
     * Get GET data
     */
    protected function getQuery(string $key = null, $default = null) {
        if ($key === null) {
            return $_GET;
        }
        return $_GET[$key] ?? $default;
    }

    /**
     * Get uploaded file
     */
    protected function getFile(string $key) {
        return $_FILES[$key] ?? null;
    }

    /**
     * Validate data
     */
    protected function validate(array $data, array $rules): array {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;
            
            // Required check
            if (strpos($rule, 'required') !== false && empty($value)) {
                $errors[$field] = 'This field is required';
                continue;
            }
            
            // Skip other validations if empty and not required
            if (empty($value) && strpos($rule, 'required') === false) {
                continue;
            }
            
            // Email validation
            if (strpos($rule, 'email') !== false) {
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = 'Invalid email format';
                }
            }
            
            // Minimum length
            if (preg_match('/min:(\d+)/', $rule, $matches)) {
                $min = $matches[1];
                if (strlen($value) < $min) {
                    $errors[$field] = "Minimum length is {$min} characters";
                }
            }
            
            // Maximum length
            if (preg_match('/max:(\d+)/', $rule, $matches)) {
                $max = $matches[1];
                if (strlen($value) > $max) {
                    $errors[$field] = "Maximum length is {$max} characters";
                }
            }
            
            // Numeric validation
            if (strpos($rule, 'numeric') !== false) {
                if (!is_numeric($value)) {
                    $errors[$field] = 'Must be a number';
                }
            }
            
            // Date validation
            if (strpos($rule, 'date') !== false) {
                if (!strtotime($value)) {
                    $errors[$field] = 'Invalid date format';
                }
            }
        }
        
        return $errors;
    }

    /**
     * Handle file upload
     */
    protected function handleUpload(array $file, string $destination, array $allowedTypes = [], int $maxSize = 0): string {
        // Validate file upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('File upload failed');
        }

        // Check file size
        if ($maxSize > 0 && $file['size'] > $maxSize) {
            throw new Exception('File size exceeds limit');
        }

        // Check file type
        if (!empty($allowedTypes)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mimeType, $allowedTypes)) {
                throw new Exception('Invalid file type');
            }
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $filepath = $destination . '/' . $filename;

        // Create directory if it doesn't exist
        if (!is_dir($destination)) {
            mkdir($destination, 0777, true);
        }

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new Exception('Failed to move uploaded file');
        }

        return $filename;
    }

    /**
     * Log activity
     */
    protected function logActivity(string $action, string $description, array $data = []): void {
        $userId = Session::getUserId();
        $this->db->insert('activity_logs', [
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'data' => json_encode($data),
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Generate pagination
     */
    protected function paginate(string $table, array $options = []): array {
        $page = max(1, intval($_GET['page'] ?? 1));
        $perPage = $options['per_page'] ?? ITEMS_PER_PAGE;
        $where = $options['where'] ?? '';
        $params = $options['params'] ?? [];
        $orderBy = $options['order_by'] ?? 'id DESC';

        // Get total count
        $countSql = "SELECT COUNT(*) FROM {$table}";
        if ($where) {
            $countSql .= " WHERE {$where}";
        }
        $total = $this->db->getValue($countSql, $params);

        // Calculate pagination
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;

        // Get data
        $sql = "SELECT * FROM {$table}";
        if ($where) {
            $sql .= " WHERE {$where}";
        }
        $sql .= " ORDER BY {$orderBy} LIMIT {$perPage} OFFSET {$offset}";
        $items = $this->db->query($sql, $params)->fetchAll();

        return [
            'items' => $items,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => $totalPages
        ];
    }
}
