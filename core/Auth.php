<?php
class Auth
{
    public function login(string $email, string $password): bool
    {
        $userModel = new User();
        $user = $userModel->findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
            ];
            AuditLog::record($user['id'], 'login', 'User logged in.');
            return true;
        }
        return false;
    }

    public function logout(): void
    {
        if (isset($_SESSION['user'])) {
            AuditLog::record($_SESSION['user']['id'], 'logout', 'User logged out.');
        }
        session_unset();
        session_destroy();
    }

    public function isLoggedIn(): bool
    {
        return !empty($_SESSION['user']);
    }

    public function user(): array
    {
        return $_SESSION['user'] ?? [];
    }

    public function hasRole(string $role): bool
    {
        return $this->isLoggedIn() && ($_SESSION['user']['role'] ?? '') === $role;
    }
}
