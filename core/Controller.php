<?php

// ==========================
// MVC Pattern - Controller Layer
// Handles HTTP requests, prepares data, and invokes views.
// Base controller also provides shared utilities for all controllers.
// ==========================
abstract class Controller
{
    protected Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ==========================
    // Factory Method Pattern
    // Creates and returns model instances by name.
    // This isolates model instantiation from controller logic.
    // ==========================
    protected function model(string $model): object
    {
        $file = BASE_PATH . '/app/models/' . $model . '.php';
        if (!file_exists($file)) {
            throw new RuntimeException("Model not found: $model");
        }
        require_once $file;
        return new $model();
    }

    /**
     * Render a view file with data.
     *
     * @param string $view  Dot-notation path: 'auth.login' => views/auth/login.php
     * @param array  $data  Variables to extract into the view
     */
    protected function view(string $view, array $data = []): void
    {
        // Convert dot notation to directory separator
        $viewPath = str_replace('.', DIRECTORY_SEPARATOR, $view);
        $file = BASE_PATH . '/app/views/' . $viewPath . '.php';

        if (!file_exists($file)) {
            throw new RuntimeException("View not found: $view ($file)");
        }

        // Extract variables into scope
        extract($data, EXTR_SKIP);

        require $file;
    }

    /**
     * Redirect to a URL (relative to BASE_URL or absolute).
     */
    protected function redirect(string $path): void
    {
        $url = str_starts_with($path, 'http') ? $path : BASE_URL . '/' . ltrim($path, '/');
        header('Location: ' . $url);
        exit;
    }

    /**
     * Return JSON response (for AJAX endpoints).
     */
    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Get POST data with optional sanitization.
     */
    protected function post(string $key, mixed $default = null): mixed
    {
        $value = $_POST[$key] ?? $default;
        return is_string($value) ? trim($value) : $value;
    }

    /**
     * Get GET/query param.
     */
    protected function get(string $key, mixed $default = null): mixed
    {
        $value = $_GET[$key] ?? $default;
        return is_string($value) ? trim($value) : $value;
    }

    /**
     * Check if request is POST.
     */
    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Check if request is AJAX.
     */
    protected function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Sanitize a string to prevent XSS.
     */
    protected function sanitize(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Validate CSRF token.
     */
    protected function validateCsrf(): bool
    {
        $token = $_POST['csrf_token'] ?? '';
        return hash_equals(Session::get('csrf_token', ''), $token);
    }

    /**
     * Generate and store CSRF token.
     */
    protected function generateCsrf(): string
    {
        if (!Session::has('csrf_token')) {
            Session::set('csrf_token', bin2hex(random_bytes(32)));
        }
        return Session::get('csrf_token');
    }

    /**
     * Log an action to the audit_logs table.
     */
    protected function auditLog(string $action, string $entity = '', string $details = ''): void
    {
        try {
            $this->db->insert(
                "INSERT INTO audit_logs (user_id, action, entity, details, ip_address, created_at)
                 VALUES (?, ?, ?, ?, ?, NOW())",
                [
                    Session::userId(),
                    $action,
                    $entity,
                    $details,
                    $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
                ]
            );
        } catch (Exception $e) {
            // Don't break the app for audit log failures
        }
    }
}
