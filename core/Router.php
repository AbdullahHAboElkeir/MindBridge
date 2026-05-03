<?php
// core/Router.php

class Router {
    private $routes = [];

    public function get($path, $handler) {
        $this->routes['GET'][$path] = $handler;
    }

    public function post($path, $handler) {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

        // Handle different base paths
        $path = str_replace('/MindBridge', '', $path);
        $path = str_replace('/index.php', '', $path);

        if (isset($this->routes[$method][$path])) {
            $handler = $this->routes[$method][$path];
            if (is_callable($handler)) {
                call_user_func($handler);
            } elseif (is_array($handler)) {
                $controller = new $handler[0]();
                $method = $handler[1];
                $controller->$method();
            }
        } else {
            // Try with parameters
            foreach ($this->routes[$method] ?? [] as $route => $handler) {
                $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $route);
                if (preg_match("#^$pattern$#", $path, $matches)) {
                    array_shift($matches); // Remove full match
                    if (is_callable($handler)) {
                        call_user_func_array($handler, $matches);
                    } elseif (is_array($handler)) {
                        $controller = new $handler[0]();
                        $methodName = $handler[1];
                        call_user_func_array([$controller, $methodName], $matches);
                    }
                    return;
                }
            }
            http_response_code(404);
            echo "404 Not Found - Path: $path";
        }
    }
}
?>