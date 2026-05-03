<?php
// app/models/MoodTracker.php

require_once __DIR__ . '/../../core/Model.php';

class MoodTracker extends Model {
    protected $table = 'mood_trackers';

    public function getByPatient($patientId) {
        $stmt = $this->db->prepare("SELECT * FROM mood_trackers WHERE patient_id = ? ORDER BY recorded_at DESC");
        $stmt->execute([$patientId]);
        return $stmt->fetchAll();
    }

    public function getAverageMood($patientId, $days = 7) {
        $stmt = $this->db->prepare("
            SELECT AVG(mood_level) as average
            FROM mood_trackers
            WHERE patient_id = ?
            AND recorded_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
        ");
        $stmt->execute([$patientId, $days]);
        $result = $stmt->fetch();
        return $result ? round($result['average'], 1) : 0;
    }
}
?>