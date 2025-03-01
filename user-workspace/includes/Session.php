<?php
class Session {
    private static $instance = null;
    private static $flashMessages = [];

    /**
     * Initialize session with secure settings
     */
    public static function init(): void {
        if (session_status() === PHP_SESSION_NONE) {
            // Set secure session parameters
            ini_set('session.use_strict_mode', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', ENVIRONMENT === 'production' ? 1 : 0);
            ini_set('session.cookie_samesite', 'Lax');
            ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
            ini_set('session.cookie_lifetime', SESSION_LIFETIME);

            session_name(SESSION_NAME);
            session_set_cookie_params([
                'lifetime' => SESSION_LIFETIME,
                'path' => SESSION_PATH,
                'domain' => SESSION_DOMAIN,
                'secure' => SESSION_SECURE,
                'httponly' => SESSION_HTTPONLY,
                'samesite' => 'Lax'
            ]);

            session_start();

            // Regenerate session ID periodically
            if (!isset($_SESSION['last_regeneration'])) {
                self::regenerateId();
            } else {
                $regeneration_time = 60 * 30; // 30 minutes
                if (time() - $_SESSION['last_regeneration'] > $regeneration_time) {
                    self::regenerateId();
                }
            }

            // Initialize flash messages
            if (!isset($_SESSION['flash_messages'])) {
                $_SESSION['flash_messages'] = [];
            }
            self::$flashMessages = &$_SESSION['flash_messages'];

            // Load flash messages and clear old ones
            foreach (self::$flashMessages as $key => &$flashMessage) {
                if ($flashMessage['remove']) {
                    unset(self::$flashMessages[$key]);
                } else {
                    $flashMessage['remove'] = true;
                }
            }
        }
    }

    /**
     * Regenerate session ID securely
     */
    public static function regenerateId(): void {
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }

    /**
     * Set a session variable
     */
    public static function set(string $key, $value): void {
        $_SESSION[$key] = $value;
    }

    /**
     * Get a session variable
     */
    public static function get(string $key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Check if a session variable exists
     */
    public static function has(string $key): bool {
        return isset($_SESSION[$key]);
    }

    /**
     * Remove a session variable
     */
    public static function remove(string $key): void {
        unset($_SESSION[$key]);
    }

    /**
     * Set a flash message
     */
    public static function setFlash(string $type, string $message): void {
        self::$flashMessages[$type] = [
            'message' => $message,
            'remove' => false
        ];
    }

    /**
     * Get all flash messages
     */
    public static function getFlashMessages(): array {
        $messages = [];
        foreach (self::$flashMessages as $type => $data) {
            $messages[$type] = $data['message'];
        }
        return $messages;
    }

    /**
     * Get user ID from session
     */
    public static function getUserId(): ?int {
        return self::get('user_id');
    }

    /**
     * Get user role from session
     */
    public static function getUserRole(): ?string {
        return self::get('user_role');
    }

    /**
     * Set user session data
     */
    public static function setUser(array $user): void {
        self::set('user_id', $user['id']);
        self::set('user_role', $user['role']);
        self::set('user_name', $user['name']);
        self::set('last_activity', time());
    }

    /**
     * Check if user is logged in
     */
    public static function isLoggedIn(): bool {
        return self::has('user_id');
    }

    /**
     * Generate and store CSRF token
     */
    public static function generateCsrfToken(): string {
        $token = bin2hex(random_bytes(32));
        self::set('csrf_token', $token);
        return $token;
    }

    /**
     * Get stored CSRF token
     */
    public static function getCsrfToken(): ?string {
        return self::get('csrf_token');
    }

    /**
     * Validate CSRF token
     */
    public static function validateCsrfToken(string $token): bool {
        return hash_equals(self::getCsrfToken() ?? '', $token);
    }

    /**
     * Check session timeout
     */
    public static function checkTimeout(): bool {
        $lastActivity = self::get('last_activity');
        if ($lastActivity && (time() - $lastActivity > SESSION_LIFETIME)) {
            self::destroy();
            return true;
        }
        self::set('last_activity', time());
        return false;
    }

    /**
     * Update last activity timestamp
     */
    public static function updateActivity(): void {
        self::set('last_activity', time());
    }

    /**
     * Destroy session securely
     */
    public static function destroy(): void {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }

    /**
     * Get session status information
     */
    public static function getStatus(): array {
        return [
            'id' => session_id(),
            'name' => session_name(),
            'cookie_params' => session_get_cookie_params(),
            'last_activity' => self::get('last_activity'),
            'last_regeneration' => self::get('last_regeneration')
        ];
    }

    /**
     * Write session data and close
     */
    public static function close(): void {
        session_write_close();
    }

    /**
     * Prevent cloning of instance
     */
    private function __clone() {}

    /**
     * Prevent unserialization of instance
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
