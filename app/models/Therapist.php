<?php
// app/models/Therapist.php

require_once __DIR__ . '/../../core/Model.php';

class Therapist extends Model {
    protected $table = 'therapists';

    public function getWithUser($userId) {
        $stmt = $this->db->prepare("
            SELECT t.*, u.username, u.email, u.first_name, u.last_name
            FROM therapists t
            JOIN users u ON t.user_id = u.id
            WHERE t.user_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    public function getAllWithUsers() {
        $stmt = $this->db->query("
            SELECT t.*, u.username, u.email, u.first_name, u.last_name
            FROM therapists t
            JOIN users u ON t.user_id = u.id
        ");
        return $stmt->fetchAll();
    }

    public function getAvailable($date, $time) {
        // Simplified availability check
        $stmt = $this->db->prepare("
            SELECT t.*, u.first_name, u.last_name
            FROM therapists t
            JOIN users u ON t.user_id = u.id
            WHERE t.is_verified = 1
            AND NOT EXISTS (
                SELECT 1 FROM sessions s
                WHERE s.therapist_id = t.user_id
                AND DATE(s.scheduled_date) = ?
                AND TIME(s.scheduled_date) = ?
                AND s.status IN ('scheduled', 'confirmed')
            )
        ");
        $stmt->execute([$date, $time]);
        return $stmt->fetchAll();
    }
}
?>