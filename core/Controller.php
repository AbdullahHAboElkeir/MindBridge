<?php
class Controller
{
    protected View $view;
    protected Auth $auth;
    protected array $config;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->view = new View();
        $this->auth = new Auth();
        $this->config = require __DIR__ . '/../config/config.php';
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    protected function authorize(array $roles = []): void
    {
        if (!$this->auth->isLoggedIn()) {
            $this->redirect($this->config['app']['base_url'] . '?controller=auth&action=login');
        }
        if (!empty($roles) && !in_array($this->auth->user()['role'], $roles, true)) {
            $this->view->render('errors/403');
            exit;
        }
    }

    protected function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }
}
