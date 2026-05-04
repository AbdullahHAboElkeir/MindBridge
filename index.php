<?php
// MindBridge - Mental Health & Wellness Portal
// Entry point for the application

error_reporting(E_ALL);
ini_set('display_errors', '1');

session_start();

// Load core classes
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Model.php';
require_once __DIR__ . '/core/View.php';
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/Controller.php';

// Autoloader for models and controllers
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/app/controllers/' . $class . '.php',
        __DIR__ . '/app/models/' . $class . '.php',
        __DIR__ . '/core/' . $class . '.php',
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return true;
        }
    }
    return false;
});

try {
    // Get controller and action from URL
    $controller = ucfirst(strtolower($_GET['controller'] ?? 'home')) . 'Controller';
    $action = $_GET['action'] ?? 'index';

    // Validate controller exists
    if (!class_exists($controller)) {
        http_response_code(404);
        echo "<h1>404 - Controller Not Found</h1>";
        echo "<p>Controller: " . htmlspecialchars($controller) . "</p>";
        exit;
    }

    // Create controller instance
    $controllerInstance = new $controller();

    // Validate action exists
    if (!method_exists($controllerInstance, $action)) {
        http_response_code(404);
        echo "<h1>404 - Action Not Found</h1>";
        echo "<p>Action: " . htmlspecialchars($action) . "</p>";
        exit;
    }

    // Execute the action
    $controllerInstance->{$action}();

} catch (PDOException $e) {
    http_response_code(500);
    echo "<h1>Database Error</h1>";
    echo "<p>Please ensure MySQL is running in XAMPP and the database is set up.</p>";
    echo "<p>Run: <code>php setup.php</code></p>";
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo "<h1>Application Error</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}
?>