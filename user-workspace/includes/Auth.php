<?php
class Auth {
    private $db;
    private static $instance = null;
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOCKOUT_TIME = 900; // 15 minutes

    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Get Auth instance (Singleton)
     */
    public static function getInstance(): Auth {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Authenticate user
     */
    public function login(string $email, string $password, bool $remember = false): bool {
        try {
            // Check login attempts
            if ($this->isLockedOut($email)) {
                throw new Exception('Account is temporarily locked. Please try again later.');
            }

            // Get user by email
            $user = $this->db->query(
                "SELECT * FROM Users WHERE email = ? AND status = 'active'",
                [$email]
            )->fetch();

            if (!$user) {
                $this->incrementLoginAttempts($email);
                throw new Exception('Invalid email or password');
            }

            // Verify password
            if (!password_verify($password, $user['password'])) {
                $this->incrementLoginAttempts($email);
                throw new Exception('Invalid email or password');
            }

            // Reset login attempts
            $this->resetLoginAttempts($email);

            // Update last login
            $this->db->update('Users', 
                ['last_login' => date('Y-m-d H:i:s')], 
                'id = ?', 
                [$user['id']]
            );

            // Set session data
            Session::setUser($user);

            // Handle remember me
            if ($remember) {
                $this->setRememberMeToken($user['id']);
            }

            // Log successful login
            $this->logActivity($user['id'], 'login', 'Successful login');

            return true;
        } catch (Exception $e) {
            $this->logActivity(0, 'login_failed', $e->getMessage(), ['email' => $email]);
            throw $e;
        }
    }

    /**
     * Register new user
     */
    public function register(string $name, string $email, string $password, string $role, ?string $phone = null): bool {
        try {
            // Validate email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email format');
            }

            // Check if email exists
            if ($this->db->exists('Users', 'email = ?', [$email])) {
                throw new Exception('Email already registered');
            }

            // Validate password strength
            if (!$this->isPasswordStrong($password)) {
                throw new Exception('Password does not meet security requirements');
            }

            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert user
            $userId = $this->db->insert('Users', [
                'name' => $name,
                'email' => $email,
                'password' => $hashedPassword,
                'role' => $role,
                'phone' => $phone,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s')
            ]);

            $this->logActivity($userId, 'register', 'User registration');
            return true;
        } catch (Exception $e) {
            $this->logActivity(0, 'register_failed', $e->getMessage(), ['email' => $email]);
            throw $e;
        }
    }

    /**
     * Send password reset link
     */
    public function sendPasswordResetLink(string $email): bool {
        try {
            $user = $this->db->query(
                "SELECT * FROM Users WHERE email = ? AND status = 'active'",
                [$email]
            )->fetch();

            if (!$user) {
                throw new Exception('Email not found');
            }

            // Generate reset token
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Store reset token
            $this->db->update('Users', 
                [
                    'reset_token' => $token,
                    'reset_token_expiry' => $expiry
                ],
                'id = ?',
                [$user['id']]
            );

            // Send reset email
            $resetLink = "https://" . $_SERVER['HTTP_HOST'] . "/reset-password.php?token=" . $token;
            $to = $user['email'];
            $subject = "Password Reset Request";
            $message = "Click the following link to reset your password: {$resetLink}";
            $headers = "From: " . MAIL_FROM_ADDRESS;

            if (!mail($to, $subject, $message, $headers)) {
                throw new Exception('Failed to send reset email');
            }

            $this->logActivity($user['id'], 'password_reset_request', 'Password reset requested');
            return true;
        } catch (Exception $e) {
            $this->logActivity(0, 'password_reset_failed', $e->getMessage(), ['email' => $email]);
            throw $e;
        }
    }

