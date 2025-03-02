<?php
class Logger {
    private static $instance = null;
    private $logPath;
    private $logFile;
    private $maxFileSize = 5242880; // 5MB
    private $maxFiles = 5;

    /**
     * Constructor
     */
    private function __construct() {
        $this->logPath = LOG_PATH;
        $this->logFile = $this->logPath . '/app.log';

        // Create log directory if it doesn't exist
        if (!is_dir($this->logPath)) {
            mkdir($this->logPath, 0777, true);
        }

        // Rotate logs if necessary
        $this->rotateLogFiles();
    }

    /**
     * Get Logger instance (Singleton)
     */
    public static function getInstance(): Logger {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Log emergency message
     */
    public function emergency(string $message, array $context = []): void {
        $this->log('EMERGENCY', $message, $context);
    }

    /**
     * Log alert message
     */
    public function alert(string $message, array $context = []): void {
        $this->log('ALERT', $message, $context);
    }

    /**
     * Log critical message
     */
    public function critical(string $message, array $context = []): void {
        $this->log('CRITICAL', $message, $context);
    }

    /**
     * Log error message
     */
    public function error(string $message, array $context = []): void {
        $this->log('ERROR', $message, $context);
    }

    /**
     * Log warning message
     */
    public function warning(string $message, array $context = []): void {
        $this->log('WARNING', $message, $context);
    }

    /**
     * Log notice message
     */
    public function notice(string $message, array $context = []): void {
        $this->log('NOTICE', $message, $context);
    }

    /**
     * Log info message
     */
    public function info(string $message, array $context = []): void {
        $this->log('INFO', $message, $context);
    }

    /**
     * Log debug message
     */
    public function debug(string $message, array $context = []): void {
        $this->log('DEBUG', $message, $context);
    }

    /**
     * Log message with level
     */
    private function log(string $level, string $message, array $context = []): void {
        $timestamp = date('Y-m-d H:i:s');
        $userId = Session::getUserId() ?? 'guest';
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $requestUri = $_SERVER['REQUEST_URI'] ?? 'unknown';

        // Format context data
        $contextStr = empty($context) ? '' : ' ' . json_encode($context);

        // Create log entry
        $logEntry = sprintf(
            "[%s] [%s] [User:%s] [IP:%s] [%s] %s%s\n",
            $timestamp,
            $level,
            $userId,
            $ip,
            $requestUri,
            $message,
            $contextStr
        );

        // Write to file
        if (@file_put_contents($this->logFile, $logEntry, FILE_APPEND | LOCK_EX) === false) {
            // If writing fails, try to create a new log file
            if (!file_exists($this->logFile)) {
                touch($this->logFile);
                chmod($this->logFile, 0666);
            }
        }

        // Check file size and rotate if necessary
        if (filesize($this->logFile) > $this->maxFileSize) {
            $this->rotateLogFiles();
        }
    }

    /**
     * Rotate log files
     */
    private function rotateLogFiles(): void {
        if (!file_exists($this->logFile)) {
            return;
        }

        // Rotate existing log files
        for ($i = $this->maxFiles - 1; $i > 0; $i--) {
            $oldFile = $this->logFile . '.' . $i;
            $newFile = $this->logFile . '.' . ($i + 1);
            
            if (file_exists($oldFile)) {
                if ($i == $this->maxFiles - 1) {
                    unlink($oldFile);
                } else {
                    rename($oldFile, $newFile);
                }
            }
        }

        // Rename current log file
        if (file_exists($this->logFile)) {
            rename($this->logFile, $this->logFile . '.1');
        }

        // Create new log file
        touch($this->logFile);
        chmod($this->logFile, 0666);
    }

    /**
     * Get all log files
     */
    public function getLogFiles(): array {
        $files = [];
        $pattern = $this->logFile . '*';
        
        foreach (glob($pattern) as $file) {
            $files[] = [
                'name' => basename($file),
                'size' => filesize($file),
                'modified' => filemtime($file)
            ];
        }

        // Sort by modified time descending
        usort($files, function($a, $b) {
            return $b['modified'] - $a['modified'];
        });

        return $files;
    }

    /**
     * Get log content
     */
    public function getLogContent(string $filename = null, int $lines = 100): array {
        $file = $filename ? $this->logPath . '/' . $filename : $this->logFile;
        
        if (!file_exists($file)) {
            return [];
        }

        $logs = [];
        $handle = fopen($file, 'r');
        
        if ($handle) {
            // Get file size
            fseek($handle, 0, SEEK_END);
            $fileSize = ftell($handle);
            
            // Calculate where to start reading
            $maxLength = 5000 * $lines; // Assume average line length of 5000 bytes
            $pos = max(0, $fileSize - $maxLength);
            
            // Read from position
            fseek($handle, $pos);
            
            // Discard first incomplete line if not at start of file
            if ($pos > 0) {
                fgets($handle);
            }
            
            // Read lines
            while (!feof($handle) && count($logs) < $lines) {
                $line = fgets($handle);
                if (preg_match('/^\[(.*?)\] \[(.*?)\] \[User:(.*?)\] \[IP:(.*?)\] \[(.*?)\] (.*)/', $line, $matches)) {
                    $logs[] = [
                        'timestamp' => $matches[1],
                        'level' => $matches[2],
                        'user' => $matches[3],
                        'ip' => $matches[4],
                        'uri' => $matches[5],
                        'message' => trim($matches[6])
                    ];
                }
            }
            fclose($handle);
        }

        return array_reverse($logs);
    }

    /**
     * Clear log file
     */
    public function clearLog(string $filename = null): bool {
        $file = $filename ? $this->logPath . '/' . $filename : $this->logFile;
        
        if (!file_exists($file)) {
            return false;
        }

        return file_put_contents($file, '') !== false;
    }

    /**
     * Download log file
     */
    public function downloadLog(string $filename = null): void {
        $file = $filename ? $this->logPath . '/' . $filename : $this->logFile;
        
        if (!file_exists($file)) {
            throw new Exception('Log file not found');
        }

        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Content-Length: ' . filesize($file));
        
        readfile($file);
        exit;
    }

    /**
     * Search logs
     */
    public function searchLogs(string $query, string $filename = null, string $level = null): array {
        $file = $filename ? $this->logPath . '/' . $filename : $this->logFile;
        
        if (!file_exists($file)) {
            return [];
        }

        $results = [];
        $handle = fopen($file, 'r');
        
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if (preg_match('/^\[(.*?)\] \[(.*?)\] \[User:(.*?)\] \[IP:(.*?)\] \[(.*?)\] (.*)/', $line, $matches)) {
                    // Skip if level filter is set and doesn't match
                    if ($level && $matches[2] !== $level) {
                        continue;
                    }

                    // Skip if query doesn't match
                    if ($query && stripos($line, $query) === false) {
                        continue;
                    }

                    $results[] = [
                        'timestamp' => $matches[1],
                        'level' => $matches[2],
                        'user' => $matches[3],
                        'ip' => $matches[4],
                        'uri' => $matches[5],
                        'message' => trim($matches[6])
                    ];
                }
            }
            fclose($handle);
        }

        return $results;
    }
}
