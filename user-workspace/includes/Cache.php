<?php
class Cache {
    private static $instance = null;
    private $path;
    private $enabled;
    private $defaultTtl;
    private $prefix;

    /**
     * Constructor
     */
    private function __construct() {
        $this->path = CACHE_PATH;
        $this->enabled = CACHE_ENABLED;
        $this->defaultTtl = CACHE_LIFETIME;
        $this->prefix = 'cache_';

        // Create cache directory if it doesn't exist
        if (!is_dir($this->path)) {
            mkdir($this->path, 0777, true);
        }
    }

    /**
     * Get Cache instance (Singleton)
     */
    public static function getInstance(): Cache {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get cached item
     */
    public function get(string $key, $default = null) {
        if (!$this->enabled) {
            return $default;
        }

        $filename = $this->getFilename($key);
        
        if (!file_exists($filename)) {
            return $default;
        }

        $content = file_get_contents($filename);
        $data = unserialize($content);

        if (!is_array($data)) {
            return $default;
        }

        // Check if cache has expired
        if ($data['expiry'] !== 0 && $data['expiry'] < time()) {
            $this->delete($key);
            return $default;
        }

        return $data['value'];
    }

    /**
     * Store item in cache
     */
    public function set(string $key, $value, int $ttl = null): bool {
        if (!$this->enabled) {
            return false;
        }

        $filename = $this->getFilename($key);
        $ttl = $ttl ?? $this->defaultTtl;
        
        $data = [
            'key' => $key,
            'value' => $value,
            'expiry' => $ttl === 0 ? 0 : time() + $ttl,
            'created_at' => time()
        ];

        return file_put_contents($filename, serialize($data), LOCK_EX) !== false;
    }

    /**
     * Check if item exists in cache
     */
    public function has(string $key): bool {
        if (!$this->enabled) {
            return false;
        }

        $value = $this->get($key);
        return $value !== null;
    }

    /**
     * Delete item from cache
     */
    public function delete(string $key): bool {
        $filename = $this->getFilename($key);
        
        if (file_exists($filename)) {
            return unlink($filename);
        }

        return true;
    }

    /**
     * Clear all cache
     */
    public function clear(): bool {
        if (!is_dir($this->path)) {
            return true;
        }

        $files = glob($this->path . '/' . $this->prefix . '*');
        
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        return true;
    }

    /**
     * Get or set cache value with callback
     */
    public function remember(string $key, int $ttl, callable $callback) {
        $value = $this->get($key);

        if ($value !== null) {
            return $value;
        }

        $value = $callback();
        $this->set($key, $value, $ttl);

        return $value;
    }

    /**
     * Get multiple cache items
     */
    public function getMultiple(array $keys, $default = null): array {
        $result = [];
        
        foreach ($keys as $key) {
            $result[$key] = $this->get($key, $default);
        }
        
        return $result;
    }

    /**
     * Store multiple items in cache
     */
    public function setMultiple(array $values, int $ttl = null): bool {
        $success = true;
        
        foreach ($values as $key => $value) {
            if (!$this->set($key, $value, $ttl)) {
                $success = false;
            }
        }
        
        return $success;
    }

    /**
     * Delete multiple items from cache
     */
    public function deleteMultiple(array $keys): bool {
        $success = true;
        
        foreach ($keys as $key) {
            if (!$this->delete($key)) {
                $success = false;
            }
        }
        
        return $success;
    }

    /**
     * Increment value
     */
    public function increment(string $key, int $value = 1): int {
        $current = (int) $this->get($key, 0);
        $new = $current + $value;
        $this->set($key, $new);
        return $new;
    }

    /**
     * Decrement value
     */
    public function decrement(string $key, int $value = 1): int {
        return $this->increment($key, -$value);
    }

    /**
     * Get cache info
     */
    public function getInfo(): array {
        $info = [
            'enabled' => $this->enabled,
            'path' => $this->path,
            'default_ttl' => $this->defaultTtl,
            'items' => 0,
            'size' => 0,
            'space_used' => '0 B'
        ];

        if (!is_dir($this->path)) {
            return $info;
        }

        $files = glob($this->path . '/' . $this->prefix . '*');
        $totalSize = 0;
        
        foreach ($files as $file) {
            if (is_file($file)) {
                $totalSize += filesize($file);
                $info['items']++;
            }
        }

        $info['size'] = $totalSize;
        $info['space_used'] = $this->formatSize($totalSize);

        return $info;
    }

    /**
     * Clean expired cache items
     */
    public function clean(): int {
        $cleaned = 0;
        $files = glob($this->path . '/' . $this->prefix . '*');
        
        foreach ($files as $file) {
            if (!is_file($file)) {
                continue;
            }

            $content = file_get_contents($file);
            $data = unserialize($content);

            if (!is_array($data) || ($data['expiry'] !== 0 && $data['expiry'] < time())) {
                unlink($file);
                $cleaned++;
            }
        }

        return $cleaned;
    }

    /**
     * Get cache filename
     */
    private function getFilename(string $key): string {
        return $this->path . '/' . $this->prefix . md5($key);
    }

    /**
     * Format file size
     */
    private function formatSize(int $bytes): string {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
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
