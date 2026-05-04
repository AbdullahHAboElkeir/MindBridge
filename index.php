<?php
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Model.php';
require_once __DIR__ . '/core/View.php';
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/Controller.php';

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

$controllerName = ucfirst(strtolower($_GET['controller'] ?? 'auth')) . 'Controller';
action:
$action = $_GET['action'] ?? 'index';

if (!class_exists($controllerName)) {
    http_response_code(404);
    echo 'Controller not found: ' . htmlspecialchars($controllerName);
    exit;
}
$controller = new $controllerName();
if (!method_exists($controller, $action)) {
    http_response_code(404);
    echo 'Action not found: ' . htmlspecialchars($action);
    exit;
}
$controller->{$action}();
