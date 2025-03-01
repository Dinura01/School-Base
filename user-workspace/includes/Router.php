<?php
class Router {
    private static $instance = null;
    private $routes = [];
    private $currentRoute = null;
    private $basePath = '';
    private $notFoundCallback;
    private $errorCallback;
    private $middlewares = [];
    private $groupStack = [];

    /**
     * Constructor
     */
    private function __construct() {
        $this->notFoundCallback = function() {
            header("HTTP/1.0 404 Not Found");
            include VIEW_PATH . '/404.php';
        };

        $this->errorCallback = function($e) {
            header("HTTP/1.0 500 Internal Server Error");
            if (ENVIRONMENT === 'development') {
                echo "<h1>Error</h1>";
                echo "<p>" . $e->getMessage() . "</p>";
                echo "<pre>" . $e->getTraceAsString() . "</pre>";
            } else {
                include VIEW_PATH . '/500.php';
            }
        };
    }

    /**
     * Get Router instance (Singleton)
     */
    public static function getInstance(): Router {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Add GET route
     */
    public function get(string $pattern, $callback): self {
        return $this->addRoute('GET', $pattern, $callback);
    }

    /**
     * Add POST route
     */
    public function post(string $pattern, $callback): self {
        return $this->addRoute('POST', $pattern, $callback);
    }

    /**
     * Add PUT route
     */
    public function put(string $pattern, $callback): self {
        return $this->addRoute('PUT', $pattern, $callback);
    }

    /**
     * Add DELETE route
     */
    public function delete(string $pattern, $callback): self {
        return $this->addRoute('DELETE', $pattern, $callback);
    }

    /**
     * Add route for any method
     */
    public function any(string $pattern, $callback): self {
        return $this->addRoute(['GET', 'POST', 'PUT', 'DELETE'], $pattern, $callback);
    }

    /**
     * Add route with custom methods
     */
    public function match(array $methods, string $pattern, $callback): self {
        return $this->addRoute($methods, $pattern, $callback);
    }

    /**
     * Add route
     */
    private function addRoute($methods, string $pattern, $callback): self {
        $methods = (array) $methods;
        $pattern = $this->basePath . '/' . trim($pattern, '/');
        
        foreach ($methods as $method) {
            $this->routes[$method][] = [
                'pattern' => $pattern,
                'callback' => $callback,
                'middlewares' => $this->groupStack
            ];
        }
        
        return $this;
    }

    /**
     * Group routes
     */
    public function group(array $attributes, callable $callback): self {
        $this->groupStack = array_merge($this->groupStack, $attributes['middleware'] ?? []);
        
        if (isset($attributes['prefix'])) {
            $previousBasePath = $this->basePath;
            $this->basePath .= '/' . trim($attributes['prefix'], '/');
        }
        
        call_user_func($callback, $this);
        
        if (isset($attributes['prefix'])) {
            $this->basePath = $previousBasePath;
        }
        
        $this->groupStack = [];
        return $this;
    }

    /**
     * Add middleware
     */
    public function middleware(string $middleware): self {
        if ($this->currentRoute) {
            $this->currentRoute['middlewares'][] = $middleware;
        }
        return $this;
    }

    /**
     * Set 404 handler
     */
    public function setNotFoundHandler(callable $callback): self {
        $this->notFoundCallback = $callback;
        return $this;
    }

    /**
     * Set error handler
     */
    public function setErrorHandler(callable $callback): self {
        $this->errorCallback = $callback;
        return $this;
    }

    /**
     * Handle request
     */
    public function handle(): void {
        try {
            $method = $_SERVER['REQUEST_METHOD'];
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $uri = urldecode($uri);
            
            // Handle PUT and DELETE methods
            if ($method === 'POST' && isset($_POST['_method'])) {
                $method = strtoupper($_POST['_method']);
            }
            
            // Check if route exists
            if (!isset($this->routes[$method])) {
                call_user_func($this->notFoundCallback);
                return;
            }
            
            // Find matching route
            foreach ($this->routes[$method] as $route) {
                $pattern = "#^{$route['pattern']}$#";
                
                if (preg_match($pattern, $uri, $matches)) {
                    array_shift($matches); // Remove full match
                    
                    // Run middlewares
                    foreach ($route['middlewares'] as $middleware) {
                        $middlewareClass = $middleware . 'Middleware';
                        $middlewareObj = new $middlewareClass();
                        if (!$middlewareObj->handle()) {
                            return;
                        }
                    }
                    
                    // Execute route callback
                    $this->currentRoute = $route;
                    if (is_callable($route['callback'])) {
                        call_user_func_array($route['callback'], $matches);
                    } else {
                        list($controller, $method) = explode('@', $route['callback']);
                        $controllerClass = $controller . 'Controller';
                        $controllerObj = new $controllerClass();
                        call_user_func_array([$controllerObj, $method], $matches);
                    }
                    return;
                }
            }
            
            // No route found
            call_user_func($this->notFoundCallback);
            
        } catch (Exception $e) {
            call_user_func($this->errorCallback, $e);
        }
    }

    /**
     * Generate URL for route
     */
    public function url(string $name, array $params = []): string {
        foreach ($this->routes as $routes) {
            foreach ($routes as $route) {
                if (isset($route['name']) && $route['name'] === $name) {
                    $url = $route['pattern'];
                    foreach ($params as $key => $value) {
                        $url = str_replace("{{$key}}", $value, $url);
                    }
                    return $url;
                }
            }
        }
        throw new Exception("Route '{$name}' not found");
    }

    /**
     * Name a route
     */
    public function name(string $name): self {
        if ($this->currentRoute) {
            $this->currentRoute['name'] = $name;
        }
        return $this;
    }

    /**
     * Get current route
     */
    public function getCurrentRoute(): ?array {
        return $this->currentRoute;
    }

    /**
     * Get all routes
     */
    public function getRoutes(): array {
        return $this->routes;
    }

    /**
     * Clear all routes
     */
    public function clearRoutes(): self {
        $this->routes = [];
        return $this;
    }
}
