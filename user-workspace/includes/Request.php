<?php
class Request {
    private $get;
    private $post;
    private $server;
    private $files;
    private $cookies;
    private $headers;
    private static $instance = null;

    /**
     * Constructor
     */
    private function __construct() {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->server = $_SERVER;
        $this->files = $_FILES;
        $this->cookies = $_COOKIE;
        $this->headers = $this->getRequestHeaders();
    }

    /**
     * Get Request instance (Singleton)
     */
    public static function getInstance(): Request {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get request headers
     */
    private function getRequestHeaders(): array {
        $headers = [];
        
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
                $headers[$header] = $value;
            }
        }
        
        return $headers;
    }

    /**
     * Get request method
     */
    public function getMethod(): string {
        return strtoupper($this->server['REQUEST_METHOD']);
    }

    /**
     * Get request URI
     */
    public function getUri(): string {
        return $this->server['REQUEST_URI'];
    }

    /**
     * Get query string
     */
    public function getQueryString(): ?string {
        return $this->server['QUERY_STRING'] ?? null;
    }

    /**
     * Get request path
     */
    public function getPath(): string {
        $path = parse_url($this->getUri(), PHP_URL_PATH);
        return $path ?? '/';
    }

    /**
     * Get all GET parameters
     */
    public function query(string $key = null, $default = null) {
        if ($key === null) {
            return $this->get;
        }
        return $this->get[$key] ?? $default;
    }

    /**
     * Get all POST parameters
     */
    public function post(string $key = null, $default = null) {
        if ($key === null) {
            return $this->post;
        }
        return $this->post[$key] ?? $default;
    }

    /**
     * Get request input (POST or GET)
     */
    public function input(string $key = null, $default = null) {
        $input = array_merge($this->get, $this->post);
        if ($key === null) {
            return $input;
        }
        return $input[$key] ?? $default;
    }

    /**
     * Get file from request
     */
    public function file(string $key) {
        return $this->files[$key] ?? null;
    }

    /**
     * Get all files from request
     */
    public function files(): array {
        return $this->files;
    }

    /**
     * Get cookie
     */
    public function cookie(string $key = null, $default = null) {
        if ($key === null) {
            return $this->cookies;
        }
        return $this->cookies[$key] ?? $default;
    }

    /**
     * Get header
     */
    public function header(string $key = null, $default = null) {
        if ($key === null) {
            return $this->headers;
        }
        return $this->headers[$key] ?? $default;
    }

    /**
     * Get server parameter
     */
    public function server(string $key = null, $default = null) {
        if ($key === null) {
            return $this->server;
        }
        return $this->server[$key] ?? $default;
    }

    /**
     * Check if request is AJAX
     */
    public function isAjax(): bool {
        return isset($this->headers['X-Requested-With']) && 
               strtolower($this->headers['X-Requested-With']) === 'xmlhttprequest';
    }

    /**
     * Check if request is secure (HTTPS)
     */
    public function isSecure(): bool {
        return (!empty($this->server['HTTPS']) && $this->server['HTTPS'] !== 'off') || 
               $this->server['SERVER_PORT'] == 443;
    }

    /**
     * Get client IP address
     */
    public function getIp(): string {
        $headers = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($headers as $header) {
            if (isset($this->server[$header])) {
                foreach (explode(',', $this->server[$header]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }

        return $this->server['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Get user agent
     */
    public function getUserAgent(): ?string {
        return $this->server['HTTP_USER_AGENT'] ?? null;
    }

    /**
     * Get request content type
     */
    public function getContentType(): ?string {
        return $this->server['CONTENT_TYPE'] ?? null;
    }

    /**
     * Get request accepts
     */
    public function accepts(): array {
        $accepts = $this->header('Accept');
        
        if (!$accepts) {
            return [];
        }
        
        return array_map('trim', explode(',', $accepts));
    }

    /**
     * Check if request accepts JSON
     */
    public function acceptsJson(): bool {
        return in_array('application/json', $this->accepts()) || 
               in_array('*/*', $this->accepts());
    }

    /**
     * Get request body
     */
    public function getBody(): string {
        return file_get_contents('php://input');
    }

    /**
     * Get JSON data from request
     */
    public function json($key = null, $default = null) {
        $data = json_decode($this->getBody(), true);
        
        if ($key === null) {
            return $data ?? [];
        }
        
        return $data[$key] ?? $default;
    }

    /**
     * Validate request data
     */
    public function validate(array $rules, array $messages = []): array {
        $validator = new Validator($this->input(), $rules, $messages);
        return $validator->validate() ? [] : $validator->getErrors();
    }

    /**
     * Check if request method matches
     */
    public function isMethod(string $method): bool {
        return $this->getMethod() === strtoupper($method);
    }

    /**
     * Check if request has input
     */
    public function has(string $key): bool {
        return isset($this->input()[$key]);
    }

    /**
     * Get request segment
     */
    public function segment(int $index, $default = null) {
        $segments = array_filter(explode('/', $this->getPath()));
        return $segments[$index] ?? $default;
    }

    /**
     * Get all segments
     */
    public function segments(): array {
        return array_filter(explode('/', $this->getPath()));
    }

    /**
     * Get request URL
     */
    public function url(): string {
        return $this->server['REQUEST_SCHEME'] . '://' . 
               $this->server['HTTP_HOST'] . 
               $this->server['REQUEST_URI'];
    }

    /**
     * Get base URL
     */
    public function baseUrl(): string {
        return $this->server['REQUEST_SCHEME'] . '://' . 
               $this->server['HTTP_HOST'];
    }

    /**
     * Get previous URL
     */
    public function previous(): ?string {
        return $this->server['HTTP_REFERER'] ?? null;
    }
}
