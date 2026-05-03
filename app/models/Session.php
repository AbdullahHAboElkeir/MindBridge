<?php
// app/models/Session.php

require_once __DIR__ . '/../../core/Model.php';

class Session extends Model {
    protected $table = 'sessions';

    public function getWithDetails($id) {
        $stmt = $this->db->prepare("
            SELECT s.*, 
                   p.first_name as patient_first, p.last_name as patient_last,
                   t.first_name as therapist_first, t.last_name as therapist_last
            FROM sessions s
            JOIN users p ON s.patient_id = p.id
            JOIN users t ON s.therapist_id = t.id
            WHERE s.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getByUser($userId, $role) {
        if ($role === 'patient') {
            $stmt = $this->db->prepare("
                SELECT s.*, u.first_name, u.last_name
                FROM sessions s
                JOIN users u ON s.therapist_id = u.id
                WHERE s.patient_id = ?
                ORDER BY s.scheduled_date DESC
            ");
        } else {
            $stmt = $this->db->prepare("
                SELECT s.*, u.first_name, u.last_name
                FROM sessions s
                JOIN users u ON s.patient_id = u.id
                WHERE s.therapist_id = ?
                ORDER BY s.scheduled_date DESC
            ");
        }
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
?>