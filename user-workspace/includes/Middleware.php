<?php
abstract class Middleware {
    protected $request;
    protected $response;
    protected $auth;
    protected $session;

    /**
     * Constructor
     */
    public function __construct() {
        $this->request = Request::getInstance();
        $this->response = new Response();
        $this->auth = Auth::getInstance();
        $this->session = Session::getInstance();
    }

    /**
     * Handle the middleware
     * Return true to continue, false to stop
     */
    abstract public function handle(): bool;

    /**
     * Create authentication middleware
     */
    public static function auth(array $roles = []): AuthMiddleware {
        return new class($roles) extends Middleware {
            private $roles;

            public function __construct(array $roles) {
                parent::__construct();
                $this->roles = $roles;
            }

            public function handle(): bool {
                if (!$this->auth->isLoggedIn()) {
                    $this->session->setFlash('error', 'Please login to continue.');
                    $this->response->redirect('/login.php')->send();
                    return false;
                }

                if (!empty($this->roles) && !in_array($this->session->getUserRole(), $this->roles)) {
                    $this->response->forbidden('You do not have permission to access this page.')->send();
                    return false;
                }

                return true;
            }
        };
    }

    /**
     * Create guest middleware
     */
    public static function guest(): GuestMiddleware {
        return new class extends Middleware {
            public function handle(): bool {
                if ($this->auth->isLoggedIn()) {
                    $role = $this->session->getUserRole();
                    $this->response->redirect("/{$role}/dashboard")->send();
                    return false;
                }
                return true;
            }
        };
    }

    /**
     * Create CSRF middleware
     */
    public static function csrf(): CsrfMiddleware {
        return new class extends Middleware {
            public function handle(): bool {
                if ($this->request->getMethod() === 'POST') {
                    $token = $this->request->post('csrf_token') ?? 
                            $this->request->header('X-CSRF-TOKEN');

                    if (!$token || !$this->session->validateCsrfToken($token)) {
                        if ($this->request->isAjax()) {
                            $this->response->json(['error' => 'Invalid CSRF token'], 403)->send();
                        } else {
                            $this->response->forbidden('Invalid CSRF token')->send();
                        }
                        return false;
                    }
                }
                return true;
            }
        };
    }

    /**
     * Create throttle middleware
     */
    public static function throttle(int $maxAttempts = 60, int $decayMinutes = 1): ThrottleMiddleware {
        return new class($maxAttempts, $decayMinutes) extends Middleware {
            private $maxAttempts;
            private $decayMinutes;

            public function __construct(int $maxAttempts, int $decayMinutes) {
                parent::__construct();
                $this->maxAttempts = $maxAttempts;
                $this->decayMinutes = $decayMinutes;
            }

            public function handle(): bool {
                $key = 'throttle:' . $this->request->getIp();
                $attempts = (int) $this->session->get($key, 0);

                if ($attempts >= $this->maxAttempts) {
                    if ($this->request->isAjax()) {
                        $this->response->json([
                            'error' => 'Too many requests',
                            'retry_after' => $this->decayMinutes * 60
                        ], 429)->send();
                    } else {
                        $this->response->tooManyRequests()->send();
                    }
                    return false;
                }

                $this->session->set($key, $attempts + 1);
                return true;
            }
        };
    }

    /**
     * Create maintenance mode middleware
     */
    public static function maintenance(): MaintenanceMiddleware {
        return new class extends Middleware {
            public function handle(): bool {
                if (MAINTENANCE_MODE && !$this->auth->hasPermission('access_maintenance')) {
                    include VIEW_PATH . '/maintenance.php';
                    return false;
                }
                return true;
            }
        };
    }

    /**
     * Create SSL middleware
     */
    public static function ssl(): SslMiddleware {
        return new class extends Middleware {
            public function handle(): bool {
                if (!$this->request->isSecure() && ENVIRONMENT === 'production') {
                    $url = 'https://' . $this->request->server('HTTP_HOST') . 
                           $this->request->server('REQUEST_URI');
                    $this->response->redirect($url)->send();
                    return false;
                }
                return true;
            }
        };
    }

    /**
     * Create cache middleware
     */
    public static function cache(int $minutes = 60): CacheMiddleware {
        return new class($minutes) extends Middleware {
            private $minutes;
            private $cache;

            public function __construct(int $minutes) {
                parent::__construct();
                $this->minutes = $minutes;
                $this->cache = Cache::getInstance();
            }

            public function handle(): bool {
                if ($this->request->getMethod() !== 'GET') {
                    return true;
                }

                $key = 'page:' . md5($this->request->getUri());
                if ($cached = $this->cache->get($key)) {
                    echo $cached;
                    return false;
                }

                ob_start();
                return true;
            }

            public function terminate(): void {
                if ($this->request->getMethod() === 'GET') {
                    $key = 'page:' . md5($this->request->getUri());
                    $content = ob_get_clean();
                    $this->cache->set($key, $content, $this->minutes * 60);
                    echo $content;
                }
            }
        };
    }

    /**
     * Create logging middleware
     */
    public static function log(): LogMiddleware {
        return new class extends Middleware {
            private $logger;

            public function __construct() {
                parent::__construct();
                $this->logger = Logger::getInstance();
            }

            public function handle(): bool {
                $this->logger->info('Request', [
                    'method' => $this->request->getMethod(),
                    'uri' => $this->request->getUri(),
                    'ip' => $this->request->getIp(),
                    'user_agent' => $this->request->getUserAgent()
                ]);
                return true;
            }
        };
    }
}
