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