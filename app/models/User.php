<?php
// app/models/User.php

require_once __DIR__ . '/../../core/Model.php';

class User extends Model {
    protected $table = 'users';

    public function getWithRole($id) {
        $stmt = $this->db->prepare("
            SELECT u.*, r.name as role_name, r.description as role_description
            FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE u.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getAllWithRoles() {
        $stmt = $this->db->query("
            SELECT u.*, r.name as role_name
            FROM users u
            JOIN roles r ON u.role_id = r.id
            ORDER BY u.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    public function authenticate($email, $password) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    public function getByRole($roleName) {
        $stmt = $this->db->prepare("
            SELECT u.*, r.name as role_name
            FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE r.name = ?
        ");
        $stmt->execute([$roleName]);
        return $stmt->fetchAll();
    }
}
?>