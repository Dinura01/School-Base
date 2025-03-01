<?php
class View {
    private static $instance = null;
    private $layout = 'main';
    private $sections = [];
    private $sectionStack = [];
    private $currentSection;
    private $data = [];
    private $blocks = [];
    private $cache;
    private $cacheTimeout = 3600;

    /**
     * Constructor
     */
    private function __construct() {
        $this->cache = Cache::getInstance();
    }

    /**
     * Get View instance (Singleton)
     */
    public static function getInstance(): View {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Render a view
     */
    public function render(string $view, array $data = [], string $layout = null): string {
        // Set layout if provided
        if ($layout !== null) {
            $this->layout = $layout;
        }

        // Merge data
        $this->data = array_merge($this->data, $data);

        // Check cache
        $cacheKey = "view:{$view}:" . md5(serialize($this->data));
        if ($cached = $this->cache->get($cacheKey)) {
            return $cached;
        }

        // Start output buffering
        ob_start();

        // Extract data to local variables
        extract($this->data);

        // Include view file
        $viewFile = $this->getViewPath($view);
        if (!file_exists($viewFile)) {
            throw new Exception("View file not found: {$view}");
        }

        include $viewFile;
        $content = ob_get_clean();

        // Render layout if set
        if ($this->layout) {
            $layoutFile = $this->getViewPath("layouts/{$this->layout}");
            if (!file_exists($layoutFile)) {
                throw new Exception("Layout file not found: {$this->layout}");
            }

            ob_start();
            include $layoutFile;
            $content = ob_get_clean();
        }

        // Cache the result
        $this->cache->set($cacheKey, $content, $this->cacheTimeout);

        return $content;
    }

    /**
     * Start a section
     */
    public function section(string $name): void {
        if ($this->currentSection) {
            $this->sectionStack[] = $this->currentSection;
        }
        $this->currentSection = $name;
        ob_start();
    }

    /**
     * End a section
     */
    public function endSection(): void {
        if (!$this->currentSection) {
            throw new Exception('No section started');
        }

        $content = ob_get_clean();
        $this->sections[$this->currentSection] = $content;

        if (!empty($this->sectionStack)) {
            $this->currentSection = array_pop($this->sectionStack);
        } else {
            $this->currentSection = null;
        }
    }

    /**
     * Get section content
     */
    public function getSection(string $name, string $default = ''): string {
        return $this->sections[$name] ?? $default;
    }

    /**
     * Check if section exists
     */
    public function hasSection(string $name): bool {
        return isset($this->sections[$name]);
    }

    /**
     * Include a partial view
     */
    public function partial(string $view, array $data = []): string {
        $data = array_merge($this->data, $data);
        extract($data);

        ob_start();
        include $this->getViewPath("partials/{$view}");
        return ob_get_clean();
    }

    /**
     * Set layout
     */
    public function setLayout(string $layout): self {
        $this->layout = $layout;
        return $this;
    }

    /**
     * Set data
     */
    public function with(string $key, $value): self {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Share data across all views
     */
    public function share(array $data): self {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    /**
     * Get view path
     */
    private function getViewPath(string $view): string {
        return VIEW_PATH . '/' . str_replace('.', '/', $view) . '.php';
    }

    /**
     * Escape HTML
     */
    public function e(string $string): string {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Format date
     */
    public function date($date, string $format = 'Y-m-d'): string {
        return date($format, is_numeric($date) ? $date : strtotime($date));
    }

    /**
     * Format currency
     */
    public function currency(float $amount, string $currency = 'USD'): string {
        return number_format($amount, 2) . ' ' . $currency;
    }

    /**
     * Create pagination links
     */
    public function pagination(int $total, int $perPage, int $currentPage, string $url): string {
        $totalPages = ceil($total / $perPage);
        
        if ($totalPages <= 1) {
            return '';
        }

        $html = '<nav><ul class="pagination">';

        // Previous link
        if ($currentPage > 1) {
            $html .= sprintf(
                '<li class="page-item"><a class="page-link" href="%s">Previous</a></li>',
                sprintf($url, $currentPage - 1)
            );
        }

        // Page numbers
        for ($i = 1; $i <= $totalPages; $i++) {
            if ($i == $currentPage) {
                $html .= sprintf(
                    '<li class="page-item active"><span class="page-link">%d</span></li>',
                    $i
                );
            } else {
                $html .= sprintf(
                    '<li class="page-item"><a class="page-link" href="%s">%d</a></li>',
                    sprintf($url, $i),
                    $i
                );
            }
        }

        // Next link
        if ($currentPage < $totalPages) {
            $html .= sprintf(
                '<li class="page-item"><a class="page-link" href="%s">Next</a></li>',
                sprintf($url, $currentPage + 1)
            );
        }

        $html .= '</ul></nav>';
        return $html;
    }

    /**
     * Create breadcrumbs
     */
    public function breadcrumbs(array $items): string {
        $html = '<nav aria-label="breadcrumb">';
        $html .= '<ol class="breadcrumb">';

        foreach ($items as $label => $url) {
            if ($url === null) {
                $html .= sprintf(
                    '<li class="breadcrumb-item active" aria-current="page">%s</li>',
                    $this->e($label)
                );
            } else {
                $html .= sprintf(
                    '<li class="breadcrumb-item"><a href="%s">%s</a></li>',
                    $url,
                    $this->e($label)
                );
            }
        }

        $html .= '</ol></nav>';
        return $html;
    }

    /**
     * Create alert message
     */
    public function alert(string $message, string $type = 'info'): string {
        return sprintf(
            '<div class="alert alert-%s alert-dismissible fade show" role="alert">
                %s
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>',
            $type,
            $this->e($message)
        );
    }
}
