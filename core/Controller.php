<?php
// core/Controller.php

abstract class Controller {
    protected function render($view, $data = []) {
        extract($data);
        $viewPath = __DIR__ . '/../app/views/' . $view . '.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            die("View $view not found");
        }
    }

    protected function redirect($url) {
        header("Location: $url");
        exit;
    }

    protected function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    protected function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return $_SESSION['user'];
        }
        return null;
    }

    protected function requireLogin() {
        if (!$this->isLoggedIn()) {
            $this->redirect('/login');
        }
    }

    protected function checkRole($role) {
        $user = $this->getCurrentUser();
        return $user && $user['role_name'] === $role;
    }

    protected function requireRole($role) {
        if (!$this->checkRole($role)) {
            $this->redirect('/unauthorized');
        }
    }
}
?>