    /**
     * Reset password using token
     */
    public function resetPassword(string $token, string $newPassword): bool {
        try {
            // Validate token
            $user = $this->db->query(
                "SELECT * FROM Users WHERE reset_token = ? AND reset_token_expiry > NOW()",
                [$token]
            )->fetch();

            if (!$user) {
                throw new Exception('Invalid or expired reset token');
            }

            // Validate password strength
            if (!$this->isPasswordStrong($newPassword)) {
                throw new Exception('Password does not meet security requirements');
            }

            // Update password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $this->db->update('Users',
                [
                    'password' => $hashedPassword,
                    'reset_token' => null,
                    'reset_token_expiry' => null
                ],
                'id = ?',
                [$user['id']]
            );

            $this->logActivity($user['id'], 'password_reset', 'Password reset successful');
            return true;
        } catch (Exception $e) {
            $this->logActivity(0, 'password_reset_failed', $e->getMessage());
            throw $e;
        }
    }

    /**
     * Check if user has permission
     */
    public function hasPermission(string $permission): bool {
        $userRole = Session::getUserRole();
        return in_array($permission, ROLE_PERMISSIONS[$userRole] ?? []);
    }

    /**
     * Get current user
     */
    public function getCurrentUser(): ?array {
        $userId = Session::getUserId();
        if (!$userId) return null;

        return $this->db->query(
            "SELECT * FROM Users WHERE id = ?",
            [$userId]
        )->fetch();
    }

    /**
     * Update user profile
     */
    public function updateProfile(array $data): bool {
        try {
            $userId = Session::getUserId();
            if (!$userId) {
                throw new Exception('User not authenticated');
            }

            $this->db->update('Users', $data, 'id = ?', [$userId]);
            $this->logActivity($userId, 'profile_update', 'Profile updated');
            return true;
        } catch (Exception $e) {
            $this->logActivity($userId, 'profile_update_failed', $e->getMessage());
            throw $e;
        }
    }

    /**
     * Change password
     */
    public function changePassword(string $currentPassword, string $newPassword): bool {
        try {
            $userId = Session::getUserId();
            if (!$userId) {
                throw new Exception('User not authenticated');
            }

            // Verify current password
            $user = $this->db->query(
                "SELECT password FROM Users WHERE id = ?",
                [$userId]
            )->fetch();

            if (!password_verify($currentPassword, $user['password'])) {
                throw new Exception('Current password is incorrect');
            }

            // Validate new password
            if (!$this->isPasswordStrong($newPassword)) {
                throw new Exception('New password does not meet security requirements');
            }

            // Update password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $this->db->update('Users',
                ['password' => $hashedPassword],
                'id = ?',
                [$userId]
            );

            $this->logActivity($userId, 'password_change', 'Password changed');
            return true;
        } catch (Exception $e) {
            $this->logActivity($userId, 'password_change_failed', $e->getMessage());
            throw $e;
        }
    }

    /**
     * Log out user
     */
    public function logout(): void {
        $userId = Session::getUserId();
        if ($userId) {
            $this->logActivity($userId, 'logout', 'User logged out');
        }
        $this->removeRememberMeToken();
        Session::destroy();
    }

    /**
     * Check if password is strong
     */
    private function isPasswordStrong(string $password): bool {
        // At least 8 characters
        if (strlen($password) < 8) return false;

        // At least one uppercase letter
        if (!preg_match('/[A-Z]/', $password)) return false;

        // At least one lowercase letter
        if (!preg_match('/[a-z]/', $password)) return false;

        // At least one number
        if (!preg_match('/[0-9]/', $password)) return false;

        // At least one special character
        if (!preg_match('/[!@#$%^&*]/', $password)) return false;

        return true;
    }

    /**
     * Check if account is locked out
     */
    private function isLockedOut(string $email): bool {
        $attempts = $this->db->query(
            "SELECT COUNT(*) as count, MAX(created_at) as last_attempt 
             FROM login_attempts 
             WHERE email = ? AND created_at > DATE_SUB(NOW(), INTERVAL ? SECOND)",
            [$email, self::LOCKOUT_TIME]
        )->fetch();

        return $attempts['count'] >= self::MAX_LOGIN_ATTEMPTS;
    }

    /**
     * Increment login attempts
     */
    private function incrementLoginAttempts(string $email): void {
        $this->db->insert('login_attempts', [
            'email' => $email,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Reset login attempts
     */
    private function resetLoginAttempts(string $email): void {
        $this->db->delete('login_attempts', 'email = ?', [$email]);
    }

    /**
     * Set remember me token
     */
    private function setRememberMeToken(int $userId): void {
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+30 days'));

        $this->db->insert('remember_tokens', [
            'user_id' => $userId,
            'token' => $token,
            'expires_at' => $expiry
        ]);

        setcookie(
            'remember_token',
            $token,
            [
                'expires' => strtotime('+30 days'),
                'path' => '/',
                'domain' => '',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Lax'
            ]
        );
    }

    /**
     * Remove remember me token
     */
    private function removeRememberMeToken(): void {
        if (isset($_COOKIE['remember_token'])) {
            $this->db->delete(
                'remember_tokens',
                'token = ?',
                [$_COOKIE['remember_token']]
            );

            setcookie('remember_token', '', time() - 3600, '/');
        }
    }

    /**
     * Log activity
     */
    private function logActivity(int $userId, string $action, string $description, array $data = []): void {
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
}
