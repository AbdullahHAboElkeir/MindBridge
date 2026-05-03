<?php
// app/models/Patient.php

require_once __DIR__ . '/../../core/Model.php';

class Patient extends Model {
    protected $table = 'patients';

    public function getWithUser($userId) {
        $stmt = $this->db->prepare("
            SELECT p.*, u.username, u.email, u.first_name, u.last_name
            FROM patients p
            JOIN users u ON p.user_id = u.id
            WHERE p.user_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    public function getAllWithUsers() {
        $stmt = $this->db->query("
            SELECT p.*, u.username, u.email, u.first_name, u.last_name
            FROM patients p
            JOIN users u ON p.user_id = u.id
        ");
        return $stmt->fetchAll();
    }
}
?>