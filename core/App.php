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
        $methodName = $this->resolveMethod($url[1] ?? 'index');

        if (method_exists($ctrl, $methodName)) {
            $this->method = $methodName;
            unset($url[1]);
        } elseif ($methodName !== 'index') {
            // Try treating it as a parameter for the default 'index' method
            // e.g. /forum/view/5 → ForumController::view(5)
            $this->show404();
            return;
        }

        // 4) Params
        $this->params = $url ? array_values($url) : [];

        // 5) Dispatch — cast numeric params to int
        $castParams = array_map(
            fn($p) => ctype_digit((string)$p) ? (int)$p : $p,
            $this->params
        );
        call_user_func_array([$ctrl, $this->method], $castParams);
    }

    /**
     * Convert URL segment to camelCase method name.
     * e.g. "do-login" → "doLogin", "update_profile" → "updateProfile"
     */
    private function resolveMethod(string $segment): string
    {
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
