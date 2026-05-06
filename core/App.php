<?php

/**
 * MindBridge Front Router
 * Maps URL segments → Controller::method(params)
 *
 * Route alias map handles mismatches between URL plurals
 * and singular controller file names.
 */
class App
{
    protected string $controller = 'HomeController';
    protected string $method     = 'index';
    protected array  $params     = [];

    /**
     * URL segment → Controller class name (without "Controller" suffix).
     * Handles plural URL segments that map to singular controllers.
     */
    private array $routeAliases = [
        // Plural URL → Singular Controller
        'messages'      => 'Message',
        'appointments'  => 'Appointment',
        'notifications' => 'Notification',
        'payments'      => 'Payment',
        'sessions'      => 'Sessions',     // SessionsController (plural intentional)
        // Exact matches (no alias needed but listed for clarity)
        'feedback'      => 'Feedback',
        'forum'         => 'Forum',
        'wellness'      => 'Wellness',
        'crisis'        => 'Crisis',
        'admin'         => 'Admin',
        'patient'       => 'Patient',
        'therapist'     => 'Therapist',
        'dashboard'     => 'Dashboard',
        'auth'          => 'Auth',
        'login'         => 'Auth',
        'logout'        => 'Auth',
        'register'      => 'Auth',
        'home'          => 'Home',
    ];

    public function __construct()
    {
        $url = $this->parseUrl();

        // 1) Determine controller via alias map or ucfirst fallback
        $segment  = strtolower($url[0] ?? 'home');
        $baseName = $this->routeAliases[$segment] ?? ucfirst($segment);
        $ctrlName = $baseName . 'Controller';
        $ctrlFile = BASE_PATH . "/app/controllers/{$ctrlName}.php";

        if (file_exists($ctrlFile)) {
            $this->controller = $ctrlName;
            unset($url[0]);
        } else {
            $this->show404();
            return;
        }

        // 2) Load all dependencies
        $this->loadDependencies($this->controller);

        $ctrl = new $this->controller();

        // 3) Determine method (convert kebab-case to camelCase)
        $methodSegment = $url[1] ?? 'index';
        $methodName = $this->resolveMethod($methodSegment);

        if (method_exists($ctrl, $methodName)) {
            $this->method = $methodName;
            unset($url[1]);
        } elseif ($methodName !== 'index') {
            // Method doesn't exist — try using it as a parameter to index()
            // e.g. /therapist/123 → TherapistController::index(123)
            // BUT if index() doesn't accept params either, show 404
            if (method_exists($ctrl, 'index')) {
                // Keep $url[1] as a param for index()
                $this->method = 'index';
                // Don't unset url[1] — it becomes a param
            } else {
                $this->show404();
                return;
            }
        }

        // 4) Params
        $this->params = $url ? array_values($url) : [];

        // 5) Dispatch — cast numeric params to int
        $castParams = array_map(
            fn($p) => ctype_digit((string)$p) ? (int)$p : $p,
            $this->params
        );

        // Guard: don't pass string params to methods that require int
        try {
            call_user_func_array([$ctrl, $this->method], $castParams);
        } catch (\TypeError $e) {
            $this->show404();
        }
    }


    /**
     * Convert URL segment to camelCase method name.
     * e.g. "do-login" → "doLogin", "update_profile" → "updateProfile"
     * If segment has no separators (hyphens/underscores), it is returned
     * as-is so camelCase URLs like /auth/doLogin still resolve correctly.
     */
    private function resolveMethod(string $segment): string
    {
        // If the segment already contains uppercase letters and no separators,
        // return it unchanged (e.g. doLogin, auditLogs, manageUser)
        if (!str_contains($segment, '-') && !str_contains($segment, '_')) {
            return $segment;
        }
        // Convert kebab-case or snake_case → camelCase
        $segment = str_replace(['-', '_'], ' ', strtolower($segment));
        return lcfirst(str_replace(' ', '', ucwords($segment)));
    }

    /**
     * Parse clean URL into segments.
     * Strips BASE_URL path prefix from REQUEST_URI.
     */
    private function parseUrl(): array
    {
        // Support both mod_rewrite (REQUEST_URI) and query-string fallback (?url=)
        if (!empty($_GET['url'])) {
            $url = trim($_GET['url'], '/');
        } else {
            $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
            $basePath   = parse_url(BASE_URL, PHP_URL_PATH) ?? '';

            // Strip base path
            if ($basePath && str_starts_with($requestUri, $basePath)) {
                $requestUri = substr($requestUri, strlen($basePath));
            }

            // Strip query string
            $requestUri = strtok($requestUri, '?');
            $url = trim($requestUri, '/');
        }

        if (empty($url)) return ['home'];
        return explode('/', filter_var($url, FILTER_SANITIZE_URL));
    }

    /** Load controller file + its model dependencies */
    private function loadDependencies(string $ctrlName): void
    {
        // Core models always needed
        $always = ['User'];
        foreach ($always as $m) {
            $f = BASE_PATH . "/app/models/$m.php";
            if (file_exists($f)) require_once $f;
        }

        // Controller file
        $ctrlFile = BASE_PATH . "/app/controllers/{$ctrlName}.php";
        if (file_exists($ctrlFile)) require_once $ctrlFile;

        // Auto-load matching model (singular)
        $modelName = str_replace('Controller', '', $ctrlName);
        // Handle "Sessions" → try "Session" model too
        foreach ([$modelName, rtrim($modelName, 's')] as $mn) {
            $modelFile = BASE_PATH . "/app/models/{$mn}.php";
            if (file_exists($modelFile)) {
                require_once $modelFile;
                break;
            }
        }
    }

    private function show404(): void
    {
        http_response_code(404);
        $view = BASE_PATH . '/app/views/errors/404.php';
        if (file_exists($view)) require_once $view;
        exit;
    }
}
