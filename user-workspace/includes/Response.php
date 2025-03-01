<?php
class Response {
    private $headers = [];
    private $content;
    private $statusCode = 200;
    private $statusText = 'OK';
    private static $statusTexts = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        422 => 'Unprocessable Entity',
        429 => 'Too Many Requests',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported'
    ];

    /**
     * Constructor
     */
    public function __construct($content = '', int $status = 200, array $headers = []) {
        $this->setContent($content);
        $this->setStatusCode($status);
        $this->headers = $headers;
    }

    /**
     * Set content
     */
    public function setContent($content): self {
        $this->content = $content;
        return $this;
    }

    /**
     * Set status code
     */
    public function setStatusCode(int $code, string $text = null): self {
        $this->statusCode = $code;
        $this->statusText = $text ?? (self::$statusTexts[$code] ?? 'Unknown Status');
        return $this;
    }

    /**
     * Add header
     */
    public function addHeader(string $name, string $value): self {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * Remove header
     */
    public function removeHeader(string $name): self {
        unset($this->headers[$name]);
        return $this;
    }

    /**
     * Send response
     */
    public function send(): void {
        // Send status code
        http_response_code($this->statusCode);

        // Send headers
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }

        // Send content
        echo $this->content;
        exit;
    }

    /**
     * Create JSON response
     */
    public static function json($data, int $status = 200, array $headers = []): self {
        $headers['Content-Type'] = 'application/json';
        return new self(
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            $status,
            $headers
        );
    }

    /**
     * Create HTML response
     */
    public static function html(string $content, int $status = 200, array $headers = []): self {
        $headers['Content-Type'] = 'text/html; charset=UTF-8';
        return new self($content, $status, $headers);
    }

    /**
     * Create plain text response
     */
    public static function text(string $content, int $status = 200, array $headers = []): self {
        $headers['Content-Type'] = 'text/plain; charset=UTF-8';
        return new self($content, $status, $headers);
    }

    /**
     * Create file download response
     */
    public static function download(string $filepath, string $filename = null, array $headers = []): self {
        if (!file_exists($filepath)) {
            throw new Exception('File not found');
        }

        $filename = $filename ?? basename($filepath);
        $mime = mime_content_type($filepath) ?? 'application/octet-stream';

        $headers = array_merge([
            'Content-Type' => $mime,
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length' => filesize($filepath),
            'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ], $headers);

        return new self(file_get_contents($filepath), 200, $headers);
    }

    /**
     * Create redirect response
     */
    public static function redirect(string $url, int $status = 302, array $headers = []): self {
        $headers['Location'] = $url;
        return new self('', $status, $headers);
    }

    /**
     * Create not found response
     */
    public static function notFound(string $message = 'Not Found'): self {
        return self::error(404, $message);
    }

    /**
     * Create error response
     */
    public static function error(int $code = 500, string $message = null): self {
        $message = $message ?? self::$statusTexts[$code] ?? 'Unknown Error';
        
        if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
            return self::json(['error' => $message], $code);
        }

        return self::html(
            '<h1>Error ' . $code . '</h1><p>' . htmlspecialchars($message) . '</p>',
            $code
        );
    }

    /**
     * Create success response
     */
    public static function success($data = null, string $message = 'Success'): self {
        return self::json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
    }

    /**
     * Create failure response
     */
    public static function failure(string $message = 'Failed', $data = null, int $code = 400): self {
        return self::json([
            'success' => false,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Create validation error response
     */
    public static function validationError(array $errors): self {
        return self::json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $errors
        ], 422);
    }

    /**
     * Create file response
     */
    public static function file(string $filepath, array $headers = []): self {
        if (!file_exists($filepath)) {
            throw new Exception('File not found');
        }

        $mime = mime_content_type($filepath) ?? 'application/octet-stream';

        $headers = array_merge([
            'Content-Type' => $mime,
            'Content-Length' => filesize($filepath),
            'Cache-Control' => 'public, max-age=31536000'
        ], $headers);

        return new self(file_get_contents($filepath), 200, $headers);
    }

    /**
     * Create no content response
     */
    public static function noContent(): self {
        return new self('', 204);
    }

    /**
     * Create unauthorized response
     */
    public static function unauthorized(string $message = 'Unauthorized'): self {
        return self::error(401, $message);
    }

    /**
     * Create forbidden response
     */
    public static function forbidden(string $message = 'Forbidden'): self {
        return self::error(403, $message);
    }

    /**
     * Create too many requests response
     */
    public static function tooManyRequests(string $message = 'Too Many Requests'): self {
        return self::error(429, $message);
    }

    /**
     * Get status code
     */
    public function getStatusCode(): int {
        return $this->statusCode;
    }

    /**
     * Get headers
     */
    public function getHeaders(): array {
        return $this->headers;
    }

    /**
     * Get content
     */
    public function getContent() {
        return $this->content;
    }
}
